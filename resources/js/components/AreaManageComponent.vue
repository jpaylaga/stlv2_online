<template>
    <div class="main-content">
        <div class="content-wrapper">

            <loading :active.sync="isLoading"></loading>

            <section id="newUser">
                <div class="row">
                    <div class="col-md-12 mt-1 mb-1">
                        <div class="content-header">Manage Areas</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';

    export default {
        data() {
            return{
                isLoading: false,
            }
        },
    }
</script>
