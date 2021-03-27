<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Area Admins</div>
                <p class="content-sub-header">Total: {{users.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: 'add-area-admin'}">
                                <i class="icon-user-follow font-medium-5 mr-2"></i>New Area Admin
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
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Area</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users" :key="user.id"> 
                                            <td> 
                                                <img class="profile-image-thumb" v-if="user.picture" :src="`/storage/profile_images/${user.picture}`" alt=""> 
                                                <img class="profile-image-thumb" v-else :src="`/images/user-default.png`" alt="">
                                            </td>
                                            <td>
                                                <user-profile-popup 
                                                :user="user"
                                                :edit="'/manage-area-admins/edit/'"
                                                />
                                            </td>
                                            <td>
                                                <span v-if="user.areas.length > 0">{{user.areas[0].name}}</span>
                                                <span v-else>N/A</span>
                                            </td>
                                            <td>
                                                <user-status :user="user" />
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
    import UserStatus from '../helpers/UserStatus.vue';
    
    export default {
        data () {
            return {
                users: [],
                search_name: '',
                isLoading: false,
            }
        },
        created () {
            this.fetchData()
        },
        components: {
            Loading, Switches, 'user-profile-popup': UserProfilePopup, 'user-status': UserStatus
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users', {
                    params: { role: 'area_admin', name: this.search_name }
                }).then(response => {
                    this.users = response.data;
                    this.isLoading = false;
                }); 
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