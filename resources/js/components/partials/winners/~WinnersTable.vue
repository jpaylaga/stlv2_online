<template>
    <div id="winners-list">
        <loading :active.sync="isLoading">
        </loading>
        <div class="row">
            <div class="col-12 mt-1 mb-1">
                <div class="content-header">Today's Winners</div>
                <p class="content-sub-header">ALL | Total Hits: {{summary_total_hits}} | Total amount: {{summary_total_winnings | currency('&#8369;')}}</p>
            </div>
        </div>
        <!--Extended Table starts-->
        <section id="extended">                        
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row" style="padding: 0 15px;">
                                    <div class="col-md-2 date-picker">
                                        <label><strong>Total Amount</strong></label>
                                        <h4 style="margin-top: 8px;">{{total_winnings | currency('&#8369;')}}</h4>
                                    </div>
                                    <div class="col-md-3"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-3 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchData" 
                                            v-model="draw_date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-2">
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
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-md text-left">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ticket No.</th>
                                            <th>Outlet</th>
                                            <th>Bet</th>
                                            <th>Game</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Win Amt</th>
                                            <th>Draw Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="win in winners" :key="win.id">
                                            <td>{{win.id}}</td>
                                            <td>{{win.ticket.ticket_number | uppercase}}</td>
                                            <td>
                                                <span v-if="win.ticket.transaction.user.outlets.length > 0">
                                                    ({{win.ticket.transaction.user.outlets[0].name}})
                                                </span>
                                                {{win.ticket.transaction.user.firstname +' '+ win.ticket.transaction.user.lastname}}
                                            </td>
                                            <td>{{win.bet.combination}}</td>
                                            <td>{{win.draw.game.name}}</td>
                                            <td>{{win.bet.type == 'straight' ? 'Target' : 'Ramble'}}</td>
                                            <td>{{win.bet.amount | currency('&#8369;')}}</td>
                                            <td>{{win.bet.winning_amount | currency('&#8369;')}}</td>
                                            <td>
                                                {{ win.ticket.draw_date | draw_date }} {{ win.ticket.draw_time | draw_time }}
                                            </td>
                                            <td>
                                                <span class="badge" :class="'badge-' + (win.status == 'paid' ? 'success' : 'danger')">
                                                    {{win.status == 'paid' ? 'Paid' : 'Unpaid'}}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
    import DatePicker from 'vue2-datepicker'

    export default {
        data () {
            return {
                isLoading: false,
                winners: [],
                total_winnings: 0,
                summary_total_winnings: 0,
                summary_total_hits: 0,
                draw_date: new Date(),
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
        computed: {
            user() {
                return this.$store.getters.getUser
            }, 
        },
        created () {
            this.fetchData(this.draw_date, this.draw_time);
            this.fetchHeadingData();
        },
        components: {
            Loading, DatePicker
        },
        methods: {
            fetchHeadingData(){
                axios.get('/api/winners/summary').then(response => {
                    this.summary_total_winnings = response.data.total;
                    this.summary_total_hits = response.data.hits;
                });
            },
            fetchData (draw_date, draw_time) {
                this.isLoading = true;

                axios.get('/api/winners/draw', {
                    params: {
                        'draw_date': draw_date,
                        'draw_time': draw_time,
                    }
                }).then(response => {
                    this.winners = response.data;
                    this.total_winnings = window._.sumBy(this.winners, (win) => { 
                        return parseInt( win.bet.winning_amount ); 
                    });
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            },
            payWinner(){
                this.$swal('Pay Winner', 'warning');
            }
        },
    }
</script>

<style>
    #winners-list .card-header{ padding-bottom: 10px; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>