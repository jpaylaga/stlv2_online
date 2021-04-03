<template>
    <div id="outlets-list">
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <div class="row">
            <div class="col-md-12 mt-1 mb-1">
                <div class="content-header">Outlets</div>
                <p class="content-sub-header">Total: {{outlets.length}}</p>
                <nav id="top-right-text">
                    <ul>
                        <li>
                            <router-link class="py-1 h6" :to="{name: 'add-outlet'}">
                                <i class="icon-user-follow font-medium-5 mr-2"></i>New Outlet
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
                            <!-- <div class="form-group filter">
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
                            </div> -->
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Zip</th>
                                            <th>Area</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="outlet in outlets" :key="outlet.id"> 
                                            <td>{{outlet.name}}</td>
                                            <td>{{outlet.street+' '+outlet.barangay}}</td>
                                            <td>{{outlet.city}}</td>
                                            <td>{{outlet.zipcode}}</td>
                                            <td>{{outlet.area.name}}</td>
                                            <td>
                                                <router-link :to="{name: 'edit', params: {id:outlet.id}}" class="btn btn-flat btn-success m-0 px-1">
                                                    <i class="ft-edit font-medium-3"></i>
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
    
    export default {
        data () {
            return {
                isLoading: false,
                outlets: [],
                search_name: '',
            }
        },
        created () {
            this.fetchData()
        },
        components: { Loading },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/outlets', {
                    params: {}
                }).then(response => {
                    this.outlets = response.data;
                    this.isLoading = false;
                }); 
            },
        }
    }
</script>

<style>
    #outlets-list .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .search-filter{ margin: 0 15px 16px; }
</style>