<template>
    <DashboardLayout>
        <div class="services-container">
            <div class="top">
                <h1 class="title">
                    Всього <span class="count">{{ services.length }}</span>
                    {{ servicesWord }}
                </h1>

                <button class="create-service-btn" @click="openCreateModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Додати послугу</span>
                </button>
            </div>

            <ServicesTable
                :services="services"
                :loading="loading"
                @edit-service="editService"
            />

            <CreateServiceModal
                :isOpen="isModalOpen"
                :editingServiceId="editingServiceId"
                :services="services"
                @close="closeModal"
                @saved="onSaved"
            />
        </div>
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import { pluralize } from '../../../../../lib/utils';
import CreateServiceModal from '../../../../modals/CreateServiceModal.vue';
import ServicesTable from './ServicesTable.vue';

export default {
    name: 'Services',
    components: {
        DashboardLayout,
        CreateServiceModal,
        ServicesTable
    },
    data() {
        return {
            loading: false,
            isModalOpen: false,
            editingServiceId: null
        };
    },
    computed: {
        services() {
            return this.$store.state.services?.services || [];
        },
        servicesWord() {
            return pluralize(
                this.services.length,
                'послуга',
                'послуги',
                'послуг'
            );
        }
    },
    mounted() {
        if (!this.$store.state.isAdmin) {
            this.$router.push({ name: 'Dashboard' });
            return;
        }
        this.fetchServices();
    },
    methods: {
        fetchServices() {
            this.loading = true;
            this.$store.dispatch('services/fetchServices', { force: true })
                .then(() => {
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                    this.$message?.error?.('Помилка завантаження послуг');
                });
        },
        openCreateModal() {
            this.editingServiceId = null;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.editingServiceId = null;
        },
        editService(serviceId) {
            this.editingServiceId = serviceId;
            this.isModalOpen = true;
        },
        onSaved() {
            this.closeModal();
        }
    }
};
</script>
