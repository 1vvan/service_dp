import axios from 'axios';

const state = {
    stats: [],
    managerStats: [],
    loading: false,
    error: null
};

const mutations = {
    setStats(state, stats) {
        state.stats = stats;
    },
    setManagerStats(state, stats) {
        state.managerStats = stats;
    }
};

const actions = {
    fetchStats({ commit }, clientId, force = false) {
        if (!force && state.stats.length > 0) {
            return Promise.resolve(state.stats);
        }

        return axios.get(`/api/dashboard/stats/${clientId}`)
            .then(response => {
                commit('setStats', response.data.stats);
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    fetchManagerStats({ commit }, force = false) {
        if (!force && state.managerStats.length > 0) {
            return Promise.resolve(state.managerStats);
        }

        return axios.get(`/api/dashboard/manager-stats`)
            .then(response => {
                commit('setManagerStats', response.data.stats);
            })
            .catch(error => {
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