<template>
    <div>
        <br><br>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div style="text-align: center;">
                    <h3 class="content-header">Summary Remittance Reports</h3>
                </div>
                <br><br>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Area:</strong> {{print_data.area.name}}</p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Coordinator:</strong> {{print_data.user.firstname+' '+print_data.user.lastname}}</p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Date:</strong> 
                    <span>{{date[0] | draw_date}} - {{date[1] | draw_date}}</span>
                </p>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;" v-if="time"><strong>Draw Time:</strong> 
                    <span>{{time | draw_time}}</span>
                </p>  
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="table-responsive">
                    <table class="table text-left">
                        <thead>
                            <tr>
                                <th>Usher</th>
                                <th v-if="time == '11' || time == 'all'">
                                    <span v-if="time == '11'">Gross</span> 
                                    <span v-if="time == 'all'">11AM</span>
                                </th>
                                <th v-if="time == '16' || time == 'all'">
                                    <span v-if="time == '16'">Gross</span> 
                                    <span v-if="time == 'all'">4PM</span>
                                </th>
                                <th v-if="time == '21' || time == 'all'">
                                    <span v-if="time == '21'">Gross</span> 
                                    <span v-if="time == 'all'">9PM</span>
                                </th>
                                <th v-if="time == 'all'">Gross</th>
                                <th>Hits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="agent in print_data.agents" :key="agent.id">
                                <td>{{agent.info.firstname +''+ agent.info.lastname}}</td>
                                <td v-if="time == '11' || time == 'all'">
                                    {{ agent.sales['11am']['gross_sales'] | currency('&#8369;') }}
                                </td>
                                <td v-if="time == '16' || time == 'all'">
                                    {{ agent.sales['4pm']['gross_sales'] | currency('&#8369;') }}
                                </td>
                                <td v-if="time == '21' || time == 'all'">
                                    {{ agent.sales['9pm']['gross_sales'] | currency('&#8369;') }}
                                </td>
                                <td v-if="time == 'all'">
                                    {{ getAgentTotalSales(agent.sales) | currency('&#8369;') }}
                                </td>
                                <td>
                                    <span v-if="time == '11'">{{ agent.winnings['11am']['hits'] | currency('&#8369;') }}</span>
                                    <span v-if="time == '16'">{{ agent.winnings['4pm']['hits']  | currency('&#8369;') }}</span>
                                    <span v-if="time == '21'">{{ agent.winnings['9pm']['hits']  | currency('&#8369;') }}</span>
                                    <span v-if="time == 'all'">{{ getAgentTotalHits(agent.winnings) | currency('&#8369;') }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row" colspan="1">Total</th>
                                <td v-if="time == '11' || time == 'all'">
                                    {{ getTotalSalesPerDraw('11am') | currency('&#8369;') }}
                                </td>
                                <td v-if="time == '16' || time == 'all'">
                                    {{ getTotalSalesPerDraw('4pm') | currency('&#8369;') }}
                                </td>
                                <td v-if="time == '21' || time == 'all'">
                                    {{ getTotalSalesPerDraw('9pm') | currency('&#8369;') }}
                                </td>
                                <td v-if="time == 'all'">
                                    {{ (getTotalSalesPerDraw('11am') + getTotalSalesPerDraw('4pm') + getTotalSalesPerDraw('9pm')) | currency('&#8369;')}}
                                </td>
                                <td>
                                    <span v-if="time == '11'">{{ getTotalHitsPerDraw('11am') | currency('&#8369;') }}</span>    
                                    <span v-if="time == '16'">{{ getTotalHitsPerDraw('4pm') | currency('&#8369;')}}</span>    
                                    <span v-if="time == '21'">{{ getTotalHitsPerDraw('9pm') | currency('&#8369;')}}</span>    
                                    <span v-if="time == 'all'">{{ (getTotalHitsPerDraw('11am') + getTotalHitsPerDraw('4pm') + getTotalHitsPerDraw('9pm')) | currency('&#8369;')}}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Gross:</strong> 
                    <span v-if="time == '11'">{{ getTotalSalesPerDraw('11am') | currency('&#8369;') }}</span>    
                    <span v-if="time == '16'">{{ getTotalSalesPerDraw('4pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == '21'">{{ getTotalSalesPerDraw('9pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == 'all'">{{ (getTotalSalesPerDraw('11am') + getTotalSalesPerDraw('4pm') + getTotalSalesPerDraw('9pm')) | currency('&#8369;')}}</span>    
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Total Win Amount:</strong>
                    <span v-if="time == '11'">{{ getTotalWinningsPerDraw('11am') | currency('&#8369;') }}</span>    
                    <span v-if="time == '16'">{{ getTotalWinningsPerDraw('4pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == '21'">{{ getTotalWinningsPerDraw('9pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == 'all'">{{ (getTotalWinningsPerDraw('11am') + getTotalWinningsPerDraw('4pm') + getTotalWinningsPerDraw('9pm')) | currency('&#8369;')}}</span>           
                </p>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Net:</strong>
                    <span v-if="time == '11'">{{ getTotalSalesPerDraw('11am') - getTotalWinningsPerDraw('11am') | currency('&#8369;') }}</span>    
                    <span v-if="time == '16'">{{ getTotalSalesPerDraw('4pm') - getTotalWinningsPerDraw('4pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == '21'">{{ getTotalSalesPerDraw('9pm') - getTotalWinningsPerDraw('9pm') | currency('&#8369;')}}</span>    
                    <span v-if="time == 'all'">
                        {{ (getTotalSalesPerDraw('11am') + getTotalSalesPerDraw('4pm') + getTotalSalesPerDraw('9pm')) - (getTotalWinningsPerDraw('11am') + getTotalWinningsPerDraw('4pm') + getTotalWinningsPerDraw('9pm')) | currency('&#8369;')}}
                    </span>           
                </p>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <p>Prepared by: ____________________________________________</p>
                <p>Checked by: ____________________________________________</p>
            </div>
        </div>

    </div>
</template>

<script>
    import Heading from "./partials/Heading.vue";
    export default {
        props: ['coordinator', 'date', 'time', 'print_data'],
        components: { 'print-heading': Heading },
        created() {
            // this.fetchData(this.date);
        },
        methods: {
            getAgentTotalSales(sales){
                return sales['11am']['gross_sales']
                + sales['4pm']['gross_sales']
                + sales['9pm']['gross_sales']; 
            },
            getAgentTotalHits(winnings){
                return winnings['11am']['hits']
                + winnings['4pm']['hits']
                + winnings['9pm']['hits']; 
            },
            getTotalSalesPerDraw(draw_time) {
                let total = 0;
                _.forEach(this.print_data.agents, function(agent, key) {
                    total += agent.sales[draw_time]['gross_sales'];
                });
                return total;
            },
            getTotalHitsPerDraw(draw_time){
                let total = 0;
                _.forEach(this.print_data.agents, function(agent, key) {
                    total += agent.winnings[draw_time]['hits'];
                });
                return total;
            },
            getTotalWinningsPerDraw(draw_time){
                let total = 0;
                _.forEach(this.print_data.agents, function(agent, key) {
                    total += agent.winnings[draw_time]['winning_amount'];
                });
                return total;
            }
        }
    }
</script>
