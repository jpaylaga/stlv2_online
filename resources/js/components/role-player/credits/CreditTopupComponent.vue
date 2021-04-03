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
                            <h3><strong> Topup / Cash-in </strong></h3>
                        </center>
                    </div>
                    <div v-for="outlet in outlets" :key="outlet.id" class="col-sm-4">
                        <div :class="{'text-white bg-primary' : selectedOutlet == outlet.id}" class="card m-0 p-1" @click="selectedOutlet = outlet.id">
                            <div class="card-body">
                                <center> {{outlet.name}} </center>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- outlets -->
                <div class="row">
                    <div v-if="selectedOutlet=='gcash'" id="gcash" class="col-sm-12">
                        <Gcash :player_id="player.id" />
                    </div>
                    <div v-if="selectedOutlet=='ml'" id="ml" class="col-sm-12">
                        <MLhuillier :player_id="player.id"/>
                    </div>
                    <div v-if="selectedOutlet=='cebuana'" id="cebuana" class="col-sm-12">
                        <Cebuana :player_id="player.id"/>
                    </div>
                </div>
                <!-- @end outlets -->
            </section>
            <!--Extended Table Ends-->   

        </div>     
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import Gcash from './outlets/Gcash.vue'
import MLhuillier from './outlets/MLhuillier.vue'
import Cebuana from './outlets/Cebuana.vue'

export default {
    data() {
        return{
            isLoading: false,
            player: null,
            credit_balance: 0,
            outlets: [
                { id: 'gcash', name: 'GCash' },
                { id: 'ml', name: 'MLhuillier' },
                { id: 'cebuana', name: 'Cebuana' },
            ],
            selectedOutlet: 'gcash'
        }
    },
    components: { Loading, Gcash, MLhuillier, Cebuana },
    created(){
        this.init();

    },
    methods: {
        init(){
            this.player = this.$getUser();
            this.getCreditBalance();
        },
        changeSelectedOutlet(outlet){
            this.selectedOutlet = outlet;
        },
        getCreditBalance(){
            axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                this.credit_balance = response.data.credit_balance
            });
        },
        navigateToCreditRequest(){
            this.$router.push({ name: 'edit', params: { id: response.data.id } });
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