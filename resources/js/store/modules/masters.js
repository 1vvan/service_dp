import axios from 'axios';

const state = {
    masters: [],
};

const mutations = {
    setMasters(state, list) {
        state.masters = list;
    },
};

const actions = {
    fetchMasters({ commit, state }, { force = false } = {}) {
        if (!force && state.masters.length > 0) {
            return Promise.resolve(state.masters);
        }

        return axios.get('/api/masters').then((response) => {
            commit('setMasters', response.data);
            return response.data;
        });
    },

    checkMasterAvailability(context, payload) {
        return axios.post('/api/masters/check-availability', payload).then((r) => r.data);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
