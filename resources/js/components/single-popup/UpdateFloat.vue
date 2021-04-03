<template>
    <div id="update-float">
        <a href="#" @click.prevent="updateFloat(coor)">{{coor.float | currency('&#8369;')}}</a>
    </div>
</template>
<script>
    import moment from 'moment';

    export default {
        props: ['coor','show_cancel'],
        created () {

        },
        methods: {
            updateFloat(coor){
                this.$swal({
                    title: '<h5>Update Float for: '+coor.firstname+' '+coor.lastname+'</h5>',
                    input: 'text',
                    inputValue: coor.float,
                    inputAttributes: {
                        autocapitalize: 'on',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Udpate',
                    showLoaderOnConfirm: true,
                    preConfirm: (float) => {
                        return axios.post('/api/user/update-float', {
                            user: coor.id, float: float
                        })
                        .then(response => {
                            if( response.data.success )
                                return response.data;
                            this.$swal.showValidationMessage(response.data.message);
                        }).catch(error => {
                            this.$swal.showValidationMessage(
                                `Request failed: ${'Please enter valid value'}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        this.$swal('Successfully Updated Float!', '', 'success');
                        this.$parent.fetchData();
                    }
                })
            },
        }
    }
</script>
