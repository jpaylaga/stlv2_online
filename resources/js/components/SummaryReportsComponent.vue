<template>
    <section id="summaryReports">
        <loading :active.sync="isLoading"></loading>
        <div class="row">
            <div class="col-sm-12">
                <div class="content-header">Summary Reports: Total Sales per Usher</div>                                
                <p class="content-sub-header">
                    <strong>Coordinator</strong>: <span v-if="owner">{{owner.firstname+' '+owner.lastname}}</span>
                </p>     
                <p class="content-sub-header">
                    <strong>Commission</strong>: <span v-if="owner">{{owner.percentage}}%</span>
                </p> 
                <p class="content-sub-header">
                    <strong>Area</strong>: <span v-if="area">{{area.name}}</span>
                </p>             
                <nav id="top-right-text">
                    <ul>
                        <li v-if="coordinator">
                            <router-link :to="{name: '/'}" class="py-1 h6">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-group filter">
                            <div class="row">
                                <div class="col-md-4 date-picker">
                                    <label>Draw Date</label>
                                    <date-picker 
                                        @change="updatePrintData" 
                                        v-model="date"
                                        :format="'MMMM DD, YYYY'"
                                        :lang="lang" range>
                                    </date-picker>
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                    <!-- <label>Draw Time</label>
                                    <select id="coordinator" v-model="time" class="form-control">
                                        <option value="">All</option>
                                        <option value="11am">11AM</option>
                                        <option value="4pm">4PM</option>
                                        <option value="9pm">9PM</option>
                                    </select> -->
                                </div>
                                <div class="col-md-2 form-actions">
                                    <button @click="print" class="btn btn-outline-primary round">
                                        <i class="ft-printer mr-2"></i>Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-for="(table,index) in tables" :key="index" class="card-block">
                            <div class="draw_details">
                                <p><strong>Draw Time:</strong> {{table.draw_time}}</p>
                                <!-- <p><strong>Draw Result:</strong> 123</p> -->
                            </div>
                            <div class="table-responsive">
                                <table style="width: 100% !important;" class="table text-left" :class="table.class">
                                    <thead>
                                        <tr>
                                            <th>Usher</th>
                                            <th>Gross Sales</th>
                                            <th>Commission</th>
                                            <th>Hits</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="agent in agents" :key="agent.id">
                                            <td>{{agent.info.firstname +''+ agent.info.lastname}}</td>
                                            <td>{{getAgentGross(agent.sales, table.draw_key) | currency('&#8369;')}}</td>
                                            <td>{{getAgentCommission(agent.sales, table.draw_key) | currency('&#8369;')}}</td>
                                            <td>{{getTotalWinningsWithFloat(agent.winnings, table.draw_key) | currency('&#8369;')}}</td>
                                            <td>{{getAgentProfit(agent, table.draw_key) | currency('&#8369;')}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total</th>
                                            <th>{{getTotalGross(table.draw_key) | currency('&#8369;')}}</th>
                                            <th>{{getTotalCommission(table.draw_key) | currency('&#8369;')}}</th>
                                            <th>{{getTotalWinnings(table.draw_key) | currency('&#8369;')}}</th>
                                            <th>{{getTotalProfit(table.draw_key) | currency('&#8369;')}}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- @end v-for -->
                        <div class="card-block">
                            <div class="draw_details">
                                <p><strong>Overview</strong></p>
                            </div>
                            <div class="table-responsive">
                                <table class="table text-left">
                                    <thead>
                                        <tr>
                                            <th>Total</th>
                                            <th>11:00AM</th>
                                            <th>04:00PM</th>
                                            <th>09:00PM</th>
                                            <th>Overall</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Total Gross</th>
                                            <td v-for="(table,index) in tables" :key="index"> {{getTotalGross(table.draw_key) | currency('&#8369;')}} </td>
                                            <th>{{getTotalGross('') | currency('&#8369;')}}</th>
                                        </tr>
                                        <tr>
                                            <th>Total Commission</th>
                                            <td v-for="(table,index) in tables" :key="index"> {{getTotalCommission(table.draw_key) | currency('&#8369;')}} </td>
                                            <th>{{getTotalCommission('') | currency('&#8369;')}}</th>
                                        </tr>
                                        <tr>
                                            <th>Total Hits</th>
                                            <td v-for="(table,index) in tables" :key="index"> {{getTotalWinnings(table.draw_key) | currency('&#8369;')}} </td>
                                            <th>{{getTotalWinnings('') | currency('&#8369;')}}</th>
                                        </tr>
                                        <tr>
                                            <th>Total Profit</th>
                                            <td v-for="(table,index) in tables" :key="index"> {{getTotalProfit(table.draw_key) | currency('&#8369;')}} </td>
                                            <th>{{getTotalProfit('') | currency('&#8369;')}}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- @end totals -->
                    </div>
                </div>
            </div>
        </div>

        <!-- PRINT -->
        <div id="printMe" v-if="print_data" v-show="false">
            <print-summary-reports 
                :coordinator="coordinator" 
                :date="date"
                :time="time"
                :print_data="print_data"
                ref="PrintSummaryReports">
            </print-summary-reports>
        </div>
        <!-- PRINT -->

        <!-- PRINTME 2 -->
        <div class="for-print" id="printMe2" v-show="false">
            <div class="row">
                <print-heading></print-heading>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div style="text-align: center;">
                        <h3 class="content-header">Summary Remittance Reports</h3>
                    </div>
                    <br><br>
                    <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Area:</strong> 
                        <span v-if="area">{{area.name}}</span>
                    </p>  
                    <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Coordinator:</strong> 
                        <span v-if="owner">{{owner.firstname+' '+owner.lastname}}</span>
                    </p>  
                    <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;">
                        <strong>Commission</strong>: <span v-if="owner">{{owner.percentage}}%</span>
                    </p>
                    <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Date:</strong> 
                        <span>{{date[0] | draw_date}} - {{date[1] | draw_date}}</span>
                    </p>
                </div>
            </div><br><br>
            <div class="totals">
                <div class="draw_details">
                    <p><strong>Overview</strong></p>
                </div>
                <div class="table-responsive">
                    <table class="table text-left">
                        <thead>
                            <tr>
                                <th>Total</th>
                                <th>11:00AM</th>
                                <th>04:00PM</th>
                                <th>09:00PM</th>
                                <th>Overall</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Total Gross</th>
                                <td v-for="(table,index) in tables" :key="index"> {{getTotalGross(table.draw_key) | currency('&#8369;')}} </td>
                                <th>{{getTotalGross('') | currency('&#8369;')}}</th>
                            </tr>
                            <tr>
                                <th>Total Commission</th>
                                <td v-for="(table,index) in tables" :key="index"> {{getTotalCommission(table.draw_key) | currency('&#8369;')}} </td>
                                <th>{{getTotalCommission('') | currency('&#8369;')}}</th>
                            </tr>
                            <tr>
                                <th>Total Hits</th>
                                <td v-for="(table,index) in tables" :key="index"> {{getTotalWinnings(table.draw_key) | currency('&#8369;')}} </td>
                                <th>{{getTotalWinnings('') | currency('&#8369;')}}</th>
                            </tr>
                            <tr>
                                <th>Total Profit</th>
                                <td v-for="(table,index) in tables" :key="index"> {{getTotalProfit(table.draw_key) | currency('&#8369;')}} </td>
                                <th>{{getTotalProfit('') | currency('&#8369;')}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- @end totals -->
            <div v-for="(table,index) in tables" :key="index">
                <br><br>
                <div class="draw_details">
                    <p><strong>Draw Time:</strong> {{table.draw_time}}</p>
                </div>
                <div class="table-responsive">
                    <table class="table text-left" :class="table.class">
                        <thead>
                            <tr>
                                <th>Usher</th>
                                <th>Gross Sales</th>
                                <th>Commission</th>
                                <th>Hits</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="agent in agents" :key="agent.id">
                                <td>{{agent.info.firstname +''+ agent.info.lastname}}</td>
                                <td>{{getAgentGross(agent.sales, table.draw_key) | currency('&#8369;')}}</td>
                                <td>{{getAgentCommission(agent.sales, table.draw_key) | currency('&#8369;')}}</td>
                                <td>{{getAgentWinnings(agent.winnings, table.draw_key) | currency('&#8369;')}}</td>
                                <td>{{getAgentProfit(agent, table.draw_key) | currency('&#8369;')}}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th>{{getTotalGross(table.draw_key) | currency('&#8369;')}}</th>
                                <th>{{getTotalCommission(table.draw_key) | currency('&#8369;')}}</th>
                                <th>{{getTotalWinnings(table.draw_key) | currency('&#8369;')}}</th>
                                <th>{{getTotalProfit(table.draw_key) | currency('&#8369;')}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- @end v-for -->
        </div>

    </section>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import PrintSummaryReports from './print/SummaryReports.vue';
    import Heading from "./print/partials/Heading.vue";

    export default {
        props: ['coordinator'],
        data () {
            return {
                isLoading: false,
                error: [],
                agents: [],
                tables: [
                    { draw_key: '11am', draw_time: '11:00AM', class: 'bg-morning' },
                    { draw_key: '4pm', draw_time: '04:00PM', class: 'bg-afternoon' },
                    { draw_key: '9pm', draw_time: '09:00PM', class: 'bg-night' },
                ],
                print_data: null,
                total_sales: [],
                owner: null,
                area: null,
                user: this.coordinator ? this.coordinator : $('meta[name="userId"]').attr('content'), 
                date: [new Date(), new Date()], 
                time: 'all',
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
        created() {
            this.fetchData(this.date);
        },
        components: {
            Loading,
            'print-summary-reports' : PrintSummaryReports,
            'print-heading': Heading
        },
        methods: {
            fetchData (date) {
                this.isLoading = true;
                let date_from = moment(date[0]).format('MM/DD/YYYY');
                let date_to = moment(date[1]).format('MM/DD/YYYY');
                axios.get('/api/reports/sales-per-agent2', {
                    params: {
                        date : date_from,
                        date_to : date_to,
                        coordinator : this.user 
                    }
                })
                .then(response => {
                    this.isLoading = false;
                    this.print_data = response.data;
                    this.agents = response.data.agents;
                    this.owner = response.data.user;
                    this.area = response.data.area;
                });
            },
            getAgentGross(sales, draw_time){
                if( draw_time )
                    return sales[draw_time]['gross_sales'];
                return sales['11am']['gross_sales'] + sales['4pm']['gross_sales'] + sales['9pm']['gross_sales']; 
            },
            getAgentCommission(sales, draw_time){
                let _gross = this.getAgentGross(sales, draw_time);
                let _comm = _gross * (this.owner.percentage / 100);
                return _comm;
            },
            getAgentWinnings(winnings, draw_time){
                if( draw_time )
                    return winnings[draw_time]['winning_amount'];
                return winnings['11am']['winning_amount'] + winnings['4pm']['winning_amount'] + winnings['9pm']['winning_amount']; 
            },
            getHitsCount(winnings, draw_time){
                if( draw_time )
                    return winnings[draw_time]['hits'];
                return winnings['11am']['hits'] + winnings['4pm']['hits'] + winnings['9pm']['hits']; 
            },
            getTotalWinningsWithFloat(winnings, draw_time){
                if( draw_time )
                    return this.getAgentWinnings(winnings, draw_time) + (this.getHitsCount(winnings,draw_time) * this.owner.float);
                return this.getAgentWinnings(winnings,'') + (this.getHitsCount(winnings,'') * this.owner.float);
            },
            getAgentProfit(agent, draw_time){
                let _gross = this.getAgentGross(agent.sales, draw_time);
                let _winnings = this.getTotalWinningsWithFloat(agent.winnings, draw_time);
                let _profit = ( _gross * ( (100 - this.owner.percentage) / 100) ) - _winnings;
                return _profit;
            },
            getTotalGross(draw_time){
                let total = 0;
                let _this = this;
                _.forEach(this.agents, function(agent, key) {
                    total += _this.getAgentGross(agent.sales, draw_time);
                });
                return total;
            },
            getTotalCommission(draw_time){
                let total_comm = 0;
                let _this = this;
                _.forEach(this.agents, function(agent, key) {
                    total_comm += (_this.getAgentGross(agent.sales, draw_time) * (_this.owner.percentage / 100));
                });
                return total_comm;
            },
            getTotalWinnings(draw_time){
                let total_winnings = 0;
                let _this = this;
                _.forEach(this.agents, function(agent, key) {
                    total_winnings += _this.getTotalWinningsWithFloat(agent.winnings, draw_time);
                });
                return total_winnings;
            },
            getTotalProfit(draw_time){
                let total_profit = 0;
                let _this = this;
                _.forEach(this.agents, function(agent, key) {
                    let _gross = _this.getAgentGross(agent.sales, draw_time);
                    let _winnings = _this.getTotalWinningsWithFloat(agent.winnings, draw_time);
                    total_profit += _gross * ( (100 - _this.owner.percentage) / 100) - _winnings;
                });
                return total_profit;
            },
            getTotalSalesPerDraw(draw_time) {
                let total = 0;
                _.forEach(this.agents, function(agent, key) {
                    total += agent.sales[draw_time]['gross_sales'];
                });
                return total;
            },
            updatePrintData(){
                this.fetchData(this.date);
                // this.$refs.PrintSummaryReports.updatePrintData(this.print_data);
            },
            print() {
                this.$htmlToPaper('printMe2', () => {
                    console.log('Printing done or got cancelled!');
                });
            }
        }
    }
</script>

<style>
    #summaryReports .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>