<template>
    <DashboardLayout>
        <div class="cars-container">
            <div class="top">
                <h1 class="title">Всього зареєстровано <span class="count">{{ cars.length }}</span> {{ carsWord }}</h1>

                <button class="create-car-btn" @click="openCreateCarModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Додати автомобіль</span>
                </button>
            </div>
            <CarsTable :cars="cars" :loading="loading" />
        </div>

        <CreateCarModal :isOpen="isCreateCarModalOpen" @close="closeCreateCarModal" managerMode :editingCarId="editingCarId" />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import { pluralize } from '../../../../../lib/utils';
import CreateCarModal from '../../../../modals/CreateCarModal.vue';
import CarsTable from '../cars/CarsTable.vue';

export default {
    name: 'ClientCars',
    components: {
        DashboardLayout,
        CarsTable,
        CreateCarModal
    },
    data() {
        return {
            isCreateCarModalOpen: false,
            loading: true,
            editingCarId: null,
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        cars() {
            return this.$store.state.cars.clientCars || [];
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
            this.$store.dispatch('cars/fetchClientCars', { payload: {} })
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
            this.editingCarId = null;
        },
    }
};
</script>

