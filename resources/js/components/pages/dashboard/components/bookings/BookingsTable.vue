<template>
    <div>
        <el-table v-loading="loading" :data="bookings" class="dash-table">
            <el-table-column prop="id" label="ID" width="30">
                <template #default="scope">
                    <span class="orange-text">#{{ scope.row.id }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="services" label="Послуги" width="100">
                <template #default="scope">
                    <span class="white-text">{{ scope.row.services.map(service => service.name).join(', ') }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="car" label="Авто" width="130">
                <template #default="scope">
                    <span>{{ scope.row.car.full_name }}</span>
                    <span class="orange-text">{{ ' ' + scope.row.car.license_plate }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="date" label="Дата та час" width="100">
                <template #default="scope">
                    <span>{{ scope.row.date }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="status_name" label="Статус" width="80">
                <template #default="scope">
                    <span class="status-badge" :class="BOOKING_STATUS_CLASS_MAPPING[scope.row.status_id]">{{ scope.row.status_name }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="total_price" label="Вартість" width="70">
                <template #default="scope">
                    <span class="white-text">{{ formatPrice(scope.row.total_price) }} грн</span>
                </template>
            </el-table-column>
            <el-table-column label="Дії" width="100" align="center">
                <template #default="scope">
                    <el-dropdown placement="bottom">
                        <el-button> <el-icon><Grid /></el-icon>  </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <!-- Online payment: only for clients, only when pending payment -->
                                <el-dropdown-item
                                    v-if="isClient && isBookingPendingPayment(scope.row)"
                                    @click="payBooking(scope.row.id)"
                                ><el-icon><Money /></el-icon> Сплатити онлайн</el-dropdown-item>
                                <!-- Manual paid: only for manager/admin, only when pending payment -->
                                <el-dropdown-item
                                    v-if="isManagerOrAdmin && isBookingPendingPayment(scope.row)"
                                    @click="markPaid(scope.row)"
                                ><el-icon><Money /></el-icon> Сплачено</el-dropdown-item>
                                <!-- Receipt: only when work is done (pending payment, paid, or completed) -->
                                <el-dropdown-item
                                    v-if="isBookingReceiptAvailable(scope.row)"
                                    @click="downloadReceipt(scope.row.id)"
                                ><el-icon><Tickets /></el-icon> Скачати чек</el-dropdown-item>
                                <!-- Edit: only for manager/admin -->
                                <el-dropdown-item
                                    v-if="isManagerOrAdmin"
                                    @click="editBooking(scope.row.id)"
                                ><el-icon><Edit /></el-icon> Редагувати</el-dropdown-item>
                                <!-- Confirm: manager/admin, new bookings only -->
                                <el-dropdown-item
                                    v-if="isManagerOrAdmin && isBookingNew(scope.row)"
                                    @click="openConfirmDialog(scope.row)"
                                ><el-icon><Check /></el-icon> Підтвердити</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <!-- Confirm dialog with master selection -->
        <el-dialog
            v-model="confirmDialogVisible"
            title="Підтвердження запису"
            width="460px"
            :close-on-click-modal="false"
            class="create-form-modal-dialog"
        >
            <template #header>
                <div class="create-form-modal-header">
                    <h2 class="create-form-modal-title">
                        <div class="icon"><el-icon><Check /></el-icon></div>
                        Підтвердження запису
                    </h2>
                    <p class="create-form-modal-description">Оберіть майстра та підтвердіть запис.</p>
                </div>
            </template>

            <div v-if="confirmingBooking" style="margin-bottom: 12px;">
                <p style="margin-bottom: 4px;"><strong>Запис #{{ confirmingBooking.id }}</strong></p>
                <p style="color: var(--el-text-color-secondary); font-size: 13px;">
                    {{ confirmingBooking.date }} • {{ confirmingBooking.services?.map(s => s.name).join(', ') }}
                </p>
            </div>

            <el-form label-position="top">
                <el-form-item label="Майстер" required>
                    <el-select
                        v-model="confirmForm.master_id"
                        placeholder="Оберіть майстра"
                        filterable
                        style="width: 100%"
                        @change="checkMasterAvailability"
                    >
                        <el-option
                            v-for="m in mastersList"
                            :key="m.id"
                            :label="m.full_name"
                            :value="m.id"
                        />
                    </el-select>
                </el-form-item>

                <el-alert
                    v-if="masterConflict"
                    type="warning"
                    :closable="false"
                    style="margin-bottom: 12px;"
                >
                    {{ masterConflict }}
                </el-alert>

                <el-form-item label="Змінити час (необов'язково)">
                    <el-date-picker
                        v-model="confirmForm.date"
                        type="datetime"
                        placeholder="Залишити поточний"
                        format="DD.MM.YYYY HH:mm"
                        value-format="YYYY-MM-DD HH:mm:ss"
                        style="width: 100%"
                        @change="checkMasterAvailability"
                    />
                </el-form-item>
            </el-form>

            <template #footer>
                <div class="create-form-modal-footer">
                    <el-button @click="confirmDialogVisible = false">Скасувати</el-button>
                    <el-button
                        type="primary"
                        :loading="confirmLoading"
                        :disabled="!confirmForm.master_id || !!masterConflict"
                        @click="submitConfirm"
                    >Підтвердити</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import axios from 'axios';
import { formatPrice } from '../../../../../lib/utils';
import { BOOKING_STATUS_CLASS_MAPPING as bookingStatusClassMapping } from '../../../../../constants/mapping';
import { Grid, Tickets, Edit, Money, Check } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';
import { PAYMENT_STATUS, USER_ROLES, BOOKING_STATUS } from '../../../../../constants/types';

export default {
    name: 'BookingsTable',
    components: {
        Tickets,
        Grid,
        Edit,
        Money,
        Check
    },
    props: {
        bookings: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            confirmDialogVisible: false,
            confirmLoading: false,
            confirmingBooking: null,
            confirmForm: { master_id: null, date: null },
            masterConflict: null,
        };
    },
    computed: {
        BOOKING_STATUS_CLASS_MAPPING() {
            return bookingStatusClassMapping;
        },
        isManagerOrAdmin() {
            return this.$store.state.user.role_id === USER_ROLES.MANAGER || this.$store.state.user.role_id === USER_ROLES.ADMIN;
        },
        isClient() {
            return this.$store.state.user.role_id === USER_ROLES.CLIENT;
        },
        mastersList() {
            return this.$store.state.masters.masters || [];
        },
    },
    methods: {
        formatPrice(price) {
            return formatPrice(price);
        },
        async downloadReceipt(bookingId) {
            this.$store.dispatch('bookings/downloadReceipt', bookingId)
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `receipt-booking-${bookingId}.pdf`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(() => {
                    ElMessage.error('Помилка при скачуванні чека');
                });
        },
        editBooking(bookingId) {
            this.$emit('edit-booking', bookingId);
        },
        async payBooking(bookingId) {
            try {
                const response = await this.$store.dispatch('bookings/createPaymentSession', bookingId);
                if (response.data && response.data.url) {
                    window.location.href = response.data.url;
                }
            } catch (error) {
                ElMessage.error(error.response?.data?.message || 'Помилка при створенні платежу');
            }
        },
        isBookingPaid(booking) {
            if (booking.payment_transactions && booking.payment_transactions.length > 0) {
                return booking.payment_transactions.some(
                    transaction => transaction.payment_status === PAYMENT_STATUS.COMPLETED
                );
            }
            return false;
        },
        isBookingNew(booking) {
            return booking.status_id === BOOKING_STATUS.NEW;
        },
        isBookingPendingPayment(booking) {
            return booking.status_id === BOOKING_STATUS.PENDING_PAYMENT;
        },
        isBookingReceiptAvailable(booking) {
            return [
                BOOKING_STATUS.PENDING_PAYMENT,
                BOOKING_STATUS.PAID,
                BOOKING_STATUS.COMPLETED,
            ].includes(booking.status_id);
        },
        async markPaid(booking) {
            try {
                await axios.post(`/api/bookings/${booking.id}/mark-paid`);
                ElMessage.success('Запис позначено як сплачений');
                await this.$store.dispatch('bookings/fetchClientBookings', { force: true });
            } catch (error) {
                ElMessage.error(error.response?.data?.message || 'Помилка');
            }
        },
        async openConfirmDialog(booking) {
            this.confirmingBooking = booking;
            this.confirmForm = { master_id: null, date: null };
            this.masterConflict = null;
            this.confirmDialogVisible = true;

            try {
                await this.$store.dispatch('masters/fetchMasters');
            } catch {
                ElMessage.error('Не вдалося завантажити список майстрів');
            }
        },
        async checkMasterAvailability() {
            this.masterConflict = null;
            if (!this.confirmForm.master_id || !this.confirmingBooking) return;

            try {
                const data = await this.$store.dispatch('masters/checkMasterAvailability', {
                    master_id: this.confirmForm.master_id,
                    date: this.confirmForm.date || this.confirmingBooking.date,
                    exclude_booking_id: this.confirmingBooking.id,
                });
                if (!data.available) {
                    this.masterConflict = data.message;
                }
            } catch {
                /* ignore */
            }
        },
        async submitConfirm() {
            if (!this.confirmForm.master_id || !this.confirmingBooking) return;
            this.confirmLoading = true;

            try {
                await axios.post(`/api/bookings/${this.confirmingBooking.id}/confirm`, {
                    master_id: this.confirmForm.master_id,
                    date: this.confirmForm.date || null,
                });
                ElMessage.success('Запис підтверджено та майстра призначено');
                this.confirmDialogVisible = false;
                await this.$store.dispatch('bookings/fetchClientBookings', { force: true });
            } catch (error) {
                ElMessage.error(error.response?.data?.message || 'Помилка підтвердження');
            } finally {
                this.confirmLoading = false;
            }
        },
    }
}
</script>