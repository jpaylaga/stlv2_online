<template>
    <div class="main-content">
        <div class="">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="form addbet" @submit.prevent="save" v-if="!success_ticket">
                            <div class="form-body">
                                <h4 class="form-section"><i class="ft-file-plus"></i> Add Bet</h4>

                                <!-- Games -->
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label id="game" v-for="game in gameOptions" :key="game.id" class="radio-inline">
                                            <input v-model="transaction.game_id" type="radio" :value="game.id"> {{game.name}} 
                                        </label>
                                    </div>
                                </div>
                                <!-- @end Games -->

                                <!-- Draw Time -->
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label id="game" v-for="drawtime in $getDrawTimeOptions()" :key="drawtime.id" class="radio-inline">
                                            <input :disabled="!canStillBet(drawtime.id)" v-model="selectedDrawTime" type="radio" :value="drawtime.id"> {{drawtime.name}} 
                                        </label>
                                    </div>
                                </div>
                                <!-- @end Draw Time -->

                                <div class="form-group row">
                                    <div class="col-md-3"> <label for="combination">Combination</label> </div>
                                    <div class="col-md-12">
                                        <input @focus="focused_input = 'combination'" name="combination" v-model="bet.combination" type="text" class="form-control" :class="focused_input == 'combination' ? 'focused_input' : ''">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-3"> <label for="amount">Amount</label> </div>
                                    <div class="col-md-12">
                                        <input @focus="focusInput('amount')" name="amount" v-model="bet.amount" type="number" min="1" max="999" class="form-control" :class="focused_input == 'amount' ? 'focused_input' : ''">
                                    </div>
                                </div>

                                <!-- View -->
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label id="game" v-for="view in drawers" :key="view" class="radio-inline">
                                            <input v-model="selectedView" type="radio" :value="view"> {{view}} 
                                        </label>
                                    </div>
                                </div>
                                <!-- @end View -->

                                <div class="form-group row keypad-wrapper" v-if="selectedView == 'keypad'">
                                    <div v-for="k in numpad_keys" :key="k" class="kbtn" @click="write(k)"> 
                                        <span v-if="k == 'back'"><i class="ft-delete"></i></span> 
                                        <span v-else>{{k}}</span> 
                                    </div>
                                    <div class="kbtn sbtn" @click="addBet('target')">
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
                                                <td><i class="ft-x-circle" @click="removeBet(index)"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button v-if="ticket.bets.length > 0" @click="submitTicket" type="button" class="btn btn-outline-primary"><i class="fa fa-check"></i> Submit Ticket 123</button>
                                </div>

                            </div>
                        </form>

                        <div v-if="success_ticket">
                            <table>
                                <tr>
                                    <th>Game</th>
                                    <td>{{success_ticket.transaction.game.name}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    data() {
        return{
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
            success_ticket: null, 
            numpad_keys: [7,8,9,4,5,6,1,2,3,'C',0,'back'],
            num: null,
            focused_input: 'combination',
            is_initial_amount: true,
            selectedDrawTime: null,
            selectedView: 'keypad',
            gameOptions: [],
            drawers: ['keypad','bets'],
            player: null,
        }
    },
    created(){
        this.init();
    },
    methods: {
        init(){
            this.player = this.$getUser();
            this.transaction.user_id = this.player.id
            // this.preSelectDrawTime();
            this.loadGames();
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
                amount: this.bet.amount,
            }
            this.ticket.bets.push(betObj)
            Vue.toasted.success('Bet successfully added.');
            this.clearBet()
        },
        removeBet(index){
            this.ticket.bets.splice(index, 1);
        },
        submitTicket(){
            // this.ticket.draw_time = this.selectedDrawTime
            // this.ticket.draw_date = moment(new Date()).format("MM/DD/YYYY")

            // TEMP
            this.ticket.draw_time = '11'
            this.ticket.draw_date = moment(new Date()).add(1, 'days').format("MM/DD/YYYY")
            this.transaction.tickets.push(this.ticket);

            axios.post('/api/transaction', this.transaction).then(resp => {
                console.log(resp.data);
                if( resp.data.success ){
                    // show ticket / redirect to ticket
                    Vue.toasted.success('Ticket successfully submitted.');
                    this.success_ticket = resp.data.transaction;
                }else{
                    Vue.toasted.error(resp.data.message);
                }
            }).catch(error => {
                console.log(error);
            }).then(() => {  })
        },

        // HELPERS
        canStillBet(draw_time){
            let current_hour = moment(new Date, "HH:mm");
            let cutoff = moment(draw_time, "HH:mm").subtract(this.DRAWTIME_CUTOFF, 'minutes');
            if( current_hour.diff(cutoff, 'minutes') > 0 )
                return false
            return true
        }
    }
}
</script>