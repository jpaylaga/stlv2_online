<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="highestBetReports">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">Highest Bet Page</div>                                
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-2 date-picker dp-fullwidth" style="padding-right: 0;">
                                        <label style="display:block;">Draw Date</label>
                                        <date-picker 
                                            @change="filter" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-1" style="padding-left: 30px;"> 
                                        <div class="form-group row">
                                            <label>Draw Time</label>
                                            <select id="draw_time" v-model="draw_time" class="form-control" @change="filter">
                                                <option value="">All</option>
                                                <option value="11">11AM</option>
                                                <option value="16">4PM</option>
                                                <option value="21">9PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="margin-left: 10px;">
                                        <div class="form-group row">
                                            <label>Area</label>
                                            <select @change="filter" v-model="area_filter" class="form-control">
                                                <option value="">All</option>
                                                <option v-for="area in areaOptions" :value="area.id" :key="area.id">{{area.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="margin-left: 10px;">
                                        <div class="form-group row">
                                            <label>Game</label>
                                            <select @change="filter" v-model="game_filter" class="form-control">
                                                <option value="">All</option>
                                                <option v-for="game in gameOptions" :value="game.id" :key="game.id">{{game.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Amount</label>
                                        <input v-model="amount_filter" type="number" min="50" class="form-control" placeholder="100">
                                    </div>
                                    <div class="col-md-2 form-actions">
                                        <button @click="filter" class="btn btn-outline-primary round">
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
                                            <th>Ticket No</th>
                                            <th>Usher</th>
                                            <th>Area</th>
                                            <th>Bet</th>
                                            <th>Game</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Win</th>
                                            <th>Draw Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="bet in bets" :key="bet.bet_id">
                                            <td>{{bet.ticket_number | uppercase}}</td>
                                            <td>{{bet.user}}</td>
                                            <td>{{bet.area}}</td>
                                            <td>{{bet.combination}}</td>
                                            <td>{{bet.game}}</td>
                                            <td>{{bet.bet_type == 'straight' ? 'Target' : 'Ramble'}}</td>
                                            <td>{{bet.bet_amount}}</td>
                                            <td>{{bet.bet_win | currency('&#8369;')}}</td>
                                            <td>
                                                {{ bet.draw_date | draw_date }} 
                                                <span class="drawtime" :class="'time-' + bet.draw_time">{{bet.draw_time | draw_time}}</span>
                                            </td>
                                        </tr>
                                    </tbody>
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
                date: new Date(), //default date
                amount_filter: 200,
                game_filter: '',
                area_filter: '',
                draw_time: '',
                bets: [],
                gameOptions: [],
                areaOptions: [],
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
            this.defineOptions();
        },
        components: {
            Loading
        },
        methods: {
            defineOptions() {
                this.isLoading = true;
                let draw_promises = [];
                draw_promises.push( 
                    axios.get('/api/games').then(resp => {
                        this.gameOptions = resp.data;
                    })
                );
                draw_promises.push(
                    axios.get('/api/areas').then(resp => {
                        this.areaOptions = resp.data;
                    })
                );

                axios.all(draw_promises)
                .then(axios.spread((response) => { }))
                .then( response => {
                    this.fetchData();
                });
            },
            fetchData () {
                this.isLoading = true;
                let draw_date = moment(this.date).format('MM/DD/YYYY');
                axios.get('/api/reports/highest-bet', {
                    params: {
                        date : draw_date,
                        draw_time : this.draw_time,
                        amount_filter : this.amount_filter,
                        game_filter: this.game_filter,
                        area_filter: this.area_filter,
                    }
                })
                .then(response => {
                    this.bets = _.orderBy(response.data, 'bet_amount', 'desc');
                    this.isLoading = false;
                });
            },
            filter(){
                this.fetchData();
            }
        },
    }
</script>

<style>
    #highestBetReports .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>