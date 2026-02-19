import axios from 'axios';

const state = {
    currentClient: null,
    clientsList: [],
    loading: false,
    error: null
};

const mutations = {
    setCurrentClient(state, client) {
        state.currentClient = client;
    },
    setClientsList(state, clients) {
        state.clientsList = clients;
    },
    setLoading(state, loading) {
        state.loading = loading;
    },
    setError(state, error) {
        state.error = error;
    }
};

const actions = {
    fetchClients({ commit }, params = {}) {
        return axios.get('/api/clients', { params })
            .then(response => {
                commit('setClientsList', response.data);
                return response.data;
            })
            .catch(error => Promise.reject(error));
    },
    createClient({ dispatch }, data) {
        return axios.post('/api/clients', data)
            .then(response => {
                dispatch('fetchClients', {});
                return response.data;
            })
            .catch(error => Promise.reject(error));
    },
    deleteClient({ dispatch }, clientId) {
        return axios.delete(`/api/clients/${clientId}`)
            .then(() => {
                dispatch('fetchClients', {});
            })
            .catch(error => Promise.reject(error));
    },
    fetchClient({ commit }, clientId) {
        commit('setLoading', true);
        return axios.get(`/api/clients/${clientId}`)
            .then(response => {
                commit('setCurrentClient', response.data);
                commit('setLoading', false);
                return response.data;
            })
            .catch(error => {
                commit('setError', error);
                commit('setLoading', false);
                return Promise.reject(error);
            });
    },

    updateClient({ commit, state }, { clientId, data }) {
        commit('setLoading', true);
        return axios.put(`/api/clients/${clientId}`, data)
            .then(response => {
                commit('setCurrentClient', response.data);
                commit('setLoading', false);
                return response.data;
            })
            .catch(error => {
                commit('setError', error);
                commit('setLoading', false);
                return Promise.reject(error);
            });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};

