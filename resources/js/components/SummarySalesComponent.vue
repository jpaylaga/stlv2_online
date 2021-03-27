<template>
    <div id="summary-sales">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Summary Sales</div>
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
                                        <label>Date From</label>
                                        <date-picker 
                                            v-model="date_from"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-3 date-picker">
                                        <label>Date To</label>
                                        <date-picker 
                                            v-model="date_to"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Percentage</label>
                                        <input v-model="percentage" type="number" min="1" max="100" class="form-control">
                                    </div>
                                    <div class="col-md-1 form-actions">
                                        <button @click="fetchData" class="btn btn-outline-primary round">
                                            <i class="ft-filter mr-2"></i>Filter
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
                                            <th>Date</th>
                                            <th>Gross Sales</th>
                                            <th>Net</th>
                                            <th>Hits</th>
                                            <th>Wins</th>
                                            <td>Earnings</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sale in sales" :key="sale.date">  
                                            <td>{{sale.date}}</td>
                                            <td>{{sale.gross_sales | currency('&#8369;')}}</td>
                                            <td>{{sale.net_sales | currency('&#8369;')}}</td>
                                            <td>{{sale.hits}}</td>
                                            <td>{{sale.winning_amount | currency('&#8369;')}}</td>
                                            <td>
                                                {{sale.earnings | currency('&#8369;')}}
                                                <i v-if="sale.earnings > 0" class="ft-arrow-up teal accent-3"></i>
                                                <i v-if="sale.earnings < 0" class="ft-arrow-down red accent-3"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th scope="row">Total</th>
                                            <th>{{getTotal('gross') | currency('&#8369;')}}</th>
                                            <th>{{getTotal('net') | currency('&#8369;')}}</th>
                                            <th>{{getTotal('hits')}}</th>
                                            <th>{{getTotal('wins') | currency('&#8369;')}}</th>
                                            <th>
                                                {{getTotal('earnings') | currency('&#8369;')}}
                                                <i v-if="getTotal('earnings') > 0" class="ft-arrow-up teal accent-3"></i>
                                                <i v-if="getTotal('earnings') < 0" class="ft-arrow-down red accent-3"></i>
                                            </th>
                                        </tr>
                                    </tfoot>
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
                sales: 0,
                date_from: new Date(),
                date_to: new Date(),
                percentage: 34,
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
        components: { Loading },
        methods: {
            fetchData() {
                this.isLoading = true;
                let date_from = moment( this.date_from ).format("MM/DD/YYYY")
                let date_to = moment( this.date_to ).format("MM/DD/YYYY")
                axios.get('/api/sales/summary-sales', {
                    params: {
                        date_from: date_from,
                        date_to: date_to,
                        percentage: this.percentage,
                        comm: 0
                    }
                })
                .then(response => {
                    this.sales = response.data
                    this.isLoading = false;
                });
            },
            getTotal(data){
                switch (data) {
                    case 'gross':
                        return _.sumBy(this.sales, 'gross_sales');
                    case 'net':
                        return _.sumBy(this.sales, 'net_sales');
                    case 'hits':
                        return _.sumBy(this.sales, 'hits');
                    case 'wins':
                        return _.sumBy(this.sales, 'winning_amount');
                    case 'earnings':
                        return _.sumBy(this.sales, 'earnings');
                    default:
                        return 0;
                }
            }
        },
    }
</script>

<style>
    #summary-sales .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>