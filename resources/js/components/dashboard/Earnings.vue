<template>
    <div class="col-xl-4 col-lg-5 col-md-5 col-12">
        <loading :active.sync="isLoading">
        </loading>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Earnings [static]</h4>
                <span class="grey">Monday, June 18</span>
            </div>
            <div class="card-body">
                <div class="card-block">
                    <div class="earning-details mb-4">
                        <h3 class="font-large-1 mb-1">{{gross_sales | currency('&#8369;', 0) }} </h3>
                        <span class="font-medium-1 grey d-block">Gross Sales</span>
                        <h3 class="font-large-1 mb-1">{{winning_amount | currency('&#8369;', 0) }} </h3>
                        <span class="font-medium-1 grey d-block">Total Payout</span>
                        <h3 class="font-large-1 mb-1">{{total_earnings | currency('&#8369;', 0) }} <i class="ft-arrow-up font-large-1 teal accent-3"></i><i class="ft-arrow-down font-large-1 red accent-3" style="display:none;"></i></h3>
                        <span class="font-medium-1 grey d-block">Total Earnings</span>
                    </div>
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
        data () {
            return {
                isLoading: false,
                errors: [],
                gross_sales: 0,
                winning_amount: 0,
                total_earnings: 0,
            }
        },
        created() {
            this.getSalesStats();
        },
        components: { Loading },
        methods: {
            getSalesStats() {
                this.isLoading = true;
                axios.get('/api/sales/stats',{
                    params:{
                        'draw_date': moment(new Date()),
                    }
                }).then(response => {
                    this.tickets = response.data;
                });
            }
            // listen() {
            //     Echo.private('user.' + this.user_id)
            //         .listen('TicketCreated', (e) => {
            //             console.log(e);
            //             this.tickets.unshift(e.ticket);
            //             this.calculateTotalAmount(e.ticket.draw_time);
            //         });
            // }
        }
    }
</script>