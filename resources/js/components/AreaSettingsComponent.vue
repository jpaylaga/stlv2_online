<template>
    <div class="main-content">
        <div class="content-wrapper">

            <loading 
                :active.sync="isLoading" 
                :is-full-page="fullPage">
            </loading>

            <section id="newUser">
                <div class="row">
                    <div class="col-md-12 mt-1 mb-1">
                        <div class="content-header">Area Settings</div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-4">
                        <select id="area" @change="onChangeArea($event)" v-model="selectedArea" class="form-control">
                            <option v-for="area in areaOptions" :value="area.id" :key="area.id">
                                {{area.name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <img v-if="area.logo" :src="`/storage/images/${area.logo}`" alt="">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="px-3">
                                    <form class="form form-horizontal striped-rows form-bordered" @submit.prevent="save">
                                        <div class="form-body">

                                            <!-- errors -->
                                            <ul v-if="errors">
                                                <li v-for="error in errors" :key="error.index">
                                                    {{error}}
                                                </li>
                                            </ul>

                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="name">Name</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" placeholder="Name" v-model="area.name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="logo">Logo</label>
                                                <div class="col-md-9">
                                                    <drop-zone :options="dropzoneOptions" id="dz" ref="dropzone" :useCustomSlot=true>
                                                        <div class="dropzone-custom-content">
                                                            <h3 class="dropzone-custom-title">Drag and drop to upload content!</h3>
                                                            <div class="subtitle">...or click to select a file from your computer</div>
                                                        </div>
                                                    </drop-zone>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="address">Address</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" placeholder="First Name" v-model="area.address">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="cutoff_time">Cutoff Time</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" placeholder="Cutoff Time" v-model="area.cutoff_time">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="limit">Bet Limit</label>
                                                <div class="col-md-9">
                                                    <input type="number" class="form-control" placeholder="Bet Limit" v-model="area.limit">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="winning_prices">Payout Prices</label>
                                                <div class="col-md-9">
                                                    <div class="form-group row" v-for="(price, index) in area.prices" :key="price.id" >
                                                        <label class="col-md-4 label-control">{{price.game.name}}</label>
                                                        <div class="col-md-4">
                                                            <label class="">Payout Rates</label>
                                                            <input type="text" class="form-control" v-model="area.prices[index].price_per_unit">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="">Bet Limit</label>
                                                            <input type="text" class="form-control" v-model="area.prices[index].bet_limit">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions right">
                                            <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save Changes </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import vue2Dropzone from 'vue2-dropzone';
    import 'vue2-dropzone/dist/vue2Dropzone.min.css';

    export default {
        data() {
            return{
                isLoading: false,
                fullPage: true,
                errors: [],
                areaOptions: [],
                gameOptions: [],
                selectedArea: '',
                area: {
                    name: '',
                    address: '',
                    cutoff_time: '',
                    limit: '',
                    logo: '',
                    prices: []
                },
                dropzoneOptions: {
                    url: '/api/area/add-image',
                    thumbnailWidth: 400,
                    headers: {
                        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
                    },
                    success(file, res){
                        file.filename = res;
                    }
                }
            }
        },
        components: {
            Loading,
            dropZone: vue2Dropzone
        },
        created() {
            this.isLoading = true;
            this.fetchOptionsData();
        },
        methods: {
            fetchOptionsData() {
                axios.get('/api/areas').then(response => {
                    this.areaOptions = response.data;
                    this.selectedArea = this.areaOptions[0].id;
                    // get games
                    axios.get('/api/games').then(response => {
                        this.gameOptions = response.data;
                        this.getArea(this.selectedArea);
                    });
                });
            },
            getArea(area_id) {
                axios.get('/api/area_with_prices/' + area_id).then(response => {
                    this.area = response.data;
                    this.isLoading = false;
                });
            },
            onChangeArea(event) {
                this.isLoading = true;
                this.getArea(event.target.value);
            },
            save() {

                let files = this.$refs.dropzone.getAcceptedFiles();
                if( files.length > 0 && files[0].filename ){
                    this.area.logo = files[0].filename;
                }

                axios.patch('/api/area/' + this.selectedArea, this.area).then(response => {
                    this.isLoading = true;
                    Vue.toasted.info('Area successfully saved.');
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    this.errors = [].concat.apply([], messages);
                    console.log(this.errors);
                }).then(() => {
                    this.isLoading = false; 
                });
            },
        }   
    }
</script>
