<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view :key="$route.fullPath"></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import DrawResult from './results/DrawResult.vue';
    export default {
        created() {

        },
        router: new VueRouter({
            mode: 'history',
            base: 'draw-results',
            routes: [
                {
                    path: '/view/:draw_time',
                    name: 'draw-results',
                    component: DrawResult,
                    props: true
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'draw-results', params: { draw_time:11} }
                },
                {
                    path: '*',
                    redirect: { name: 'draw-results', params: { draw_time:11} }
                }
            ]
        })
    }
</script>
