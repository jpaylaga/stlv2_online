<template>
    <div class="main-content">
        <div class="content-wrapper px-2">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import CreditRequestsTable from './components/CreditRequestsTable.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'credit-requests',
            routes: [
                {
                    path: '/',
                    name: 'credit-requests',
                    component: CreditRequestsTable,
                },
                {
                    path: '*',
                    redirect: '/'
                }
            ]
        })
    }
</script>
