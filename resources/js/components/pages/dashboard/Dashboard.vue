<template>
    <DashboardLayout>
        <div class="dashboard-page">
            <div class="page-header">
                <h1 class="page-title">Панель керування</h1>
                <p class="page-subtitle">Ласкаво просимо до системи керування автосервісом</p>
            </div>
            
            <div class="stats-grid">
                <el-card class="stat-card" v-loading="isLoading">
                    <div class="stat-content">
                        <div class="stat-icon bookings">
                            <el-icon :size="32"><Calendar /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ stats.total_bookings }}</div>
                            <div class="stat-label">Записів</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card" v-loading="isLoading">
                    <div class="stat-content">
                        <div class="stat-icon clients">
                            <el-icon :size="32"><Van /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ stats.total_cars }}</div>
                            <div class="stat-label">Мої авто</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card" v-loading="isLoading">
                    <div class="stat-content">
                        <div class="stat-icon revenue">
                            <el-icon :size="32"><Wallet /></el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ formatCurrency(stats.total_spent) }} грн</div>
                            <div class="stat-label">Витрачено</div>
                        </div>
                    </div>
                </el-card>
            </div>
            
            <div class="dashboard-grid">
                <el-card class="dashboard-card" v-loading="isLoading">
                    <template #header>
                        <div class="card-header">
                            <span>Найближчі записи</span>
                        </div>
                    </template>
                    <div class="dashboard-card__list">
                        <div v-if="userBookings.length === 0" class="empty-state">
                            <el-empty description="Немає записів" />
                        </div>
                        <div v-else
                            v-for="booking in userBookings.slice(0, 3)"
                            :key="booking.id"
                            class="dashboard-card__item"
                        >
                            <div class="dashboard-card_item-info">
                                <div class="text">
                                    <div class="title">{{ booking.services.map(service => service.name).join(', ') }}</div>
                                    <div class="meta">
                                        {{ booking.date }} • <span class="orange-text">{{ booking.car?.license_plate }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="status" :class="BOOKING_STATUS_CLASS_MAPPING[booking.status_id]">{{ booking.status?.name || 'Невідомо' }}</span>
                        </div>
                    </div>
                </el-card>
                <el-card class="dashboard-card" v-loading="isLoading">
                    <template #header>
                        <div class="card-header">
                            <span>Мої автомобілі</span>
                        </div>
                    </template>
                    <div class="dashboard-card__list">
                        <div v-if="userCars.length === 0" class="empty-state">
                            <el-empty description="Немає авто" />
                        </div>
                        <div v-else
                            v-for="car in userCars"
                            :key="car.id"
                            class="dashboard-card__item"
                        >
                            <div class="dashboard-card_item-info">
                                <div class="icon">
                                    <img :src="car.brand_logo" alt="Car Brand Logo">
                                </div>
                                <div class="text">
                                    <div class="title">{{ car.full_name }}</div>
                                    <div class="meta">
                                        {{ car.license_plate }}
                                    </div>
                                </div>
                            </div>
                            <span class="add-info" v-if="car.latest_booking">
                                <span class="label">{{ getBookingLabel(car.latest_booking) }}</span>
                                <span class="value">{{ car.latest_booking?.date }}</span>
                            </span>
                        </div>
                    </div>
                </el-card>
            </div>
        </div>
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../layouts/DashboardLayout.vue';
import { BOOKING_STATUS_CLASS_MAPPING as bookingStatusClassMapping } from '../../../constants/mapping';
import {
    Calendar,
    Van,
    Setting,
    Wallet
} from '@element-plus/icons-vue';
import { formatPrice, moment } from '../../../lib/utils';

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
            isLoading: true,
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
        },
        stats() {
            return this.$store.state.dash.stats;
        },
        BOOKING_STATUS_CLASS_MAPPING() {
            return bookingStatusClassMapping;
        },
    },
    methods: {
        formatCurrency(amount) {
            return formatPrice(amount || 0);
        },
        getBookingLabel(booking) {
            if (!booking || !booking.date) {
                return '';
            }

            const bookingDate = moment(booking.date);
            const now = moment();
            const isPast = bookingDate.isBefore(now);

            if (isPast) {
                return `Останній запис`;
            } else {
                return `Найближчий запис`;
            }
        },
        fetchUserData() {
            Promise.all([
                this.$store.dispatch('cars/fetchUserCars', this.$store.state.user.client_id),
                this.$store.dispatch('bookings/fetchUserBookings', { clientId: this.$store.state.user.client_id }),
                this.$store.dispatch('dash/fetchStats', this.$store.state.user.client_id)
            ]).then(() => {
                this.isLoading = false;
            });
        }
    },
    mounted() {
        this.fetchUserData();
    }
};
</script>


