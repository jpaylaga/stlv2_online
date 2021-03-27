<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import TellersTable from './partials/TellersTable.vue';
    import UserItem from './partials/UserItem.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'manage-tellers',
            routes: [
                {
                    path: '/tellers',
                    name: 'tellers',
                    component: TellersTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'tellers' }
                },
                {
                    path: '/add-teller',
                    name: 'add-teller',
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
