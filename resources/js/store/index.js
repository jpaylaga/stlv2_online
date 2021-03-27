import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: {}
    },
    mutations: {
        SET_USER(state, user){
            state.user = user;
        }
    },
    actions: {
        fetchUser(context){
            axios.get('/api/user').then(response => {
                context.commit('SET_USER', response.data);
            });
        }
    },
    getters: {
        getUser: state => {
            return state.user;
        },
    }
});