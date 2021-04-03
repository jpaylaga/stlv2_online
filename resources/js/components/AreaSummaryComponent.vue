<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="highestBetReports">
            <div class="row">
                <div class="col-sm-4">
                    <div class="content-header">Sales Per Area/Draw</div>  
                </div>
                <div class="col-sm-8" style="padding-top: 18px;">
                    <div class="pull-right row">
                        <div class="col-md-6 form-actions">
                            <date-picker 
                                @change="fetchData" 
                                v-model="date"
                                :format="'MMMM DD, YYYY'"
                                :lang="lang">
                            </date-picker>
                        </div>
                        <div class="col-md-3 form-actions">
                            <select id="coordinator" v-model="time" class="form-control">
                                <option value="all">All</option>
                                <option value="11">11AM</option>
                                <option value="16">4PM</option>
                                <option value="21">9PM</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-actions">
                            <button @click="print" class="btn btn-outline-primary round">
                                <i class="ft-printer mr-2"></i>Print
                            </button>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <div class="card-block" v-for="(draw_time, index) in tables" :key="index">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>
                                                <span v-if="draw_time == 'all'">Total</span>
                                                <span v-else>{{draw_time | uppercase}}</span> 
                                                Gross
                                            </th>
                                            <th>Hits</th>
                                            <th>Win Amount</th>
                                            <th>Net</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="areas">
                                        <tr v-for="area in areas" :key="area.details.id">
                                            <th>{{area.details.name}}</th>
                                            <td>{{getTotalSales(area.gross,draw_time) | currency('&#8369;')}}</td>
                                            <td>{{getTotalSales(area.hits,draw_time) | currency('&#8369;')}}</td>
                                            <td>{{getTotalSales(area.wins,draw_time) | currency('&#8369;')}}</td>
                                            <td>{{getTotalSales(area.net,draw_time) | currency('&#8369;')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRINT -->
            <div id="printAreaSummary" v-if="areas" v-show="false">
                <print-area-reports 
                    :title="'Area Summary Reports'"
                    :date="date"
                    :time="time"
                    :areas="areas"
                    ref="PrintSummaryReports">
                </print-area-reports>
            </div>
            <!-- PRINT -->

        </section>
        <!--Extended Table Ends-->   
    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import PrintAreaReports from './print/AreaReports.vue';

    export default {
        data () {
            return {
                isLoading: false,
                date: new Date(), //default date
                areas: null,
                time: 'all',
                tables: ['11am', '4pm', '9pm', 'all'],
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
            Loading, 'print-area-reports' : PrintAreaReports
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                let draw_date = moment(this.date).format('MM/DD/YYYY');
                axios.get('/api/reports/area-sales', {
                    params: {
                        date : draw_date,
                    }
                })
                .then(response => {
                    this.areas = response.data;
                    this.isLoading = false;
                });
            },
            getTotalSales(sales_type, draw_time){
                if( draw_time == 'all' ){
                    let sum = 0;
                    _.forEach(sales_type, function(value, key) {
                        sum += value;
                    }); 
                    return sum;
                }else{
                    return sales_type[draw_time];
                }
            },
            print() {
                this.$htmlToPaper('printAreaSummary', () => {
                    console.log('Printing done or got cancelled!');
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