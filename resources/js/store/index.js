import { createStore } from 'vuex';
import axios from 'axios';
import references from './modules/references';
import { USER_ROLES } from '../constants/types';
import bookings from './modules/bookings';
import cars from './modules/cars';
import dash from './modules/dash';

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default createStore({
    state: {
        user: null,
        token: token || null,
        isAdmin: false,
        isManager: false,
        isClient: false,
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
            state.isAdmin = user.role_id === USER_ROLES.ADMIN;
            state.isManager = user.role_id === USER_ROLES.MANAGER;
            state.isClient = user.role_id === USER_ROLES.CLIENT;
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
                    const errorMessage = error.response?.data?.message || 'Помилка реєстрації';
                    return Promise.reject(new Error(errorMessage));
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
                    const errorMessage = error.response?.data?.message || 'Помилка входу';
                    return Promise.reject(new Error(errorMessage));
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
        getUser({ commit, state }) {
            if (!state.token) {
                return Promise.reject(new Error('Немає токену авторизації'));
            }
            
            return axios.get('/api/auth/me')
                .then((response) => {
                    commit('setUser', response.data.user);
                    return response;
                })
                .catch((error) => {
                    if (error.response?.status === 401) {
                        commit('clearAuth');
                    }
                    return Promise.reject(error);
                });
        },
    },
    getters: {
        isAuthenticated: (state) => !!state.token,
        user: (state) => state.user,
    },
    modules: {
        references,
        bookings,
        cars,
        dash
    }
});
