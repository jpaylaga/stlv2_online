<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="float-left row">
                                    <h3>All Players</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <vue-good-table 
                                    :columns="columns" 
                                    :rows="players"
                                    :search-options="searchOptions"
                                    :pagination-options="paginationOptions"
                                >
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'actions'">
                                            <button class="btn btn-sm btn-primary" @click="$router.push({name:'update-player',params:{playerId: props.row.id}})">
                                                <i class="fa fa-edit"></i> Update
                                            </button>
                                            <button class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> Convert to Agent</button>
                                        </span>
                                    </template>
                                </vue-good-table>
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

import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';
    
    export default {
        data () {
            return {
                players: [],
                search_name: '',
                isLoading: false,
                searchOptions: {
                    enabled: true
                },
                paginationOptions: {
                    enabled: true,
                    position: 'top'
                },
                columns: [
                    { label: 'ID', html: true, field: this.getIdStatus },
                    { label: 'Details', html: true, field: this.getDetailsHhtml },
                    { label: 'Coordinator', html: true, field: this.getDirect },
                    { label: 'Current Points', field: 'credits' },
                    { label: 'Date Registered', type: 'date', dateInputFormat: 'yyyy-MM-dd HH:mm:ss', dateOutputFormat: 'MMM dd, yyyy', field: 'created_at' },
                    { label: 'Actions', field: 'actions', sortable: false },
                ]
            }
        },
        created () {
            this.fetchData()
        },
        components: {
            Loading, Switches, 'user-profile-popup': UserProfilePopup, VueGoodTable
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                axios.get('/api/users', {
                    params: { role: 'player', name: this.search_name }
                }).then(response => {
                    this.players = response.data;
                    this.isLoading = false;
                }); 
            },
            getIdStatus(item){
                return `<div class="row"> 
                    <div class="col-md-4">${item.id}</div>
                    <div class="col-md-8">
                        <span class="badge badge-${(item.is_active ? 'success' : 'danger')}">${item.is_active ? 'Active' : 'Inactive' }</span>
                    </div>
                </div>`
            },
            getDirect(item){
                if( item.parent.length > 0 )
                    return `${item.parent[0].firstname} ${item.parent[0].lastname}`
                else
                    return `<span class="badge badge-danger">Registration Unconfirmed</span>`
            },
            getDetailsHhtml(item){ 
                return `<div class="row">
                    <div class="col-md-6"><i class="fa fa-user-circle"></i> <strong>${item.username}</strong></div>
                    <div class="col-md-6"><i class="fa fa-address-card"></i> ${item.firstname} ${item.lastname}</div>
                    <div class="col-md-6"><i class="fa fa-phone-square"></i> ${item.phone}</div>
                    <div class="col-md-6"><i class="fa fa-facebook-square"></i> ${item.facebook || 'n/a'}</div>
                </div>`;
            },
            showUpdate(item){
                // console.log(item);
                this.showModal = true;
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