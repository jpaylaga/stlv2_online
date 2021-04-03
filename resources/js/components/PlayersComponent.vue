<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import PlayersTable from './partials/PlayersTable.vue';
    // import UserItem from './partials/UserItem.vue';
    import UpdatePlayer from './players/UpdatePlayerData.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'manage-players',
            routes: [
                {
                    path: '/players',
                    name: 'players',
                    component: PlayersTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'players' }
                },
                {
                    path: '/update/:playerId',
                    name: 'update-player',
                    component: UpdatePlayer,
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
