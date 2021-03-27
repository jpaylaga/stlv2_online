<template>
    <div>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div style="text-align: center;">
                    <h3 class="content-header">Draw Winners</h3>
                </div>
                <br><br>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Date:</strong> 
                    <span>{{draw_date | draw_date}}</span>
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Draw Time:</strong> 
                    <span>{{draw_time | draw_time}}</span>
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Total Winning:</strong> 
                    <span>{{total_winnings | currency('&#8369;')}}</span>
                </p>  
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-responsive-lg text-left">
                    <thead>
                        <tr>
                            <th>Ticket#</th>
                            <th>Usher</th>
                            <th>Bet</th>
                            <th>Game</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Win Amount</th>
                            <th>Draw Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="win in winners" :key="win.id">
                            <td>{{win.ticket.ticket_number | uppercase}}</td>
                            <td>
                                <span v-if="win.ticket.transaction.user.outlets.length > 0">
                                    ({{win.ticket.transaction.user.outlets[0].name}})
                                </span>
                                {{win.ticket.transaction.user.firstname +' '+ win.ticket.transaction.user.lastname}}
                            </td>
                            <td>{{win.bet.combination}}</td>
                            <td>{{win.draw.game.name}}</td>
                            <td>{{win.bet.type == 'straight' ? 'Target' : 'Ramble'}}</td>
                            <td>{{win.bet.amount | currency('&#8369;')}}</td>
                            <td>{{win.bet.winning_amount | currency('&#8369;')}}</td>
                            <td>
                                {{ win.ticket.draw_date | draw_date }} 
                                <span class="drawtime" :class="'time-' + win.ticket.draw_time">{{win.ticket.draw_time | draw_time}}</span>
                            </td>
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
        props: ['draw_date', 'draw_time', 'winners', 'total_winnings'],
        components: { 'print-heading': Heading },
        created() {

        },
    }
</script>