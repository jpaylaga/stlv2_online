<template>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 mt-1 mb-1 mx-1">
                    <div class="content-header">
                        <h2>Howdy, {{user.firstname}} {{user.lastname}}!</h2>
                    </div>
                    <p class="content-sub-header">Welcome to your Dashboard.</p>
                    <nav id="top-right-text">
                        <ul>
                            <li>
                                <a href="#" class="py-1 h6" data-toggle="modal" data-target="#filterDashboard">
                                    <!-- <i class="ft-search font-medium-5 mr-2"></i> -->
                                    <date-picker 
                                        @change="onChangeDate" 
                                        v-model="draw_date"
                                        :format="'MMMM DD, YYYY'"
                                        :lang="lang">
                                    </date-picker>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import DatePicker from 'vue2-datepicker'

    export default {
        components: { DatePicker },  
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
                // if( this.draw_date ){
                    // let draw_date = moment(this.draw_date).format('MM/DD/YYYY');
                    // this.$refs.DrawSales.initData(draw_date);
                    // this.$refs.SalesPerGame.fetchGames(draw_date);
                // }
            }
        }
    }
</script>