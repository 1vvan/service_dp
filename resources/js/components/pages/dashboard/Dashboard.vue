<template>
    <DashboardLayout>
        <div class="dashboard-page">
            <div class="page-header">
                <h1 class="page-title">Панель керування</h1>
                <p class="page-subtitle">Ласкаво просимо до системи керування автосервісом</p>
            </div>
            
            <div class="stats-grid">
                <el-card class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon bookings">
                            <el-icon :size="32"><Calendar /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ stats.bookings }}</div>
                            <div class="stat-label">Бронювань</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon clients">
                            <el-icon :size="32"><Van /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ stats.clients }}</div>
                            <div class="stat-label">Мої авто</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon revenue">
                            <el-icon :size="32"><Wallet /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ formatCurrency(stats.revenue) }}</div>
                            <div class="stat-label">Витрачено</div>
                        </div>
                    </div>
                </el-card>
            </div>
            
            <div class="dashboard-grid">
                <el-card class="dashboard-card">
                    <template #header>
                        <div class="card-header">
                            <span>Останні бронювання</span>
                            <el-button type="primary" link @click="$router.push('/dashboard/bookings')">
                                Всі
                            </el-button>
                        </div>
                    </template>
                    <div class="recent-bookings">
                        <div v-if="recentBookings.length === 0" class="empty-state">
                            <el-empty description="Немає бронювань" />
                        </div>
                        <div v-else>
                            <div
                                v-for="booking in recentBookings"
                                :key="booking.id"
                                class="booking-item"
                            >
                                <div class="booking-info">
                                    <div class="booking-title">{{ booking.client?.full_name || 'Клієнт' }}</div>
                                    <div class="booking-meta">
                                        {{ formatDate(booking.date) }} • {{ booking.car?.brand?.name }} {{ booking.car?.model?.name }}
                                    </div>
                                </div>
                                <el-tag :type="getStatusType(booking.status?.name)">
                                    {{ booking.status?.name || 'Невідомо' }}
                                </el-tag>
                            </div>
                        </div>
                    </div>
                </el-card>
                
                <!-- <el-card class="dashboard-card">
                    <template #header>
                        <div class="card-header">
                            <span>Довідники</span>
                        </div>
                    </template>
                    <div class="references-info">
                        <div class="reference-item">
                            <span class="reference-label">Марки автомобілів:</span>
                            <span class="reference-value">{{ references.carBrands.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Моделі автомобілів:</span>
                            <span class="reference-value">{{ references.carModels.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Типи палива:</span>
                            <span class="reference-value">{{ references.fuelTypes.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Типи двигунів:</span>
                            <span class="reference-value">{{ references.engineTypes.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Типи КПП:</span>
                            <span class="reference-value">{{ references.gearboxTypes.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Типи приводу:</span>
                            <span class="reference-value">{{ references.driveUnitTypes.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Статуси бронювань:</span>
                            <span class="reference-value">{{ references.bookingStatuses.length }}</span>
                        </div>
                        <div class="reference-item">
                            <span class="reference-label">Послуги:</span>
                            <span class="reference-value">{{ references.services.length }}</span>
                        </div>
                    </div>
                </el-card> -->
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../layouts/DashboardLayout.vue';
import {
    Calendar,
    Van,
    Setting,
    Wallet
} from '@element-plus/icons-vue';

export default {
    name: 'Dashboard',
    components: {
        DashboardLayout,
        Calendar,
        Van,
        Setting,
        Wallet
    },
    data() {
        return {
            stats: {
                bookings: 0,
                clients: 0,
                services: 0,
                revenue: 0
            },
            recentBookings: []
        };
    },
    computed: {
        references() {
            return {
                carBrands: this.$store.getters['references/carBrands'],
                carModels: this.$store.getters['references/carModels'],
                fuelTypes: this.$store.getters['references/fuelTypes'],
                engineTypes: this.$store.getters['references/engineTypes'],
                gearboxTypes: this.$store.getters['references/gearboxTypes'],
                driveUnitTypes: this.$store.getters['references/driveUnitTypes'],
                bookingStatuses: this.$store.getters['references/bookingStatuses'],
                services: this.$store.getters['references/services']
            };
        }
    },
    methods: {
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('uk-UA', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('uk-UA', {
                style: 'currency',
                currency: 'UAH'
            }).format(amount || 0);
        },
        getStatusType(statusName) {
            const statusMap = {
                'Новий': 'info',
                'В роботі': 'warning',
                'Завершено': 'success',
                'Скасовано': 'danger'
            };
            return statusMap[statusName] || 'info';
        }
    },
    mounted() {
        this.$store.dispatch('references/fetchAllReferences');
    }
};
</script>


