<template>
    <div class="main-content">
        <div class="content-wrapper px-2">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import CreditsComponent from '../CreditsComponent.vue';
    import CreditRequestsTable from './components/CreditRequestsTable.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'credits',
            routes: [
                {
                    path: '/',
                    name: 'credits',
                    component: CreditsComponent,
                },
                {
                    path: '*',
                    redirect: '/'
                }
            ]
        })
    }
</script>
