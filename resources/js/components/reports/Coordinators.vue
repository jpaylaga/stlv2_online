<template>
    <div id="reports-coordinator">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Coordinator Total Sales</div>
            </div>
        </div>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-4 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="fetchData" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang" range>
                                        </date-picker>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label>Area</label>
                                            <select id="game" v-model="area" class="form-control" @change="fetchData">
                                                <option value="">All</option>
                                                <option v-for="area in areaOptions" :value="area.id" :key="area.id">{{area.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Coordinator</label>
                                        <input v-model="coordinator_search" type="text" class="form-control">
                                    </div>
                                    <div class="col-md-1 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2 form-actions text-right">
                                        <button @click="print" :disabled="coordinators.length <= 0" style="width: 100%;" class="btn btn-outline-primary round">
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
                                            <th>Name</th>
                                            <th>Gross</th>
                                            <th>%</th>
                                            <th>Commission</th>
                                            <th>Hit</th>
                                            <th>Profit</th>
                                            <th>Controls</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(coor, index) in coordinators" :key="coor.id">
                                            <td>
                                                <router-link :to="{name: 'view-usher-sales', params: {coordinator:coor.id}}" data-toggle="tooltip" data-placement="top" title="View Usher Sales" data-trigger="hover">
                                                    {{coor.firstname+' '+coor.lastname}}
                                                </router-link>
                                            </td>
                                            <td>{{coor.sales_total | currency('&#8369;') }}</td>
                                            <td><a href="#" @click.prevent="updatePercentage(coor)">{{coor.percentage}}%</a></td>
                                            <td>{{getCommission(coor, index) | currency('&#8369;')}}</td>
                                            <td>
                                                {{coor.hits.winning_amount + (coor.float*coor.hits.hits) | currency('&#8369;')}}({{Math.round(coor.hits.hits * 10) / 10}})
                                                <!-- <a href="#">{{getHitsWithFloat(coor, index) | currency('&#8369;')}}</a> -->
                                            </td>
                                            <td>
                                                {{getProfit(coor, index) | currency('&#8369;')}}
                                                <i v-if="getProfit(coor, index) > 0" class="ft-arrow-up teal accent-3"></i>
                                                <i v-else-if="getProfit(coor, index) < 0" class="ft-arrow-down red accent-3"></i>
                                            </td>
                                            <td>
                                                <details-popup 
                                                    :coor="coor"
                                                    :date="date"
                                                    :show_cancel="false">
                                                </details-popup>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-block">
                                <h3>Totals</h3>
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>Gross</th>
                                            <th>Commission</th>
                                            <th>Hit</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{this.getTotalGross() | currency('&#8369;') }}</td>
                                            <td>{{this.getTotalCommission() | currency('&#8369;') }}</td>
                                            <td>{{this.getTotalHit() | currency('&#8369;') }}</td>
                                            <td>{{this.getTotalProfit() | currency('&#8369;') }}</td>
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
        <div id="printCoordinatorsSales" v-if="coordinators" v-show="false">
            <print-coordinators-report 
                :draw_date="date"
                :coordinators="coordinators">
            </print-coordinators-report>
        </div>
        <!-- PRINT -->

    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import CoordinatorsReport from '../print/CoordinatorsReport.vue';
    import TimeDetailsPopup from '../single-popup/SalesTimeDetails.vue';
    
    export default {
        data () {
            return {
                coordinators: [],
                isLoading: false,
                areaOptions: [],
                area: '',
                coordinator_search: '',
                date: [new Date(), new Date()], 
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
            this.initOptions()
        },
        components: {
            Loading, 
            'print-coordinators-report': CoordinatorsReport,
            'details-popup': TimeDetailsPopup,
        },
        methods: {
            initOptions(){
                this.isLoading = true;
                axios.get('/api/areas').then(resp => {
                    this.areaOptions = resp.data;
                    this.fetchData();
                });
            },
            fetchData () {
                this.isLoading = true;
                let date = moment(this.date[0]).format('MM/DD/YYYY');
                let date_to = moment(this.date[1]).format('MM/DD/YYYY');
                axios.get('/api/reports/coordinators-sales', {
                    params: { 
                        date: date,
                        date_to: date_to,
                        area: this.area,
                        coordinator: this.coordinator_search 
                    }
                }).then(response => {
                    this.coordinators = _.orderBy(response.data, 'sales_total', 'desc');
                    this.isLoading = false;
                }); 
            },
            getCommission(coor, index){
                let comm = coor.sales_total * (coor.percentage / 100);
                this.coordinators[index]._commision = comm;
                return comm;
            },
            getHitsWithFloat(coor, index){
                let hits = coor.hits.winning_amount + (coor.hits.hits * coor.float);
                this.coordinators[index]._hits = hits;
                return hits;
            },
            getProfit(coor, index){
                // let profit = coor.sales_total * ( (100 - coor.percentage) / 100) - coor.hits.winning_amount;
                let profit = coor.sales_total * ( (100 - coor.percentage) / 100) - this.getHitsWithFloat(coor,index);
                this.coordinators[index]._profit = profit;
                return profit;
            },
            getTotalGross(){
                return _.sumBy(this.coordinators, function(coor) { 
                    return coor.sales_total; 
                });
            },
            getTotalCommission(){
                return _.sumBy(this.coordinators, function(coor) { 
                    return coor._commision; 
                });
            },
            getTotalHit(){
                return _.sumBy(this.coordinators, function(coor) { 
                    return coor.hits.winning_amount + (coor.float * coor.hits.hits); 
                });
            },
            getTotalProfit(){
                return _.sumBy(this.coordinators, function(coor) { 
                    return coor._profit; 
                });
            },
            print(){
                this.$htmlToPaper('printCoordinatorsSales', () => {
                    console.log('Printing done or got cancelled!');
                });
            },
            updatePercentage(coor) {
                this.$swal({
                    title: '<h5>Update Percentage for: '+coor.firstname+' '+coor.lastname+'</h5>',
                    input: 'text',
                    inputValue: coor.percentage,
                    inputAttributes: {
                        autocapitalize: 'on',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Udpate',
                    showLoaderOnConfirm: true,
                    preConfirm: (percentage) => {
                        return axios.post('/api/user/update-percentage', {
                            user: coor.id, percentage: percentage
                        })
                        .then(response => {
                            if( response.data.success )
                                return response.data;
                            this.$swal.showValidationMessage(response.data.message);
                        }).catch(error => {
                            this.$swal.showValidationMessage(
                                `Request failed: ${'Please enter valid value'}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        this.$swal('Successfully Updated Percentage!', '', 'success');
                        this.fetchData();
                    }
                })
            },
        }
    }
</script>

<style>
    #reports-coordinator .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>