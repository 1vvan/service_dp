import axios from 'axios';

const state = {
    currentClient: null,
    loading: false,
    error: null
};

const mutations = {
    setCurrentClient(state, client) {
        state.currentClient = client;
    },
    setLoading(state, loading) {
        state.loading = loading;
    },
    setError(state, error) {
        state.error = error;
    }
};

const actions = {
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

