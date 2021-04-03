<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Results / Sales</h4>
            <span class="grey">{{draw_date | draw_date}}</span>
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table table-responsive-lg text-left">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>11AM</th>
                            <th>4PM</th>
                            <th>9PM</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="game in games" :key="game.id">
                            <th scope="row">{{game.name}}</th>
                            <td>{{ getGameResult(game.results, '11') }}</td>
                            <td>{{ getGameResult(game.results, '16') }}</td>
                            <td>{{ getGameResult(game.results, '21') }}</td>
                            <td></td>
                        </tr>
                        <tr v-if="reports.gross_sales">
                            <th scope="row">Gross</th>
                            <td>{{ reports.gross_sales['11am'] | currency('&#8369;') }}</td>
                            <td>{{ reports.gross_sales['4pm'] | currency('&#8369;') }}</td>
                            <td>{{ reports.gross_sales['9pm'] | currency('&#8369;') }}</td>
                            <td>{{ reports.gross_sales['11am'] + reports.gross_sales['4pm'] + reports.gross_sales['9pm'] | currency('&#8369;') }}</td>
                        </tr>
                        <tr v-if="reports.commission">
                            <th scope="row">Commission</th>
                            <td>{{ reports.commission['11am'] | currency('&#8369;') }}</td>
                            <td>{{ reports.commission['4pm'] | currency('&#8369;') }}</td>
                            <td>{{ reports.commission['9pm'] | currency('&#8369;') }}</td>
                            <td>{{ reports.commission['11am'] + reports.commission['4pm'] + reports.commission['9pm'] | currency('&#8369;') }}</td>
                        </tr>
                        <tr v-if="reports.winnings">
                            <th scope="row">Hit</th>
                            <td>{{ reports.winnings['11am'] | currency('&#8369;') }}({{Math.round(reports.hits['11am'] * 10) / 10}})</td>
                            <td>{{ reports.winnings['4pm'] | currency('&#8369;') }}({{Math.round(reports.hits['4pm'] * 10) / 10}})</td>
                            <td>{{ reports.winnings['9pm'] | currency('&#8369;') }}({{Math.round(reports.hits['9pm'] * 10) / 10}})</td>
                            <td>{{ reports.winnings['11am'] + reports.winnings['4pm'] + reports.winnings['9pm'] | currency('&#8369;') }}</td>
                        </tr>
                        <tr v-if="reports.profit">
                            <th scope="row">Profit</th>
                            <td>
                                <span class="amount" :class="reports.profit['11am'] >= 0 ? 'positive' : 'negative'">
                                    {{ Math.abs(reports.profit['11am']) | currency('&#8369;') }}
                                </span>
                                <status-flag :value="reports.profit['11am']"/>
                            </td>
                            <td>
                                <span class="amount" :class="reports.profit['4pm'] >= 0 ? 'positive' : 'negative'">
                                {{ Math.abs(reports.profit['4pm']) | currency('&#8369;') }}
                                </span>
                                <status-flag :value="reports.profit['4pm']"/>
                            </td>
                            <td>
                                <span class="amount" :class="reports.profit['9pm'] >= 0 ? 'positive' : 'negative'">
                                {{ Math.abs(reports.profit['9pm']) | currency('&#8369;') }}
                                </span>
                                <status-flag :value="reports.profit['9pm']"/>
                            </td>
                            <td>
                                <span class="amount" :class="getTotalProfit() >= 0 ? 'positive' : 'negative'">
                                {{ Math.abs(getTotalProfit()) | currency('&#8369;') }}
                                </span>
                                <status-flag :value="getTotalProfit()"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import StatusFlag from '../helpers/StatusFlag.vue';

    export default {
        props: ['draw_date'],
        data () {
            return {
                isLoading: false,
                games: [],
                reports: [],
            }
        },
        created() {
            this.fetchResults(this.draw_date);
        },
        components: { Loading, 'status-flag': StatusFlag },
        methods: {
            fetchResults(draw_date){
                axios.get('/api/results-by-game', {
                    params: {
                        draw_date: draw_date
                    }
                }).then(response => {
                    this.games = response.data;
                    this.isLoading = false;
                });
            },
            getGameResult(results, draw_time){
                let result = _.find(results, ['draw_time', draw_time]);
                return result ? result.result : 'TBA';
            },
            getReports(draw_date){
                this.reports = draw_date;

                // axios.get('/api/sales/result-sales', {
                //     params: {
                //         date: draw_date,
                //         date_to: draw_date,
                //     }
                // }).then( response => {
                //     this.reports = response.data;
                //     // this.$parent.updateSummary(response.data);
                // });
            },
            getTotalProfit(){
                let _profits = this.reports.profit;
                return _profits['11am'] + _profits['4pm'] + _profits['9pm'];
            }
        }
    }
</script>