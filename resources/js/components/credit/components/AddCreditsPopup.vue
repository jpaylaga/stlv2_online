<template>
    <div id="manage-credit">
        <loading :active.sync="isLoading"></loading>
        
        <vue-modal-2 
            name="modalAddCredit" 
            :headerOptions="{
                title: 'Add Credits To Player',
            }"
            fontLight
            :footerOptions="{
                btn1: 'Submit',
                btn2: 'Cancel',
                btn2Style: {
                    backgroundColor: 'red',
                },
                btn2OnClick: close,
                btn1OnClick: submit,
            }"
            @on-close="close">
            <div class="row container">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="label-control" for="amount">Amount</label>
                        <input type="text" class="form-control" placeholder="Amount" v-model="amount" required>
                    </div>
                    <span>{{errorText}}</span>
                </div>
            </div>
        </vue-modal-2>

        <vue-modal-2
            @on-close="$vm2.close('modalSuccess')"
            name="modalSuccess"
            :headerOptions="{
                title: 'Success!',
            }"
            noFooter
            >
            <div class="mb-4"> <center>Credit successfully added.</center> </div>
        </vue-modal-2>

    </div>
</template>
<script>
    import Loading from 'vue-loading-overlay';

    export default {
        props: {
            show: {
                type: Boolean,
                required: true,
            },
            user: {
                type: Number,
                required: true
            }
        },
        components: { Loading },
        data() {
            return {
                amount: 0,
                isLoading: false,
                errorText: ''
            }
        },
        watch: {
            show: {
                handler(val) {
                    if(val) {
                        this.openModal();
                    }
                }
            }
        },
        mounted () {
            
        },
        methods: {
            submit(){
                const self = this

                if( self.amount <= 0 ){
                    self.errorText = 'Please input amount.'
                    return false
                }
                self.errorText = ''

                let params = {
                    user: self.user,
                    credits: self.amount
                }

                self.isLoading = true
                axios.post('/api/credits/add', params).then(response => {
                    // this.$swal({ title: 'Success!', text: 'Credit successfully added.', type: 'success' });
                    this.openSuccessModal();
                    this.close();
                    self.isLoading = false;
                }).catch(error => {
                    let messages = Object.values(error.response.data.errors);
                    let errors = [].concat.apply([], messages);
                    self.$swal({ title: 'Error!', text: errors, type: 'error' });
                    self.isLoading = false;
                });
                
            },
            openSuccessModal(){
                this.$vm2.open('modalSuccess')
            },
            closeSuccessModal(){
                this.$vm2.close('modalSuccess')
            },
            openModal () {
                this.$vm2.open('modalAddCredit')
            },
            close () {
                this.amount = 0;
                // this.$vm2.close('modalAddCredit')
                this.$emit("callbackClosePopup")
            }
        }
    }
</script>
