<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import AreaAdminsTable from './partials/AreaAdminsTable.vue';
    import UserItem from './partials/UserItem.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'manage-area-admins',
            routes: [
                {
                    path: '/area-admins',
                    name: 'area-admins',
                    component: AreaAdminsTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'area-admins' }
                },
                {
                    path: '/add-area-admin',
                    name: 'add-area-admin',
                    component: UserItem
                },
                {
                    path: '/edit/:id',
                    name: 'edit-user',
                    component: UserItem,
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
