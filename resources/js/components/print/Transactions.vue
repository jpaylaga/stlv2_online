<template>
    <section id="extended">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <table class="table table-responsive-lg text-left" id="transactions">
                                    <thead>
                                        <tr>
                                            <th>Ticket No.</th>
                                            <th>Usher</th>
                                            <th>Bets</th>
                                            <th>Amount</th>
                                            <th>Bet Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ticket in tickets" :key="ticket.id">  
                                            <td>{{ticket.ticket_number | uppercase}}</td>
                                            <td> {{ticket.firstname +' '+ ticket.lastname}} </td>
                                            <td>
                                                <span v-for="(value, combi) in ticket.bets" :key="value">
                                                    <span>{{ combi }} &nbsp;</span>
                                                </span>
                                            </td>
                                            <td>{{ticket.total_amount}}</td>
                                            <td>{{ticket.created_at | bet_date}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
</template>

<script>
    export default {
        props: ['tickets'],
        created() {

        },
    }
</script>