<template>
<div>
    <loading :active.sync="isLoading">
    </loading>
    <!--About section starts-->
    <section id="newUser">
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">{{id ? 'Edit' : 'Create New'}} User</div>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link v-if="referrer" class="py-1 h6" :to="{name: 'view-agent-list', params: {coordinator: referrer}}">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back to Ushers List</span>
                            </router-link>
                            <router-link v-else class="py-1 h6" :to="{name: '/'}">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </router-link>
                            <!-- <a v-else @click="$router.go(-1)" class="py-1 h6">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </a> -->
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
                                    <h4 class="form-section-heading"><i class="ft-user"></i> Personal Info</h4>
                                    <div class="row">
                                        <div class="col-md-6 profile-group">
                                            <div class="form-group">
                                                <label class="label-control" for="username">Username</label>
                                                <input type="text" class="form-control" placeholder="Username" v-model="user.username">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="id_number">User ID</label>
                                                <input type="text" class="form-control" placeholder="ID Number" v-model="user.id_number">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="firstname">First Name</label>
                                                <input type="text" class="form-control" placeholder="First Name" v-model="user.firstname">
                                            </div>
                                             <div class="form-group">
                                                <label class="label-control" for="lastname">Last Name</label>
                                                <input type="text" class="form-control" placeholder="Last Name" v-model="user.lastname">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="email">E-mail</label>
                                                <input type="email" class="form-control" placeholder="E-mail" v-model="user.email">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="gender">Gender</label>
                                                <select id="gender" v-model="user.gender" class="form-control">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="civil_status">Civil Status</label>
                                                <select id="civil_status" v-model="user.civil_status" class="form-control">
                                                    <option value="">Select Civil Status</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="widowed">Widowed</option>
                                                </select>
                                            </div>
                                            <div class="form-group user-dob">
                                                <label class="label-control" for="username">Birth Date</label>
                                                <date-picker 
                                                    v-model="user.dob"
                                                    :format="'MMMM DD, YYYY'"
                                                    :lang="lang">
                                                </date-picker>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="address">Address</label>
                                                <input type="text" class="form-control" placeholder="User Address" v-model="user.address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="profile-image">
                                                <img v-if="user.picture" :src="`/storage/profile_images/${user.picture}`" alt="">
                                                <img v-else :src="`/images/user-default.png`" alt="">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="logo">Profile Picture</label>
                                                <input type="file" class="form-control" placeholder="Phone" @change="previewFiles($event)" ref="picture">
                                            </div>
                                            <div class="form-group" v-if="user.role=='coordinator'">
                                                <label class="label-control" for="percentage">Commission %</label>
                                                <input type="number" class="form-control" placeholder="Commission %" v-model="user.percentage">
                                            </div>
                                            <div class="form-group" v-if="user.role=='coordinator'">
                                                <label class="label-control" for="float">Float</label>
                                                <input type="number" class="form-control" placeholder="Float" v-model="user.float">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="password">Password</label>
                                                <input type="password" class="form-control" placeholder="password" v-model="user.password">
                                            </div>
                                            <div class="form-group last">
                                                <label class="label-control" for="confirmpassword">Confirm Password</label>
                                                <input type="password" class="form-control" placeholder="Confirm Password" v-model="user.password_confirmation">
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control" for="contactnumber">Contact Number</label>
                                                <input type="number" class="form-control" placeholder="Phone" v-model="user.phone">
                                            </div>
                                            <div class="form-group" v-if="user.role != 'super_admin'">
                                                <label class="label-control" for="roleposition">Role/Position</label>
                                                <select id="roleposition" v-model="user.role" class="form-control" required>
                                                    <option value="">Select a role</option>
                                                    <option v-for="role in roleOptions" v-bind:value="role.value" :key="role.role">{{role.text}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group" v-if="user.role == 'area_admin' || user.role == 'coordinator'">
                                                <label class="label-control" for="areastation">Area/Station</label>
                                                <select id="areastation" v-model="user.area" class="form-control">
                                                    <option value="">Select an area</option>
                                                    <option v-for="area in areaOptions" :value="area.id" :key="area.id">{{area.name}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group" v-if="current_user.role != 'coordinator' && (user.role == 'teller' || user.role == 'usher')">
                                                <label class="label-control" for="coordinator">Coordinator</label>
                                                <select id="coordinator" v-model="user.coordinator" class="form-control">
                                                    <option value="">Select a Coordinator</option>
                                                    <option v-for="coordinator in coordinatorOptions" :value="coordinator.id" :key="coordinator.id">{{coordinator.firstname + ' ' + coordinator.lastname}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group" v-if="user.role == 'teller'">
                                                <label class="label-control" for="outletname">Outlet Name</label>
                                                <select id="outletname" v-model="user.outlet" class="form-control">
                                                    <option value="">Select an outlet</option>
                                                    <option v-for="outlet in outletOptions" :value="outlet.id" :key="outlet.id">{{outlet.name}}</option>
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
        props: ['id', 'referrer'],
        data() {
            return{
                current_user: [],
                user: {
                    username: '',
                    firstname: '',
                    lastname: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    phone: '',
                    picture: '',
                    gender: '',
                    civil_status: '',
                    dob: '',
                    id_number: '',
                    role: '',
                    area: '',
                    outlet: '',
                    coordinator: '',
                },
                image: '',
                isLoading: false,
                errors: [],
                roleOptions: [
                    { value: 'teller', text: 'Teller' },
                    { value: 'usher', text: 'Usher' },
                ],
                areaOptions: [],
                outletOptions: [],
                coordinatorOptions: [],
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
        components: {
            Loading
        },
        mounted () {
            if( this.id ){
                this.isLoading = true;
                axios.get('/api/user/' + this.id).then(response => {
                    this.user = response.data;
                    this.user.dob = moment(this.user.dob);
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            }
            this.getCurrentUser();
        },
        methods: {
            getCurrentUser() {
                this.isLoading = true;
                axios.get('/api/user').then(response => {
                    this.current_user = response.data;
                    if( this.current_user.role == 'super_admin' ){
                        this.roleOptions.push(
                            { value: 'area_admin', text: 'Area/Station Admin' },
                            { value: 'coordinator', text: 'Coordinator' },
                        );
                    }else if( this.current_user.role == 'area_admin' ){
                        this.roleOptions.push(
                            { value: 'coordinator', text: 'Coordinator' },
                        );
                    }
                    this.isLoading = false;
                    this.fetchOptionsData();
                }).catch(err => {
                    this.isLoading = false;
                });
            },
            fetchOptionsData() {
                this.isLoading = true;
                axios.get('/api/areas').then(response => {
                    this.areaOptions = response.data;
                    if( this.current_user.role == 'super_admin' || this.current_user.role == 'area_admin' ){
                        axios.get('/api/users', {
                            params: {
                                role: 'coordinator'
                            }
                        }).then(response => {
                            this.coordinatorOptions = response.data;
                            axios.get('/api/outlets').then(response => {
                                this.outletOptions = response.data;
                            });
                        });
                    }else{ //coordinator
                        axios.get('/api/outlets').then(response => {
                            this.outletOptions = response.data;
                        });
                        this.user.coordinator = this.current_user.id;
                    }
                    this.isLoading = false;
                }).catch(err => {
                    this.isLoading = false;
                });
            },
            save(){
                let url = '/api/user/add';
                let method = 'POST';
                if( this.id ){
                    url = '/api/user/' + this.id;
                    method = 'PATCH';
                } 
                this.isLoading = true;
                this.user.dob = moment(this.user.dob).isValid() ? moment(this.user.dob).format('MM/DD/YYYY') : '';
                axios({ 
                    method: method, url: url, data: this.user,
                }).then(response => {
                    if(this.image){
                        let data = new FormData();
                        data.append('image', this.image);
                        data.append('user', response.data.id);
                        axios.post('/api/user/add-image', data).then(resp => {
                            console.log(resp.data);
                        });
                    }
                    this.$swal({ title: 'Success!', text: 'User successfully saved.', type: 'success' })
                    .then((result) => {  
                        if( method == 'POST' )
                            this.$router.push({ name: 'edit-user', params: { id: response.data.id } });

                        // window.location.reload(); 
                        this.isLoading = false;
                    });
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    this.errors = [].concat.apply([], messages);
                    this.$swal({ title: 'Error!', text: this.errors[0], type: 'error' });
                    this.isLoading = false;
                });
            },
            previewFiles(event){
                this.image = event.target.files[0];
            }
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
