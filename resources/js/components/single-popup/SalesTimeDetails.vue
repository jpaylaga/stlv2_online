<template>
    <div id="user-profile">
        <a href="#" 
            @click.prevent="viewDetails(coor)"
            class="btn btn-flat btn-success m-0 px-1" 
            data-toggle="tooltip" 
            data-placement="top" 
            title="View Details by Draw Time" data-trigger="hover"> 
                <i class="ft-file-text font-medium-3"></i>
        </a>
    </div>
</template>
<script>
    import moment from 'moment';

    export default {
        props: ['coor','date','show_cancel'],
        created () {

        },
        methods: {
            viewDetails(coor){
                this.isLoading = true;
                let date = moment(this.date[0]).format('MM/DD/YYYY');
                let date_to = moment(this.date[1]).format('MM/DD/YYYY');

                axios.get('/api/reports/coordinators-sales-detailed', {
                    params: { coor: coor.id, date: date, date_to: date_to }
                }).then(response => {
                    let details = response.data;

                    let html = '';

                    html += '<div class="ticket">';
                    html += '<p><strong>Coordinator: </strong> '+this.$options.filters.uppercase(coor.firstname+' '+coor.lastname)+'</p>';
                    html += '<p><strong>Area: </strong> '+this.$options.filters.uppercase(coor.area)+'</p>';
                    html += '<p><strong>Percentage: </strong> '+this.$options.filters.uppercase(coor.percentage)+'%</p>';
                    html += '<p><strong>Float: </strong> '+this.$options.filters.uppercase(coor.float)+'</p>';
                    html += '<p><strong>Sales Date Range: </strong> '+this.$options.filters.draw_date(this.date[0])+' — ';
                    html += this.$options.filters.draw_date(this.date[1])+'</p>';
                    html += '</p>';
                    html += '</div>';

                    html += '<table class="table text-left">';
                    html += '<tr>';
                    html += '<th>Daily Overview</th>';
                    html += '<th>11:00AM</th>';
                    html += '<th>4:00PM</th>';
                    html += '<th>9:00PM</th>';
                    html += '<th>Overall</th>';
                    html += '</tr>';

                    html += '<tbody>';
                    html += '<tr>';
                    html += '<th>Total Gross</th>';
                    html += '<td>'+this.currencize(details.gross_sales['11am'])+'</td>';
                    html += '<td>'+this.currencize(details.gross_sales['4pm'])+'</td>';
                    html += '<td>'+this.currencize(details.gross_sales['9pm'])+'</td>';
                    html += '<th>'+this.currencize(this.getTotalGross(details.gross_sales))+'</th>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<th>Total Commission</th>';
                    html += '<td>'+this.currencize( this.getCommission(details.gross_sales['11am'],coor.percentage) )+'</td>';
                    html += '<td>'+this.currencize( this.getCommission(details.gross_sales['4pm'],coor.percentage) )+'</td>';
                    html += '<td>'+this.currencize( this.getCommission(details.gross_sales['9pm'],coor.percentage) )+'</td>';
                    html += '<th>'+this.currencize( this.getCommission( this.getTotalGross(details.gross_sales),coor.percentage ) )+'</th>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<th>Total Hit</th>';
                    html += '<td>'+this.currencize( details.winnings['11am'] + (coor.float*details.hits['11am']) )+'('+(Math.round(details.hits['11am'] * 10) / 10)+')</td>';
                    html += '<td>'+this.currencize( details.winnings['4pm'] + (coor.float*details.hits['4pm']) )+'('+(Math.round(details.hits['4pm'] * 10) / 10)+')</td>';
                    html += '<td>'+this.currencize( details.winnings['9pm'] + (coor.float*details.hits['9pm']) )+'('+(Math.round(details.hits['9pm'] * 10) / 10)+')</td>';
                    html += '<th>'+this.currencize( this.getTotalWinningsWithFloat(details) )+'</th>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<th>Total Profit</th>';
                    html += '<td>'+this.currencize( this.getProfit(details, coor.percentage, '11am') )+'</td>';
                    html += '<td>'+this.currencize( this.getProfit(details, coor.percentage, '4pm') )+'</td>';
                    html += '<td>'+this.currencize( this.getProfit(details, coor.percentage, '9pm') )+'</td>';
                    html += '<th>'+this.currencize( this.getProfit(details, coor.percentage, '') )+'</th>';
                    html += '</tr>';
                    html += '</tbody>';
                    html += '</table>';

                    this.isLoading = false;
                    this.$swal({
                        customClass: { container: 'popup-profile', popup: 'width-45' },
                        title: 'Sales Details',
                        html: html,
                    }).then(result => {

                    });
                })
            },
            currencize(amount){
                return this.$options.filters.currency(amount, '&#8369;');
            },
            getTotalGross(sales){
                return sales['11am'] + sales['4pm'] + sales['9pm'];
            },
            getTotalWinnings(winnings){
                return winnings['11am'] + winnings['4pm'] + winnings['9pm'];
            },
            getTotalWinningsWithFloat(details){
                return this.getTotalWinnings(details.winnings) + (this.getTotalHits(details.hits) * this.coor.float);
            },
            getTotalHits(hits){
                return hits['11am'] + hits['4pm'] + hits['9pm'];
            },
            getCommission(sales, percentage){
                return  sales * (percentage / 100);
            },
            getProfit(details, percentage, draw_time){
                if( draw_time ){
                    let hits = details.winnings[draw_time] + (details.hits[draw_time] * this.coor.float);
                    return details.gross_sales[draw_time] * ( (100 - percentage) / 100) - hits;
                }else{
                    let hits = this.getTotalWinningsWithFloat(details);
                    return this.getTotalGross(details.gross_sales) * ( (100 - percentage) / 100) - hits;
                }
            },
        }
    }
</script>
