<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import TransactionsTable from './partials/TransactionsTable.vue';
    import TicketItem from './partials/TicketItem.vue';

    export default {
        created() {
        },
        router: new VueRouter({
            mode: 'history',
            base: 'transactions',
            routes: [
                {
                    path: '/list',
                    name: 'list',
                    component: TransactionsTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'list' }
                },
                {
                    path: '/view/:id',
                    name: 'view-ticket',
                    component: TicketItem,
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
