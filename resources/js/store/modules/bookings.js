import axios from 'axios';

const state = {
    userBookings: [],
    upcomingBookings: [],
    loading: false,
    error: null
};

const mutations = {
    setUserBookings(state, bookings) {
        state.userBookings = bookings;
    },
    setUpcomingBookings(state, bookings) {
        state.upcomingBookings = bookings;
    }
};

const actions = {
    fetchUpcomingBookings({ commit }, { payload, force = false }) {
        if (!force && state.upcomingBookings.length > 0) {
            return Promise.resolve(state.upcomingBookings);
        }

        return axios.get(`/api/bookings/upcoming`, { params: payload })
            .then(response => {
                commit('setUpcomingBookings', response.data);
                return response.data;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    fetchUserBookings({ commit }, { clientId, force = false }) {
        if (!force && state.userBookings.length > 0) {
            return Promise.resolve(state.userBookings);
        }

        return axios.get(`/api/clients/${clientId}/bookings`)
            .then(response => {
                commit('setUserBookings', response.data);
                return response.data;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    getBooking({ }, bookingId) {
        return axios.get(`/api/bookings/${bookingId}`)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    createBooking({ dispatch }, payload) {
        return axios.post(`/api/bookings/${payload.client_id}/create`, payload.data)
            .then(async response => {
                await dispatch('fetchUserBookings', { clientId: payload.client_id, force: true });
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },

    updateBooking({ dispatch }, payload) {
        return axios.post(`/api/bookings/${payload.booking_id}/update`, payload.data)
            .then(async response => {
                await dispatch('fetchUserBookings', { clientId: payload.client_id, force: true });
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