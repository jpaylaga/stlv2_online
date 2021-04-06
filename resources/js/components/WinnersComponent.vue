<template>
    <div id="winners-list">
        <loading :active.sync="isLoading">
        </loading>
        <div class="row">
            <div class="col-12 mt-1 mb-1">
                <div class="content-header mt-0">{{draw_date | draw_date}} Winners</div>
                <!-- <p class="content-sub-header"><strong>Total Hits</strong>: {{summary_total_hits}}</p> -->
                <div class="alert alert-info inline-block mb-0 mt-2" v-if="!$is('super_admin')">
                    <h5 class="mb-0">Current Balance: <strong>{{credit_balance | currency('&#8369;')}}</strong></h5>
                </div>
                <button type="button" class="display-block btn btn-success mt-2 mb-0" @click.prevent="newPayout">
                    <i class="icon-docs"></i> New Payout
                </button>
            </div>
        </div>
        <!--Extended Table starts-->
        <section id="extended">                        
            <div class="row">
                <div class="col-sm-12 p-0">
                    <div class="card">
                        <div class="card-header pt-2 pb-0">
                            <div class="form-group filter">
                                <div class="row p-0">
                                    <div class="mb-2 col-md-2 date-picker">
                                        <label><strong>Total Amount({{summary_total_hits}} Hits)</strong></label>
                                        <h4 style="margin-top: 8px;">{{total_winnings | currency('&#8369;')}}</h4>
                                    </div>
                                    <div class="mb-2 col-md-2 date-picker dp-fullwidth">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchHeadingData" 
                                            v-model="draw_date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="mb-2 col-md-2">
                                        <label>Ticket#</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="mb-0 col-md-2">
                                        <label>Draw Time</label>
                                        <select id="draw_time" v-model="draw_time" class="form-control" @change="fetchData">
                                            <option value="">All</option>
                                            <option v-for="dt in $getDrawTimeOptions()" :key="dt.id" :value="dt.id">{{dt.name}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <label>&nbsp;</label>
                                        <button @click="fetchData" class="btn btn-info mb-0 w-full"> <i class="ft-search mr-2"></i>Search </button>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <label>&nbsp;</label>
                                        <button @click="print" class="btn btn-info mb-0 w-full"> <i class="ft-printer mr-2"></i>Print </button>
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
                                            <th style="width: 115px;">Status</th>
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

        <!-- PRINT -->
        <div id="printWinners" v-if="winners" v-show="false">
            <print-winners-list 
                :draw_date="draw_date"
                :draw_time="draw_time"
                :total_winnings="total_winnings"
                :winners="winners">
            </print-winners-list>
        </div>
        <!-- PRINT -->

    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker'
    import WinnersList from './print/WinnersList.vue';

    export default {
        data () {
            return {
                isLoading: false,
                winners: null,
                total_winnings: 0,
                summary_total_winnings: 0,
                summary_total_hits: 0,
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
                credit_balance: 0,
                current_user: null,
            }
        },
        computed: {
            user() {
                return this.$store.getters.getUser
            }, 
        },
        created () {
            this.fetchHeadingData();
            this.current_user = this.$getUser();
            this.getCreditBalance()
        },
        components: {
            Loading, DatePicker, 'print-winners-list': WinnersList
        },
        methods: {
            fetchHeadingData(){
                let draw_date = moment(this.draw_date).format('MM/DD/YYYY');
                axios.get('/api/winners/summary', {
                    params: { draw_date: draw_date }
                }).then(response => {
                    this.summary_total_winnings = response.data.total;
                    this.summary_total_hits = response.data.hits;
                    this.fetchData();
                });
            },
            fetchData () {
                this.isLoading = true;
                let draw_date = moment(this.draw_date).format('MM/DD/YYYY');
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
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            },
            getCreditBalance(){
                axios.get('/api/user/credit-balance/' + this.current_user.id).then(response => {
                    this.credit_balance = response.data.credit_balance
                });
            },
            payWinner(){
                this.$swal('Pay Winner', 'warning');
            },
            print(){
                this.$htmlToPaper('printWinners', () => {
                    console.log('Printing done or got cancelled!');
                });
            },
            newPayout() {
                this.$swal({
                    title: '<h4>Search Ticket Number</h4>',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'on'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Search',
                    showLoaderOnConfirm: true,
                    preConfirm: (ticket_number) => {
                        return axios.get('/api/winners/get-winning-ticket', {
                            params: { ticket_number: ticket_number }
                        })
                        .then(response => {
                            if( response.data.success )
                                return response.data;
                            this.$swal.showValidationMessage(response.data.message);
                        }).catch(error => {
                            this.$swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value) {
                            let tickets = result.value.tickets;
                            let html = '<table class="table text-left">';
                            html += '<tr>';
                            html += '<th>Combination</th>';
                            html += '<th>Type</th>';
                            html += '<th>Amount</th>';
                            html += '<th>Win</th>';
                            html += '</tr>';
                            _.forEach(tickets, function(value, key) {
                                html += '<tr>';
                                html += '<td>'+ value.bet.combination +'</td>';
                                html += '<td>'+ (value.bet.type == 'straight' ? 'Target' : 'Ramble') +'</td>';
                                html += '<td>&#8369;'+ value.bet.amount +'</td>';
                                html += '<td>&#8369;'+ value.bet.winning_amount +'</td>';
                                html += '</tr>';
                            });
                            html += '</table>';

                            this.$swal({
                                title: 'Winning Ticket Found',
                                html: html,
                                type: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Payout'
                            }).then(result => {
                                if (result.value) {
                                    this.payoutTicket(tickets[0].ticket_number);
                                }
                            })
                    }
                })
            }, //@end newPayout
            payoutTicket(ticket_number){
                axios.post('/api/winners/payout', {
                    ticket_number: ticket_number
                }).then(resp => {
                    if( resp.data.success ){
                        this.$swal('Payout Success!', ticket_number, 'success');
                        window.location.reload();
                    }else{
                        this.$swal('Error', resp.data.message, 'error');
                    }
                });
            }, //@end payoutTicket
            removeWinner(win){
                this.$swal({
                    title: 'Delete Duplicate',
                    html: 'Are you sure?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then(result => {
                    if (result.value) {
                        axios.post('/api/winners/remove', {
                            winner_id: win.id
                        }).then(resp => {
                            if( resp.data.success ){
                                this.$swal('Success', 'Duplicate successfully deleted!', 'success');
                                this.fetchData();
                            }else{
                                this.$swal('Error', resp.data.message, 'error');
                            }
                        });
                    }
                })
            }, //@end removeWinner
        },
    }
</script>

<style>
    /* #winners-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; } */
</style>