<template>
    <div id="user-profile">
        <a href="#" @click.prevent="viewProfile(user)"> 
            {{user.firstname+' '+user.lastname}}
        </a>
    </div>
</template>
<script>
    export default {
        props: ['user', 'edit'],
        created () {

        },
        methods: {
            viewProfile(user){

                console.log();

                let html = '<img class="profile-image-thumb" v-if="user.picture" :src="`/storage/profile_images/${user.picture}`" alt="">';

                html += '<p><strong>ID Number: </strong> '+(user.id_number ? user.id_number : '&nbsp;')+'</p>';
                html += '<p><strong>Phone Number: </strong> '+(user.phone ? user.phone : '&nbsp;')+'</p>';
                html += '<p><strong>Email: </strong> '+user.email+'</p>';
                html += '<p><strong>Address: </strong> '+(user.address ? user.address : '&nbsp;')+'</p>';
                html += '<p><strong>Birth Date: </strong> '+(user.dob ? user.dob : '&nbsp;')+'</p>';
                html += '<p><strong>Gender: </strong> '+(user.gender ? user.gender : '&nbsp;')+'</p>';
                if( user.api_token )
                    html += `<p><strong>Referral Link: </strong>${window.baseUrl}/register?referral=${user.api_token}</p>`;
                else
                    html += `<p><strong>Referral Link: </strong>No API Token set for this user</p>`;

                this.$swal({
                    customClass: { container: 'popup-profile' },
                    title: user.firstname+' '+user.lastname,
                    imageUrl: '/storage/profile_images/'+user.picture,
                    imageWidth: 200,
                    html: html,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Edit Profile',
                }).then(result => {
                    if (result.value) {
                        window.location.href = this.edit + user.id;
                    }
                })
            }
        }
    }
</script>
