<template>
    <DashboardLayout>
        <div class="client-profile-container" v-loading="loading">
            <el-card class="profile-section">
                <template #header>
                    <div class="section-header">
                        <el-icon class="section-icon"><UserFilled /></el-icon>
                        <span>Інформація про клієнта</span>
                    </div>
                </template>
                <div class="client-info-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>ПІБ</label>
                            <el-input v-model="clientForm.full_name" placeholder="Повне ім'я" />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <el-input v-model="clientForm.email" type="email" placeholder="email@example.com" />
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <el-input v-model="clientForm.phone" placeholder="+380 (XX) XXX-XX-XX" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label>Примітки менеджера</label>
                            <el-input
                                v-model="clientForm.manager_notes"
                                type="textarea"
                                :rows="4"
                                placeholder="Нотатки про клієнта (видимі лише менеджерам та адміністраторам)"
                                maxlength="5000"
                                show-word-limit
                            />
                        </div>
                    </div>
                    <div class="form-actions">
                        <el-button type="primary" @click="saveClientInfo" :loading="saving">
                            <el-icon><DocumentChecked /></el-icon>
                            Зберегти зміни
                        </el-button>
                    </div>
                </div>
            </el-card>

            <!-- Client Cars Section -->
            <el-card class="profile-section">
                <template #header>
                    <div class="section-header">
                        <el-icon class="section-icon"><Van /></el-icon>
                        <span>Автомобілі клієнта</span>
                    </div>
                </template>
                <div class="cars-table-wrapper">
                    <el-table :data="client?.cars || []" class="dash-table" v-if="client?.cars?.length">
                        <el-table-column prop="full_name" label="Автомобіль" width="200">
                            <template #default="scope">
                                <span class="white-text">{{ scope.row.full_name }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="license_plate" label="Номер" width="150">
                            <template #default="scope">
                                <span class="orange-text">{{ scope.row.license_plate }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="vin" label="VIN" width="150">
                            <template #default="scope">
                                <span>{{ scope.row.vin || '-' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="year" label="Рік" width="100">
                            <template #default="scope">
                                <span>{{ scope.row.year || '-' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="Перевірено" width="65" align="center" class-name="actions-column">
                            <template #default="scope">
                                <el-tooltip :content="scope.row.checked_by ? 'Перевірено' : 'Не перевірено'" placement="top">
                                    <div class="indicator" :class="scope.row.checked_by ? 'green' : 'red'"></div>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div v-else class="empty-state">
                        <p>У клієнта поки немає автомобілів</p>
                    </div>
                </div>
            </el-card>

            <!-- Client Bookings Section -->
            <el-card class="profile-section">
                <template #header>
                    <div class="section-header">
                        <el-icon class="section-icon"><Calendar /></el-icon>
                        <span>Записи клієнта</span>
                    </div>
                </template>
                <div class="bookings-table-wrapper">
                    <el-table :data="client?.bookings || []" class="dash-table" v-if="client?.bookings?.length">
                        <el-table-column prop="services" label="Послуги" width="200">
                            <template #default="scope">
                                <span class="white-text">{{ scope.row.services?.map(s => s.name).join(', ') || '-' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="date" label="Дата" width="150">
                            <template #default="scope">
                                <span>{{ scope.row.date || '-' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="status_name" label="Статус" width="120">
                            <template #default="scope">
                                <span class="status-badge" :class="BOOKING_STATUS_CLASS_MAPPING[scope.row.status_id]">
                                    {{ scope.row.status_name }}
                                </span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="total_price" label="Сума" width="120">
                            <template #default="scope">
                                <span class="white-text">{{ formatPrice(scope.row.total_price) }} грн</span>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div v-else class="empty-state">
                        <p>У клієнта поки немає записів</p>
                    </div>
                </div>
            </el-card>
        </div>
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import { formatPrice } from '../../../../../lib/utils';
import { BOOKING_STATUS_CLASS_MAPPING as bookingStatusClassMapping } from '../../../../../constants/mapping';
import { UserFilled, Van, Calendar, DocumentChecked } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';

export default {
    name: 'ClientProfile',
    components: {
        DashboardLayout,
        UserFilled,
        Van,
        Calendar,
        DocumentChecked
    },
    data() {
        return {
            loading: false,
            saving: false,
            clientForm: {
                full_name: '',
                email: '',
                phone: '',
                manager_notes: ''
            }
        };
    },
    computed: {
        client() {
            return this.$store.state.clients.currentClient;
        },
        BOOKING_STATUS_CLASS_MAPPING() {
            return bookingStatusClassMapping;
        }
    },
    watch: {
        client: {
            handler(newClient) {
                if (newClient) {
                    this.clientForm = {
                        full_name: newClient.full_name || '',
                        email: newClient.email || '',
                        phone: newClient.phone || '',
                        manager_notes: newClient.manager_notes || ''
                    };
                }
            },
            immediate: true
        }
    },
    mounted() {
        const clientId = this.$route.params.id;
        if (clientId) {
            this.loadClient(clientId);
        }
    },
    methods: {
        formatPrice(price) {
            return formatPrice(price);
        },
        async loadClient(clientId) {
            this.loading = true;
            try {
                await this.$store.dispatch('clients/fetchClient', clientId);
            } catch (error) {
                console.error('Помилка при завантаженні клієнта:', error);
                ElMessage.error('Помилка при завантаженні клієнта');
                this.$router.push({ name: 'Clients' });
            } finally {
                this.loading = false;
            }
        },
        async saveClientInfo() {
            if (!this.client) return;

            this.saving = true;
            try {
                await this.$store.dispatch('clients/updateClient', {
                    clientId: this.client.id,
                    data: {
                        full_name: this.clientForm.full_name,
                        email: this.clientForm.email,
                        phone: this.clientForm.phone,
                        manager_notes: this.clientForm.manager_notes || null
                    }
                });
                ElMessage.success('Інформацію про клієнта оновлено');
            } catch (error) {
                console.error('Помилка при оновленні клієнта:', error);
                ElMessage.error(error.response?.data?.message || 'Помилка при оновленні клієнта');
            } finally {
                this.saving = false;
            }
        }
    }
};
</script>


