import axios from 'axios';

const state = {
    userCars: [],
    loading: false,
    error: null
};

const mutations = {
    setUserCars(state, cars) {
        state.userCars = cars;
    }
};

const actions = {
    fetchUserCars({ commit }, clientId, force = false) {
        if (!force && state.userCars.length > 0) {
            return Promise.resolve(state.userCars);
        }

        return axios.get(`/api/cars/${clientId}`)
            .then(response => {
                commit('setUserCars', response.data);
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    createCar({ dispatch }, payload) {
        return axios.post(`/api/cars/${payload.client_id}/create`, payload.data)
            .then(response => {
                dispatch('fetchUserCars', payload.client_id, true);
                return response;
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