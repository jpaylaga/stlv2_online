<template>
    <div class="main-content">
        <div id="all-transasctions" class="px-1 py-1">
            <loading :active.sync="isLoading">
            </loading>
            <!--Extended Table starts-->
            <div class="row">
                <div class="col-md-6 col-sm-8">
                    <h4>Winnings</h4>
                    <div class="alert alert-info mb-0">
                        <h5 class="mb-0">Current Balance: <strong>{{credit_balance | currency('&#8369;')}}</strong></h5>
                    </div>
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
                                        <div class="col-md-2 date-picker dp-fullwidth">
                                            <label>Draw Date</label>
                                            <date-picker 
                                                @change="fetchData" 
                                                v-model="draw_date"
                                                :format="'MMMM DD, YYYY'"
                                                :lang="lang">
                                            </date-picker>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Ticket#</label>
                                            <input v-model="ticket_number" type="text" class="form-control">
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
                                        <div class="col-md-2 form-actions">
                                            <button @click="fetchData" class="btn btn-outline-primary round">
                                                <i class="ft-search mr-2"></i>Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-block">
                                    <table class="table table-responsive-md text-left">
                                        <thead>
                                            <tr>
                                                <th>Ticket No.</th>
                                                <th>Bet</th>
                                                <th>Game</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Win Amt</th>
                                                <th>Draw Date</th>
                                                <th style="width: 115px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="win in winners" :key="win.id">
                                                <td>{{win.ticket.ticket_number | uppercase}}</td>
                                                <td>{{win.bet.combination}}</td>
                                                <td>{{win.draw.game.name}}</td>
                                                <td>{{win.bet.type == 'straight' ? 'Target' : 'Ramble'}}</td>
                                                <td>{{win.bet.amount | currency('&#8369;')}}</td>
                                                <td>{{win.bet.winning_amount | currency('&#8369;')}}</td>
                                                <td>
                                                    {{ win.ticket.draw_date | draw_date }} 
                                                    <span class="drawtime" :class="'time-' + win.ticket.draw_time">{{$getDrawTimeOptions(win.ticket.draw_time)}}</span>
                                                </td>
                                                <td>
                                                    <span class="badge" :class="'badge-' + (win.status == 'paid' ? 'success' : 'danger')">
                                                        {{win.status == 'paid' ? 'Paid' : 'Unpaid'}}
                                                    </span>
                                                    <span v-if="user.username=='dev'" class="added">
                                                        <span @click="removeWinner(win)" class="remove"><i class="ft-x-circle"></i></span>
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
                winners: null,
                total_winnings: 0,
                draw_date: new Date(),
                draw_time: '',
                lang: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                    placeholder: {
                        date: 'Select Date',
                    }
                },
                ticket_number: '',
                player: null,
                credit_balance: 0,
            }
        },
        computed: {
            user() {
                return this.$store.getters.getUser
            }, 
        },
        created () {
            this.player = this.$getUser();
            this.getCreditBalance();
            this.fetchData();
        },
        components: {
            Loading, DatePicker
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                let draw_date = this.draw_date ? moment(this.draw_date).format('MM/DD/YYYY') : '';
                axios.get('/api/winners/draw', {
                    params: {
                        draw_date: draw_date,
                        draw_time: this.draw_time,
                        ticket_number: this.ticket_number
                    }
                }).then(response => {
                    this.winners = response.data;
                    this.total_winnings = window._.sumBy(this.winners, (win) => { 
                        return parseInt( win.bet.winning_amount ); 
                    });
                    // this.setDrawDateString();
                }).catch(err => { })
                .then(() => {
                    this.isLoading = false;
                })
            },
            getCreditBalance(){
                axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                    this.credit_balance = response.data.credit_balance
                });
            },
        },
    }
</script>

<style>
    #all-transasctions .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>