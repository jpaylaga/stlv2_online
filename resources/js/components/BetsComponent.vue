<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import AddBet from './bets/AddBet.vue';

    export default {
        created() {
            axios.get('/api/users');
        },
        router: new VueRouter({
            mode: 'history',
            base: 'manage-bets',
            routes: [
                {
                    path: '/add-bet',
                    name: 'add-bet',
                    component: AddBet,
                },
                {
                    path: '/',
                    redirect: { name: 'add-bet' }
                },
                {
                    path: '*',
                    redirect: '/'
                }
            ]
        })
    }
</script>
