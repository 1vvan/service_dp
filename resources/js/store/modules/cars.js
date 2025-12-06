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
    fetchUserCars({ commit }, clientId) {
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
                dispatch('fetchUserCars', payload.client_id);
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