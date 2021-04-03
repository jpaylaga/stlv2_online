<template>
    <div id="dashboard">
        <loading :active.sync="isLoading"></loading>
        <div class="row match-height">
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 danger" v-if="reports.gross_sales">{{reports.gross_sales['11am'] | currency('&#8369;', 0) }}</h3>
                                    <span>Draw Sales 11 AM</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-bar-chart danger font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 warning" v-if="reports.gross_sales">{{reports.gross_sales['4pm'] | currency('&#8369;', 0) }}</h3>
                                    <span>Draw Sales 4 PM</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-bar-chart warning font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 success" v-if="reports.gross_sales">{{reports.gross_sales['9pm'] | currency('&#8369;', 0) }}</h3>
                                    <span>Draw Sales 9 PM</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-bar-chart success font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <div class="row match-height">
            <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 primary">{{total_sales | currency('&#8369;', 0) }}</h3>
                                    <span>Total Sales</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-bar-chart-2 primary font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                <a href="/winners"><div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 success"  v-if="reports.winnings">
                                        {{getTotalWinnings() | currency('&#8369;', 0) }}
                                        ({{ Math.round(getTotalHits() * 10) / 10}})
                                    </h3>
                                    <span>Total Payout</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-user-check success font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div></a>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3 py-3">
                            <div class="media">
                                <div class="media-body text-left">
                                    <h3 class="mb-1 danger">{{total_transactions}}</h3>
                                    <span>Total Transactions</span>
                                </div>
                                <div class="media-right align-self-center">
                                    <i class="ft-file-text danger font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-md-8">
                <results :draw_date="draw_date" ref="Results"></results>
            </div>
            <div class="col-xl-4 col-md-4">
                <bet-stats :draw_date="draw_date" ref="BetStats" />
            </div>
        </div>

    </div>
</template>


<script>
    import moment from 'moment';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import results from './ResultSales.vue';
    import SalesPerGame from './SalesPerGame.vue';
    import BetStats from './BetStats.vue';

    export default {
        props: ['draw_date'],
        data () {
            return {
                isLoading: false,
                errors: [],
                today: this.draw_date,
                reports: [],
                draw_sales: {
                    11: [],
                    16: [],
                    21: [],
                },
                total_sales: 0,
                net_sales: 0,
                percentage: 0,
                float: 0,
                total_transactions: 0,
                tickets: [],
                summary_total_winnings: 0,
                summary_total_hits: 0,
                user_id: $('meta[name="userId"]').attr('content'),
                total_earnings: 0,
            }
        },
        created() {
            this.initData(this.draw_date);
            // this.fetchWinnersSummary(draw_date);
            this.listen();
        },
        components: { Loading, results, 'sales-per-game' : SalesPerGame, 'bet-stats': BetStats },
        methods: {
            initData (draw_date) {
                this.isLoading = true;
                // let draw_date = this.draw_date;
                axios.get('/api/sales/draw-sales',{
                    params:{
                        'draw_date': draw_date,
                    }
                }).then(response => {
                    this.refreshResultsTable(draw_date);
                    this.tickets = response.data;
                    // this.$refs.Results.getReports(draw_date);
                    this.$refs.Results.fetchResults(draw_date);
                    this.calculateTotalAmount();

                    // this.fetchWinnersSummary(draw_date);
                    // this.calculateTotalAmount('16');
                    // this.calculateTotalAmount('21');
                    // this.calculateEarnings();
                    // this.calculateNetSales();
                });
            },
            // fetchWinnersSummary(draw_date){
            //     axios.get('/api/winners/summary', {
            //         params: {
            //             draw_date: draw_date
            //         }
            //     }).then(response => {
            //         this.summary_total_winnings = response.data.total;
            //         this.summary_total_hits = response.data.hits;
            //         // this.calculateEarnings();
            //     });
            // },
            calculateTotalAmount(draw_time) {
                // let filteredTickets = _.filter(this.tickets, { 'draw_time': draw_time });
                // let sum = _.sumBy(filteredTickets, (ticket) => { 
                //         return parseInt( ticket.total_amount ); 
                //     });
                // this.draw_sales[draw_time] = sum;
                this.total_transactions = this.tickets.length;
                this.total_sales = _.sumBy(this.tickets, (ticket) => { 
                        return parseInt( ticket.total_amount ); 
                    });
                this.isLoading = false;
            },
            // calculateEarnings() {
            //     let total_winnings = this.summary_total_winnings + (this.summary_total_hits * this.float);
            //     this.total_earnings = this.net_sales - total_winnings;
            // },
            // calculateNetSales() {
            //     this.net_sales = this.total_sales - ( this.total_sales * (this.percentage / 100) );
            // },
            refreshResultsTable(draw_date) {
                axios.get('/api/sales/result-sales', {
                    params: {
                        date: draw_date,
                        date_to: draw_date,
                    }
                }).then( response => {
                    this.reports = response.data;
                    this.$refs.Results.getReports(response.data);
                });
                // this.$refs.Results.getReports(this.draw_date);
            },
            fetchHotNumbers() {
                this.$refs.BetStats.fetchHotNumbers();
            },
            getTotalWinnings(){
                return this.reports.winnings['11am'] + this.reports.winnings['4pm'] + this.reports.winnings['9pm'];
            },
            getTotalHits(){
                return this.reports.hits['11am'] + this.reports.hits['4pm'] + this.reports.hits['9pm'];
            },
            listen() {
                Echo.private('user.' + this.user_id)
                    .listen('TicketCreated', (e) => {
                        if( moment(new Date()).format('MM/DD/YYYY') == moment(this.draw_date).format('MM/DD/YYYY') ){
                            this.tickets.unshift(e.ticket);
                            this.calculateTotalAmount(e.ticket.draw_time);
                            // this.calculateEarnings();
                            // this.calculateNetSales();
                            this.refreshResultsTable(this.draw_date);
                            this.fetchHotNumbers();
                        }
                    });
            }
        }
    }
</script>