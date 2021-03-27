<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12">
                <div class="content-header">Transaction ID: <small>{{transaction.code | uppercase}}</small></div>
                <p class="content-sub-header">Total Bet Amount: <strong>{{transaction.total_amount | currency('&#8369;') }}</strong> | <em>{{ transaction.created_at | bet_date }}</em></p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link :to="{name: 'list'}" class="py-1 h6">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>                        
        </div>  
        <section>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <span v-if="transaction.outlet">{{transaction.outlet.name}} / </span>
                                <span v-if="transaction.user">
                                    {{transaction.user.firstname}} {{transaction.user.lastname}} 
                                </span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Game</th>                                                            
                                            <th>Type</th>                                                            
                                            <th>Bet Number</th>
                                            <th>Bet Amount</th>
                                            <th>Win Price</th>
                                            <th>Draw Date/Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-justify">
                                        <tr v-for="bet in transaction.bets" :key="bet.it">
                                            <td>{{bet.id}}</td>
                                            <td>{{transaction.game.name}}</td>
                                            <td>{{bet.type | capitalize}}</td>
                                            <td>{{bet.combination}}</td>
                                            <td>{{bet.amount | currency('&#8369;') }}</td>
                                            <td>{{bet.winning_amount | currency('&#8369;') }}</td>
                                            <td>
                                                <span class="text-success">
                                                    {{ bet.ticket.draw_date | draw_date }} {{ bet.ticket.draw_time | draw_time }}
                                                </span>
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
    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    export default {
        props: ['id'],
        created () {
            if( this.id ){
                this.fetchData(this.id);
            }
        },
        data() {
            return{
                isLoading: false,
                transaction: []
            }
        },
        watch: {
            '$route': 'fetchData'
        },
        components: {
            Loading
        },
        methods: {
            fetchData(transaction_id) {
                this.isLoading = true;
                axios.get('/api/transaction/' + transaction_id).then(response => {
                    this.transaction = response.data;
                    this.isLoading = false;
                });
            },
        }
    }
</script>