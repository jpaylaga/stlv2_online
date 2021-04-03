<template>
    <div id="all-transasctions">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Transactions</div>
                <span v-if="!$isAgent()">
                    <p class="content-sub-header">
                        <strong>Total Transactions</strong>: {{tickets.length}} 
                        &nbsp;|&nbsp; 
                        <strong>Total Sales</strong>: {{total_sales | currency('&#8369;')}}</p>
                    <nav id="top-right-text">
                        <ul>
                            <li><a @click.prevent="downloadPdf" class="py-1 h6"><i class="ft-download font-medium-5 mr-2"></i><span>Download PDF</span></a></li>
                        </ul>
                    </nav>
                </span>
            </div>
        </div>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <!-- <div class="col-md-2 date-picker">
                                        <label><strong>Total Sales</strong></label>
                                        <h4 style="margin-top: 8px;">{{total_sales | currency('&#8369;')}}</h4>
                                    </div> -->
                                    <div class="col-md-3 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchData" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <!-- <div class="col-md-2">
                                        <div class="form-group row">
                                            <label>Game</label>
                                            <select id="game" v-model="game" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option v-for="game in gameOptions" :value="game.id" :key="game.id">{{game.name}}</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="col-md-2" style="padding-left: 30px;"> 
                                        <div class="form-group row">
                                            <label>Draw Time</label>
                                            <select id="draw_time" v-model="draw_time" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option v-for="dt in $getDrawTimeOptions()" :key="dt.id" :value="dt.id">{{dt.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ticket Number</label>
                                        <input v-model="ticket_number" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-4" v-if="!$isAgent()">
                                        <label>Teller</label>
                                        <model-select 
                                            :list="userOptions"
                                            option-value="id"
                                            :custom-text="fullName"
                                            v-model="teller"
                                            placeholder="Select Teller">
                                        </model-select>
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
                                <vue-good-table 
                                    mode="remote"
                                    :totalRows="totalRecords"
                                    :columns="columns" 
                                    :rows="tickets"
                                    :sort-options="{ enabled: false }"
                                    :pagination-options="paginationOptions"
                                    @on-page-change="onPageChange"
                                    @on-per-page-change="onPerPageChange"
                                >
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'ticket_number'">
                                            <ticket-popup 
                                                :ticket_id="props.row.id"
                                                :ticket_number="props.row.ticket_number"
                                                :show_cancel="true">
                                            </ticket-popup>
                                        </span>
                                        <span v-else-if="props.column.field == 'teller'">
                                            {{props.row.firstname +' '+ props.row.lastname}}
                                        </span>
                                        <span v-else-if="props.column.field == 'txn_code'">
                                            {{props.row.txn_code}} 
                                            <span v-if="props.row.is_cancelled" class="badge badge-danger">Cancelled</span>
                                        </span>
                                        <span v-else-if="props.column.field == 'bets'">
                                            ({{Object.keys(props.row.bets).length}})
                                            <span v-for="(value, combi) in props.row.bets" :key="value" class="bet-item">
                                                <span>{{ combi }}</span>
                                            </span>
                                        </span>
                                        <span v-else-if="props.column.field == 'total_amount'">
                                            {{props.row.total_amount | currency('&#8369;')}}
                                        </span>
                                        <span v-else-if="props.column.field == 'bet_date'">
                                            <small>
                                                {{props.row.created_at | bet_date}}
                                            </small>
                                        </span>
                                        <span v-else-if="props.column.field == 'draw_date'">
                                            <small>
                                                {{ props.row.draw_date | draw_date }} 
                                                <span class="drawtime" :class="'time-' + props.row.draw_time">{{$getDrawTimeOptions(props.row.draw_time)}}</span>
                                            </small>
                                        </span>
                                    </template>
                                </vue-good-table>
                                <!-- <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>TXN ID</th>
                                            <th>Ticket No.</th>
                                            <th>Teller</th>
                                            <th style="width: 200px;">Bets</th>
                                            <th>Amount</th>
                                            <th>Bet Date</th>
                                            <th>Draw Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ticket in tickets" :key="ticket.id">  
                                            <td>{{ticket.txn_code | uppercase }}</td>
                                            <td>
                                                <ticket-popup 
                                                    :ticket_id="ticket.id"
                                                    :ticket_number="ticket.ticket_number"
                                                    :show_cancel="true">
                                                </ticket-popup>
                                            </td>
                                            <td>
                                                {{ticket.firstname +' '+ ticket.lastname}}
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
                                </table> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Extended Table Ends-->   

        <div id="printTransactions" v-if="tickets" v-show="false">
            <transactions-print 
                :draw_time="draw_time"
                :tickets="tickets">
            </transactions-print>
        </div>

    </div>        
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import TicketPopup from '../single-popup/TicketPopup.vue';
    import TransactionsTable from '../print/Transactions.vue';

    import jsPDF from 'jspdf';
    import 'jspdf-autotable';  
    
    import {ModelListSelect} from 'vue-search-select';
    import 'vue-search-select/dist/VueSearchSelect.css';

    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';

    export default {
        data () {
            return {
                isLoading: false,
                tickets: [],
                total_sales: 0,
                ticket_number: '',
                game: '',
                teller: '',
                gameOptions: [],
                userOptions: [],
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

                // GOOD TABLE
                paginationOptions: {
                    enabled: true,
                    dropdownAllowAll: false,
                    position: 'top'
                },
                columns: [
                    { label: 'TXN ID', field: 'txn_code' },
                    { label: 'Ticket No.', field: 'ticket_number' },
                    { label: 'Teller', field: 'teller' },
                    { label: 'Bets', field: 'bets' },
                    { label: 'Total', field: 'total_amount' },
                    { label: 'Bet Date', field: 'bet_date' },
                    { label: 'Draw Date', field: 'draw_date' },
                ],
                requests: [],
                totalRecords: 0,
                serverParams: {
                    page: 1, 
                    per_page: 10,
                    filter: {
                        trans_type: '',
                        player: 0,
                        ref: '',
                        status: ''
                    }
                },
                credit_history: [],
            }
        },
        created () {
            this.initOptions();
        },
        components: {
            Loading, 'ticket-popup': TicketPopup, 'transactions-print': TransactionsTable, 'model-select': ModelListSelect, VueGoodTable
        },
        methods: {
            initOptions(){
                this.isLoading = true;
                this.loadGames();

                if( !this.$isAgent() )
                    this.loadUsers();

                this.fetchData();

                // axios.get('/api/games').then(resp => {
                //     this.gameOptions = resp.data;

                //     axios.get('/api/users', {params: { 'type':'tellers' }}).then(resp => {
                //         this.userOptions = resp.data;
                //         this.userOptions.unshift({
                //             id: '',
                //             firstname: 'Select',
                //             lastname: 'All',
                //         });
                //         this.fetchData();
                //     });
                // });
            },
            fetchData () {
                this.isLoading = true;
                let date = moment( this.date ).format("MM/DD/YYYY")
                axios.get('/api/transactions/tickets', {
                    params: {
                        date: date,
                        game: this.game,
                        user: this.teller,
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
            },
            downloadPdf() {
                let doc = new jsPDF('p', 'pt');

                let date = moment( this.date ).format("MMM DD, YYYY");
                let date_formatted = moment( this.date ).format("MMM_DD_YYYY");
                let draw_time = this.$options.filters.draw_time(this.draw_time);
                let filename = 'draw_' + date_formatted + '_' + draw_time;
                
                if( this.draw_time == '' ){
                    this.$swal('Please select draw time.', 'Oops!', 'error');
                    return;
                }
                if( this.tickets.length <= 0 ){
                    this.$swal('Data is empty.', 'Ooops!', 'error');
                    return;
                }

                var res = doc.autoTableHtmlToJson(document.getElementById("transactions"));

                var header = function(data) {
                    if( data.pageNumber == 1 ){
                        //doc.addImage(headerImgData, 'JPEG', data.settings.margin.left, 20, 50, 50);
                        doc.setFontSize(12);
                        doc.setTextColor(40);
                        doc.text("Draw Transactions", data.settings.margin.left, 40);
                        doc.setFontSize(10);
                        doc.text("Date: " + date, data.settings.margin.left, 60);
                        doc.text("Time: " + draw_time, data.settings.margin.left, 75);
                    }

                };

                doc.autoTable(
                    res.columns, 
                    res.data, 
                    {
                        addPageContent: header,
                        margin: {top: 90},
                        columnStyles: {
                            0: {columnWidth: 130},
                            1: {columnWidth: 100},
                            2: {columnWidth: 110},
                            3: {columnWidth: 50},
                        }
                    }
                );
                doc.save(filename + '.pdf')

                // https://www.npmjs.com/package/jspdf-autotable
            
            },
            fullName(user) {
                return `${user.firstname} ${user.lastname}`;
            },
            
            // options
            async loadGames(){
                await axios.get('/api/games').then(resp => {
                    this.gameOptions = resp.data;
                });
            },
            async loadUsers(){
                await axios.get('/api/users', {params: { 'type':'tellers' }}).then(resp => {
                    this.userOptions = resp.data;
                    this.userOptions.unshift({
                        id: '',
                        firstname: 'Select',
                        lastname: 'All',
                    });
                });
            },

            // table handlers
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.fetchData();
            },
            onPerPageChange(params) {
                this.updateParams({per_page: params.currentPerPage});
                this.fetchData();
            },
            // helpers
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