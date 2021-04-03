<template>
    <div class="card">
        <loading :active.sync="isLoading"></loading>
        <div class="card-header">
            <h4 class="card-title">Hot Numbers {{draw_time | draw_time}}</h4>
            <span class="grey">{{draw_date2 | draw_date}}</span>
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table text-left">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Target</th>
                            <th>Ramble</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="num in hot_numbers" :key="num.id">
                            <td>{{num.combination}}</td>
                            <td>{{num.types.straight | currency('&#8369;')}}</td>
                            <td>{{num.types.ramble | currency('&#8369;')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <div class="card-block"></div> -->
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import Loading from 'vue-loading-overlay';
    export default {
        props: ['draw_date'],
        data(){
            return{
                isLoading: false,
                hot_numbers: [],
                draw_date2: moment( new Date() ).format('MM/DD/YYYY'),
                draw_time: this.$options.filters.valid_draw_time(moment().format("HH")) 
            }
        },
        components: { Loading },
        created () {
            this.fetchHotNumbers();
        },
        methods: {
            fetchHotNumbers(){
                // this.isLoading = true,
                axios.get('/api/reports/hot-numbers-control', {
                    params: {
                        date: this.draw_date2,
                        draw_time: this.draw_time,
                    }
                }).then(response => {
                    this.hot_numbers = _.orderBy(response.data, 'total_amount', 'desc').slice(0,5);
                    // this.isLoading = false;
                });
            },
        }
    }
</script>
