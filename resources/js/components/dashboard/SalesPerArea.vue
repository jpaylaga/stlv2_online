<template>
    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
        <div class="card">
            <div class="card-header">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="font-large-1 mb-0">Sales Per Area/Station</h3>
                        <span class="grey"></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-block">
                    <table class="table table-responsive-lg text-left">
                        <thead>
                            <tr>
                                <th>Area/Station</th>
                                <th>Station 1</th>
                                <th>Station 2</th>
                                <th>Station 3</th>
                                <th>Station 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">11 AM</th>
                                <td>928,254.00</td>
                                <td>131,214.00</td>
                                <td>254,225.00</td>
                                <td>254,225.00</td>
                            </tr>
                            <tr>
                                <th scope="row">4 PM</th>
                                <td>914,258.00</td>
                                <td>112,254.00</td>
                                <td>874,147.00</td>
                                <td>254,225.00</td>
                            </tr>
                            <tr>
                                <th scope="row">9 PM</th>
                                <td>225,256.00</td>
                                <td>254145.00</td>
                                <td>528,214.00</td>
                                <td>254,225.00</td>
                            </tr>
                            <tr>
                                <th scope="row">Total</th>
                                <td>4,225,256.00</td>
                                <td>2,254,145.00</td>
                                <td>4,528,214.00</td>
                                <td>7,256,236.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from 'moment';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        data () {
            return {
                isLoading: false,
                errors: [],
            }
        },
        created() {

        },
        components: { Loading },
        methods: {
            
        }
    }
</script>