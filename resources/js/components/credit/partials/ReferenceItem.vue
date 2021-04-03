<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <section id="extended">
            <div class="row mt-2">
                <div class="col-md-6 col-sm-8">
                    <h3>{{ id ? 'Edit' : 'Add' }} Credit Request Reference</h3>
                </div>
                <div class="col-md-6 col-sm-4">
                    <router-link class="btn btn-outline-primary pull-right remove-pull-sm mb-0" :to="{name: '/'}">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form class="form" @submit.prevent="submit">
                                <div class="row px-4 py-2">
                                    <div class="col-md-6 mt-2">
                                        <label class="mb-0" for="name">Name</label>
                                        <input name="name" v-model="reference.name" type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="mb-0" for="ref_number">Reference #</label>
                                        <input name="ref_number" v-model="reference.ref_number" type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="mb-0" for="mobile_number">Mobile Number</label>
                                        <input name="mobile_number" v-model="reference.mobile_number" type="text" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="mb-0" for="outlet">Payment Outlet</label>
                                        <select v-model="reference.outlet" class="form-control" required>
                                            <option value="">Select</option>
                                            <option v-for="outlet in $getOutletOptions()" :key="outlet.id" :value="outlet.id">{{outlet.name}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <label class="mb-0" for="notes">Notes</label>
                                        <textarea name="notes" v-model="reference.notes" type="text" class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <button class="btn btn-success">{{ id ? 'Update' : 'Submit' }}</button>
                                    </div>
                                </div>
                            </form>
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
        data() {
            return{
                reference: {
                    'name': '',
                    'ref_number': '',
                    'mobile_number': '',
                    'notes': '',
                    'outlet': '',
                },
                isLoading: false,
            }
        },
        components: { Loading },
        mounted () {
            if( this.id ){
                this.isLoading = true;
                axios.get('/api/credits/reference/' + this.id).then(response => {
                    this.reference = response.data;
                }).catch(err => { })
                .then(()=>{ this.isLoading = false; })
            }
            this.init();
        },
        methods: {
           init(){

           },
           submit(){
                if( this.id )
                    this.update()
                else
                    this.create()
           },
           create(){
               this.isLoading = true;
                axios.post('/api/credits/reference', this.reference).then(resp => {
                    if( resp.data.success ){
                        this.$swal({
                            type: 'success',
                            html: resp.data.message,
                            showConfirmButton: true, 
                            showCancelButton: false,
                        }).then(result => {
                            if (result.value) {
                                this.isLoading = true;
                                window.location.href = '/credit-references/list';
                            }
                        });
                    }else{
                        Vue.toasted.error(resp.data.message);
                    }
                }).catch(error => {
                    Vue.toasted.error('Something went wrong. Please contact admin.');
                }).then(() => { this.isLoading = false })
           },
           update(){
               this.isLoading = true;
                axios.put('/api/credits/reference/' + this.id, this.reference).then(resp => {
                    if( resp.data.success ){
                        this.$swal('success', resp.data.message);
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
