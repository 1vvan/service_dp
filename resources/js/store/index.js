import { createStore } from 'vuex';
import axios from 'axios';

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default createStore({
    state: {
        user: null,
        token: token || null,
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setToken(state, token) {
            state.token = token;
            if (token) {
                localStorage.setItem('token', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            } else {
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },
        clearAuth(state) {
            state.user = null;
            state.token = null;
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        },
    },
    actions: {
        register({ commit }, payload) {
            return axios.post('/api/auth/register', payload)
                .then((response) => {
                    commit('setToken', response.data.token);
                    commit('setUser', response.data.user);
                    return response;
                })
                .catch((error) => {
                    return Promise.reject(error);
                });
        },
        login({ commit }, payload) {
            return axios.post('/api/auth/login', payload)
                .then((response) => {
                    commit('setToken', response.data.token);
                    commit('setUser', response.data.user);
                    return response;
                })
                .catch((error) => {
                    return Promise.reject(error);
                });
        },
        logout({ commit }) {
            return axios.post('/api/auth/logout')
                .then((response) => {
                    commit('clearAuth');
                    return response;
                })
                .catch((error) => {
                    return Promise.reject(error);
                });
        },
        getUser({ commit }) {
            return axios.get('/api/auth/me')
                .then((response) => {
                    commit('setUser', response.data.user);
                    return response;
                })
                .catch((error) => {
                    return Promise.reject(error);
                });
        },
    },
    getters: {
        isAuthenticated: (state) => !!state.token,
        user: (state) => state.user,
    },
    modules: {}
});
