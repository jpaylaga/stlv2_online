<template>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Results / Sales</h4>
            <span class="grey">{{draw_date | draw_date}}</span>
            <span class="grey pull-right">Percentage: {{this.percentage}}% <span v-if="this.float > 0">| Float: {{this.float | currency('&#8369;')}}</span></span>
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table table-responsive-lg text-left">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>11 AM</th>
                            <th>4 PM</th>
                            <th>9 PM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="game in games" :key="game.id">
                            <th scope="row">{{game.name}}</th>
                            <td>{{ getGameResult(game.results, '11') }}</td>
                            <td>{{ getGameResult(game.results, '16') }}</td>
                            <td>{{ getGameResult(game.results, '21') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Gross</th>
                            <td>{{ gross['11'] | currency('&#8369;') }}</td>
                            <td>{{ gross['16'] | currency('&#8369;') }}</td>
                            <td>{{ gross['21'] | currency('&#8369;') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Hits</th>
                            <td>{{ hits['11'] | currency('&#8369;') }}</td>
                            <td>{{ hits['16'] | currency('&#8369;') }}</td>
                            <td>{{ hits['21'] | currency('&#8369;') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Win</th>
                            <td>{{ winnings['11'] + (hits['11'] * float) | currency('&#8369;') }}</td>
                            <td>{{ winnings['16'] + (hits['16'] * float) | currency('&#8369;') }}</td>
                            <td>{{ winnings['21'] + (hits['21'] * float) | currency('&#8369;') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Net</th>
                            <td> {{ net_sales['11'] | currency('&#8369;') }} </td>
                            <td> {{ net_sales['16'] | currency('&#8369;') }} </td>
                            <td> {{ net_sales['21'] | currency('&#8369;') }} </td>
                        </tr>
                        <tr>
                            <th scope="row">Earnings</th>
                            <td> {{ earnings['11'] | currency('&#8369;') }} 
                                <i v-if="earnings['11'] > 0" class="ft-arrow-up teal accent-3"></i>
                                <i v-if="earnings['11'] < 0" class="ft-arrow-down red accent-3"></i>
                            </td>
                            <td> {{ earnings['16'] | currency('&#8369;') }} 
                                <i v-if="earnings['16'] > 0" class="ft-arrow-up teal accent-3"></i>
                                <i v-if="earnings['16'] < 0" class="ft-arrow-down red accent-3"></i>
                            </td>
                            <td> {{ earnings['21'] | currency('&#8369;') }} 
                                <i v-if="earnings['21'] > 0" class="ft-arrow-up teal accent-3"></i>
                                <i v-if="earnings['21'] < 0" class="ft-arrow-down red accent-3"></i>
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

    export default {
        props: ['draw_date'],
        data () {
            return {
                isLoading: false,
                games: [],
                reports: [],
                gross: [],
                hits: [],
                winnings: [],
                percentage: 0,
                float: 0,
                net_sales: {
                    '11': 0,
                    '16': 0,
                    '21': 0,
                },
                earnings: {
                    '11': 0,
                    '16': 0,
                    '21': 0,
                },
            }
        },
        created() {
            this.fetchResults(this.draw_date);
        },
        components: { Loading },
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
                axios.get('/api/sales/gross-sales-by-draw', {
                    params: {
                        date_from: draw_date,
                        date_to: draw_date,
                    }
                }).then( response => {
                    this.gross = response.data;
                    this.getWinnings(draw_date);
                });
            },
            getWinnings(draw_date){
                axios.get('/api/sales/winnings-by-draw', {
                    params: {
                        date_from: draw_date,
                        date_to: draw_date,
                    }
                }).then( response => {
                    this.winnings = response.data.winnings;
                    this.hits = response.data.hits;
                    this.isLoading = false;
                    this.calculateNetSales();
                    this.calculateEarnings();
                });
            },
            calculateNetSales(){
                this.net_sales['11'] = this.gross['11'] - ( this.gross['11'] * (this.percentage / 100) );
                this.net_sales['16'] = this.gross['16'] - ( this.gross['16'] * (this.percentage / 100) );
                this.net_sales['21'] = this.gross['21'] - ( this.gross['21'] * (this.percentage / 100) );
            },
            calculateEarnings(){
                let _this = this;
                _.forEach(_this.earnings, function(value, key) {
                    let winnings = _this.winnings[key] + (_this.hits[key] * _this.float);
                    _this.earnings[key] = _this.net_sales[key] - winnings;
                });
            },
        }
    }
</script>