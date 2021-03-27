<template>
    <div id="user-profile">
        <a href="#" @click.prevent="viewTicket(ticket_id)"> 
            {{ticket_number | uppercase}}
        </a>
    </div>
</template>
<script>
    export default {
        props: ['ticket_id', 'ticket_number', 'show_cancel'],
        data(){
            return {
                ticket: '',
            }
        },
        created () {

        },
        methods: {
            viewTicket(ticket_id){
                this.isLoading = true;
                axios.get('/api/ticket/' + ticket_id).then(response => {
                    let ticket = response.data;
                    let html = '';
                    
                    html += '<div class="ticket">';
                    html += '<p><strong>Transaction ID: </strong> '+this.$options.filters.uppercase(ticket.transaction.code)+'</p>';
                    html += '<p><strong>Ticket ID: </strong> '+this.$options.filters.uppercase(ticket.ticket_number)+'</p>';
                    html += '<p><strong>Total Bet Amount: </strong> '+this.$options.filters.currency(ticket.transaction.total_amount, '&#8369;')+'</p>';
                    html += '<p><strong>Bet Date/Time: </strong> '+this.$options.filters.bet_date(ticket.created_at)+'</p>';
                    html += '<p><strong>Draw Date/Time: </strong> '+this.$options.filters.draw_date(ticket.draw_date)+' ';
                    html += '<span class="drawtime time-'+ticket.draw_time+'">'+this.$options.filters.draw_time(ticket.draw_time)+'</span>';
                    html += '</p>';
                        
                        // +this.$options.filters.draw_time(ticket.draw_time)
                        // <span class="drawtime" :class="'time-' + ticket.draw_time">{{ticket.draw_time | draw_time}}</span>''
                   
                    html += '<p><strong>Game: </strong> '+ticket.game.name+'</p>';
                    html += '</div>';

                    html += '<table class="table text-left">';
                    html += '<tr>';
                    html += '<th>Number</th>';
                    html += '<th>Type</th>';
                    html += '<th>Amount</th>';
                    html += '<th>Win</th>';
                    html += '</tr>';
                    _.forEach(ticket.bets, function(bet, key) {
                        html += '<tr>';
                        html += '<td>'+ bet.combination +'</td>';
                        html += '<td>'+ (bet.type == 'straight' ? 'Target' : 'Ramble') +'</td>';
                        html += '<td>&#8369;'+ bet.amount +'</td>';
                        html += '<td>&#8369;'+ bet.winning_amount +'</td>';
                        html += '</tr>';
                    });
                    html += '</table>';
                    
                    this.$swal({
                        customClass: { container: 'popup-profile' },
                        title: 'Ticket Details',
                        // title: this.$options.filters.uppercase(ticket.ticket_number),
                        html: html,
                        showConfirmButton: this.show_cancel, 
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        confirmButtonText: 'Cancel Ticket',
                    }).then(result => {
                        this.isLoading = false;

                        if (result.value) {
                            
                            this.$swal({
                                title: 'Please Confirm',
                                text: 'Are you sure to cancel this ticket?',
                                showCancelButton: true,
                                cancelButtonText: 'No',
                                confirmButtonText: 'Yes',
                                type: 'warning'
                            }).then(result => {
                                if (result.value) {
                                    // cancel ticket here
                                    axios.patch('/api/ticket/cancel', {
                                        ticket_number: ticket.ticket_number,
                                    }).then(response => {
                                        if( response.data.success ){
                                            this.$swal({ 
                                                title: 'Success!', 
                                                text: 'Ticket successfully cancelled.', 
                                                type: 'success'
                                            }).then((result) => { 
                                                this.$parent.fetchData();
                                            })
                                        }else{
                                            this.$swal({
                                                title: '', 
                                                text: response.data.message, 
                                                type: 'error'
                                            });
                                        }
                                    });
                                }
                            });

                        }
                    })

                });


                // let html = '<img class="profile-image-thumb" v-if="user.picture" :src="`/storage/profile_images/${user.picture}`" alt="">';

                // html += '<p><strong>ID Number: </strong> '+(user.id_number ? user.id_number : '&nbsp;')+'</p>';
                // html += '<p><strong>Phone Number: </strong> '+(user.phone ? user.phone : '&nbsp;')+'</p>';
                // html += '<p><strong>Email: </strong> '+user.email+'</p>';
                // html += '<p><strong>Address: </strong> '+(user.address ? user.address : '&nbsp;')+'</p>';
                // html += '<p><strong>Birth Date: </strong> '+(user.dob ? user.dob : '&nbsp;')+'</p>';
                // html += '<p><strong>Gender: </strong> '+(user.gender ? user.gender : '&nbsp;')+'</p>';
                // html += '<p><strong>Civil Status: </strong> '+(user.civil_status ? user.civil_status : '&nbsp;')+'</p>';

                // this.$swal({
                //     customClass: { container: 'popup-profile' },
                //     title: user.firstname+' '+user.lastname,
                //     imageUrl: '/storage/profile_images/'+user.picture,
                //     imageWidth: 200,
                //     html: html,
                //     showCancelButton: true,
                //     cancelButtonText: 'Close',
                //     confirmButtonText: 'Edit Profile',
                // }).then(result => {
                //     if (result.value) {
                //         window.location.href = '/manage-users/edit-user/' + user.id;
                //     }
                // })
            }
        }
    }
</script>
