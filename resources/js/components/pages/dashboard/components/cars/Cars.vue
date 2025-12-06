<template>
    <DashboardLayout>
        <div class="cars-container">
            <div class="top">
                <h1 class="title">Ви маєте <span class="count">{{ cars.length }}</span> {{ carsWord }}</h1>

                <button class="create-car-btn" @click="openCreateCarModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Додати автомобіль</span>
                </button>
            </div>
            <CarsTable :cars="cars" :loading="loading" />
        </div>

        <CreateCarModal :isOpen="isCreateCarModalOpen" @close="closeCreateCarModal" />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import CreateCarModal from '../../../../modals/CreateCarModal.vue';
import CarsTable from './CarsTable.vue';
import { pluralize } from '../../../../../lib/utils.js';

export default {
    name: 'Cars',
    components: {
        DashboardLayout,
        CarsTable,
        CreateCarModal
    },
    data() {
        return {
            isCreateCarModalOpen: false,
            loading: true,
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        cars() {
            return this.$store.state.cars.userCars || [];
        },
        carsWord() {
            return pluralize(
                this.cars.length,
                'автомобіль',
                'автомобілі',
                'автомобілів'
            );
        }
    },
    mounted() {
        this.getCars();
    },
    methods: {
        getCars() {
            this.loading = true;
            this.$store.dispatch('cars/fetchUserCars', this.user.client_id)
                .then(() => {
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        openCreateCarModal() {
            this.isCreateCarModalOpen = true;
        },
        closeCreateCarModal() {
            this.isCreateCarModalOpen = false;
        }
    }
};
</script>

