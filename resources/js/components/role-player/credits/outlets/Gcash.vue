<template>
    <div class="card">
        <div class="card-body">
            <div class="card-block">
                <loading :active.sync="isLoading"></loading>
                <form class="form" @submit.prevent="submitGcash">
                    <div class="form-body">
                        <center class="mb-1">
                            <img class="rounded img-thumbnail mb-2" :src="`/images/outlets/gcash.jpg`" alt="Gcash">
                            <p class="mb-0"><strong>200.00 to 10,000.00</strong></p>
                            <p>to any numbers below</p>
                            
                            <div v-for="ref in referenceOptions" :key="ref.id" class="alert alert-success" role="alert">
                                <h5 class="mb-0"><strong>{{ref.mobile_number}}</strong></h5>
                                <p class="mb-0">{{ref.name}}</p>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <!-- Our GCash number change every 10 minutes. Refresh this page before sending.<br> -->
                                Fill the reference no., amount, receiver number in the form below.<br>
                                <!-- Attach the screenshot receipt in the attachment field. -->
                            </div>

                        </center>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="amount">Amount</label> </center>
                                <input name="amount" v-model="gcash.amount" type="number" min="200" max="10000" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="sender">Sender</label> </center>
                                <input name="sender" v-model="gcash.sender" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="receiver">Receiver</label> </center>
                                <!-- <input name="receiver" v-model="gcash.receiver" type="text" class="form-control" required> -->
                                <select v-model="gcash.receiver" class="form-control" required>
                                    <option value="">Select</option>
                                    <option v-for="ref in referenceOptions" :key="ref.id" :value="`${ref.name} ${ref.mobile_number}`">{{ref.name}} - {{ref.mobile_number}}</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="ref">Reference no.</label> </center>
                                <input name="ref" v-model="gcash.ref" type="text" class="form-control" required>
                            </div>
                            <!-- <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="screenshot">Screenshot of the GCash receipt</label> </center>
                                <input name="screenshot" type="file" class="form-control">
                            </div> -->
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="description">Notes</label> </center>
                                <textarea name="description" v-model="gcash.description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12 mt-2">
                                <center>
                                    <button class="btn btn-success">Submit GCash</button>
                                </center>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>      
</template>

<script>
import Loading from 'vue-loading-overlay';

export default {
    props: ['player_id'],
    components: { Loading },
    data() {
        return{
            gcash: {
                'amount': 200,
                'sender': '',
                'receiver': '',
                'ref': '',
                'description': '',
                'outlet' : 'gcash',
            },
            isLoading: false,
            referenceOptions: [],
        }
    },
    created(){
        this.init();
    },
    methods: {
        init(){
            this.gcash.player_id = this.player_id
            this.loadReferences();
        },
        async loadReferences(){
            await axios.get('/api/credits/references', {
                params: {
                    filter: {
                        outlet: 'gcash',
                        is_active: true
                    }
                }
            }).then(response => {
                this.referenceOptions = response.data.data
            }).catch(error => {})
        },
        submitGcash(){
            this.isLoading = true;
            axios.post('/api/credits/topup', this.gcash).then(resp => {
                if( resp.data.success ){
                    this.$swal({
                        type: 'success',
                        title: resp.data.message,
                        showConfirmButton: true, 
                        showCancelButton: false,
                    }).then(result => {
                        if (result.value) {
                            this.isLoading = true;
                            if( this.$isAgent() ) window.location.href = '/credit-balance/info';
                            else window.location.href = '/credit-requests';
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
