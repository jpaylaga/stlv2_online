<template>
    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
        <loading :active.sync="isLoading"></loading>
        <div class="card">
            <div class="card-header">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="font-large-1 mb-0">Sales Per Game</h3>
                        <span class="grey"></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-block">
                    <table class="table table-responsive-lg text-left">
                        <thead>
                            <tr>
                                <th>Game Type</th>
                                <th>11 AM</th>
                                <th>4 PM</th>
                                <th>9 PM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="game in games" :key="game.id">
                                <th scope="row">{{game.name}}</th>
                                <td>{{ game.total_sales_11am | currency('&#8369;') }}</td>
                                <td>{{ game.total_sales_4pm | currency('&#8369;') }}</td>
                                <td>{{ game.total_sales_9pm | currency('&#8369;') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Total</th>
                                <td>{{ getTotalSales11am() | currency('&#8369;') }}</td>
                                <td>{{ getTotalSales4pm() | currency('&#8369;') }}</td>
                                <td>{{ getTotalSales9pm() | currency('&#8369;') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
            }
        },
        created() {
            this.fetchGames(this.draw_date);
        },
        components: { Loading },
        methods: {
            fetchGames(draw_date){
                this.isLoading = true;
                axios.get('/api/sales/per-game', {
                    params: {
                        draw_date: draw_date
                    }
                }).then(response => {
                    this.games = response.data;
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            },
            getTotalSales11am(){
                return _.sumBy(this.games, (game) => { 
                    return parseInt( game.total_sales_11am ); 
                });
            },
            getTotalSales4pm(){
                return _.sumBy(this.games, (game) => { 
                    return parseInt( game.total_sales_4pm ); 
                });
            },
            getTotalSales9pm(){
                return _.sumBy(this.games, (game) => { 
                    return parseInt( game.total_sales_9pm ); 
                });
            }
        }
    }
</script>