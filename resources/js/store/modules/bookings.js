import axios from 'axios';

const state = {
    userBookings: [],
    loading: false,
    error: null
};

const mutations = {
    setUserBookings(state, bookings) {
        state.userBookings = bookings;
    }
};

const actions = {
    fetchUserBookings({ commit }, userId) {
        return axios.get(`/api/bookings/${userId}`)
            .then(response => {
                commit('setUserBookings', response.data);
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