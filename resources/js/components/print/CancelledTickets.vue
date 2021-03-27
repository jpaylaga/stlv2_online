<template>
    <div>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div style="text-align: center;">
                    <h3 class="content-header">Cancelled Tickets</h3>
                </div>
                <br><br>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Date:</strong> 
                    <span>{{draw_date | draw_date}}</span>
                </p>  
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-responsive-lg text-left">
                    <thead>
                        <tr>
                            <th>Ticket No.</th>
                            <th>Usher</th>
                            <th>Bets</th>
                            <th>Amount</th>
                            <th>Cancelled by</th>
                            <th>Cancelled Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ticket in tickets" :key="ticket.id">
                            <td>{{ticket.ticket_number | uppercase}}</td>
                            <td>
                                <span v-if="ticket.agent.outlets.length > 0">
                                    ({{ticket.agent.outlets[0].name}})
                                </span>
                                {{ticket.agent.firstname +' '+ ticket.agent.lastname}}
                            </td>
                            <td>({{Object.keys(ticket.bets).length}})
                                <span v-for="(value, combi) in ticket.bets" :key="value" class="bet-item">
                                    <span>{{ combi }}</span>
                                </span>
                            </td>
                            <td>{{ticket.total_amount | currency('&#8369;')}}</td>
                            <td>{{ticket.cancelled_by.firstname +' '+ ticket.cancelled_by.lastname}}</td>
                            <td>{{ticket.details.created_at | bet_date}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <p>Prepared by: ____________________________________________</p>
                <p>Checked by: ____________________________________________</p>
            </div>
        </div>
    </div>
</template>

<script>
    import Heading from "./partials/Heading.vue";
    export default {
        props: ['draw_date', 'tickets'],
        components: { 'print-heading': Heading },
        created() {

        },
    }
</script>