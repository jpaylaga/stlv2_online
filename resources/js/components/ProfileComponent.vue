<template>
    <div class="main-content">
        <div class="content-wrapper" v-if="user.id">
            <user-item :id="user.id" />
        </div>
    </div>
</template>

<script>
    import UserItem from './partials/UserItem.vue';
    export default {
        data() {
            return{
                user: {}
            }
        },
        created() {
            axios.get('/api/user').then(response => {
                this.user = response.data;
                console.log(this.user.id);
            });
        },
        components: { 'user-item': UserItem },
    }
</script>
