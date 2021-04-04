<template>
    <div class="main-content">
        <loading :active.sync="isLoading"></loading>
        <div class="">
            <div class="col-md-12">
                <div class="card mt-0">
                    <div class="card-body">
                        <form class="form addbet" @submit.prevent="save">
                            <div class="form-body">
                                <h4 class="form-section"><i class="ft-file-plus"></i> Add Bet</h4>
                                <p>Credit Balance: <strong>{{credit_balance | currency('&#8369;')}}</strong></p>

                                <!-- Games -->
                                <!-- <div class="form-group row">
                                    <div class="col-md-12">
                                        <label id="game" v-for="game in gameOptions" :key="game.id" class="radio-inline">
                                            <input v-model="transaction.game_id" type="radio" :value="game.id"> {{game.name}} 
                                        </label>
                                    </div>
                                </div> -->
                                <div class="row force-unwidth mb-2 clearfix">
                                    <div class="col-sm-12 m-0">
                                        <label class="mb-0">GAME</label>
                                    </div>
                                    <div v-for="game in gameOptions" :key="game.id" class="col-sm-4">
                                        <div 
                                            :class="{ 'text-white bg-success' : transaction.game_id == game.id }" 
                                            class="card m-0 p-1" 
                                            @click="transaction.game_id = game.id">
                                            <div class="card-body">
                                                <center> {{game.name}} </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- @end Games -->

                                <!-- Draw Time -->
                                <div class="row force-unwidth clearfix">
                                    <div class="col-sm-12 m-0">
                                        <label class="mb-0">DRAW TIME</label>
                                    </div>
                                    <div v-for="drawtime in $getDrawTimeOptions()" :key="drawtime.id" class="col-sm-4">
                                        <div 
                                            :class="{
                                                'text-white bg-success' : selectedDrawTime == drawtime.id, 
                                                'text-white bg-danger': !canStillBet(drawtime.actual_time)}" 
                                            class="card m-0 p-1" 
                                            @click="selectDrawTime(drawtime)">
                                            <div class="card-body">
                                                <center> {{drawtime.name}} </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- @end Draw Time -->

                                <div class="form-group row mt-2 mb-1">
                                    <div class="col-md-3"> <label for="combination" class="mb-0">Combination</label> </div>
                                    <div class="col-md-12">
                                        <input disabled @focus="focused_input = 'combination'" name="combination" v-model="bet.combination" type="text" class="form-control" :class="focused_input == 'combination' ? 'focused_input' : ''">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-3"> <label class="mb-0" for="amount">Amount</label> </div>
                                    <div class="col-md-12">
                                        <input disabled @focus="focusInput('amount')" name="amount" v-model="bet.amount" type="number" min="1" max="999" class="form-control" :class="focused_input == 'amount' ? 'focused_input' : ''">
                                    </div>
                                </div>

                                <!-- View -->
                                <div class="force-unwidth clearfix mb-2">
                                    <div v-for="view in drawers" :key="view" class="col-sm-6">
                                        <div :class="selectedView == view ? 'text-white bg-success' : 'unselected-btn-card'" class="card m-0 p-1" @click="selectedView = view">
                                            <div class="card-body">
                                                <center> 
                                                    {{view}} 
                                                    <span v-if="view=='bets' && ticket.bets.length > 0">({{ticket.bets.length}})</span>
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- @end View -->

                                <div class="form-group row keypad-wrapper" v-if="selectedView == 'keypad'">
                                    <div v-for="k in numpad_keys" :key="k" class="kbtn" @click="write(k)"> 
                                        <span v-if="k == 'back'"><i class="ft-delete"></i></span> 
                                        <span v-else>{{k}}</span> 
                                    </div>
                                    <div class="kbtn sbtn" @click="addBet('straight')">
                                        <span>Target</span> 
                                    </div>
                                    <div class="kbtn sbtn" @click="addBet('ramble')">
                                        <span>Ramble</span> 
                                    </div>
                                </div>

                                <div class="form-group row keypad-wrapper" v-if="selectedView == 'bets'">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Bet</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Del</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(bet, index) in ticket.bets" :key="index">  
                                                <td>{{bet.combination}}</td>
                                                <td>{{bet.type}}</td>
                                                <td>{{bet.amount}}</td>
                                                <td><i class="ft-x-circle danger" @click="removeBet(index)"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button v-if="ticket.bets.length > 0" @click="submitTicket" type="button" class="btn btn-outline-primary"><i class="fa fa-check"></i> Submit Ticket</button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import Loading from 'vue-loading-overlay';

