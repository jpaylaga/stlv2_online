/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// https: //www.npmjs.com/package/vue-simple-progress
// import ProgressBar from 'vue-simple-progress';
// Vue.use(ProgressBar);

// https://www.npmjs.com/package/vue2-filters
import Vue2Filters from 'vue2-filters'
Vue.use(Vue2Filters)

import Toasted from 'vue-toasted';
Vue.use(Toasted, {
    duration: 3000,
    keepOnHover: true,
    theme: 'bubble',
});

// https: //www.npmjs.com/package/vue-sweetalert2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);

// https://www.npmjs.com/package/vue-html-to-paper
import VueHtmlToPaper from 'vue-html-to-paper';
const options = {
  name: '_blank',
  specs: [
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
  ]
}
Vue.use(VueHtmlToPaper, options);

// https://onewaytech.github.io/vue2-datatable/doc
import Datatable from 'vue2-datatable-component';
Vue.use(Datatable)

import Modal from "@burhanahmeed/vue-modal-2";
Vue.use(Modal);

// Custom Filters
import moment from 'moment';

Vue.filter('bet_date', function (date) {
    return moment(date).format('ddd, MMM D YYYY, h:mm:ss A');
});
Vue.filter('draw_date', function (date) {
    return moment(date).format('ddd, MMM D YYYY');
});
Vue.filter('date_time', function (date) {
    return moment(date).format('MM/D/YYYY, h:mm:ss A');
});
Vue.filter('draw_time', function (time) {
    switch (time) {
        case '11':
            return '11AM'
        case '16':
            return '4PM'
        case '21':
            return '9PM'
        default:
            return 'All'
    }
});
Vue.filter('valid_draw_time', function (time) {
    if( time < 11 )
        return '11';
    else if( time < 16 )
        return '16';
    else
        return '21';
});
Vue.filter('user_role', function (role) {
    switch (role) {
        case 'super_admin':
            return 'Super Admin'
        case 'area_admin':
            return 'Area Admin'
        case 'coordinator':
            return 'Sales Supervisor'
        case 'usher':
            return 'Usher'
        case 'teller':
            return 'Teller'
        default:
            return ''
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('dashboard', require('./components/DashboardComponent.vue').default);
// Vue.component('users', require('./components/UsersComponent.vue').default);
Vue.component('coordinators', require('./components/CoordinatorsComponent.vue').default);
Vue.component('tellers', require('./components/TellersComponent.vue').default);
Vue.component('area-admins', require('./components/AreaAdminsComponent.vue').default);
Vue.component('players', require('./components/PlayersComponent.vue').default);
Vue.component('profile', require('./components/ProfileComponent.vue').default);
Vue.component('area-settings', require('./components/AreaSettingsComponent.vue').default);
Vue.component('transactions', require('./components/TransactionsComponent.vue').default);
Vue.component('cancelled-tickets', require('./components/CancelledTicketsComponent.vue').default);
Vue.component('bets', require('./components/BetsComponent.vue').default);
Vue.component('draw-results', require('./components/DrawResultsComponent.vue').default);
Vue.component('winners', require('./components/WinnersComponent.vue').default);
Vue.component('payouts', require('./components/PayoutsComponent.vue').default);
Vue.component('summary-sales', require('./components/SummarySalesComponent.vue').default);
Vue.component('summary-reports', require('./components/SummaryReportsComponent.vue').default);
Vue.component('highest-bet', require('./components/HighestBetComponent.vue').default);
Vue.component('hot-numbers', require('./components/HotNumbersComponent.vue').default);
Vue.component('reports-coordinators', require('./components/ReportsCoordinatorsComponent.vue').default);
Vue.component('area-summary', require('./components/AreaSummaryComponent.vue').default);

// settings
Vue.component('bet-limits', require('./components/settings/BetLimitsComponent.vue').default);
Vue.component('payout-rates', require('./components/settings/PayoutRatesComponent.vue').default);
Vue.component('outlets', require('./components/settings/OutletsComponent.vue').default);

// credit
Vue.component('credits', require('./components/credit/CreditsComponent.vue').default);
Vue.component('credit-requests', require('./components/credit/CreditRequestsComponent.vue').default);

// Super Admin
Vue.component('manage-credits', require('./components/CreditsComponent.vue').default);

// Players
Vue.component('players-dashboard', require('./components/role-player/DashboardComponent.vue').default);
Vue.component('games', require('./components/role-player/GamesComponent.vue').default);
Vue.component('stl', require('./components/role-player/PlayStlComponent.vue').default);

// Vue.component('area-summary', require('./components/super-admin/GamesComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// require
Vue.use(require('vue-cookies'))

import { constants } from "./config/constants";

import store from './store';
import functions from "./helpers/functions";

Vue.mixin(functions);
Vue.mixin({
    data: function() {
        return constants;
    }
});

const app = new Vue({
    store,
    el: '#app',
    created() {
        this.$store.dispatch('fetchUser');
        this.$init();
    }
});
