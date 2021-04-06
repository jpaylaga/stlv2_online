<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mt-0">
                        <div class="card-header p-2">
                            <h3>Credit Requests</h3>
                            <div class="mb-2" v-if="!$is('super_admin')">
                                <div class="alert alert-info">
                                    <h5 class="mb-0">Current Balance: <strong>{{credit_balance | currency('&#8369;')}}</strong></h5>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-2 mb-2">
                                    <label class="mb-0">Transaction Type</label>
                                    <select v-model="serverParams.filter.trans_type" class="form-control">
                                        <option value="">All</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="withdraw">Withdraw</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="mb-0">Member</label>
                                    <model-select 
                                        :list="playerOptions"
                                        option-value="id"
                                        :custom-text="fullName"
                                        v-model="serverParams.filter.player"
                                        placeholder="Select Member">
                                    </model-select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="mb-0">Ref #</label>
                                    <input v-model="serverParams.filter.ref" type="text" class="form-control">
                                </div>
                                <div class="col-md-2 mb-0">
                                    <label class="mb-0">Status</label>
                                    <select v-model="serverParams.filter.status" class="form-control">
                                        <option value="">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="done">Done</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-0">&nbsp;</label>
                                    <div class="display-block">
                                        <button @click="fetchData" class="mb-0 btn btn-info">
                                            <i class="ft-search mr-2"></i>Search
                                        </button>
                                        <button @click="resetFilter" class="mb-0 btn btn-danger">
                                            <i class="ft-x mr-2"></i>Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block px-2">
                                <vue-good-table 
                                    mode="remote"
                                    :totalRows="totalRecords"
                                    :columns="columns" 
                                    :rows="requests"
                                    :sort-options="{ enabled: false }"
                                    :pagination-options="paginationOptions"
                                    @on-page-change="onPageChange"
                                    @on-per-page-change="onPerPageChange"
                                >
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'status'">
                                            <button v-if="props.row.status == 'pending'" @click="updateStatus(props.row)" class="btn btn-sm" :class="`btn-${getStatusClass(props.row.status)}`">{{props.row.status}}</button>
                                            <button v-else class="btn btn-sm" :class="`btn-${getStatusClass(props.row.status)}`">{{props.row.status}}</button>
                                        </span>
                                        <span v-else-if="props.column.field == 'outlet'">
                                            <span v-if="props.row.outlet == 'manual'">Manual</span>
                                            <img v-else class="rounded" width="40" :src="`/images/outlets/${$getOutletOptions(props.row.outlet,true)}`" alt="">
                                        </span>
                                        <span v-else-if="props.column.field == 'type'">
                                            <span v-if="props.row.type == 'withdraw'" class="text-danger">
                                                <i class="fa fa-upload"></i> {{props.row.type}}
                                            </span>
                                            <span v-else class="text-primary">
                                                <i class="fa fa-download"></i> {{props.row.type}}
                                            </span>
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

    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';
    
    import {ModelListSelect} from 'vue-search-select';
    import 'vue-search-select/dist/VueSearchSelect.css';

    export default {
        components: {
            Loading, Switches, VueGoodTable, 'model-select': ModelListSelect
        },
        data () {
            return {
                isLoading: false,
                playerOptions: [],
                paginationOptions: {
                    enabled: true,
                    dropdownAllowAll: false,
                    position: 'top'
                },
                columns: [
                    { label: 'Ref', field: 'ref' },
                    { label: 'Date/Time', type: 'date', dateInputFormat: 'yyyy-MM-dd HH:mm:ss', dateOutputFormat: 'MMM dd, yyyy h:mma', field: 'created_at' },
                    { label: 'Type', field: 'type' },
                    { label: 'Via', field: 'outlet' },
                    { label: 'Amount', field: 'amount' },
                    { label: 'Requester', html: true, field: this.getMember },
                    // { label: 'Fulfilled By', html: true, field: this.getAgent },
                    { label: 'Status', field: 'status' },
                    { label: 'Notes', field: 'description', width: '180px', },
                ],
                requests: [],
                totalRecords: 0,
                serverParams: {
                    page: 1, 
                    per_page: 10,
                    filter: {
                        trans_type: '',
                        player: 0,
                        ref: '',
                        status: ''
                    }
                },
                current_user: null,
                credit_balance: 0,
            }
        },
        created () {
            this.init();
            this.fetchData(()=>{});
            this.fetchPlayers(()=>{});
        },
        methods: {
            init(){
                this.current_user = this.$getUser();
                this.getCreditBalance();
            },
            getCreditBalance(){
                axios.get('/api/user/credit-balance/' + this.current_user.id).then(response => {
                    this.credit_balance = response.data.credit_balance
                });
            },
            fetchData () {
                const self = this
                self.isLoading = true;
                axios.get('/api/credits/requests', {
                    params: self.serverParams
                }).then(response => {
                    self.requests = response.data.data
                    self.totalRecords = response.data.total
                    self.isLoading = false;
                }); 
            },
            fetchPlayers(){
                const self = this
                self.isLoading = true;
                axios.get('/api/users', {
                    params: { role: 'player' }
                }).then(response => {
                    self.playerOptions = response.data;
                    self.isLoading = false;
                }); 
            },
            getMember(item){
                if( this.$isOwn(item.player_id) )
                    return `${item.player.firstname} ${item.player.lastname}`;
                return `<a href="/manage-players/update/${item.player.id}">${item.player.firstname} ${item.player.lastname}</a>`;
            },
            getAgent(item){
                return `${item.agent.firstname} ${item.agent.lastname}`;
            },
            getStatusClass(status){
                switch (status) {
                    case 'done': return 'success'; break;
                    case 'cancelled': return 'danger'; break;
                    default: return 'warning'; break;
                }
            },
            fullName(user) {
                return `${user.firstname} ${user.lastname}`;
            },
            updateStatus(item){
                if( this.$isOwn(item.player_id) ) return; // if own

                const self = this

                this.$swal({
                    title: '<h4>Update Request</h4>',
                    html: `
                        <label>Status</label>
                        <select id="swal-input-status" class="swal2-input">
                        <option value="done" ${(item.status == 'done' ? 'selected="selected"' : '')}>Done</option>
                        <option value="pending" ${(item.status == 'pending' ? 'selected="selected"' : '')}>Pending</option>
                        <option value="cancelled" ${(item.status == 'cancelled' ? 'selected="selected"' : '')}>Cancelled</option>
                        </select>
                        <label>Notes</label>
                        <textarea id="swal-input-notes" class="swal2-input">${item.description || ''}</textarea>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return {
                            status: document.getElementById('swal-input-status').value,
                            notes: document.getElementById('swal-input-notes').value
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value) {
                            let params = {
                                status: result.value.status, 
                                notes : result.value.notes, 
                            }
                            self.isLoading = true;
                            axios.patch('/api/credits/requests/update-status/'+item.id, params)
                            .then(response => {
                                if( response.data.success ){
                                    self.$swal({ title: 'Success!', text: 'Credit Request successfully udpated.', type: 'success' });
                                    this.fetchData();
                                    this.getCreditBalance();
                                }else{
                                    self.$swal({ title: 'Oops!', text: response.data.message, type: 'error' });
                                } 
                                self.isLoading = false;
                            });
                        }
                    })

                 
            },
            // table handlers
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.fetchData();
            },
            onPerPageChange(params) {
                this.updateParams({per_page: params.currentPerPage});
                this.fetchData();
            },
            resetFilter(){
                this.serverParams.filter = {
                    trans_type: '',
                    player: 0,
                    ref: '',
                    status: ''
                }
                this.fetchData();
            }
        }
    }
</script>

<style>
    /* #coordinators-list .card-header{ padding-bottom: 0; } */
    /* .filter{ margin: 0; } */
    /* .filter label{ margin: 0; font-size: 14px; } */
    /* .search-filter{ margin: 0 15px 16px; } */
</style>