<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="highestBetReports">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">Hot Numbers</div>  
                    <!-- <nav id="top-right-text">
                        <ul>
                            <li>
                                <a href="/hot-numbers-multiple" class="py-1 h6">
                                    <i class="icon-docs font-medium-5 mr-2"></i>Set Multiple Combinations
                                </a>
                            </li>
                        </ul>
                    </nav>                              -->
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="form-group filter">
                                <div class="row">
                                    <div class="col-md-3 date-picker">
                                        <label>Draw Date</label>
                                        <date-picker 
                                            @change="filter" 
                                            v-model="date"
                                            :format="'MMMM DD, YYYY'"
                                            :lang="lang">
                                        </date-picker>
                                    </div>
                                    <div class="col-md-2" style="margin-left: 10px;">
                                        <div class="form-group row">
                                            <label>Draw Time</label>
                                            <select id="draw_time" v-model="draw_time" class="form-control" @change="filter">
                                                <option value="11">11AM</option>
                                                <option value="16">4PM</option>
                                                <option value="21">9PM</option>
                                            </select>
                                        </div>
                                        </div>
                                        <div class="col-md-2">
                                        <label>Combination</label>
                                        <input v-model="combination" type="number" class="form-control">
                                    </div>
                                    <div class="col-md-2 form-actions">
                                        <button @click="filter" class="btn btn-outline-primary round">
                                            <i class="ft-filter mr-2"></i>Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group filter control">
                                <div class="row">
                                    <div class="col-md-3 control-type">
                                        <div class="form-group row">
                                            <label style="width: 100%;">Type</label>

                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="target" v-model="control.type" value="target" checked>
                                                <label class="custom-control-label" for="target">Target</label>
                                            </div>

                                           <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="ramble" v-model="control.type" value="ramble">
                                                <label class="custom-control-label" for="ramble">Ramble</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Amount</label>
                                        <input v-model="control.amount" min="0" type="number" class="form-control">
                                    </div>
                                    <div class="col-md-5 form-actions">
                                        <button @click="addItemsToControl" class="btn btn-outline-primary round">
                                            <i class="ft-plus mr-2"></i>Add to Control
                                        </button>
                                        <control-popup 
                                            :draw_time="draw_time"
                                            :control_items="control_items"
                                            :control="control">
                                        </control-popup>
                                    </div>
                                </div>
                            </div> 
                            <!-- @end control -->
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Combination</th>    
                                            <th>Total Amount</th>
                                            <th>Target</th>
                                            <th>Ramble</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 
                                            :class="com.is_selected ? 'warning' : ''"
                                            <tr v-for="(com, index) in hot_numbers" :key="index" :class="(com.total_amount + com.ramble_occurence_amount) >= com.bet_limit && com.type == 'straight' ? 'danger' : ''"> -->
                                        <tr v-for="(com, index) in hot_numbers" :key="index">
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" v-model="com.is_selected" class="custom-control-input" v-bind:id="'select'+index">
                                                    <label class="custom-control-label" v-bind:for="'select'+index">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>{{com.combination}}</td>
                                            <td>{{com.total_amount | currency('&#8369;')}}</td>
                                            <td>
                                                <span v-if="com.types.straight" :class="com.added_target ? 'added' : ''">
                                                    {{com.types.straight | currency('&#8369;')}}
                                                    <span v-if="com.added_target" class="remove" @click="removeItem(index, 'target')">
                                                        <i class="ft-x-circle"></i>
                                                    </span>
                                                </span>
                                            </td>
                                            <td>
                                                <span v-if="com.types.ramble" :class="com.added_ramble ? 'added' : ''">
                                                    {{com.types.ramble | currency('&#8369;')}}
                                                    <span v-if="com.added_ramble" class="remove" @click="removeItem(index, 'ramble')"><i class="ft-x-circle"></i></span>
                                                </span>
                                            </td>
                                            <!-- <td>{{com.bet_count}}</td> -->
                                            <!-- <td>
                                                <span v-if="com.type == 'straight'">
                                                    <span v-if="com.ramble_occurence_amount > 0">
                                                        {{com.total_amount}}(T) + {{com.ramble_occurence_amount}}(R) = 
                                                    </span>
                                                    <span>{{ (com.total_amount + com.ramble_occurence_amount) | currency('&#8369;') }}</span>
                                                </span>
                                                <span v-else>
                                                    <span>{{com.ramble_raw_amount | currency('&#8369;')}}({{com.total_amount}})</span>
                                                </span>
                                            </td> -->
                                            <!-- <td>
                                                <a v-if="com.type == 'straight'" href="#" @click.prevent="setLimit(com)">{{com.bet_limit | currency('&#8369;')}}</a>
                                                <span v-else>N/A</span>
                                            </td> -->
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
    import moment from 'moment';
    import ControlPopup from './single-popup/ControlPopup.vue';

    export default {
        data () {
            return {
                isLoading: false,
                date: new Date(), //default date
                combination: '',
                bets: [],
                gameOptions: [],
                draw_time: '11',
                hot_numbers: [],
                area: '',
                lang: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    pickers: ['next 7 days', 'next 30 days', 'previous 7 days', 'previous 30 days'],
                    placeholder: {
                        date: 'Select Date',
                    }
                },
                control: {
                    type: 'target',
                    amount: 0
                },
                control_items: [],
            }
        },
        created () {
            this.fetchData();
        },
        components: {
            Loading, 'control-popup': ControlPopup,
        },
        methods: {
            fetchData () {
                this.isLoading = true;
                let draw_date = moment(this.date).format('MM/DD/YYYY');
                axios.get('/api/reports/hot-numbers-control', {
                    params: {
                        date : draw_date,
                        combination : this.combination,
                        draw_time : this.draw_time,
                    }
                })
                .then(response => {
                    this.hot_numbers = _.orderBy(response.data, 'total_amount', 'desc');
                    this.control_items = [];
                    this.isLoading = false;
                });
            },
            onChangeDate(){
                this.fetchData();
            },
            filter(){
                this.fetchData();
            },
            addItemsToControl(){
                let _this = this;
                let selected = _.filter(this.hot_numbers, function(o) { return o.is_selected; });

                if( this.control.amount <= 0 ){
                    Vue.toasted.error('Please input amount');
                    return;
                }

                _.forEach(selected, function(item, key) {

                    let check_existence = _.filter(_this.control_items, function(o) { 
                        return o.combi == item.combination && o.type == _this.control.type; 
                    });
                    let is_valid = true;

                    if( check_existence.length > 0 ){
                        is_valid = false; 
                        // Vue.toasted.error('Duplicate removed: ' + item.combination);
                    }else if( _this.control.type == 'target' && !item.types.straight ){
                        is_valid = false;
                    }else if( _this.control.type == 'ramble' && !item.types.ramble ){
                        is_valid = false;
                    }else if( _this.control.type == 'target' && (_this.control.amount > item.types.straight) ){
                        is_valid = false;
                        Vue.toasted.error('Insufficient: ' + item.combination);
                    }else if( _this.control.type == 'ramble' && (_this.control.amount > item.types.ramble) ){
                        is_valid = false;
                        Vue.toasted.error('Insufficient: ' + item.combination);
                    }

                    if( is_valid ){
                        _this.control_items.push({
                            combi: item.combination,
                            amount: _this.control.amount,
                            type: _this.control.type 
                        });

                        // set added by type
                        let indexOfSelected = _.findIndex(_this.hot_numbers, function(o) { 
                            return o.combination == item.combination; 
                        });
                        if( _this.control.type == 'target' )
                            _this.$set(_this.hot_numbers[indexOfSelected], 'added_target', true);
                        else
                            _this.$set(_this.hot_numbers[indexOfSelected], 'added_ramble', true);

                        Vue.toasted.success('Item added: ' + item.combination);
                    }

                });

                console.log(_this.hot_numbers);
            },
            removeItem(index, type){
                let _this = this;
                let item = _this.hot_numbers[index];
                // remove flag
                if( type == 'target' )
                    _this.hot_numbers[index].added_target = false;
                else
                    _this.hot_numbers[index].added_ramble = false;

                // remove from items
                _.remove(_this.control_items, function(n) {
                    return n.combi == item.combination && n.type == type;
                });

                Vue.toasted.success('Item removed: '+ item.combination +'-'+ (type=='target' ? 'S' : 'R'));
            },
            setLimit(com){
                let area = this.area;
                this.$swal({
                    title: '<h4>Set bet limit for: '+com.combination+'</h4>',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: (limit) => {
                        let params =  {
                            combination: com.combination, 
                            type: com.type, 
                            limit: limit, 
                            area: area
                        }
                        return axios.post('/api/reports/set-bet-limit',params)
                        .then(response => {
                            if( response.data.success )
                                return params;
                            this.$swal.showValidationMessage(response.data.message);
                        }).catch(error => {
                            this.$swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value){
                            this.fetchData();
                            this.$swal( 'Success!', '', 'success');
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
    #highestBetReports .filter .form-actions{ margin: 24px 0 10px; }

    .filter.control { padding-top: 20px; border-top: 1px solid #ccc; }
    .control-type{ margin-left: 10px; }
    .control-type .custom-radio{ width: 80px; margin-left: 10px; margin-top: 5px; }
    .tabulation .tab-col{ max-width: 9.09%; min-width: 9.0%; border-left: 1px solid #ccc; padding: 0 5px; }
    .tabulation .col-md-6{ border-bottom: 1px solid #ccc; padding: 3px 0; width: 50%; }
    .tabulation .col-md-6:first-child{ border-right: 1px solid #ccc; }
    .tabulation .tab-col:last-child{ border-right: 1px solid #ccc; }
</style>