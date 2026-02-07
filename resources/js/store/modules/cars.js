import axios from 'axios';

const state = {
    userCars: [],
    clientCars: [],
    loading: false,
    error: null
};

const mutations = {
    setUserCars(state, cars) {
        state.userCars = cars;
    },
    setClientCars(state, cars) {
        state.clientCars = cars;
    }
};

const actions = {
    fetchUserCars({ commit }, { clientId, force = false }) {
        if (!force && state.userCars.length > 0) {
            return Promise.resolve(state.userCars);
        }

        return axios.get(`/api/clients/${clientId}/cars`)
            .then(response => {
                commit('setUserCars', response.data);
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    fetchClientCars({ commit }, { payload = {}, force = false }) {
        if (!force && state.clientCars.length > 0) {
            return Promise.resolve(state.clientCars);
        }

        return axios.get(`/api/cars`, { params: payload })
            .then(response => {
                commit('setClientCars', response.data);
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    getCar({ }, carId) {
        return axios.get(`/api/cars/${carId}`)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    createCar({ dispatch }, payload) {
        return axios.post(`/api/cars/${payload.client_id}/create`, payload.data)
            .then(response => {
                if (payload.managerMode) {
                    dispatch('fetchClientCars', { payload: {}, force: true });
                } else {
                    dispatch('fetchUserCars', { clientId: payload.client_id, force: true });
                }
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    updateCar({ dispatch }, payload) {
        return axios.post(`/api/cars/${payload.car_id}/update`, payload.data)
            .then(response => {
                if (payload.managerMode) {
                    dispatch('fetchClientCars', { payload: {}, force: true });
                } else {
                    dispatch('fetchUserCars', { clientId: payload.client_id, force: true });
                }
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    confirmCar({ dispatch }, carId) {
        return axios.post(`/api/cars/${carId}/confirm`)
            .then(response => {
                dispatch('fetchClientCars', { payload: {}, force: true });
                return response;
            })
            .catch(error => {
                return Promise.reject(error);
            });
    },
    uploadCarPhoto({ }, { carId, file }) {
        const formData = new FormData();
        formData.append('photo', file);
        return axios.post(`/api/cars/${carId}/photos`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        }).then(response => response.data);
    },
    deleteCarPhoto({ }, { carId, photoId }) {
        return axios.delete(`/api/cars/${carId}/photos/${photoId}`);
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};