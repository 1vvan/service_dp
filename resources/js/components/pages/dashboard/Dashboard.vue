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
                            <div class="stat-value">{{ isUserClient ? stats.total_bookings : managerStats.total_bookings }}</div>
                            <div class="stat-label">Записів</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card" v-loading="isLoading">
                    <div class="stat-content">
                        <div class="stat-icon clients">
                            <CarIcon :size="32" />
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ isUserClient ? stats.total_cars : managerStats.total_cars }}</div>
                            <div class="stat-label">{{ isUserClient ? 'Мої авто' : 'Авто' }}</div>
                        </div>
                    </div>
                </el-card>
                
                <el-card class="stat-card" v-loading="isLoading">
                    <div class="stat-content">
                        <div class="stat-icon revenue">
                            <el-icon :size="32">
                                <Wallet v-if="isUserClient" />
                                <User v-else />
                            </el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ isUserClient ? formatCurrency(stats.total_spent) + ' грн' : managerStats.total_clients }}</div>
                            <div class="stat-label">{{ isUserClient ? 'Витрачено' : 'Клієнти' }}</div>
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
                        <div v-if="bookings.length === 0" class="empty-state">
                            <el-empty description="Немає записів" />
                        </div>
                        <div v-else
                            v-for="booking in bookings.slice(0, 3)"
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
                <el-card class="dashboard-card" v-loading="isLoading" v-if="!isManagerOrAdmin">
                    <template #header>
                        <div class="card-header">
                            <span>Мої автомобілі</span>
                        </div>
                    </template>
                    <div class="dashboard-card__list">
                        <div v-if="cars.length === 0" class="empty-state">
                            <el-empty description="Немає авто" />
                        </div>
                        <div v-else
                            v-for="car in cars"
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
    Wallet,
    User
} from '@element-plus/icons-vue';
import CarIcon from '../../ui/CarIcon.vue';
import { formatPrice, moment } from '../../../lib/utils';
import { USER_ROLES } from '../../../constants/types';

export default {
    name: 'Dashboard',
    components: {
        DashboardLayout,
        Calendar,
        Van,
        Setting,
        Wallet,
        User,
        CarIcon
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
        user() {
            return this.$store.state.user;
        },
        isManagerOrAdmin() {
            return this.$store.state.isManagerOrAdmin;
        },
        userCars() {
            return this.$store.state.cars.userCars;
        },
        userBookings() {
            return this.$store.state.bookings.userBookings;
        },
        upcomingBookings() {
            return this.$store.state.bookings.upcomingBookings;
        },
        cars() {
            if (this.isUserClient) {
                return this.userCars;
            } else {
                return []
            }
        },
        bookings() {
            if (this.isUserClient) {
                return this.userBookings;
            } else {
                return this.upcomingBookings;
            }
        },
        stats() {
            return this.$store.state.dash.stats;
        },
        managerStats() {
            return this.$store.state.dash.managerStats;
        },
        isUserClient() {
            return this.user.role_id === USER_ROLES.CLIENT;
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
            if (this.isUserClient) {
                Promise.all([
                    this.$store.dispatch('cars/fetchUserCars', { clientId: this.$store.state.user.client_id }),
                        this.$store.dispatch('bookings/fetchUserBookings', { clientId: this.$store.state.user.client_id }),
                        this.$store.dispatch('dash/fetchStats', this.$store.state.user.client_id)
                    ]).then(() => {
                        this.isLoading = false;
                    });
            } else {
                Promise.all([
                    this.$store.dispatch('bookings/fetchUpcomingBookings', {}),
                    this.$store.dispatch('dash/fetchManagerStats')
                ]).then(() => {
                    this.isLoading = false;
                });
            }
        }
    },
    mounted() {
        this.fetchUserData();
    }
};
</script>


