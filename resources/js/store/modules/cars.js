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
    fetchUserCars({ commit }, userId) {
        return axios.get(`/api/cars/${userId}`)
            .then(response => {
                commit('setUserCars', response.data);
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