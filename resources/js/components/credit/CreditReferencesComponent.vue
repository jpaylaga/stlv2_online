<template>
    <div class="main-content">
        <div class="content-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import VueRouter from 'vue-router';
    import ReferencesTable from './partials/ReferencesTable.vue';
    import ReferenceItem from './partials/ReferenceItem.vue';

    export default {
        router: new VueRouter({
            mode: 'history',
            base: 'credit-references',
            routes: [
                {
                    path: '/list',
                    name: 'credit-references-list',
                    component: ReferencesTable,
                },
                {
                    path: '/',
                    name: '/',
                    redirect: { name: 'credit-references-list' }
                },
                {
                    path: '/add-reference',
                    name: 'add-reference',
                    component: ReferenceItem
                },
                {
                    path: '/edit/:id',
                    name: 'edit-reference',
                    component: ReferenceItem,
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
