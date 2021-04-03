<template>
    <div id="coordinators-list">
        <loading :active.sync="isLoading">
        </loading>
        <section id="extended">
            <div class="row mt-2">
                <div class="col-md-6 col-sm-8">
                    <h3>Credit Requests References</h3>
                </div>
                <div class="col-md-6 col-sm-4">
                    <router-link class="btn btn-outline-primary pull-right remove-pull-sm mb-0" :to="{name: 'add-reference'}">
                        <i class="fa fa-plus"></i> Add Reference
                    </router-link>
                    <!-- <a class="btn btn-outline-primary pull-right remove-pull-sm mb-0" href="/credit-balance/topup">
                        <i class="fa fa-plus"></i> Add Reference
                    </a> -->
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- <h3>Credit Requests References</h3> -->
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <label>Outlet</label>
                                    <select v-model="serverParams.filter.outlet" class="form-control">
                                        <option value="">All</option>
                                        <option value="gcash">GCash</option>
                                        <option value="ml">MLhuillier</option>
                                        <option value="cebuana">Cebuana</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Ref #</label>
                                    <input v-model="serverParams.filter.ref_number" type="text" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Name</label>
                                    <input v-model="serverParams.filter.name" type="text" class="form-control">
                                </div>
                                <div class="col-md-2 filter-buttons">
                                    <button @click="fetchData" class="btn btn-outline-primary round">
                                        <i class="ft-search mr-2"></i>Search
                                    </button>
                                    <button @click="resetFilter" class="btn btn-outline-danger round">
                                        <i class="ft-x mr-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <vue-good-table 
                                    mode="remote"
                                    :totalRows="totalRecords"
                                    :columns="columns" 
                                    :rows="references"
                                    :sort-options="{ enabled: false }"
                                    :pagination-options="paginationOptions"
                                    @on-page-change="onPageChange"
                                    @on-per-page-change="onPerPageChange"
                                >
                                    <template slot="table-row" slot-scope="props">
                                        <span v-if="props.column.field == 'actions'">
                                            <button class="btn btn-sm btn-primary" @click="$router.push({name:'edit-reference',params:{id: props.row.id}})">
                                                <i class="fa fa-edit"></i> Update
                                            </button>
                                        </span>
                                        <span v-else-if="props.column.field == 'outlet'">
                                            <img class="rounded" width="40" :src="`/images/outlets/${$getOutletOptions(props.row.outlet,true)}`" alt="">
                                        </span>
                                        <span v-else-if="props.column.field == 'is_active'">
                                            <span class="pull-left" @click.prevent="changeReferenceStatus(props.row.id, props.row.is_active)">
                                                <switches 
                                                    v-model="props.row.is_active"
                                                    theme="custom"
                                                    color="blue"
                                                    type-bold="true"
                                                ></switches>  
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
                paginationOptions: {
                    enabled: true,
                    dropdownAllowAll: false,
                    position: 'top'
                },
                columns: [
                    { label: 'Name', field: 'name' },
                    { label: 'Outlet', field: 'outlet'  },
                    { label: 'Ref #', field: 'ref_number' },
                    { label: 'Mobile #', field: 'mobile_number' },
                    { label: 'Notes', field: 'notes', width: '180px', },
                    { label: 'Status', field: 'is_active' },
                    { label: 'Actions', field: 'actions', sortable: false },
                ],
                references: [],
                totalRecords: 0,
                serverParams: {
                    page: 1, 
                    per_page: 10,
                    filter: {
                        name: '',
                        outlet: '',
                        mobile_number: '',
                        ref_number: '',
                    }
                }
            }
        },
        created () {
            this.fetchData(()=>{});
        },
        methods: {
            fetchData () {
                const self = this
                self.isLoading = true;
                axios.get('/api/credits/references', {
                    params: self.serverParams
                }).then(response => {
                    self.references = response.data.data
                    self.totalRecords = response.data.total
                }).catch(error => {
                    console.log(error);
                }).then(() => { this.isLoading = false })
            },
            updateStatus(item){
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
                                }else{
                                    self.$swal({ title: 'Oops!', text: response.data.message, type: 'error' });
                                } 
                                self.isLoading = false;
                            });
                        }
                    })

                 
            },
            getOutlet(item){
                let outlet = this.$getOutletOptions(item.outlet);
                return `<span class="badge badge-success">${outlet}</span>`;
            },
            changeReferenceStatus(ref_id, ref_status){
                let action = ref_status ? 'deactivate' : 'activate';
                let action_val = ref_status ? false : true;
                this.$swal({
                    title: 'Update Reference Status',
                    text: "Are you sure to " + action + ' this reference?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        this.isLoading = true
                        axios.put('/api/credits/reference/' + ref_id, { is_active: action_val }).then(resp => {
                            if( resp.data.success ){
                                this.$swal({
                                    'title': 'Success!',
                                    'text': 'Reference ' + action + 'd!',
                                    'type': 'success'
                                }).then((result) => { 
                                    this.fetchData();
                                });
                            }else{
                                Vue.toasted.error(resp.data.message);
                            }
                        }).catch(error => {
                            Vue.toasted.error('Something went wrong. Please contact admin.');
                        }).then(() => { this.isLoading = false })
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
                    name: '',
                    outlet: '',
                    mobile_number: '',
                    ref_number: '',
                }
                this.fetchData();
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