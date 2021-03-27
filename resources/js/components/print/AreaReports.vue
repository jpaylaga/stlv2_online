<template>
    <div>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div style="text-align: center;">
                    <h3 class="content-header">{{title}}</h3>
                </div>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Date:</strong> 
                    <span>{{date | draw_date}}</span>
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Time:</strong> 
                    <span v-if="time != 'all'">{{time | draw_time}}</span>
                    <span v-else>Whole Day</span>
                </p>  
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-10 offset-md-1 table-responsive" v-for="_time in getTableFiltered(tables)" :key="_time.code">
                <table class="table text-left">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>
                                <span v-if="_time.code == 'all'">Total</span>
                                <span v-else>{{_time.code | uppercase}}</span> 
                                Gross
                            </th >
                            <th>Hits</th>
                            <th>Win Amount</th>
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody v-if="areas">
                        <tr v-for="area in areas" :key="area.details.id">
                            <th>{{area.details.name}}</th>
                            <td>{{getTotalSales(area.gross,_time.code) | currency('&#8369;')}}</td>
                            <td>{{getTotalSales(area.hits,_time.code) | currency('&#8369;')}}</td>
                            <td>{{getTotalSales(area.wins,_time.code) | currency('&#8369;')}}</td>
                            <td>{{getTotalSales(area.net,_time.code) | currency('&#8369;')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <p>Prepared by: ____________________________________________</p>
                <p>Checked by: ____________________________________________</p>
            </div>
        </div>
    </div>
</template>

<script>
    import Heading from "./partials/Heading.vue";
    export default {
        props: ['title', 'date', 'time', 'areas'],
        data(){
            return {
                tables: [
                    {code: '11am', value: '11'},
                    {code: '4pm', value: '16'},
                    {code: '9pm', value: '21'},
                    {code: 'all', value: 'all'},
                ],
            }
        },
        components: { 'print-heading': Heading },
        created() {
        },
        methods: {
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
            getTableFiltered(){
                if( this.time == 'all' )
                    return this.tables; 
                else
                    return _.filter(this.tables, { 'value': this.time });
            }
        }
    }
</script>