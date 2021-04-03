<template>
    <div class="pull-left" @click.prevent="changeStatus(user)">
        <switches 
            v-model="user.is_active"
            theme="custom"
            color="blue"
            type-bold="true"
        ></switches>  
    </div>
</template>
<script>
    import Switches from 'vue-switches';

    export default {
        props: ['user'],
        created () {

        },
        components: { Switches },
        methods: {
            changeStatus(user){
                let action = user.is_active ? 'deactivate' : 'activate';
                this.$swal({
                    title: action,
                    text: "Are you sure to " + action + ' this user?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        axios.post('/api/user/change-status', {
                            user: user.id,
                            action: action
                        }).then(resp => {
                            if( resp.data.success ){
                                this.$swal({
                                    'title': 'Success!',
                                    'text': 'User ' + action + 'd!',
                                    'type': 'success'
                                }).then((result) => { 
                                    this.$parent.fetchData();
                                });
                            }
                        });
                    }
                })
            },
        }
    }
</script>
