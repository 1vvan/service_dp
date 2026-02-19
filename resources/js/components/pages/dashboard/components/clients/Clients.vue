<template>
    <DashboardLayout>
        <div class="clients-container">
            <div class="top">
                <h1 class="title">Всього зареєстровано <span class="count">{{ clients.length }}</span> клієнтів</h1>

                <button class="create-client-btn" @click="openCreateClientModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Додати клієнта</span>
                </button>
            </div>

            <div class="search-row">
                <el-input
                    v-model="searchQuery"
                    placeholder="Пошук за ПІБ, телефоном, email або авто (номер, VIN)"
                    clearable
                    class="search-input"
                    @input="onSearchInput"
                >
                    <template #prefix>
                        <el-icon><Search /></el-icon>
                    </template>
                </el-input>
            </div>

            <ClientsTable
                :clients="clients"
                :loading="loading"
                @view-client="viewClient"
                @delete-client="handleDeleteClient"
            />

            <CreateClientModal
                :isOpen="isCreateClientModalOpen"
                @close="closeCreateClientModal"
                @saved="onClientSaved"
            />
        </div>
    </DashboardLayout>
</template>

<script>
import { ElMessage, ElMessageBox } from 'element-plus';
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import CreateClientModal from '../../../../modals/CreateClientModal.vue';
import ClientsTable from './ClientsTable.vue';
import { USER_ROLES } from '../../../../../constants/types';

export default {
    name: 'Clients',
    components: {
        DashboardLayout,
        CreateClientModal,
        ClientsTable
    },
    data() {
        return {
            loading: false,
            searchQuery: '',
            searchDebounce: null,
            isCreateClientModalOpen: false
        };
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        isManagerOrAdmin() {
            if (!this.user) return false;
            return this.user.role_id === USER_ROLES.MANAGER || this.user.role_id === USER_ROLES.ADMIN;
        },
        clients() {
            return this.$store.state.clients?.clientsList ?? [];
        }
    },
    mounted() {
        if (!this.isManagerOrAdmin) {
            this.$router.push({ name: 'Dashboard' });
            return;
        }
        this.fetchClients();
    },
    methods: {
        onSearchInput() {
            if (this.searchDebounce) clearTimeout(this.searchDebounce);
            this.searchDebounce = setTimeout(() => {
                this.fetchClients();
            }, 300);
        },
        async fetchClients() {
            this.loading = true;
            try {
                await this.$store.dispatch('clients/fetchClients', {
                    search: this.searchQuery.trim() || undefined
                });
            } catch (error) {
                ElMessage.error('Помилка при завантаженні клієнтів');
            } finally {
                this.loading = false;
            }
        },
        viewClient(clientId) {
            this.$router.push({ name: 'ClientProfile', params: { id: clientId } });
        },
        openCreateClientModal() {
            this.isCreateClientModalOpen = true;
        },
        closeCreateClientModal() {
            this.isCreateClientModalOpen = false;
        },
        onClientSaved() {
            this.closeCreateClientModal();
        },
        async handleDeleteClient(client) {
            try {
                await ElMessageBox.confirm(
                    `Видалити клієнта "${client.full_name}"? Усі пов\'язані авто та записи також будуть видалені.`,
                    'Підтвердження видалення',
                    {
                        confirmButtonText: 'Видалити',
                        cancelButtonText: 'Скасувати',
                        type: 'warning'
                    }
                );

                await this.$store.dispatch('clients/deleteClient', client.id);
                ElMessage.success('Клієнта видалено');
            } catch (error) {
                if (error !== 'cancel') {
                    ElMessage.error(error.response?.data?.message || 'Помилка видалення клієнта');
                }
            }
        }
    }
};
</script>

<style scoped>
.clients-container {
    padding: 24px;
}

.top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.title {
    font-size: 24px;
    font-weight: 600;
    color: var(--el-text-color-primary);
}

.count {
    color: var(--el-text-color-secondary);
    font-weight: 400;
}
</style>
