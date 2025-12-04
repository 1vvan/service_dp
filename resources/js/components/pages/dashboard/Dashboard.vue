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
                            <div class="stat-value">{{ stats.cars }}</div>
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
                            <span>Найближчі бронювання</span>
                        </div>
                    </template>
                    <div class="recent-bookings">
                        <div v-if="userBookings.length === 0" class="empty-state">
                            <el-empty description="Немає бронювань" />
                        </div>
                        <div v-else>
                            <div
                                v-for="booking in userBookings"
                                :key="booking.id"
                                class="dashboard-card__item"
                            >
                                <div class="dashboard-card_item-info">
                                    <div class="title">{{ booking.client?.full_name || 'Клієнт' }}</div>
                                    <div class="meta">
                                        {{ formatDate(booking.date) }} • {{ booking.car?.license_plate }}
                                    </div>
                                </div>
                                <span class="status">{{ booking.status?.name || 'Невідомо' }}</span>
                            </div>
                        </div>
                    </div>
                </el-card>
                <el-card class="dashboard-card">
                    <template #header>
                        <div class="card-header">
                            <span>Мої авто</span>
                        </div>
                    </template>
                    <div class="recent-bookings">
                        <div v-if="userCars.length === 0" class="empty-state">
                            <el-empty description="Немає авто" />
                        </div>
                        <div v-else>
                            <div
                                v-for="car in userCars"
                                :key="car.id"
                                class="dashboard-card__item"
                            >
                                <div class="dashboard-card_item-info">
                                    <div class="title">{{ car.brand?.name }} {{ car.model?.name }}</div>
                                    <div class="meta">
                                        {{ car.license_plate }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-card>
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
                cars: 0,
                revenue: 0
            },
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
            };
        },
        userCars() {
            return this.$store.state.cars.userCars;
        },
        userBookings() {
            return this.$store.state.bookings.userBookings;
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
        },
        fetchUserData() {
            this.$store.dispatch('cars/fetchUserCars', this.$store.state.user.id);
            this.$store.dispatch('bookings/fetchUserBookings', this.$store.state.user.id);
        }
    },
    mounted() {
        this.fetchUserData();
    }
};
</script>


