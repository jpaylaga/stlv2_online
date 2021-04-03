<template>
    <div class="main-content">
        <loading :active.sync="isLoading"></loading>
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 mb-1 mx-1">
                    <div class="content-header mt-0">
                        <h3>Howdy, {{player.firstname}}!</h3>
                    </div>
                    <p class="content-sub-header">Welcome to your Dashboard.</p>
                </div>
            </div>

            <section id="minimal-statistics">
                <div class="row match-height">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-body text-left">
                                            <h3 class="mb-1 success">{{credit_balance | currency('&#8369;') }}</h3>
                                            <span>Credit Balance</span>
                                            <a class="display-block btn btn-success mb-0 mt-1" href="/credit-balance/info">View Credit Requests</a>
                                        </div>
                                        <div class="media-right">
                                            <i class="fa fa-rub success font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   

                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>Results</h3>
                            </div>
                            <div class="col-sm-6">
                                <a href="#" class="py-1 h6" data-toggle="modal" data-target="#filterDashboard">
                                    <date-picker 
                                        @change="onChangeDate" 
                                        v-model="draw_date"
                                        :format="'MMMM DD, YYYY'"
                                        :lang="lang">
                                    </date-picker>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-12" v-for="dr in draw_list" :key="dr">
                        <div class="card text-white bg-primary mb-0">
                            <div class="card-body">
                                <div class="px-3 py-3">
                                    <div class="media">
                                        <div class="media-left align-self-center">
                                            <h3 class="mb-0 font-large-1"><strong>{{ $getDrawTimeOptions(dr) }}</strong></h3>
                                            <small>Draw Results</small>
                                        </div>
                                        <div class="media-body text-right">
                                            <div v-for="game in games" :key="game.id" class="mb-2">
                                                <h4 class="mb-0"><strong>{{ getGameResult(game.results, dr) }}</strong></h4>
                                                <small>{{game.name}}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-12 mt-2">
                        <a class="display-block btn btn-success mb-0 mt-1" href="/transactions/list">View Transactions</a>
                    </div>

                </div>
            </section>

        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker'
    import Loading from 'vue-loading-overlay';

    export default {
        components: { DatePicker, Loading },  
        data(){
            return{
                draw_date: moment( new Date() ).format('MM/DD/YYYY'),
                lang: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                    placeholder: {  
                        date: 'Select Date',
                    }
                },
                player: null,
                games: null,
                credit_balance: 0,
                isLoading: false,
                draw_list: ['11','16','21'],
            }
        },
        created() {
            this.init()
        },
        methods: {
            init(){
                this.player = this.$getUser();
                this.getCreditBalance();
                this.getResults();
            },
            getResults(){
                this.isLoading = true;
                axios.get('/api/results-by-game', {
                    params: { draw_date: this.draw_date }
                }).then(response => {
                    this.games = response.data;
                }).catch(error => {
                    Vue.toasted.error('Something went wrong. Please try again');
                }).then(() => { this.isLoading = false })
            },
            onChangeDate(){
                if( this.draw_date ){
                    this.draw_date = moment(this.draw_date).format('MM/DD/YYYY');
                    this.getResults();
                }
            },
            getCreditBalance(){
                axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                    this.credit_balance = response.data.credit_balance
                });
            },

            // helpers
            getGameResult(results, draw_time){
                let result = _.find(results, ['draw_time', draw_time]);
                return result ? result.result : '---';
            },
        }
    }
</script>