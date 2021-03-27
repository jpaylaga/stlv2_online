<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import CoordinatorsTable from './partials/CoordinatorsTable.vue';
    import UserItem from './partials/UserItem.vue';
    import AgentsList from './partials/AgentsList.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'manage-coordinators',
            routes: [
                {
                    path: '/coordinators',
                    name: 'coordinators',
                    component: CoordinatorsTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'coordinators' }
                },
                {
                    path: '/add-coordinator',
                    name: 'add-coordinator',
                    component: UserItem
                },
                {
                    path: '/view-agent-list/:coordinator',
                    name: 'view-agent-list',
                    component: AgentsList,
                    props: true
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
