<template>
    <div id="update-player">
        <loading :active.sync="isLoading"></loading>
        <div class="row">
            <div class="col-md-12">
                <h4 class="form-section-heading"><i class="ft-user"></i> Player Information</h4>
                <form class="form form-horizontal" @submit.prevent="save">
                    <div class="form-body">
                        <input type="hidden" v-model="player.role">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="username">Username</label>
                                    <input type="text" class="form-control" placeholder="Username" v-model="player.username" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="firstname">First Name</label>
                                    <input type="text" class="form-control" placeholder="First Name" v-model="player.firstname" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="lastname">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name" v-model="player.lastname" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="username">Email</label>
                                    <input type="text" class="form-control" placeholder="Username" v-model="player.email" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="facebook">Facebook</label>
                                    <input type="text" class="form-control" placeholder="Facebook" v-model="player.facebook">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="phone">Direct</label>
                                    <select id="direct" v-model="player.coordinator" class="form-control">
                                        <option value="">Select a Direct</option>
                                        <option v-for="coor in coordinatorOptions" :value="coor.id" :key="coor.id">{{coor.firstname}} {{coor.lastname}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="phone">Is Active</label>
                                    <span class="row clearfix ml-1">
                                        <switches 
                                            v-model="player.is_active"
                                            theme="custom"
                                            color="blue"
                                            type-bold="true"
                                        ></switches>  
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control" for="phone">Can Bet via Credit</label>
                                    <span class="row clearfix ml-1" @click="changeCreditStatus(player)">
                                        <switches 
                                            v-model="player.activate_credit"
                                            theme="custom"
                                            color="blue"
                                            type-bold="true"
                                        ></switches>  
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save </button>
                                <button type="button" class="btn btn-outline-danger round" @click="$router.push({path: '/manage-players/players'})">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <h4 class="form-section-heading"><i class="fa fa-rub"></i> Credit History</h4>
                <h5 class="pull-left"><strong>Current Credits: {{player.creditBalance | currency('&#8369;', 0)}} </strong></h5>
                <!-- <button @click="openModal" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Credits</button> -->
                <div class="clearfix">&nbsp;</div>
                <div class="clearfix" v-if="player.creditHistory">
                    <vue-good-table 
                        :columns="columns" 
                        :rows="player.creditHistory"
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

        <add-credit-modal 
            :user="selectedUser" 
            :show="showCreditPopup"
            @callbackClosePopup="callbackClosePopup"
        />
        
    </div>
</template>
<script>
    import Loading from 'vue-loading-overlay';
    import Switches from 'vue-switches';
    import AddCreditModal from '../credit/components/AddCreditsPopup.vue';
    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';

    export default {
        props: ['playerId'],
        components: {
            Loading, Switches, AddCreditModal, VueGoodTable
        },
        data() {
            return {
                player: [],
                coordinatorOptions: [],
                isLoading: false,
                selectedUser: parseInt( this.playerId ),
                showCreditPopup: false,
                searchOptions: {
                    enabled: false
                },
                paginationOptions: {
                    enabled: true,
                    rowsPerPageLabel: 'Rows',
                },
                columns: [
                    { label: 'Amount', field: 'amount' },
                    { label: 'Type', field: 'type' },
                    { label: 'By', field: 'by' },
                    { label: 'Date/Time', type: 'date', dateInputFormat: 'yyyy-MM-dd HH:mm:ss', dateOutputFormat: 'MMM dd, yyyy HH:mm:ss', field: 'created_at' },
                    { label: 'Notes', field: 'description' },
                ]
            }
        },
        created () {
            this.loadPlayer(()=>{});
            this.loadCoordinators(()=>{});
        },
        methods: {
            loadPlayer(){
                const self = this
                self.isLoading = true
                axios.get('/api/user/' + self.playerId).then(response => {
                    self.player = response.data;
                    self.isLoading = false
                }); 
            },
            loadCoordinators(){
                const self = this
                axios.get('/api/users', {
                    params: { role: 'coordinator' }
                }).then(response => {
                    self.coordinatorOptions = response.data;
                });
            },
            save(){
                const self = this

                if( self.player.coordinator === null || typeof self.player.coordinator == 'undefined' ){
                    self.$swal({ title: 'Error!', text: 'Please Select Coordinator', type: 'error' });
                    return false
                }

                let params = {
                    username: self.player.username,
                    lastname: self.player.lastname,
                    firstname: self.player.firstname,
                    email: self.player.email,
                    facebook: self.player.facebook,
                    is_active: self.player.is_active,
                    coordinator: self.player.coordinator,
                    role: self.player.role,
                    deleted: false
                }

                self.isLoading = true
                axios.patch('/api/user/'+self.playerId, params).then(response => {
                    self.$swal({ title: 'Success!', text: 'Player successfully udpated.', type: 'success' });
                    self.isLoading = false
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    let errors = [].concat.apply([], messages);
                    self.$swal({ title: 'Error!', text: errors, type: 'error' });
                    self.isLoading = false;
                });
            },
            changeCreditStatus(player){
                const self = this
                let action = self.player.activate_credit ? 'deactivate' : 'activate';
                self.isLoading = true
                axios.post('/api/user/change-credit-status', {
                    user: self.playerId,
                    action: action
                }).then(resp => {
                    if( resp.data.success ){
                        self.$swal({ title: 'Success!', text: 'Player credit status successfully udpated.', type: 'success' });
                        self.isLoading = false
                    }
                });
            },
            isDeposit(type){
                if(type=='add' || type=='transfer' || type=='deposit')
                    return true
                return false
            },
            openModal () {
                this.showCreditPopup = true;
            },
            callbackClosePopup() {
                this.loadPlayer();
                this.$vm2.close('modalAddCredit')
                this.showCreditPopup = false;
            },
        }
    }
</script>
