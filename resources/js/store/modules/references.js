import axios from 'axios';

const state = {
    carBrands: [],
    carModels: [],
    fuelTypes: [],
    engineTypes: [],
    gearboxTypes: [],
    driveUnitTypes: [],
    bookingStatuses: [],
    services: [],
    loading: false,
    error: null
};

const mutations = {
    SET_CAR_BRANDS(state, brands) {
        state.carBrands = brands;
    },
    SET_CAR_MODELS(state, models) {
        state.carModels = models;
    },
    SET_FUEL_TYPES(state, types) {
        state.fuelTypes = types;
    },
    SET_ENGINE_TYPES(state, types) {
        state.engineTypes = types;
    },
    SET_GEARBOX_TYPES(state, types) {
        state.gearboxTypes = types;
    },
    SET_DRIVE_UNIT_TYPES(state, types) {
        state.driveUnitTypes = types;
    },
    SET_BOOKING_STATUSES(state, statuses) {
        state.bookingStatuses = statuses;
    },
    SET_SERVICES(state, services) {
        state.services = services;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    SET_ERROR(state, error) {
        state.error = error;
    }
};

const actions = {
    async fetchCarBrands({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/car-brands');
            commit('SET_CAR_BRANDS', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження марок автомобілів');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchCarModels({ commit }, brandId = null) {
        try {
            commit('SET_LOADING', true);
            const url = brandId 
                ? `/api/references/car-models/${brandId}`
                : '/api/references/car-models';
            const response = await axios.get(url);
            commit('SET_CAR_MODELS', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження моделей автомобілів');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchFuelTypes({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/fuel-types');
            commit('SET_FUEL_TYPES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження типів палива');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchEngineTypes({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/engine-types');
            commit('SET_ENGINE_TYPES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження типів двигунів');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchGearboxTypes({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/gearbox-types');
            commit('SET_GEARBOX_TYPES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження типів коробок передач');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchDriveUnitTypes({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/drive-unit-types');
            commit('SET_DRIVE_UNIT_TYPES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження типів приводу');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchBookingStatuses({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/booking-statuses');
            commit('SET_BOOKING_STATUSES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження статусів бронювань');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchServices({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/api/references/services');
            commit('SET_SERVICES', response.data);
            commit('SET_ERROR', null);
        } catch (error) {
            commit('SET_ERROR', error.response?.data?.message || 'Помилка завантаження послуг');
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchAllReferences({ dispatch }) {
        await Promise.all([
            dispatch('fetchCarBrands'),
            dispatch('fetchFuelTypes'),
            dispatch('fetchEngineTypes'),
            dispatch('fetchGearboxTypes'),
            dispatch('fetchDriveUnitTypes'),
            dispatch('fetchBookingStatuses'),
            dispatch('fetchServices')
        ]);
    }
};

const getters = {
    carBrands: (state) => state.carBrands,
    carModels: (state) => state.carModels,
    fuelTypes: (state) => state.fuelTypes,
    engineTypes: (state) => state.engineTypes,
    gearboxTypes: (state) => state.gearboxTypes,
    driveUnitTypes: (state) => state.driveUnitTypes,
    bookingStatuses: (state) => state.bookingStatuses,
    services: (state) => state.services,
    loading: (state) => state.loading,
    error: (state) => state.error
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};

