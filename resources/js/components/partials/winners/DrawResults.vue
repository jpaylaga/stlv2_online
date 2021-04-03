<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <div class="row">
            <div class="col-12 mt-1 mb-1">
                <div class="content-header">
                    Draw Results: {{draw_date | draw_date}} <br><br>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <select id="draw_time" @change="fetchData" v-model="draw_time" class="form-control">
                                <option v-for="time in drawTimeOptions" :value="time.code" :key="time.code">{{time.name}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: '/'}" >
                                <span>Back</span>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!--Extended Table starts-->
        <section id="extended">                        
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header" style="padding: 5px">
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <form class="form form-horizontal" @submit.prevent="save">
                                    <div class="form-body">
                                        <div class="row game-row" v-for="game in games" :key="game.id">
                                            <div class="form-group col-md-3">
                                                <label class="label-control" for="combination">{{game.name | uppercase}}:</label>
                                                <input v-if="game.type === 'swertres'" v-model="game.result" type="number" min="1" max="999" class="form-control" placeholder="Combination" required>
                                                <input v-if="game.type === 'pares'" v-model="game.result" type="text" class="form-control" placeholder="Combination" required>
                                            </div>
                                            <div class="col-md-6">
                                                <span v-if="game.winners">
                                                    <p>Hits: {{game.winners.length}} | Total Amount: {{ getTotalWinningAmount(game.winners) | currency('&#8369;')}}</p>
                                                    <p>Checked: {{game.total_tickets_checked}}/{{game.total_tickets}} Tickets</p>
                                                </span>
                                                <!-- <vue-simple-progress size="medium" :val="game.increasing_pct" :text="increasing_pct + '%'"></vue-simple-progress> -->
                                            </div>
                                            <div class="col-md-3">
                                                <button v-if="game.draw_id" type="button" class="btn btn-outline-primary" @click="checkTickets(game.draw_id)"><i class="fa fa-check mr-1"></i> Check </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Submit </button>
                                    </div>  
                                </form>
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
    import ProgressBar from 'vue-simple-progress';
    import moment from 'moment';

    export default {
        data () {
            return {
                isLoading: false,
                errors: [],
                games: [],
                drawTimeOptions: [
                    { code: 11, name: '11AM' },
                    { code: 16, name: '4PM' },
                    { code: 21, name: '9PM' },
                ],
                draw_date: moment(new Date()).format("MM/DD/YYYY"),
                draw_time: 11,
                increasing_pct: 0,
                checked: 0,
            }
        },
        created () {
            this.fetchData();
            // setInterval(() => {
            //     this.checked += 1;
            //     this.increasing_pct = Math.min(this.increasing_pct + 1, 100);
            //     }, 500)
        },
        components: {
            Loading, ProgressBar
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/games-draw',{
                    params:{
                        'draw_date': this.draw_date,
                        'draw_time': this.draw_time,
                    }
                }).then(response => {
                    this.loading = false;
                    this.games = response.data;
                    this.isLoading = false;
                });
            },
            save () {
                // save draws first
                this.isLoading = true;
                let draw_promises = [];
                let draw_date = this.draw_date;
                let draw_time = this.draw_time;
                $.each(this.games, function(key, game) {
                    let params = {
                        'draw_date': draw_date,
                        'draw_time': draw_time,
                        'result': game.result,
                        'game_id': game.id,
                    }
                    draw_promises.push( 
                        axios.post('/api/draw', params).then( response => {
                            axios.get('/api/check-tickets/' + response.data.id).then( response => {
                                // winners details
                                // console.log('winners of draw: ' + response.data.id);
                                // console.log(response.data);
                            })
                        })
                    );
                });

                axios.all(draw_promises)
                .then(axios.spread((response) => {
                    console.log('done all');
                    this.isLoading = false;
                }))
                .then( response => {
                    this.isLoading = false;
                    // refresh data
                });

            },
            checkTickets (draw_id) {
                axios.get('/api/check-tickets/' + draw_id).then(response => {
                    this.fetchData();
                });
            },
            getTotalWinningAmount (winners) {
                return window._.sumBy(winners, (win) => { 
                    return parseInt( win.bet.winning_amount ); 
                });
            }
        },
    }
</script>