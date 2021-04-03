<template>
    <div class="card">
        <div class="card-body">
            <div class="card-block">
                <loading :active.sync="isLoading"></loading>
                <form class="form" @submit.prevent="submitML">
                    <div class="form-body">
                        <center class="mb-1">
                            <img class="rounded img-thumbnail mb-2" :src="`/images/outlets/ml.jpg`" alt="ml">
                            <p class="mb-0"><strong>Send a minimum of 200</strong></p>
                            <p>to any persons below</p>
                            
                            <div v-for="ref in referenceOptions" :key="ref.id" class="alert alert-success" role="alert">
                                <h5 class="mb-0"><strong>{{ref.name}}</strong></h5>
                                <p class="mb-0"><strong>Address: </strong>{{ref.notes}}</p>
                                <p class="mb-0"><strong>Phone: </strong>{{ref.mobile_number}}</p>
                            </div>

                            <div class="alert alert-info" role="alert">
                                Fill the control no., amount, sender and receiver in the form below.<br>
                            </div>

                        </center>
                        <h3>Deposit Form</h3>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="amount">Amount</label> </center>
                                <input name="amount" v-model="ml.amount" type="number" min="200" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="sender">Sender Name</label> </center>
                                <input name="sender" v-model="ml.sender" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="receiver">Receiver Name</label> </center>
                                <select v-model="ml.receiver" class="form-control" required>
                                    <option value="">Select</option>
                                    <option v-for="ref in referenceOptions" :key="ref.id" :value="`${ref.name} ${ref.mobile_number}`">{{ref.name}} - {{ref.mobile_number}}</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="ref">Control no.</label> </center>
                                <input name="ref" v-model="ml.ref" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <center> <label class="mb-0" for="description">Notes</label> </center>
                                <textarea name="description" v-model="ml.description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12 mt-2">
                                <center>
                                    <button class="btn btn-success">Submit</button>
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
            ml: {
                'amount': 200,
                'sender': '',
                'receiver': '',
                'ref': '',
                'description': '',
                'outlet' : 'ml',
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
            this.ml.player_id = this.player_id
            this.loadReferences();
        },
        async loadReferences(){
            await axios.get('/api/credits/references', {
                params: {
                    filter: {
                        outlet: 'ml',
                        is_active: true
                    }
                }
            }).then(response => {
                this.referenceOptions = response.data.data
            }).catch(error => {})
        },
        submitML(){
            this.isLoading = true;
            axios.post('/api/credits/topup', this.ml).then(resp => {
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
