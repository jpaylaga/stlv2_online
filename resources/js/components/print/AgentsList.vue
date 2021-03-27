<template>
    <div>
        <br><br>
        <div class="row">
            <print-heading></print-heading>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <br><br>
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;">
                    <strong>Area:</strong> {{coordinator.area_details.name}}
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;">
                    <strong>Coordinator:</strong> {{coordinator.firstname+' '+coordinator.lastname}}
                </p>  
                <p style="margin-bottom: 0; font-size: 20px; margin-right: 25px;"><strong>Date:</strong> 
                    <span>{{date | bet_date}}</span>
                </p>  
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="table-responsive">
                    <table class="table text-left">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="agent in agents" :key="agent.id">
                               <td>{{agent.firstname+' '+agent.lastname}}</td>
                               <td>{{agent.address}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Heading from "./partials/Heading.vue";
    export default {
        props: ['coordinator', 'date', 'agents'],
        components: { 'print-heading': Heading },
        created() {
            // this.fetchData(this.date);
        },
    }
</script>
