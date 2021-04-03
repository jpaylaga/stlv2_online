<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <div class="row">
            <div class="col-12 mt-1 mb-1">
                <div class="content-header">Draw Results: {{display_date | draw_date}}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <ul class="router-links-menu">
                    <li>
                        <router-link :to="{name: 'draw-results', params: {draw_time:11}}" >11 AM</router-link>
                    </li>
                    <li>
                        <router-link :to="{name: 'draw-results', params: {draw_time:16}}" >4 PM</router-link>
                    </li>
                    <li>
                        <router-link :to="{name: 'draw-results', params: {draw_time:21}}" >9 PM</router-link>
                    </li>
                </ul>
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
                                <form class="form form-horizontal" @submit.prevent="confirmSave">
                                    <div class="form-body">
                                        <div class="row game-row" v-for="game in games" :key="game.id">
                                            <div class="form-group col-md-3">
                                                <label class="label-control" for="combination">{{game.name | uppercase}}:</label>
                                                <input :disabled="game.draw_id || !game.is_enabled" v-if="game.type == 'swertres'" v-model="game.result" type="number" min="1" max="999" class="form-control" placeholder="Combination">
                                                <input :disabled="game.draw_id || !game.is_enabled" v-if="game.type == 'pares'" v-model="game.result" type="text" class="form-control" placeholder="Combination">
                                            </div>
                                            <div class="col-md-6">
                                                <span v-if="game.winners">
                                                    <p>
                                                        <span>Number of Winners: {{ getWinnersCount(game.winners) }} &nbsp;|&nbsp;</span>
                                                        <span>Hits: {{ game.hits | currency('&#8369;')}} &nbsp;|&nbsp;</span>
                                                        <span>Total Amount: {{ getTotalWinningAmount(game.winners) | currency('&#8369;')}}</span>
                                                    </p>
                                                    <p>Checked: {{game.total_tickets_checked}}/{{game.total_tickets}} Tickets</p>
                                                </span>
                                            </div>
                                            <!-- <div class="col-md-3">
                                                <button v-if="game.draw_id" type="button" class="btn btn-outline-primary" @click="checkTickets(game.draw_id)"><i class="fa fa-check mr-1"></i> Check </button>
                                            </div> -->
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
    import moment, { localeData } from 'moment';

    export default {
        props: ['draw_time'],
        data () {
            return {
                isLoading: false,
                display_date: new Date(),
                draw_date: moment(new Date()).format("MM/DD/YYYY"),
                games: [],
            }
        },
        created () {
            this.fetchData(this.draw_date, this.draw_time);
        },
        components: {
            Loading
        },
        methods: {
            fetchData (draw_date, draw_time) {
                this.isLoading = true;
                axios.get('/api/games-draw',{
                    params:{
                        'draw_date': draw_date,
                        'draw_time': draw_time,
                    }
                }).then(response => {
                    this.loading = false;
                    this.games = response.data;
                    this.isLoading = false;
                });
            },
            confirmSave() {
                this.$swal({
                    title: 'Are you sure?',
                    text: "You can always review before submitting.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Review'
                }).then((result) => {
                    if (result.value) {
                        this.save();
                    }
                })
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
                    if( !game.draw_id ){
                        draw_promises.push( 
                            axios.post('/api/draw', params).then( response => {
                                if( response.data.id ){
                                    axios.get('/api/check-tickets/' + response.data.id);
                                }
                            })
                        );  
                    }
                });

                axios.all(draw_promises)
                .then(axios.spread((response) => {
                    this.isLoading = false;
                }))
                .then( response => {
                    this.$swal({ title: 'Success!', text: 'Results successfully submitted.', type: 'success'
                    }).then((result) => { this.$router.go(); })
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
            },
            getTotalHits (winners) {
                return window._.sumBy(winners, (win) => { 
                    return parseInt( win.bet.amount ); 
                });
            },
            getWinnersCount (winners){
                return _.uniqBy(winners, 'ticket_number').length;
            }
        },
    }
</script>