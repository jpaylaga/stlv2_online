<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="highestBetReports">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">{{area.name}} Bet Limits</div>  
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Set Individual Bet Limit</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <div class="form-group">
                                    <div class="row" v-for="(input, index) in combinations" :key="index">
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" v-model.number="input.combi" placeholder="#"> 
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <select id="type" v-model="input.type" class="form-control" >
                                                <option value="straight">Straight</option>
                                                <option value="ramble">Ramble</option>
                                            </select>
                                        </div> -->
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" v-model="input.limit" placeholder="Limit"> 
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" v-model.number="input.winning" placeholder="Win"> 
                                        </div>
                                        <div class="col-md-1">
                                            <p style="color: #e3342f; cursor: pointer; padding-top: 9px;" @click="deleteRow(index)">
                                                <i class="ft-x"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-outline-info round" @click="addRow"><i class="ft-plus"></i> Add row</button>
                                <button @click="submit" class="btn btn-outline-primary round"><i class="ft-check-square"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </div> <!-- @end col -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6"> 
                                    <h4>Bet Limit List</h4>
                                </div>
                                <div class="col-md-6">
                                     <div class="form-group pull-right">
                                        <select id="area" @change="fetchData" v-model="area" class="form-control">
                                            <option v-for="area in areaOptions" :value="area" :key="area.id">
                                                {{area.name}}
                                            </option>
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
                                            <th>#</th>
                                            <th>Limit</th>
                                            <th>Winning</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="combi in combinations_list" :key="combi.id">
                                            <td>
                                                {{combi.combination}}
                                                <!-- <span class="drawtime" :class="combi.type=='straight'?'time-11':'time-16'">
                                                    {{combi.type=='straight'?'T':'R'}}
                                                </span> -->
                                            </td>
                                            <td>{{combi.limit | currency('&#8369;')}}</td>
                                            <td>
                                                <span v-if="!combi.winning">N/A</span>
                                                <span v-else>{{(combi.winning ? combi.winning : 0) | currency('&#8369;') }}</span>
                                            </td>
                                            <td>
                                                <span style="color: #4dc0b5; cursor: pointer;" @click="editCombi(combi)">
                                                    <i class="ft-edit"></i>
                                                </span>
                                                &nbsp;
                                                <span style="color: #e3342f; cursor: pointer;" @click="deleteCombi(combi)">
                                                    <i class="ft-trash-2"></i>
                                                </span>
                                            </td>
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

    export default {
        data () {
            return {
                isLoading: false,
                combinations: [
                    {
                        combi: '',
                        limit: '',
                        winning: '',
                    }
                ],
                combinations_list: [],
                area: '',
                areaOptions: [],
            }
        },
        created () {
            this.fetchOptionsData();
        },
        components: {
            Loading
        },
        methods: {
            addRow() {
                this.combinations.push({
                    combi: '',
                    limit: '',
                    winning: '',
                })
            },
            deleteRow(index) {
                this.combinations.splice(index,1)
            },
            resetRow() {
                this.combinations = [{
                    combi: '',
                    limit: '',
                    winning: '',
                }];
            },
            fetchOptionsData() {
                axios.get('/api/areas').then(response => {
                    this.areaOptions = response.data;
                    this.area = this.areaOptions[0];
                    this.fetchData();
                });
            },
            fetchData() {
                this.isLoading = true;
                axios.get('/api/reports/bet-limits', {
                    params: { area: this.area.id }
                })
                .then(response => {
                    this.combinations_list = _.orderBy(response.data, 'limit', 'desc');
                    this.resetRow();
                    this.isLoading = false;
                });
            },
            submit() {
                let draw_promises = [];
                let _this = this;
                _.forEach(this.combinations, function(item, key) {
                    console.log(item.combi, item.limit);
                    let params =  {
                        combination: item.combi, 
                        limit: item.limit, 
                        winning: item.winning, 
                        type: 'straight',
                        area: _this.area.id
                    };
                    draw_promises.push( 
                        axios.post('/api/betlimit/set', params).then( response => {
                            
                        })
                    );
                });

                axios.all(draw_promises)
                .then(axios.spread((response) => {
                    this.isLoading = false;
                }))
                .then( response => {
                    this.$swal('Success', 'Successfully Updated Bet Limit List!', 'success');
                    this.fetchData();
                    // redirect
                });
            },
            deleteCombi(combi){
                this.$swal({
                    type: 'warning',
                    html: 'Are you sure to remove this combination: ' + combi.combination, 
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        axios.post('/api/betlimit/remove', {
                            combi_id: combi.id
                        }).then( response => {
                            this.$swal('Success removing combination!', '', 'success');
                            this.fetchData();
                        });
                    }
                });
                
            }
        },
    }
</script>

<style>
    #highestBetReports .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>