<template>
    <div class="technician-dashboard" style="font-family: 'Inter', sans-serif">
        <!-- Sidebar -->
        <aside class="technician-sidebar">
            <div class="sidebar-top">
                <div class="sidebar-logo">
                    <div class="logo-icon-wrap">
                        <el-icon :size="20"><Tools /></el-icon>
                    </div>
                    <div>
                        <h2 class="logo-title">AutoCare</h2>
                        <p class="logo-subtitle">Панель майстра</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <router-link
                    to="/dashboard/master"
                    class="nav-link nav-link--active"
                >
                    <el-icon class="nav-icon"><List /></el-icon>
                    <span>Мої замовлення</span>
                </router-link>
            </nav>

            <div class="sidebar-footer">
                <div class="user-block">
                    <div class="user-avatar">{{ userInitials }}</div>
                    <div>
                        <p class="user-name">{{ userDisplayName }}</p>
                        <p class="user-role">Майстер</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="logout-btn"
                    @click="handleLogout"
                >
                    <el-icon class="logout-icon"><ArrowLeftBold /></el-icon>
                    <span>Вийти</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="technician-main">
            <div class="main-header">
                <h1 class="main-title">Мої замовлення</h1>
                <p class="main-date">{{ dateLabel }}</p>
            </div>

            <!-- Toolbar -->
            <div class="toolbar">
                <div class="date-picker-wrap" v-click-outside="closeCalendar">
                    <div class="date-trigger" @click="showCalendar = !showCalendar">
                        <el-icon class="toolbar-icon"><Calendar /></el-icon>
                        <span>{{ dateValue }}</span>
                    </div>
                    <div v-if="showCalendar" class="calendar-popover">
                    <el-date-picker
                        v-model="filterDate"
                        type="date"
                        :placeholder="'Оберіть дату'"
                        format="DD.MM.YYYY"
                        value-format="YYYY-MM-DD"
                        class="calendar-inline"
                        @change="onDateChange"
                    />
                    </div>
                </div>

                <div class="select-wrap">
                    <select v-model="filterStatus" class="status-select" @change="fetchBookings">
                        <option value="">Всі статуси</option>
                        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Skeleton -->
            <div v-if="loading" class="orders-list">
                <div v-for="i in 3" :key="i" class="order-card order-card--skeleton">
                    <div class="skeleton-row">
                        <div class="skeleton skeleton-w40" />
                        <div class="skeleton skeleton-w24" />
                    </div>
                    <div class="skeleton skeleton-w56" />
                    <div class="skeleton skeleton-w48" />
                    <div class="skeleton-row tags">
                        <div class="skeleton skeleton-w20" />
                        <div class="skeleton skeleton-w24" />
                    </div>
                    <div class="skeleton skeleton-w24 align-end" />
                </div>
            </div>

            <!-- Empty -->
            <div v-else-if="filteredBookings.length === 0" class="empty-state">
                <div class="empty-circle">
                    <el-icon :size="40" class="empty-icon"><List /></el-icon>
                </div>
                <h3 class="empty-title">Замовлень немає</h3>
                <p class="empty-text">
                    На обрану дату немає призначених замовлень. Оберіть іншу дату або зверніться до менеджера.
                </p>
            </div>

            <!-- Orders list -->
            <div v-else class="orders-list">
                <div
                    v-for="booking in filteredBookings"
                    :key="booking.id"
                    class="order-card"
                >
                    <div class="card-top">
                        <div class="card-datetime">
                            <el-icon class="card-icon"><Calendar /></el-icon>
                            <span>{{ formatBookingDateTime(booking) }}</span>
                        </div>
                        <div class="card-actions">
                            <span :class="['status-badge', statusClass(booking.status_id)]">
                                {{ booking.status_name }}
                            </span>
                            <button
                                v-if="isConfirmed(booking)"
                                type="button"
                                class="action-btn action-btn--primary"
                                :disabled="updatingId === booking.id"
                                @click="startWork(booking)"
                            >
                                {{ updatingId === booking.id ? '...' : 'Взяти в роботу' }}
                            </button>
                            <button
                                v-if="isInProgress(booking)"
                                type="button"
                                class="action-btn action-btn--complete"
                                :disabled="updatingId === booking.id"
                                @click="complete(booking)"
                            >
                                {{ updatingId === booking.id ? '...' : 'Виконано' }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-item">
                                <el-icon class="info-icon"><User /></el-icon>
                                <span>{{ booking.client?.full_name }}</span>
                            </div>
                            <div v-if="booking.client?.phone" class="info-item info-item--muted">
                                <el-icon class="info-icon"><Phone /></el-icon>
                                <span>{{ booking.client.phone }}</span>
                            </div>
                        </div>
                        <div class="info-item car-row">
                            <el-icon class="info-icon"><Van /></el-icon>
                            <span>{{ booking.car?.full_name }}</span>
                            <span class="separator">•</span>
                            <span class="plate-badge">{{ booking.car?.license_plate }}</span>
                        </div>
                        <div class="services-row">
                            <span
                                v-for="service in booking.services"
                                :key="service.id"
                                class="service-tag"
                            >
                                {{ service.name }}
                            </span>
                        </div>
                        <div v-if="booking.description" class="comment-row">
                            <el-icon class="comment-icon"><ChatDotRound /></el-icon>
                            <span>{{ booking.description }}</span>
                        </div>
                    </div>

                    <div class="card-footer">
                        <span class="price">{{ formatPrice(booking.total_price) }} ₴</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus';
import axios from 'axios';
import {
    Calendar,
    Van,
    ChatDotRound,
    User,
    Phone,
    Tools,
    List,
    ArrowLeftBold,
} from '@element-plus/icons-vue';
import { BOOKING_STATUS } from '../../../../../constants/types';
import { moment } from '../../../../../lib/utils';

export default {
    name: 'MasterDashboard',
    components: {
        Calendar,
        Van,
        ChatDotRound,
        User,
        Phone,
        Tools,
        List,
        ArrowLeftBold,
    },
    directives: {
        clickOutside: {
            mounted(el, binding) {
                el._clickOutside = (e) => {
                    if (!el.contains(e.target)) binding.value();
                };
                document.addEventListener('click', el._clickOutside);
            },
            unmounted(el) {
                document.removeEventListener('click', el._clickOutside);
            },
        },
    },
    data() {
        return {
            bookings: [],
            statuses: [],
            loading: false,
            updatingId: null,
            filterDate: null,
            filterStatus: null,
            showCalendar: false,
        };
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        userDisplayName() {
            const u = this.user;
            if (!u) return 'Майстер';
            return u.full_name || u.name || u.email || 'Майстер';
        },
        userInitials() {
            const name = this.userDisplayName;
            if (!name || name === 'Майстер') return 'МТ';
            const parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return name.slice(0, 2).toUpperCase();
        },
        dateLabel() {
            const d = this.filterDate ? moment(this.filterDate) : moment();
            return d.format('dddd, D MMMM YYYY');
        },
        dateValue() {
            const d = this.filterDate ? moment(this.filterDate) : moment();
            return d.format('DD.MM.YYYY');
        },
        filteredBookings() {
            if (!this.filterStatus) return this.bookings;
            return this.bookings.filter((b) => b.status_id === this.filterStatus);
        },
    },
    async mounted() {
        await Promise.all([this.fetchBookings(), this.fetchStatuses()]);
    },
    methods: {
        closeCalendar() {
            this.showCalendar = false;
        },
        onDateChange() {
            this.showCalendar = false;
            this.fetchBookings();
        },
        async fetchBookings() {
            this.loading = true;
            try {
                const params = {};
                if (this.filterDate) params.date = this.filterDate;
                const res = await axios.get('/api/master/bookings', { params });
                this.bookings = res.data;
            } catch {
                ElMessage.error('Помилка завантаження записів');
            } finally {
                this.loading = false;
            }
        },
        async fetchStatuses() {
            const res = await axios.get('/api/references/booking-statuses');
            this.statuses = res.data;
        },
        isConfirmed(booking) {
            return booking.status_id === BOOKING_STATUS.CONFIRMED;
        },
        isInProgress(booking) {
            return booking.status_id === BOOKING_STATUS.IN_PROGRESS;
        },
        async startWork(booking) {
            await this.changeStatus(booking, BOOKING_STATUS.IN_PROGRESS);
        },
        async complete(booking) {
            await this.changeStatus(booking, BOOKING_STATUS.PENDING_PAYMENT);
        },
        async changeStatus(booking, statusId) {
            this.updatingId = booking.id;
            try {
                const res = await axios.post(`/api/master/bookings/${booking.id}/status`, {
                    status_id: statusId,
                });
                const index = this.bookings.findIndex((b) => b.id === booking.id);
                if (index !== -1) this.bookings[index] = { ...res.data.booking };
                ElMessage.success(res.data.message);
            } catch (err) {
                ElMessage.error(err.response?.data?.message || 'Помилка оновлення статусу');
            } finally {
                this.updatingId = null;
            }
        },
        statusClass(statusId) {
            const map = {
                [BOOKING_STATUS.NEW]: 'status--new',
                [BOOKING_STATUS.CONFIRMED]: 'status--confirmed',
                [BOOKING_STATUS.IN_PROGRESS]: 'status--in-progress',
                [BOOKING_STATUS.COMPLETED]: 'status--completed',
                [BOOKING_STATUS.CANCELLED]: 'status--cancelled',
                [BOOKING_STATUS.PENDING_PAYMENT]: 'status--pending',
                [BOOKING_STATUS.PAID]: 'status--paid',
            };
            return map[statusId] ?? 'status--new';
        },
        formatBookingDateTime(booking) {
            if (!booking.date) return '';
            const m = moment(booking.date, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD', 'DD.MM.YYYY HH:mm'], true);
            return m.isValid() ? m.format('HH:mm — DD.MM.YYYY') : booking.date;
        },
        formatPrice(val) {
            return Number(val).toLocaleString('uk-UA');
        },
        handleLogout() {
            this.$store.dispatch('logout').then(() => {
                this.$router.push('/');
            });
        },
    },
};
</script>

<style scoped lang="scss">
/* Color scheme */
$page-bg:    #1a1d2e;
$sidebar-bg: #1e2132;
$card-bg:    #242840;
$primary:    #f97316;
$text:       #f5f7fa;
$muted:      #7a8299;
$border:     #2e3348;
$secondary:  #2a2f45;
$muted-bg:   #272c42;
$destructive: #ef4444;

.technician-dashboard {
    min-height: 100vh;
    display: flex;
    background: $page-bg;
}

/* Sidebar */
.technician-sidebar {
    width: 16rem;
    background: $sidebar-bg;
    color: $text;
    display: flex;
    flex-direction: column;
    position: fixed;
    inset: 0;
    top: 0;
    left: 0;
    z-index: 30;
    border-right: 1px solid $border;
}

.sidebar-top {
    padding: 1.5rem;
    border-bottom: 1px solid $border;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-icon-wrap {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background: rgba($primary, 0.2);
    color: $primary;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: $text;
}

.logo-subtitle {
    font-size: 0.75rem;
    color: $muted;
    margin: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: $muted;
    text-decoration: none;
    border: 1px solid transparent;
    transition: color 0.15s, background 0.15s, border-color 0.15s;

    &:hover {
        color: $text;
        background: $muted-bg;
    }

    &--active {
        background: rgba($primary, 0.1);
        color: $primary;
        border-color: rgba($primary, 0.2);
    }
}

.nav-icon {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid $border;
}

.user-block {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    margin-bottom: 0.75rem;
}

.user-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: $secondary;
    color: $muted;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 500;
    margin: 0;
    color: $text;
}

.user-role {
    font-size: 0.75rem;
    color: $muted;
    margin: 0;
}

.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: $muted;
    background: none;
    border: none;
    cursor: pointer;
    border-radius: 0.5rem;
    transition: color 0.15s, background 0.15s;

    &:hover {
        color: $destructive;
        background: rgba($destructive, 0.1);
    }
}

.logout-icon {
    width: 1rem;
    height: 1rem;
}

/* Main */
.technician-main {
    flex: 1;
    margin-left: 16rem;
    padding: 2rem;
}

.main-header {
    margin-bottom: 2rem;
}

.main-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: $text;
    margin: 0 0 0.25rem;
}

