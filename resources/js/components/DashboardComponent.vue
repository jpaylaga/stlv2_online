<template>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 mt-1 mb-1 mx-1">
                    <div class="content-header">
                        <h2>Howdy, {{user.firstname}} {{user.lastname}}!</h2>
                    </div>
                    <p class="content-sub-header mb-2">Welcome to Dashboard.</p>
                    <a href="#" class="py-1 h6" data-toggle="modal" data-target="#filterDashboard">
                        <date-picker 
                            @change="onChangeDate" 
                            v-model="draw_date"
                            :format="'MMMM DD, YYYY'"
                            :lang="lang">
                        </date-picker>
                    </a>
                    <!-- <nav id="top-right-text">
                        <ul>
                            <li>
                                <a href="#" class="py-1 h6" data-toggle="modal" data-target="#filterDashboard">
                                    <date-picker 
                                        @change="onChangeDate" 
                                        v-model="draw_date"
                                        :format="'MMMM DD, YYYY'"
                                        :lang="lang">
                                    </date-picker>
                                </a>
                            </li>
                        </ul>
                    </nav> -->
                </div>
            </div>

            <section id="minimal-statistics">
                <div class="row" v-if="user.role == 'coordinator'">
                    <div class="col-12">
                        <div class="card text-white bg-primary mb-0">
                            <div class="card-body">
                                <div class="px-2 py-2">
                                    <div class="media">
                                        <div class="media-body text-left">
                                            <div class="mb-0">
                                                <p class="mb-0">Referral Link:</p>
                                                <input id="testing-code" class="form-control display-block" type="text" :value="`${APP_BASE_URL}/register?referral=${user.api_token}`">
                                            </div>
                                        </div>
                                        <div @click="copyReferral" class="pl-4 media-right align-self-center">
                                            <i class="fa fa-clipboard font-large-2 float-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <draw-sales :draw_date="draw_date" ref="DrawSales"></draw-sales>
                <!-- <div class="row match-height"> -->
                    <!-- <sales-per-game :draw_date="draw_date" ref="SalesPerGame"></sales-per-game> -->
                <!-- </div> -->
            </section>

        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import DrawSales from './dashboard/DrawSales.vue';
    import SalesPerGame from './dashboard/SalesPerGame.vue';
    import DatePicker from 'vue2-datepicker'

    export default {
        components: {
            'draw-sales' : DrawSales,
            'sales-per-game' : SalesPerGame,
            DatePicker 
        },  
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
            }
        },
        computed: {
            user() {
                return this.$store.getters.getUser
            }, 
        },
        created() {

        },
        methods: {
            onChangeDate(){
                if( this.draw_date ){
                    let draw_date = moment(this.draw_date).format('MM/DD/YYYY');
                    this.$refs.DrawSales.initData(draw_date);
                    // this.$refs.SalesPerGame.fetchGames(draw_date);
                }
            },
            copyReferral(referral_link){
                let testingCodeToCopy = document.querySelector('#testing-code')
                testingCodeToCopy.setAttribute('type', 'text')
                testingCodeToCopy.select()

                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                    Vue.toasted.success('Referral copied to clipboard.');
                } catch (err) {
                    Vue.toasted.error('Unable to copy Referral.');
                }

                /* unselect the range */
                window.getSelection().removeAllRanges()
            }
        }
    }
</script>