export default {
    data() {
        return{
            isLoading: false,
            transaction: {
                user_id: null,
                game_id: 1,
                tickets: []
            },
            ticket: {
                draw_date: null,
                draw_time: null,
                bets: []
            },
            bet: {
                combination: "",
                amount: "",
            },
            numpad_keys: [7,8,9,4,5,6,1,2,3,'C',0,'back'],
            num: null,
            focused_input: 'combination',
            is_initial_amount: true,
            selectedDrawTime: null,
            selectedView: 'keypad',
            gameOptions: [],
            drawers: ['keypad','bets'],
            player: null,
            credit_balance: 0,
        }
    },
    components: {
        Loading
    },
    created(){
        this.init();
    },
    methods: {
        init(){
            this.player = this.$getUser();
            this.transaction.user_id = this.player.id

            this.preSelectDrawTime();
            this.loadGames();
            this.getCreditBalance();
        },
        // options
        async loadGames(){
            await axios.get('/api/games').then(resp => {
                this.gameOptions = resp.data;
            });
        },
        //keypad
        focusInput(ctx){
            if( ctx == 'amount' ){
                this.focused_input = 'amount'
            if( this.bet.amount == "" || this.bet.amount <= 0 ){ 
                    this.is_initial_amount = true;
                    this.bet.amount = 1
                }
            }
        },
        write(k){
            if(this.focused_input == 'combination'){
                if( k == 'C' && this.bet.combination.length > 0 ) this.bet.combination = ''
                if( k == 'back' && this.bet.combination.length > 0 ) this.bet.combination = this.bet.combination.slice(0, -1)
                if( this.bet.combination.length >= 3 ) return
                if( k != 'back' && k != 'C' ){
                    this.bet.combination += k.toString();
                    if( this.bet.combination.length >= 3 ){
                        this.focused_input = 'amount'
                    }
                } 
            }
            else if(this.focused_input == 'amount'){
                if( k == 'C' && this.bet.amount.length > 0 ) this.bet.amount = ''
                if( k == 'back' && this.bet.amount.length > 0 ) this.bet.amount = this.bet.amount.slice(0, -1)
                if( this.bet.amount.length >= 3 ) return
                if( k != 'back' && k != 'C' ){
                    if( this.is_initial_amount ){
                        this.bet.amount = ''
                        this.is_initial_amount = false
                    }
                    this.bet.amount += k.toString();
                }
            }
        },
        preSelectDrawTime(){
            let current_hour = moment(new Date, "HH:mm");

            if( parseInt( current_hour.format("H") ) < this.DRAWTIME_MORNING && this.canStillBet(this.DRAWTIME_MORNING) ){
                this.selectedDrawTime = this.DRAWTIME_MORNING;
            } else if( parseInt( current_hour.format("H") ) < this.DRAWTIME_AFTERNOON && this.canStillBet(this.DRAWTIME_AFTERNOON) ){
                this.selectedDrawTime = this.DRAWTIME_AFTERNOON;
            } else if( parseInt( current_hour.format("H") ) < this.DRAWTIME_EVENING && this.canStillBet(this.DRAWTIME_EVENING) ){
                this.selectedDrawTime = this.DRAWTIME_EVENING;
            } 
        },
        resetTickets(){
            this.transaction.tickets = [];
            this.ticket.bets = [];
            this.clearBet();
        },
        clearBet(){
            this.bet = {
                combination: '',
                amount: '',
            }
            this.focused_input = 'combination';
        },
        addBet(bet_type){

            if( this.bet.combination.length < 3 ){
                Vue.toasted.error('Invalid combination'); return;
            } 
            if( this.bet.amount < 1 ){
                Vue.toasted.error('Invalid amount'); return;
            } 

            let betObj = {
                combination: this.bet.combination,
                type: bet_type,
                amount: parseInt(this.bet.amount),
            }
            this.ticket.bets.push(betObj)
            Vue.toasted.success('Bet successfully added.');
            this.clearBet()
        },
        removeBet(index){
            this.ticket.bets.splice(index, 1);
        },
        submitTicket(){
            this.ticket.draw_time = this.selectedDrawTime
            this.ticket.draw_date = moment(new Date()).format("MM/DD/YYYY")

            // TEMP
            // this.ticket.draw_time = '11'
            // this.ticket.draw_date = moment(new Date()).add(1, 'days').format("MM/DD/YYYY")

            this.transaction.tickets.push(this.ticket);
            this.isLoading = true;
            axios.post('/api/transaction', this.transaction).then(resp => {
                if( resp.data.success ){
                    // show ticket / redirect to ticket
                    this.showTicket(resp.data.transaction.tickets[0].id);
                    this.resetTickets();
                    this.getCreditBalance();
                    this.selectedView = 'keypad';
                    Vue.toasted.success('Ticket successfully submitted.');
                }else{
                    Vue.toasted.error(resp.data.message);
                }
            }).catch(error => {
                if( error.response.status == 422 )
                    Vue.toasted.error(`DRAW TIME ${this.$getDrawTimeOptions(this.selectedDrawTime)} ALREADY CUTOFF!`)
                else
                    Vue.toasted.error('Something went wrong please try again.')
            }).then(() => { this.isLoading = false })
        },

        // SELECT OPTIONS
        selectDrawTime(drawtime){
            if( this.canStillBet(drawtime.actual_time) )
                this.selectedDrawTime = drawtime.id;
        },

        // HELPERS
        canStillBet(draw_time){
            let current_hour = moment(new Date, "HH:mm");
            let cutoff = moment(draw_time, "HH:mm").subtract(this.DRAWTIME_CUTOFF, 'minutes');
            if( current_hour.diff(cutoff, 'minutes') > 0 )
                return false
            return true
        },
        getCreditBalance(){
            axios.get('/api/user/credit-balance/' + this.player.id).then(response => {
                this.credit_balance = response.data.credit_balance
            });
        },
        showTicket(ticket_id){
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
                // html += '<span class="drawtime time-'+ticket.draw_time+'">'+this.$options.filters.draw_time(ticket.draw_time)+'</span>';
                html += '<span class="drawtime time-'+ticket.draw_time+'">'+this.$getDrawTimeOptions(ticket.draw_time)+'</span>';
                html += '</p>';
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
                    html += parseInt(bet.amount) > 0 ? '<tr>' : '<tr class="danger">';
                    html += '<td>'+ bet.combination +'</td>';
                    html += '<td>'+ (bet.type == 'straight' ? 'T' : 'R') +'</td>';
                    html += '<td>'+ (parseInt(bet.amount) > 0 ? bet.amount : 'S/O' ) +'</td>';
                    html += '<td>'+ (parseInt(bet.winning_amount) > 0 ? bet.winning_amount : 'S/O' ) +'</td>';
                    html += '</tr>';
                });
                html += '</table>';
                
                this.$swal({
                    customClass: { container: 'popup-profile' },
                    title: 'Bet Success!',
                    html: html,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                })

            });

        }
    }
}
</script>