.main-date {
    font-size: 0.875rem;
    color: $muted;
    margin: 0;
    text-transform: capitalize;
}

.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    position: relative;
}

.date-picker-wrap {
    position: relative;
    display: inline-block;
}

.date-trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: $secondary;
    border: 1px solid $border;
    border-radius: 0.5rem;
    color: $text;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 140px;
}

.toolbar-icon {
    width: 1rem;
    height: 1rem;
    color: $muted;
}

.calendar-popover {
    position: absolute;
    left: 0;
    top: 100%;
    margin-top: 4px;
    z-index: 10;
    background: $card-bg;
    border: 1px solid $border;
    border-radius: 0.5rem;
    padding: 0.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
}

.calendar-inline {
    --el-date-editor-daterange-width: 100%;
}
:deep(.calendar-inline .el-input__wrapper) {
    background: $secondary !important;
    border-color: $border !important;
    box-shadow: none !important;
    color: $text;
}

.status-select {
    width: 12rem;
    padding: 0.5rem 0.75rem;
    background: $secondary;
    border: 1px solid $border;
    border-radius: 0.5rem;
    color: $text;
    font-size: 0.875rem;
    cursor: pointer;

    option {
        background: $card-bg;
        color: $text;
    }
}

/* Orders list */
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-card {
    background: $card-bg;
    border: 1px solid $border;
    border-radius: 0.75rem;
    padding: 1.25rem;
    transition: box-shadow 0.2s;

    &:hover {
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.25);
    }
}

