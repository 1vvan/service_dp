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
    fetchUserBookings({ commit }, clientId, force = false) {
        if (!force && state.userBookings.length > 0) {
            return Promise.resolve(state.userBookings);
        }

        return axios.get(`/api/bookings/${clientId}`)
            .then(response => {
                commit('setUserBookings', response.data);
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    createBooking({ dispatch }, payload) {
        return axios.post(`/api/bookings/${payload.client_id}/create`, payload.data)
            .then(response => {
                dispatch('fetchUserBookings', payload.client_id, true);
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    calculatePrice({ commit }, payload) {
        return axios.post(`/api/bookings/calculate-price`, payload)
            .then(response => {
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    downloadReceipt({}, bookingId) {
        return axios.get(`/api/bookings/${bookingId}/receipt`, {
            responseType: 'blob'
        })
            .then(response => {
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