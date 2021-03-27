<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import OutletsTable from '../partials/OutletsTable.vue';
    import OutletItem from '../partials/OutletItem.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'outlets',
            routes: [
                {
                    path: '/list',
                    name: 'list',
                    component: OutletsTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'list' }
                },
                {
                    path: '/add-outlet',
                    name: 'add-outlet',
                    component: OutletItem
                },
                {
                    path: '/edit/:id',
                    name: 'edit',
                    component: OutletItem,
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
