<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Coordinators</div>
                <p class="content-sub-header">Total: {{coordinators.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: 'add-coordinator'}">
                                <i class="icon-user-follow font-medium-5 mr-2"></i>New Coordinator
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
                                            <!-- <th>Image</th> -->
                                            <th>Name</th>
                                            <th>Area/Station</th>
                                            <th>Commission</th>
                                            <th>Float</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="coordinator in coordinators" :key="coordinator.id"> 
                                            <!-- <td> 
                                                <img class="profile-image-thumb" v-if="coordinator.picture" :src="`/storage/profile_images/${coordinator.picture}`" alt=""> 
                                                <img class="profile-image-thumb" v-else :src="`/images/user-default.png`" alt="">
                                            </td> -->
                                            <td>
                                                <user-profile-popup 
                                                :user="coordinator"
                                                :edit="'/manage-coordinators/edit/'"
                                                />
                                            </td>
                                            <td>{{coordinator.areas[0].name}}</td>
                                            <td>{{coordinator.percentage}}%</td>
                                            <td>
                                                <floating-popup
                                                    :coor="coordinator"
                                                    :show_cancel="false"
                                                />
                                            </td>
                                            <td>{{coordinator.username}}</td>
                                            <td>
                                                <user-status :user="coordinator" />
                                            </td>
                                            <td>
                                                <router-link 
                                                    :to="{ name: 'view-agent-list', params: { coordinator:coordinator.id }}" 
                                                    class="btn btn-sm btn-primary m-0 px-1">
                                                    Agent List
                                                </router-link>
                                                <router-link 
                                                    :to="{ name: 'edit-user', params: { id:coordinator.id }}" 
                                                    class="btn btn-sm btn-success m-0 px-1">
                                                    Update
                                                </router-link>
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
    import UserProfilePopup from './UserProfilePopup.vue';
    import UserStatus from '../helpers/UserStatus.vue';
    import FloatingPopup from '../single-popup/UpdateFloat.vue';
    
    export default {
        data () {
            return {
                coordinators: [],
                search_name: '',
                isLoading: false,
            }
        },
        created () {
            this.fetchData()
        },
        components: {
            Loading, 'user-profile-popup': UserProfilePopup, 'user-status': UserStatus, 'floating-popup': FloatingPopup,
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users', {
                    params: { role: 'coordinator', name: this.search_name }
                }).then(response => {
                    this.coordinators = response.data;
                    this.isLoading = false;
                }); 
            }
        }
    }
</script>

<style>
    #coordinators-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>