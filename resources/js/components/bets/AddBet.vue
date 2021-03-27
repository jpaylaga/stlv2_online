<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <section id="newUser">
            <div class="row">
                <!-- <div class="col-md-3">
                    <div class="card">
                    </div>
                </div> -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="px-3">
                                <form class="form form-horizontal striped-rows form-bordered" @submit.prevent="save">
                                    <div class="form-body">
                                        <h4 class="form-section"><i class="ft-file-plus"></i> Add Bet</h4>

                                        <!-- errors -->
                                        <ul v-if="errors">
                                            <li v-for="error in errors" :key="error.index">
                                                {{error}}
                                            </li>
                                        </ul>

                                        <!-- <div class="form-group row">
                                            <label class="col-md-3 label-control" for="draw_date">Draw Date</label>
                                            <div class="col-md-9">
                                                <input disabled type="text" class="form-control" placeholder="Draw Date">
                                            </div>
                                        </div> -->

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="draw_time">Draw Time</label>
                                            <div class="col-md-9">
                                                <label class="radio-inline">
                                                    <input v-model="live_transaction.tickets[0].draw_time" type="radio" value="11" checked> 11AM 
                                                </label>
                                                <label class="radio-inline">
                                                    <input v-model="live_transaction.tickets[0].draw_time" type="radio" value="16"> 4PM 
                                                </label>
                                                <label class="radio-inline">
                                                    <input v-model="live_transaction.tickets[0].draw_time" type="radio" value="21"> 9PM 
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="game">Game</label>
                                            <div class="col-md-9">
                                                <label class="radio-inline" v-for="game in gameOptions" :key="game.id">
                                                    <input v-model="live_transaction.game_id" type="radio" :value="game.id"> {{game.name}} 
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="combination">Combination</label>
                                            <div class="col-md-9">
                                                <input v-model="live_transaction.tickets[0].bets[0].combination" type="number" min="1" max="999" class="form-control" placeholder="Combination" >
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="type">Bet Type</label>
                                            <div class="col-md-9">
                                                <label class="radio-inline">
                                                    <input v-model="live_transaction.tickets[0].bets[0].type" type="radio" value="straight" checked> Straight 
                                                </label>
                                                <label class="radio-inline">
                                                    <input v-model="live_transaction.tickets[0].bets[0].type" type="radio" value="ramble"> Rambol 
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="amount">Amount</label>
                                            <div class="col-md-9">
                                                <input v-model="live_transaction.tickets[0].bets[0].amount" type="number" min="1" class="form-control" placeholder="Amount">
                                            </div>
                                        </div>

                                        <!-- <div class="form-group row" >
                                            <label class="col-md-3 label-control" for="payable_amount">Payable Amount</label>
                                            <div class="col-md-9">
                                                <input disabled type="number" step="0.01" min="0.00" class="form-control" placeholder="Payable Amount">
                                            </div>
                                        </div> -->

                                        <div class="form-group row">
                                            <label class="col-md-3 label-control" for="customer">Customer Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Customer Name">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Submit </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import moment from 'moment';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        data() {
            return{
                //it would be better if winning amount will be calculated on the mobile app side
                // isLoading = false,
                transaction: {
                    game_id: 1,
                    user_id: 8,
                    tickets: [
                        {
                            draw_date: moment(new Date()).format("MM/DD/YYYY"),
                            draw_time: '21',
                            bets: [
                                {
                                    combination: '123',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '456',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '789',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '489',
                                    type: 'straight',
                                    amount: 1,
                                }
                            ]
                        },
                        {
                            draw_date: moment(new Date()).format("MM/DD/YYYY"),
                            draw_time: '16',
                            bets: [
                                {
                                    combination: '123',
                                    type: 'straight',
                                    amount: 2,
                                },
                                {
                                    combination: '456',
                                    type: 'straight',
                                    amount: 2,
                                },
                                {
                                    combination: '789',
                                    type: 'straight',
                                    amount: 2,
                                }
                            ]
                        }
                    ],
                },
                transaction_pares: {
                    game_id: 4,
                    user_id: 8,
                    tickets: [
                        {
                            draw_date: moment(new Date()).format("MM/DD/YYYY"),
                            draw_time: '21',
                            bets: [
                                {
                                    combination: '4:9',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '40:11',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '40:24',
                                    type: 'straight',
                                    amount: 1,
                                },
                                {
                                    combination: '6:1',
                                    type: 'straight',
                                    amount: 1,
                                }
                            ]
                        },
                    ],
                },
                transaction_rambol: {
                    game_id: 1,
                    user_id: 8,
                    tickets: [
                        {
                            draw_date: moment(new Date()).format("MM/DD/YYYY"),
                            draw_time: '11',
                            bets: [
                                {
                                    combination: '199',
                                    type: 'ramble',
                                    amount: 12,
                                },
                            ]
                        },
                    ],
                },
                errors: [],
                gameOptions: [],
                live_transaction: {
                    game_id: 1,
                    user_id: $('meta[name="userId"]').attr('content'),
                    tickets: [
                        {
                            draw_date: moment(new Date()).format("MM/DD/YYYY"),
                            draw_time: 11,
                            bets: [
                                {
                                    combination: '',
                                    type: '',
                                    amount: ''
                                },
                            ]
                        }
                    ]
                },
                isLoading: false
            }
        },
        components: {
            Loading
        },
        created () {
            this.fetchOptionsData();
        },
        methods: {
            fetchOptionsData() {
                axios.get('/api/games').then(response => {
                    this.gameOptions = response.data;
                });
            },
            save(){
                let url = '/api/transaction';
                let method = 'POST';
                this.isLoading = true;

                axios({ method: method, url: url, data: this.live_transaction }).then(response => {
                    this.isLoading = false;
                    Vue.toasted.info('Bet successfully saved.');
                    this.$router.go();
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    this.errors = [].concat.apply([], messages);
                    console.log(this.errors);
                    this.isLoading = false;
                });
            }
        }
    }
</script>