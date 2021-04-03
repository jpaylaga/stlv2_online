<template>
    <div>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div style="text-align: center;">
                    <h3 class="content-header">Coordinator Report</h3>
                </div>
                <br><br>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Date From:</strong> 
                    <span>{{draw_date[0] | draw_date}}</span>
                </p>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Date To:</strong> 
                    <span>{{draw_date[1] | draw_date}}</span>
                </p>  
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table text-left">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Gross</th>
                                <th>%</th>
                                <th>Commission</th>
                                <th>Hit</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(coor, index) in coordinators" :key="coor.id">
                                <td>{{coor.firstname+' '+coor.lastname}}</td>
                                <td>{{coor.sales_total | currency('&#8369;') }}</td>
                                <td>{{coor.percentage}}%</td>
                                <td>{{getCommission(coor,index) | currency('&#8369;')}}</td>
                                <td>{{coor.hits.winning_amount | currency('&#8369;')}}</td>
                                <td>{{getProfit(coor,index) | currency('&#8369;')}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
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
        <br>
        <div class="row">
            <div class="col-md-12">
                <p>Prepared by: ____________________________________________</p>
                <p>Checked by: ____________________________________________</p>
            </div>
        </div>
    </div>
</template>

<script>
    import Heading from "./partials/Heading.vue";
    export default {
        props: ['draw_date', 'coordinators'],
        components: { 'print-heading': Heading },
        created() {

        },
        methods:{
            getCommission(coor, index){
                let comm = coor.sales_total * (coor.percentage / 100);
                this.coordinators[index]._commision = comm;
                return comm;
            },
            getProfit(coor, index){
                let profit = coor.sales_total * ( (100 - coor.percentage) / 100) - coor.hits.winning_amount;
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
                    return coor.hits.winning_amount; 
                });
            },
            getTotalProfit(){
                return _.sumBy(this.coordinators, function(coor) { 
                    return coor._profit; 
                });
            },
        }
    }
</script>