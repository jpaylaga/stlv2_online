<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="highestBetReports">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">Payouts Page</div>      
                    <p class="content-sub-header">Total Payouts: {{ getTotalPayouts() | currency('&#8369;') }}</p>
                    <nav id="top-right-text">
                        <ul>
                            <li>
                                <a href="#" class="py-1 h6" @click.prevent="newPayout">
                                    <i class="icon-docs font-medium-5 mr-2"></i>New Payout
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-3 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchData" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Ticket#</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-2 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>Search
                                        </button>
                                    </div>
                                    <!-- <div class="col-md-2">
                                        <div class="form-group row">
                                            <label>Game</label>
                                            <select id="game" v-model="game" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option v-for="game in gameOptions" :value="game.id" :key="game.id">{{game.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ticket#</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-2 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-filter mr-2"></i>Filter
                                        </button>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>Teller</th>
                                            <th>Ticket No.</th>
                                            <th>Bet</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Winning Price</th>
                                            <th>Draw Date</th>
                                            <th>Payout Date</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="payouts">
                                        <tr v-for="payout in payouts" :key="payout.id">
                                            <td>
                                                <span v-if="payout.ticket.transaction.user.outlets.length > 0">
                                                    ({{payout.ticket.transaction.user.outlets[0].name}})
                                                </span>
                                                {{payout.ticket.transaction.user.firstname +' '+ payout.ticket.transaction.user.lastname}}
                                            </td>
                                            <td>{{payout.ticket.ticket_number | uppercase}}</td>
                                            <td>{{payout.bet.combination}}</td>
                                            <td>{{payout.bet.type == 'straight' ? 'Target' : 'Ramble'}}</td>
                                            <td>{{payout.bet.amount | currency('&#8369;')}}</td>
                                            <td>{{payout.bet.winning_amount | currency('&#8369;')}}</td>
                                            <td>
                                                {{ payout.ticket.draw_date | draw_date }} {{ payout.ticket.draw_time | draw_time }}
                                            </td>
                                            <td>{{ payout.payout_date | draw_date }}</td>
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

    export default {
        data () {
            return {
                isLoading: false,
                date: new Date(), //default date
                ticket_number: '',
                payouts: [],
                // gameOptions: [],
                // game: '',
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
            this.defineOptions();
        },
        components: {
            Loading
        },
        methods: {
            defineOptions(){
                this.fetchData();
            },
            fetchData () {
                this.isLoading = true;
                let draw_date = moment(this.date).format('MM/DD/YYYY');
                axios.get('/api/winners/draw', {
                    params: {
                        draw_date : draw_date,
                        ticket_number : this.ticket_number,
                        // game : this.game,
                        status : 'paid',
                    }
                })
                .then(response => {
                    this.payouts = _.orderBy(response.data, 'id', 'desc');
                    this.isLoading = false;
                });
            },
            getTotalPayouts(){
                return _.sumBy(this.payouts, function(payout) { 
                    return parseInt( payout.bet.winning_amount ); 
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
            },
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
            }
        },
    }
</script>

<style>
    #highestBetReports .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>