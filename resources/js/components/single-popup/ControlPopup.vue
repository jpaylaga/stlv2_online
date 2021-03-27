<template>
    <div style="display: inline-block;">
        <button @click="previewTabulation" class="btn btn-outline-primary round">
            <i class="ft-file-text mr-2"></i>Preview
        </button>
    </div>
</template>
<script>
    export default {
        props: ['draw_time','control_items','control'],
        data(){
            return {
                counters: [1,2,3,4,5,6,7,8,9,0,'R']
            }
        },
        created () {

        },
        methods: {
            previewTabulation(ticket_id){
                this.isLoading = true;
                let _this = this;
                let total_amount = _.sumBy(_this.control_items, function(o) { return parseInt(o.amount); });
                let html = '';

                html += '<div class="tab-total">';
                html += '<p class="inline-block"><strong>Draw Time:</strong> '+this.$options.filters.draw_time(_this.draw_time)+'</p>&nbsp;|&nbsp;';
                html += '<p class="inline-block"><strong>Total Amount:</strong> '+this.$options.filters.currency(total_amount, '&#8369;')+'</p><br>';
                html += '</div>';

                html += '<div class="tabulation">';
                html += '<div class="tab-wrapper">';
                html += '<div class="row">';

                _.forEach(_this.counters, function(c,key) {
                    html += '<div class="col-md-1 col-sm-1 col-xs-1 tab-col">';
                        html += '<div class="row col-headers">';
                        html += '<div class="col-md-6 col-sm-6 col-xs-6"><strong>'+c+'</strong></div>';
                        html += '<div class="col-md-6 col-sm-6 col-xs-6"></div>';
                        html += '</div>';

                    _.forEach(_this.getColItems(c), function(item,key) {
                            html += '<div class="row">';
                            html += '<div class="combis col-md-6 col-sm-6 col-xs-6">'+item.combi+'</div>';
                            html += '<div class="amount col-md-6 col-sm-6 col-xs-6">'+item.amount+'</div>';
                            html += '</div>';
                    });

                    html += '</div>';
                });

                html += '</div>'; //@end row
                html += '</div>'; //@end wrapper
                html += '</div>'; //@end tabulation

                this.$swal({
                    customClass: { container: 'popup-control' },
                    title: 'Tabulation',
                    html: html,
                    showConfirmButton: false, 
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                });
         
            },
            getColItems(num){
                let combis = [];
                if( num == 'R' ){
                    combis = _.filter(this.control_items, function(o) { return o.type == 'ramble'; });
                }else{
                    combis = _.filter(this.control_items, function(o) { return String( o.combi ).substring(0,1) == num && o.type=='target'; });
                }

                return _.orderBy(combis, 'amount', 'desc');
            },
            removeItem(combi){
                console.log(combi);
            }
        }
    }
</script>
