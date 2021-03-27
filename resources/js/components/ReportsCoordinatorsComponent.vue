<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import Coordinators from './reports/Coordinators.vue';
    import UsherSales from './SummaryReportsComponent.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'reports-coordinators',
            routes: [
                {
                    path: '/list',
                    name: 'list',
                    component: Coordinators,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'list' }
                },
                {
                    path: '/view-usher-sales/:coordinator',
                    name: 'view-usher-sales',
                    component: UsherSales,
                    props: true
                },
                {
                    path: '*',
                    redirect: '/'
                }
            ]
        })
    }
</script>
