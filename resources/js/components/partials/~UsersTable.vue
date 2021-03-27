<template>
    <div id="users-list">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">System Users</div>
                <p class="content-sub-header">Total Users: {{users.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: 'add-user'}">
                                <i class="icon-user-follow font-medium-5 mr-2"></i>New User
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div v-if="loading" class="loading">
            Loading...
        </div>
        <section v-if="users" id="extended">
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

                                <!-- <p><code>query: {{ query }}</code></p>
                                <datatable v-bind="$data">
                                </datatable> -->

                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users" :key="user.id" :id="'user-' + user.id"> 
                                            <td>
                                                <img class="profile-image-thumb" v-if="user.picture" :src="`/storage/profile_images/${user.picture}`" alt="">
                                                <img class="profile-image-thumb" v-else :src="`/images/user-default.png`" alt="">
                                            </td>
                                            <td>
                                                <user-profile-popup :user="user">
                                                </user-profile-popup>
                                            </td>
                                            <td>{{user.email}}</td>
                                            <td>{{user.role | user_role}}</td>
                                            <td>
                                                <a href="#" class="btn btn-flat btn-success m-0 px-1" data-toggle="tooltip" data-placement="top" title="View User Log" data-trigger="hover"> <i class="ft-file-text font-medium-3"></i></a>
                                                <span @click.prevent="changeStatus(user)">
                                                    <switches 
                                                        v-model="user.is_active"
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
    // https://github.com/drewjbartlett/vue-switches
    import Switches from 'vue-switches';
    import UserProfilePopup from './UserProfilePopup.vue';

    export default {
        data () {
            return {
                isLoading: false,
                users: null,
                search_name: '',
                // data: null,
                // columns: [
                //     { title: 'User ID', field: 'id_number', sortable: true },
                //     { title: 'Name', field: 'firstname', sortable: true },
                //     { title: 'Email', field: 'email', sortable: true },
                //     { title: 'Role', field: 'role', sortable: true },
                // ],
                query: {},
                // total: 0
            }
        },
        created () {
            this.fetchData()
        },
        components: {
            Loading, Switches, 'user-profile-popup': UserProfilePopup
        },
        // watch: {
        //     query: {
        //         handler () { this.fetchData() },
        //         deep: true
        //     }
        // },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users', {
                    params: {
                        // query: this.query
                        name: this.search_name
                    }
                }).then(response => {
                    this.users = response.data;
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
    label.vue-switcher{ float: right; margin: 12px 0 0 0; }
    #users-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>
