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
            <ClientsTable :clients="clients" :loading="loading" @view-client="viewClient" />
        </div>
    </DashboardLayout>
</template>

<script>
import axios from 'axios';
import { ElMessage } from 'element-plus';
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import ClientsTable from './ClientsTable.vue';
import { USER_ROLES } from '../../../../../constants/types';

export default {
    name: 'Clients',
    components: {
        DashboardLayout,
        ClientsTable
    },
    data() {
        return {
            clients: [],
            loading: false
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        isManagerOrAdmin() {
            if (!this.user) return false;
            return this.user.role_id === USER_ROLES.MANAGER || this.user.role_id === USER_ROLES.ADMIN;
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
        async fetchClients() {
            this.loading = true;
            try {
                const response = await axios.get('/api/clients');
                this.clients = response.data;
            } catch (error) {
                console.error('Помилка при завантаженні клієнтів:', error);
                ElMessage.error('Помилка при завантаженні клієнтів');
            } finally {
                this.loading = false;
            }
        },
        viewClient(clientId) {
            this.$router.push({ name: 'ClientProfile', params: { id: clientId } });
        },
        openCreateClientModal() {
            console.log('Open create client modal');
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

