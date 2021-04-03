<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="credits">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">Manage Credits</div>  
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h4>User Credits</h4>
                                    <p>Total: {{getTotalCredits() | currency('&#8369;')}}</p>
                                </div>
                                <div class="col-md-6">
                                     <div class="form-group pull-right">
                                        <select id="role" v-model="role" class="form-control" @change="fetchData">
                                            <option value="">All</option>
                                            <option value="player">Players/Bettors</option>
                                            <option value="teller">Tellers</option>
                                            <option value="coordinator">Coordinators</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table text-left">
                                    <thead>
                                        <tr>
                                            <th style="width: 150px;">Name</th>
                                            <th>Credits</th>
                                            <th>Activate</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users" :key="user.id">
                                            <td>
                                                <span data-toggle="tooltip" data-placement="top" :title="user.id" data-trigger="hover">
                                                    {{user.firstname+' '+user.lastname}}
                                                </span>
                                            </td>
                                            <td>{{user.credits | currency('&#8369;')}}</td>
                                            <td>
                                                <span class="pull-left" @click.prevent="changeCreditStatus(user)">
                                                    <switches 
                                                        v-model="user.activate_credit"
                                                        theme="custom"
                                                        color="blue"
                                                        type-bold="true"
                                                    ></switches>  
                                                </span>
                                            </td>
                                            <td>
                                                <a @click.prevent="addCredits(user)" href="#" class="btn btn-flat btn-success m-0 px-1">
                                                    <i class="ft-plus-circle"></i> Add
                                                </a>
                                                <a @click.prevent="transferCredits(user)" href="#" class="btn btn-flat btn-success m-0 px-1">
                                                    <i class="ft-repeat"></i> Transfer
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- @end col -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-6"> 
                                        <h4>History</h4>
                                    </div>
                                    <div class="col-md-6"> 
                                        <div class="form-group pull-right">
                                            <select id="history_type" v-model="history_type" class="form-control" @change="fetchHistory">
                                                <option value="">All</option>
                                                <option value="add">Add</option>
                                                <option value="transfer">Transfer</option>
                                                <option value="refund">Refund</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table text-left">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Date/Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="history in histories" :key="history.id">
                                            <td><span class="added">{{history.from}}</span>: <span class="amount strong">{{history.amount | currency('&#8369;', 0)}}</span> {{history.type}}ed to <span class="added">{{history.to}}</span></td>
                                            <td>{{history.created_at | date_time}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- @end col -->
            </div>
        </section>
        <!--Extended Table Ends-->   
    </div>                
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import moment from 'moment';
    import Switches from 'vue-switches';

    export default {
        data () {
            return {
                isLoading: false,
                users: [],
                histories: [],
                history_type: '',
                role: ''
            }
        },
        created () {
            this.fetchData();
        },
        components: {
            Loading, Switches
        },
        methods: {
            fetchData() {
                this.isLoading = true;
                axios.get('/api/credits', {
                    params: { role: this.role }
                })
                .then(response => {
                    this.users = _.orderBy(response.data, 'role', 'desc');
                    this.fetchHistory();
                    this.isLoading = false;
                });
            },
            fetchHistory(){
                axios.get('/api/credits/history', {
                    params: { type: this.history_type }
                })
                .then(response => {
                    this.histories = _.orderBy(response.data, 'created_at', 'desc');
                });
            },
            getTotalCredits(){
                return _.sumBy(this.users, function(user) { 
                    return parseInt(user.credits); 
                }); 
            },
            changeCreditStatus(user){
                let action = user.activate_credit ? 'deactivate' : 'activate';
                this.$swal({
                    title: action,
                    text: "Are you sure to " + action + ' credit to this user?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        axios.post('/api/user/change-credit-status', {
                            user: user.id,
                            action: action
                        }).then(resp => {
                            if( resp.data.success ){
                                this.$swal({
                                    'title': 'Success!',
                                    'text': 'Credit ' + action + 'd!',
                                    'type': 'success'
                                }).then((result) => { 
                                    this.fetchData();
                                });
                            }
                        });
                    }
                })
            },
            addCredits(user){
                this.$swal({
                    title: '<p>Add credits for: '+user.firstname+' '+user.lastname+'</p>',
                    input: 'text',
                    // inputValue: ,
                    inputAttributes: {
                        autocapitalize: 'on',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    showLoaderOnConfirm: true,
                    preConfirm: (credits) => {
                        return axios.post('/api/credits/add', {
                            user: user.id, credits: credits
                        })
                        .then(response => {
                            if( response.data.success )
                                return response.data;
                            this.$swal.showValidationMessage('Success adding credits!');
                        }).catch(error => {
                            this.$swal.showValidationMessage(
                                `Request failed: ${'Please enter valid value'}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        this.$swal('Successfully Updated Percentage!', '', 'success');
                        this.fetchData();
                    }
                });
            },
            transferCredits(user){
                var users_clone = _.orderBy( _.clone(this.users, true), 'firstname' );
                var html = '<h5>From: '+user.firstname+' '+user.lastname+'</h5>';
                    html += '<h5>Balance: '+this.$options.filters.currency(user.credits, '&#8369;')+'</h5>';
                    html += '<div>' +
                        '<div class="form-group">' +
                            '<label for="amount">Amount</label>' +
                            '<input type="number" id="amount" class="form-control">' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label for="to">Transfer to</label>' +
                            '<select id="to" class="form-control">';
                        _.forEach( users_clone, function(user,key) {
                            html += '<option value="'+user.id+'">'+user.firstname+' '+user.lastname+'</option>' 
                        });
                    html += '</select>' +
                        '</div>' + 
                        '</div>';

                this.$swal({
                    title: '<p>Transfer Credits</p>',
                    html: html, 
                    showCancelButton: true,
                    focusConfirm: false,
                    preConfirm: () => {
                        let amount = document.getElementById('amount').value;
                        let transfer_to = document.getElementById('to').value;
                        return axios.post('/api/credits/transfer', {
                            from: user.id, to: transfer_to, amount: amount
                        })
                        .then(response => {
                            if( response.data.success ){
                                return response.data;
                            }else{
                                this.$swal.showValidationMessage('Request failed: '+response.data.message);
                            }
                        }).catch(error => {
                            this.$swal.showValidationMessage( `Request failed: ${'Please enter valid value'}` )
                        });
                    }
                }).then((result) => {
                    if (result.value) {
                        this.$swal('Success transfering credits!', '', 'success');
                        this.fetchData();
                    }
                });
            }
        },
    }
</script>

<style>
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
</style>