.card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.card-datetime {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: $text;
}

.card-icon {
    width: 1rem;
    height: 1rem;
    color: $muted;
    flex-shrink: 0;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.status-badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid transparent;
    white-space: nowrap;
}

.status--new {
    background: rgba(#6b7280, 0.2);
    color: #9ca3af;
    border-color: rgba(#6b7280, 0.3);
}
.status--confirmed {
    background: rgba(#3b82f6, 0.2);
    color: #60a5fa;
    border-color: rgba(#3b82f6, 0.3);
}
.status--in-progress {
    background: rgba($primary, 0.2);
    color: #fb923c;
    border-color: rgba($primary, 0.3);
}
.status--completed {
    background: rgba(#22c55e, 0.2);
    color: #4ade80;
    border-color: rgba(#22c55e, 0.3);
}
.status--cancelled {
    background: rgba($destructive, 0.2);
    color: #f87171;
    border-color: rgba($destructive, 0.3);
}
.status--pending,
.status--paid {
    background: rgba($primary, 0.2);
    color: #fb923c;
    border-color: rgba($primary, 0.3);
}

.action-btn {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.375rem 0.875rem;
    border-radius: 0.375rem;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity 0.15s;

    &:hover:not(:disabled) {
        opacity: 0.9;
    }
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    &--primary {
        background: $primary;
        color: #fff;
    }
    &--complete {
        background: #22c55e;
        color: #fff;
    }
}

.card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: $text;

    &--muted {
        color: $muted;
    }
}

.info-icon {
    width: 1rem;
    height: 1rem;
    color: $muted;
    flex-shrink: 0;
}

.car-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.separator {
    color: $muted;
}

.plate-badge {
    font-family: ui-monospace, monospace;
    font-size: 0.75rem;
    background: $muted-bg;
    color: $muted;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
}

.services-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
    margin-top: 0.5rem;
}

.service-tag {
    font-size: 0.75rem;
    font-weight: 500;
    background: rgba($primary, 0.15);
    color: #fdba74;
    border: 1px solid rgba($primary, 0.25);
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
}

.comment-row {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: $muted;
    font-style: italic;
    margin-top: 0.25rem;
}

.comment-icon {
    width: 0.875rem;
    height: 0.875rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.card-footer {
    padding-top: 0.75rem;
    border-top: 1px solid $border;
    display: flex;
    justify-content: flex-end;
}

.price {
    font-size: 1.125rem;
    font-weight: 700;
    color: $primary;
}

/* Skeleton */
.order-card--skeleton {
    pointer-events: none;
}

.skeleton {
    height: 0.875rem;
    border-radius: 0.375rem;
    background: linear-gradient(
        90deg,
        $secondary 25%,
        $muted-bg 50%,
        $secondary 75%
    );
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}

.skeleton-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;

    &.tags {
        margin-top: 0.5rem;
    }
}

.skeleton-w20 { width: 5rem; }
.skeleton-w24 { width: 6rem; }
.skeleton-w40 { width: 10rem; }
.skeleton-w48 { width: 12rem; }
.skeleton-w56 { width: 14rem; }
.align-end { margin-left: auto; }

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Empty state */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 5rem 1.25rem;
    text-align: center;
}

.empty-circle {
    width: 5rem;
    height: 5rem;
    border-radius: 50%;
    background: $muted-bg;
    border: 1px solid $border;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: $muted;
}

.empty-icon {
    width: 2.5rem;
    height: 2.5rem;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: $text;
    margin: 0 0 0.5rem;
}

.empty-text {
    font-size: 0.875rem;
    color: $muted;
    max-width: 24rem;
    line-height: 1.5;
    margin: 0;
}
</style>
