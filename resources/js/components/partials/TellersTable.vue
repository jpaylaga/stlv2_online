<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Agents/Tellers</div>
                <p class="content-sub-header">Total: {{tellers.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: 'add-teller'}">
                                <i class="icon-user-follow font-medium-5 mr-2"></i>New Teller
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="float-left row">
                                    <form class="form-inline" @submit.prevent="fetchData">
                                        <input type="text" class="form-control search-filter" 
                                            v-model="search_name" 
                                            placeholder="Search by name">
                                        <button class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>Search
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Outlet</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="teller in tellers" :key="teller.id"> 
                                            <td>{{teller.id}}</td>
                                            <td> 
                                                <img class="profile-image-thumb" v-if="teller.picture" :src="`/storage/profile_images/${teller.picture}`" alt=""> 
                                                <img class="profile-image-thumb" v-else :src="`/images/user-default.png`" alt="">
                                            </td>
                                            <td>
                                                <user-profile-popup 
                                                :user="teller"
                                                :edit="'/manage-tellers/edit/'"
                                                />
                                            </td>
                                            <td>{{teller.outlet}}</td>
                                            <!-- <span v-if="teller.outlets.length > 0">{{teller.outlets[0].name}}</span> -->
                                                <!-- <span v-else>N/A</span> -->
                                            <td>
                                                <span class="pull-left" @click.prevent="changeStatus(teller)">
                                                    <switches 
                                                        v-model="teller.is_active"
                                                        theme="custom"
                                                        color="blue"
                                                        type-bold="true"
                                                    ></switches>  
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Extended Table Ends-->   
    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import Switches from 'vue-switches';
    import UserProfilePopup from './UserProfilePopup.vue';
    
    export default {
        data () {
            return {
                tellers: [],
                search_name: '',
                isLoading: false,
            }
        },
        created () {
            this.fetchData()
        },
        components: {
            Loading, Switches, 'user-profile-popup': UserProfilePopup
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users', {
                    params: { role: 'teller', name: this.search_name }
                }).then(response => {
                    this.tellers = response.data;
                    this.isLoading = false;
                }); 
            },
            changeStatus(user){
                let action = user.is_active ? 'deactivate' : 'activate';
                this.$swal({
                    title: action,
                    text: "Are you sure to " + action + ' this user?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        axios.post('/api/user/change-status', {
                            user: user.id,
                            action: action
                        }).then(resp => {
                            if( resp.data.success ){
                                this.$swal({
                                    'title': 'Success!',
                                    'text': 'User ' + action + 'd!',
                                    'type': 'success'
                                }).then((result) => { 
                                    this.fetchData();
                                });
                            }
                        });
                    }
                })
            },
        }
    }
</script>

<style>
    #coordinators-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>