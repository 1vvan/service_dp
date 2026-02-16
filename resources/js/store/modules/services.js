import axios from 'axios';

const state = {
    services: [],
    loading: false
};

const mutations = {
    setServices(state, services) {
        state.services = services;
    }
};

const actions = {
    fetchServices({ commit }, { force = false } = {}) {
        if (!force && state.services.length > 0) {
            return Promise.resolve(state.services);
        }

        return axios.get('/api/services')
            .then(response => {
                commit('setServices', response.data);
                return response.data;
            })
            .catch(error => Promise.reject(error));
    },
    createService({ dispatch }, data) {
        return axios.post('/api/services', data)
            .then(response => {
                dispatch('fetchServices', { force: true });
                dispatch('references/fetchServices', null, { root: true });
                return response.data;
            })
            .catch(error => Promise.reject(error));
    },
    updateService({ dispatch }, { id, data }) {
        return axios.put(`/api/services/${id}`, data)
            .then(response => {
                dispatch('fetchServices', { force: true });
                dispatch('references/fetchServices', null, { root: true });
                return response.data;
            })
            .catch(error => Promise.reject(error));
    },
    deleteService({ dispatch }, id) {
        return axios.delete(`/api/services/${id}`)
            .then(response => {
                dispatch('fetchServices', { force: true });
                dispatch('references/fetchServices', null, { root: true });
                return response.data;
            })
            .catch(error => Promise.reject(error));
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
