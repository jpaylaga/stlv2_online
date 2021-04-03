<template>
    <div class="main-content">
        <div id="all-transasctions" class="p-2">
            <loading :active.sync="isLoading"></loading>
            <!--Extended Table starts-->
            <div class="row mb-2">
                <div class="col-md-6 col-sm-8">
                    <h5>Current Balance: </h5>
                    <h4>
                        <strong>{{credit_balance | currency('&#8369;')}}</strong>
                    </h4>
                </div>
                <div class="col-md-6 col-sm-4 mt-2">
                    <a class="btn btn-outline-primary pull-right remove-pull-sm mb-0" href="/credit-balance/info">
                        <i class="fa fa-info-circle"></i> View Requests
                    </a>
                </div>
            </div>
            <section id="extended">
                <div class="row force-unwidth">
                    <div class="col-sm-12 mb-1">
                        <center>
                            <h3><strong> Withdraw / Cash-out </strong></h3>
                        </center>

                        <form class="form" @submit.prevent="submitWithdrawal">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="mb-0" for="amount"><strong>*Outlet</strong></label> 
                                    <select v-model="selectedOutlet" class="form-control" required>
                                        <option value="">Select</option>
                                        <option v-for="outlet in $getWithdrawalOutletOptions()" :key="outlet.id" :value="outlet">{{outlet.name}}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-2" v-if="selectedOutlet">
                                    <label class="mb-0" for="amount"><strong>*Amount to withdraw:</strong> <small class="text-center">Minimum of {{selectedOutlet.min | currency('', 0)}} - Maximum of {{selectedOutlet.max | currency('', 0)}}</small></label>
                                    <input name="amount" v-model="withdraw.amount" type="number" :min="selectedOutlet.min" :max="selectedOutlet.max" class="form-control" required>
                                </div>

                                <div class="col-md-12 mb-2" v-if="selectedOutlet && (selectedOutlet.id == 'gcash')">
                                    <label class="mb-0" for="receiver"><strong>Receiver:</strong> <small class="notice">GCash Only: Fill this up if you want to receive the amount from other account.</small></label> 
                                    <input name="receiver" v-model="withdraw.receiver" type="text" class="form-control">
                                </div>
                                
                                <div class="col-md-12 mb-2" v-if="selectedOutlet && (selectedOutlet.id == 'ml' || selectedOutlet.id == 'cebuana')">
                                    <label class="mb-0" for="receiver"><strong>*Receiver Name</strong></label>
                                    <input name="receiver" v-model="withdraw.receiver" type="text" class="form-control" required>

                                    <label class="mb-0 mt-1" for="description"><strong>*Address</strong></label>
                                    <textarea name="description" v-model="withdraw.description" class="form-control" required></textarea>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="alert alert-info" role="alert">
                                        <small>Withdrawals are processed at a minimum of 1 banking day. Applicable fees and charges will be shouldered by the user.</small>
                                    </div>
                                    <button class="btn btn-success w-full">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <!--Extended Table Ends-->   

        </div>     
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';

export default {
    data() {
        return{
            isLoading: false,
            player: null,
            credit_balance: 0,
            selectedOutlet: null,
            withdraw: {
                'outlet' : '',
                'amount': 0,
                'receiver': '',
                'description': '',
                'player_id': '',
            },
        }
    },
    components: { Loading },
    created(){
        this.init();

    },
    methods: {
        init(){
            this.player = this.$getUser();
            this.withdraw.player_id = this.player.id;
            this.getCreditBalance();
        },
        getCreditBalance(){
            axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                this.credit_balance = response.data.credit_balance
            });
        },
        submitWithdrawal(){
            // check balance
            if( parseInt(this.withdraw.amount) > parseInt(this.credit_balance) ){
                Vue.toasted.error("Insufficient credit balance."); 
                return true;
            }
            if( !this.selectedOutlet ){
                Vue.toasted.error("Please select outlet."); return true;
            }

            this.isLoading = true;
            this.withdraw.outlet = this.selectedOutlet.id;
            axios.post('/api/credits/withdraw', this.withdraw).then(resp => {
                if( resp.data.success ){
                    this.$swal({
                        type: 'success',
                        title: resp.data.message,
                        showConfirmButton: true, 
                        showCancelButton: false,
                    }).then(result => {
                        if (result.value) {
                            this.isLoading = true
                            window.location.href = '/credit-balance/info';
                        }
                    });
                }else{
                    Vue.toasted.error(resp.data.message);
                }
            }).catch(error => {
                Vue.toasted.error('Something went wrong. Please contact admin.');
            }).then(() => { this.isLoading = false })
        }
    }
}
</script>

<style>
    #all-transasctions .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>