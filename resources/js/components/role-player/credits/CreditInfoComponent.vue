<template>
    <div class="main-content">
        <div id="all-transasctions" class="px-1 py-1">
            <loading :active.sync="isLoading"></loading>
            <!--Extended Table starts-->
            <div class="row">
                <div class="col-md-6 col-sm-8">
                    <h4>Credit Requests</h4>
                    <div class="alert alert-success mb-0">
                        <h5 class="mb-0">Current Balance: <strong>{{credit_balance | currency('&#8369;')}}</strong></h5>
                    </div>
                </div>
                <div class="col-md-6 col-sm-4 mt-2">
                    <a class="btn btn-outline-primary pull-right remove-pull-sm mb-0" href="/credit-balance/topup">
                        <i class="fa fa-plus"></i> Topup
                    </a>
                </div>
            </div>
            <section id="extended">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="clearfix" v-if="credit_history">
                                    <vue-good-table 
                                        mode="remote"
                                        :totalRows="totalRecords"
                                        :columns="columns" 
                                        :rows="credit_history"
                                        :sort-options="{ enabled: false }"
                                        :pagination-options="paginationOptions"
                                        @on-page-change="onPageChange"
                                        @on-per-page-change="onPerPageChange"
                                    >
                                        <template slot="table-row" slot-scope="props">
                                            <span v-if="props.column.field == 'status'">
                                                <button type="button" class="btn btn-sm" :class="`btn-${getStatusClass(props.row.status)}`">{{props.row.status}}</button>
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
    </div>      
</template>

<script>
import Loading from 'vue-loading-overlay';
import 'vue-good-table/dist/vue-good-table.css'
import { VueGoodTable } from 'vue-good-table';

export default {
    data() {
        return{
            isLoading: false,
            paginationOptions: {
                enabled: true,
                dropdownAllowAll: false,
                position: 'top'
            },
            columns: [
                { label: 'Date/Time', type: 'date', dateInputFormat: 'yyyy-MM-dd HH:mm:ss', dateOutputFormat: 'MMM dd, yyyy h:mma', field: 'created_at' },
                { label: 'Type', field: 'type' },
                { label: 'Outlet', field: 'outlet' },
                { label: 'Amount', field: 'amount' },
                { label: 'Status', field: 'status' },
                { label: 'Ref', field: 'ref' },
                { label: 'Notes', field: 'description' },
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
            credit_history: [],
            player: null,
            credit_balance: 0,
        }
    },
    components: { Loading, VueGoodTable },
    created(){
        this.init();
    },
    methods: {
        init(){
            this.player = this.$getUser();
            this.serverParams.filter.player = this.player.id;
            this.getCreditBalance();
            this.fetchData();
        },
        async fetchData(){
            this.isLoading = true
            await axios.get('/api/credits/requests', {
                    params: this.serverParams
                }).then(response => {
                this.credit_history = response.data.data;
            }).catch(error => {
                Vue.toasted.error('Something went wrong. Please try again');
            }).then(() => { this.isLoading = false })
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
        // helpers
        resetFilter(){
            this.serverParams.filter = {
                trans_type: '',
                player: this.player.id,
                ref: '',
                status: ''
            }
            this.fetchData();
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
        getCreditBalance(){
            axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                this.credit_balance = response.data.credit_balance
            });
        },
    }
}
</script>

<style>
    #all-transasctions .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>