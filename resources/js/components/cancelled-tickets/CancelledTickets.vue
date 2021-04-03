<template>
    <div id="cancelled-tickets">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Cancelled Tickets</div>
                <p class="content-sub-header">Total Cancelled Transactions: {{tickets.length}}</p>
            </div>
        </div>
        <section id="extended">
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
                                    <div class="col-md-2">
                                        <label>Ticket Number</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-1 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-2 form-actions text-right">
                                        <button :disabled="tickets.length <= 0" style="width: 100%;" @click="print" class="btn btn-outline-primary round">
                                            <i class="ft-printer mr-2"></i>Print
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
                                            <th>Ticket No.</th>
                                            <th>Usher</th>
                                            <th>Bets</th>
                                            <th>Amount</th>
                                            <th>Cancelled by</th>
                                            <th>Cancelled Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ticket in tickets" :key="ticket.id">
                                            <td>
                                                <ticket-popup 
                                                    :ticket_id="ticket.id"
                                                    :ticket_number="ticket.ticket_number"
                                                    :show_cancel="false">
                                                </ticket-popup>
                                            </td>
                                            <td>
                                                <span v-if="ticket.agent.outlets.length > 0">
                                                    ({{ticket.agent.outlets[0].name}})
                                                </span>
                                                {{ticket.agent.firstname +' '+ ticket.agent.lastname}}
                                            </td>
                                            <td>({{Object.keys(ticket.bets).length}})
                                                <span v-for="(value, combi) in ticket.bets" :key="value" class="bet-item">
                                                    <span>{{ combi }}</span>
                                                </span>
                                            </td>
                                            <td>{{ticket.total_amount | currency('&#8369;')}}</td>
                                            <td>{{ticket.cancelled_by.firstname +' '+ ticket.cancelled_by.lastname}}</td>
                                            <td>{{ticket.details.created_at | bet_date}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRINT -->
        <div id="printCancelled" v-if="tickets" v-show="false">
            <print-cancelled-list 
                :draw_date="date"
                :tickets="tickets">
            </print-cancelled-list>
        </div>
        <!-- PRINT -->

    </div>
</template>


<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import TicketPopup from '../single-popup/TicketPopup.vue';
    import CancelledList from '../print/CancelledTickets.vue';

    export default {
        data () {
            return {
                isLoading: false,
                tickets: [],
                date: new Date(),
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
            this.fetchData();
        },
        components: {
            Loading, 'ticket-popup': TicketPopup, 'print-cancelled-list': CancelledList
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                let date = moment( this.date ).format("MM/DD/YYYY")
                axios.get('/api/cancelled-tickets', {
                    params: {
                        date: date,
                        ticket_number: this.ticket_number
                    }
                })
                .then(response => {
                    this.tickets = response.data;
                    this.tickets = _.orderBy(response.data, 'details', 'desc');
                    this.isLoading = false;
                });
            },
            print(){
                this.$htmlToPaper('printCancelled', () => {
                    console.log('Printing done or got cancelled!');
                });
            }
        },
    }
</script>

<style>
    #cancelled-tickets .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>