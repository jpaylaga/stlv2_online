<template>
    <div id="agents-list">
        <loading :active.sync="isLoading"></loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header" v-if="sales_supervisor">
                    {{sales_supervisor.firstname+' '+sales_supervisor.lastname}}: Agents List
                </div>
                <p class="content-sub-header" v-if="agents">Total Agents: {{agents.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li v-if="coordinator">
                            <a @click="$router.go(-1)" class="py-1 h6">
                                <i class="ft-arrow-left font-medium-5 mr-2"></i><span>Back</span>
                            </a>
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
                                        <input 
                                            type="text" 
                                            class="form-control search-filter" 
                                            v-model="search_name" 
                                            placeholder="Search by name">
                                        <button class="btn btn-outline-primary round">
                                            <i class="ft-search mr-2"></i>Search
                                        </button>
                                    </form>
                                </div>
                                <div class="float-right">
                                    <button @click="print" class="btn btn-outline-primary round">
                                        <i class="ft-printer mr-2"></i>Print
                                    </button>
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
                                            <th>Email</th>
                                            <th>Contact Number</th>
                                            <th>Username</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="agent in agents" :key="agent.id"> 
                                            <td> 
                                                <img class="profile-image-thumb" v-if="agent.picture" :src="`/storage/profile_images/${agent.picture}`" alt=""> 
                                                <img class="profile-image-thumb" v-else :src="`/images/user-default.png`" alt="">
                                            </td>
                                            <td>
                                                <user-profile-popup :user="agent">
                                                </user-profile-popup>
                                            </td>
                                            <td>{{agent.email}}</td>
                                            <td>{{agent.phone}}</td>
                                            <td>{{agent.username}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRINT -->
        <div id="printMe" v-if="agents" v-show="false">
            <print-agents-list 
                :coordinator="sales_supervisor" 
                :date="date"
                :agents="agents"
                ref="PrintSummaryReports">
            </print-agents-list>
        </div>
        <!-- PRINT -->

    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import AgentsList from '../print/AgentsList.vue';
    import UserProfilePopup from './UserProfilePopup.vue';
    import moment from 'moment';

    export default {
        props: ['coordinator'],
        data () {
            return {
                isLoading: false,
                agents: null,
                sales_supervisor: null,
                search_name: '',
                date: moment(new Date())
            }
        },
        created () {
            this.getCoordinator();
        },
        components: { Loading, 'print-agents-list': AgentsList, 'user-profile-popup': UserProfilePopup },
        methods: {
            getCoordinator(){
                this.isLoading = true;
                axios.get('/api/user/' + this.coordinator).then(response => {
                    this.sales_supervisor = response.data;
                    this.fetchData();
                });
            },
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users',{
                    params: { 
                        user: this.coordinator,
                        name: this.search_name
                    }
                }).then(response => {
                    this.agents = response.data;
                    this.isLoading = false;
                });
            },
            print() {
                this.$htmlToPaper('printMe', () => {
                    console.log('Printing done or got cancelled!');
                });
            }
        }
    }
</script>

<style>
    #agents-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>