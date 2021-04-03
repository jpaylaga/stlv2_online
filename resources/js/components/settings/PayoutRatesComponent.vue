<template>
    <div>
        <loading :active.sync="isLoading">
        </loading>
        <!--Extended Table starts-->
        <section id="payoutRates">
            <div class="row">
                <div class="col-sm-12">
                    <div class="content-header">Payout Rates</div>  
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Combination Payout Rates</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <form class="form" @submit.prevent="saveSettings">
                                    <div v-for="setting in settings" :key="setting.id" class="form-group">
                                        <label class="label-control">{{setting.name}}</label>
                                        <input type="text" class="form-control" v-model="setting.val">
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" style="margin-bottom: 0;" class="btn btn-outline-primary round">
                                            <i class="fa fa-check mr-1"></i> Save Changes </button>
                                    </div>
                                </form>
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
                settings: [
                    {name: 'Triple', val: 0, field_type: 'integer', type: 'price'},
                    {name: 'Double', val: 0, field_type: 'integer', type: 'price'},
                ],
            }
        },
        created () {
            this.fetchSettings();
        },
        components: {
            Loading
        },
        methods: {
            fetchSettings() {
                this.isLoading = true;
                axios.get('/api/settings/type', {
                    params: { type: 'price' }
                })
                .then(response => {
                    if( response.data.length > 0 )
                        this.settings = response.data;
                    this.isLoading = false;
                });
            },
            saveSettings(){
                this.isLoading = true;
                axios.post('/api/settings', this.settings)
                .then( response => {
                    this.isLoading = false;
                    this.$swal('Success', 'Successfully Updated Combination Payout Rates!', 'success');
                });
            },
        },
    }
</script>

<style>
    #payoutRates .card-header{ padding-bottom: 0; }
    .filter{ margin: 0; }
    .filter label{ margin: 0; font-size: 14px; }
    .date-picker .mx-input{ height: 37px; }
    .filter .form-actions{ margin: 24px 0; }
</style>