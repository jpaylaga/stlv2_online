<template>
<div>
    <loading :active.sync="isLoading">
    </loading>
    <!--About section starts-->
    <section id="newUser">
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">{{id ? 'Edit' : 'Create New'}} Outlet</div>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: '/'}">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="px-3">
                            <form class="form form-horizontal" @submit.prevent="save">
                                <div class="form-body">
                                    <h4 class="form-section-heading"><i class="ft-user"></i> Basic Info</h4>
                                    <div class="row">
                                        <div class="col-md-12 profile-group">
                                            <div class="form-group">
                                                <label class="label-control" for="name">Name</label>
                                                <input type="text" class="form-control" placeholder="Name" v-model="outlet.name">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="street">Street</label>
                                                <input type="text" class="form-control" placeholder="Street" v-model="outlet.street">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="barangay">Barangay</label>
                                                <input type="text" class="form-control" placeholder="Barangay" v-model="outlet.barangay">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="city">City</label>
                                                <input type="text" class="form-control" placeholder="City" v-model="outlet.city">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="zipcode">Zip Code</label>
                                                <input type="text" class="form-control" placeholder="Zip Code" v-model="outlet.zipcode">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="province">Province</label>
                                                <input type="text" class="form-control" placeholder="Province" v-model="outlet.province">
                                            </div>
                                            <div class="form-group" v-if="areas">
                                                <label class="label-control" for="areastation">Area/Station</label>
                                                <select id="areastation" v-model="outlet.area_id" class="form-control">
                                                    <option value="">Select an area</option>
                                                    <option v-for="area in areas" :value="area.id" :key="area.id">{{area.name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save Changes </button>
                                    <router-link class="btn btn-outline-danger round" :to="{name: '/'}">
                                        <i class="fa fa-times mr-1"></i> Cancel
                                    </router-link>
                                </div>
                            </form>
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
    import moment from 'moment';
    
    export default {
        props: ['id'],
        data() {
            return{
                isLoading: false,
                areas: [],
                outlet: {
                    name: '',
                    street: '',
                    barangay: '',
                    city: '',
                    zipcode: '',
                    province: '',
                    area_id: '',
                },
            }
        },
        components: { Loading },
        mounted () {
            this.fetchAreas();
            if( this.id ){
                this.isLoading = true;
                axios.get('/api/outlet/' + this.id).then(response => {
                    this.outlet = response.data;
                    consol
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            }
        },
        methods: {
            fetchAreas(){
                this.isLoading = true;
                axios.get('/api/areas').then(response => {
                    this.areas = response.data
                    this.isLoading = false;
                });
            },
            save(){
                let url = '/api/outlet/';
                let method = 'POST';
                if( this.id ){
                    url = '/api/outlet/' + this.id;
                    method = 'PATCH';
                } 
                this.isLoading = true;
                axios({ 
                    method: method, url: url, data: this.outlet,
                }).then(response => {
                    this.$swal({ title: 'Success!', text: 'Outlet successfully saved.', type: 'success' })
                    .then((result) => {  
                        if( method == 'POST' )
                            this.$router.push({ name: 'edit', params: { id: response.data.id } });
                        this.isLoading = false;
                    });
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    this.errors = [].concat.apply([], messages);
                    this.$swal({ title: 'Error!', text: this.errors[0], type: 'error' });
                    this.isLoading = false;
                });
            },
        }
    }
</script>

<style>
    .form-section-heading{ margin: 20px 0; }
    .user-dob .mx-datepicker{ display: block; width: 100%; }
    .user-dob .mx-datepicker input.mx-input{ height: 37px; border: 1px solid #A6A9AE; }
    .profile-group{ margin-top: 15px; }
    .profile-image{ text-align: center; height: 200px; }
    .profile-image img{  width: 200px; border-radius: 100px; }
</style>
