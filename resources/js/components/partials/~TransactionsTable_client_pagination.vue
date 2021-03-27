<template>
    <div id="all-transasctions">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Transactions</div>
                <p class="content-sub-header">Total Transactions: {{tickets.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li><a href="#" class="py-1 h6"><i class="ft-download font-medium-5 mr-2"></i><span>Download PDF</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-2 date-picker">
                                        <label><strong>Total Sales</strong></label>
                                        <h4 style="margin-top: 8px;">{{total_sales | currency('&#8369;')}}</h4>
                                    </div>
                                    <div class="col-md-3 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchData" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label>Game</label>
                                            <select id="game" v-model="game" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option v-for="game in gameOptions" :value="game.id" :key="game.id">{{game.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="padding-left: 30px;"> 
                                        <div class="form-group row">
                                            <label>Draw Time</label>
                                            <select id="draw_time" v-model="draw_time" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option value="11">11AM</option>
                                                <option value="16">4PM</option>
                                                <option value="21">9PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ticket Number</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-1 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TXN ID</th>
                                            <th>Ticket No.</th>
                                            <th>Outlet</th>
                                            <th>Bets</th>
                                            <th>Valid Amount</th>
                                            <th>Bet Date</th>
                                            <th>Draw Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ticket in ticketsToDisplay" :key="ticket.id">  
                                            <td>{{ticket.id}}</td>
                                            <td>{{ticket.txn_code | uppercase }}</td>
                                            <td>
                                                <ticket-popup 
                                                    :ticket_id="ticket.id"
                                                    :ticket_number="ticket.ticket_number"
                                                    :show_cancel="true">
                                                </ticket-popup>
                                            </td>
                                            <td>
                                                <span v-if="ticket.user.outlets.length > 0">
                                                    ({{ticket.user.outlets[0].name}})
                                                </span>
                                                {{ticket.user.firstname +' '+ ticket.user.lastname}}
                                            </td>

                                            <td>({{Object.keys(ticket.bets).length}})
                                                <span v-for="(value, combi) in ticket.bets" :key="value" class="bet-item">
                                                    <span>{{ combi }}</span>
                                                </span>
                                            </td>

                                            <td>{{ticket.total_amount | currency('&#8369;')}}</td>
                                            <td>{{ticket.created_at | bet_date}}</td>
                                            <td>
                                                {{ ticket.draw_date | draw_date }} 
                                                <span class="drawtime" :class="'time-' + ticket.draw_time">{{ticket.draw_time | draw_time}}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row text-center">
                                <div class="col-sm-6">
                                    <label class='control-label'>Show</label>
                                    <select class="form-control" v-model='perPage'>
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn-sm" v-show='showPrev' @click.stop.prevent='renderList(currentPage-1)'>Prev</button>
                                    Page {{currentPage}} of {{totalPages}}
                                    <button class="btn btn-primary btn-sm" v-show='showNext' @click.stop.prevent='renderList(currentPage+1)'>Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Extended Table Ends-->   
    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import TicketPopup from '../single-popup/TicketPopup.vue';

    export default {
        data () {
            return {
                isLoading: false,
                tickets: [],

                ticketsToDisplay: [],
                perPage: 5,
                pageToOpen: 1,
                currentPage: 1,
                
                total_sales: 0,
                ticket_number: '',
                game: '',
                gameOptions: [],
                date: new Date(),
                draw_time: '',
                lang: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                    placeholder: {
                        date: 'Select Date',
                    }
                },
            }
        },
        created () {
            this.initOptions();
        },
        components: {
            Loading, 'ticket-popup': TicketPopup
        },
        methods: {
            initOptions(){
                this.isLoading = true;
                axios.get('/api/games').then(resp => {
                    this.gameOptions = resp.data;
                    this.fetchData();
                });
            },
            fetchData () {
                this.isLoading = true;
                let date = moment( this.date ).format("MM/DD/YYYY")
                axios.get('/api/transactions', {
                    params: {
                        date: date,
                        game: this.game,
                        ticket_number: this.ticket_number, 
                        draw_time: this.draw_time
                    }
                })
                .then(response => {
                    this.loading = false;
                    this.tickets = _.orderBy(response.data, 'id', 'desc');
                    this.getTotalAmount();
                    this.isLoading = false;
                });
            },
            getTotalAmount () {
                this.total_sales = _.sumBy(this.tickets, (ticket) => { 
                    return parseInt( ticket.total_amount ); 
                });
                this.renderList();//re-renderlist on DOM
            },
            renderList(pageNumber=1){
                this.ticketsToDisplay = [];
                if(this.tickets.length){
                    let _this = this;
                    return new Promise(function(res, rej){
                        _this.pageToOpen = pageNumber;
                        for(let i = _this.start; i <= _this.stop; i++){
                            _this.ticketsToDisplay.push(_this.tickets[i]);
                        }
                        res();
                    }).then(function(){
                        _this.currentPage = _this.pageToOpen;
                    }).catch(function(err){
                        console.log(err);
                    });                  
                }
            }
        },
        computed: {
            totalPages(){ return this.tickets.length && (this.tickets.length > this.perPage) ? Math.ceil(this.tickets.length/this.perPage) : 1; },
            start(){ return (this.pageToOpen - 1) * this.perPage; },
            stop(){
                if((this.tickets.length - this.start) >= this.perPage)
                    return (this.pageToOpen * this.perPage) - 1;
                else
                    return this.tickets.length - 1;
            },
            showNext(){ return this.currentPage < this.totalPages; },
            showPrev(){ return this.currentPage > 1; }
        },
        watch: {
            perPage: function(){
                this.renderList();
            }
        }
    }
</script>

<style>
    #all-transasctions .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>