<template>
    <DashboardLayout>
        <div class="cars-container">
            <div class="top">
                <h1 class="title">Ви маєте <span class="count">{{ cars.length }}</span> автомобілів</h1>

                <button class="create-car-btn" @click="openCreateCarModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Додати автомобіль</span>
                </button>
            </div>
            <CarsTable :cars="cars" />
        </div>

        <CreateCarModal :isOpen="isCreateCarModalOpen" @close="closeCreateCarModal" />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import CreateCarModal from '../../../../modals/CreateCarModal.vue';
import CarsTable from './CarsTable.vue';

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
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        cars() {
            return this.$store.state.cars.userCars || [];
        }
    },
    mounted() {
        this.getCars();
    },
    methods: {
        getCars() {
            this.$store.dispatch('cars/fetchUserCars', this.user.id);
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

