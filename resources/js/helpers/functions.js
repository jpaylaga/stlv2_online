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
                { id: this.DRAWTIME_MORNING, name: moment(this.DRAWTIME_MORNING, "HH").format("hA"), moment_stamp: moment(this.DRAWTIME_MORNING, "HH:mm") },
                { id: this.DRAWTIME_AFTERNOON, name: moment(this.DRAWTIME_AFTERNOON, "HH").format("hA"), moment_stamp: moment(this.DRAWTIME_AFTERNOON, "HH:mm") },
                { id: this.DRAWTIME_EVENING, name: moment(this.DRAWTIME_EVENING, "HH").format("hA"), moment_stamp: moment(this.DRAWTIME_EVENING, "HH:mm") },
            ]

            if (key) {
                return this.$dd_return_text(data, key);
            }

            return data;
        },

        $dd_return_text: function(data, key) {
            let item = data.find(i => i.id === key);
            return item ? item.name : '';
        },

        // helpers
        async fetchUser(){
            await axios.get('/api/user').then(response => {
                this.$cookies.set(`${this.COOKIE_PREFIX}user`, response.data)
            });
        }
    }
};