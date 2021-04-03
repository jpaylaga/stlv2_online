import moment from 'moment';
export default {
    methods: {
        $init() {
            if( !this.$isLoggedIn() ){
                this.fetchUser();
            }
        },
        $isLoggedIn() {
            let user = this.$cookies.get(this.COOKIE_PREFIX + "user");
            return Boolean(user);
        },
        $getUser() {
            this.fetchUser();
            return this.$cookies.get(this.COOKIE_PREFIX + "user");
        },
        $is(value = '') {
            if(value) {
                let role = this.$cookies.get(`${this.COOKIE_PREFIX}user`).role
                if(role) {
                    if(role == 'super-admin' || role == value) {
                        return true;
                    }
                }
            }

            return false;
        },
        $isOwn(user_id) {
            let current_user_id = this.$cookies.get(this.COOKIE_PREFIX + "user").id;
            if( current_user_id == user_id ) return true;
            return false
        },
        $isCoordinator() {
            let role = this.$cookies.get(`${this.COOKIE_PREFIX}user`).role
            if(role == 'coordinator') return true
            return false
        },
        $isAgent() {
            let role = this.$cookies.get(`${this.COOKIE_PREFIX}user`).role
            if(role == 'teller' || role == 'player') return true
            return false
        },
        $isMobile() {
            if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
              return true
            } else {
              return false
            }
        },

        $getDrawTimeOptions(key = null){
            let data = [
                { id: this.DRAWTIME_MORNING, actual_time: 14, name: moment(14, "HH").format("hA"), moment_stamp: moment(14, "HH:mm") },
                { id: this.DRAWTIME_AFTERNOON, actual_time: 17, name: moment(17, "HH").format("hA"), moment_stamp: moment(17, "HH:mm") },
                { id: this.DRAWTIME_EVENING, actual_time: 21, name: moment(this.DRAWTIME_EVENING, "HH").format("hA"), moment_stamp: moment(this.DRAWTIME_EVENING, "HH:mm") },
            ]

            if (key) return this.$dd_return_text(data, key);

            return data;
        },

        $getOutletOptions(key = null, return_logo = false){
            let data = [
                { id: 'gcash', name: 'GCash', logo: 'gcash.jpg' },
                { id: 'ml', name: 'MLhuillier', logo: 'ml.jpg' },
                { id: 'cebuana', name: 'Cebuana', logo: 'cebuana.jpg' },
            ]
            if (return_logo){
                let item = data.find(i => i.id === key);
                return item ? item.logo : '';
            }
            if (key) return this.$dd_return_text(data, key);
            return data;
        },

        $getWithdrawalOutletOptions(key = null, return_object = false){
            let data = [
                { id: 'gcash', name: 'GCash', min: 500, max: 25000 },
                { id: 'ml', name: 'MLhuillier', min: 100, max: 10000 },
                { id: 'cebuana', name: 'Cebuana', min: 100, max: 10000 },
            ]
            if (return_object) return this.$dd_return_object(data, key); 
            if (key) return this.$dd_return_text(data, key);
            return data;
        },

        $dd_return_text: function(data, key) {
            let item = data.find(i => i.id === key);
            return item ? item.name : '';
        },

        $dd_return_object: function(data, key) {
            let item = data.find(i => i.id === key);
            return item
        },

        // helpers
        async fetchUser(){
            await axios.get('/api/user').then(response => {
                this.$cookies.remove(`${this.COOKIE_PREFIX}user`);
                this.$cookies.set(`${this.COOKIE_PREFIX}user`, response.data)
            });
        }
    }
};