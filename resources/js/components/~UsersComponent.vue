<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import UsersTable from './partials/UsersTable.vue';
    import UserItem from './partials/UserItem.vue';

    export default {
        created() {
        },
        router: new VueRouter({
            mode: 'history',
            base: 'manage-users',
            routes: [
                {
                    path: '/users',
                    name: 'users',
                    component: UsersTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'users' }
                },
                {
                    path: '/add-user',
                    name: 'add-user',
                    component: UserItem
                },
                {
                    path: '/edit-user/:id',
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
