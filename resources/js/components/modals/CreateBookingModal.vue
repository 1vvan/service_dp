<template>
    <el-dialog
        v-model="dialogVisible"
        title="Зробити запис"
        width="450px"
        :close-on-click-modal="false"
        :close-on-press-escape="true"
        @close="handleClose"
        class="create-form-modal-dialog"
    >
        <template #header>
            <div class="create-form-modal-header">
                <h2 class="create-form-modal-title">
                    <div class="icon">
                        <el-icon><Calendar /></el-icon>
                    </div>
                    {{ editingBookingId ? 'Редагувати запис' : 'Зробити запис' }}
                </h2>
                <p class="create-form-modal-description">
                    {{ editingBookingId ? 'Редагуйте запис на сервіс вашого автомобіля.' : 'Зробіть запис на сервіс вашого автомобіля.' }}
                </p>
            </div>
        </template>

        <el-form
            ref="createBookingFormRef"
            :model="formData"
            :rules="formRules"
            label-position="top"
            @submit.prevent="handleSubmit"
        >
            <div class="form-row">
                <el-form-item label="Автомобіль" prop="car">
                    <el-select
                        v-model="formData.car"
                        placeholder="Виберіть автомобіль"
                        clearable
                        :disabled="editingBookingId"
                    >
                        <el-option
                            v-for="car in cars"
                            :key="car.id"
                            :label="carOptionLabel(car)"
                            :value="Number(car.id)"
                        />
                    </el-select>
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Послуга" prop="services">
                    <el-select
                        v-model="formData.services"
                        placeholder="Виберіть послугу"
                        :options="services"
                        :props="{ label: 'name', value: 'id' }"
                        clearable
                        multiple
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Дата та час" prop="date">
                    <el-date-picker
                        v-model="formData.date"
                        type="datetime"
                        placeholder="Виберіть дату та час"
                        format="DD.MM.YYYY HH:mm"
                        date-format="DD.MM.YYYY"
                        time-format="HH:mm"
                        value-format="YYYY-MM-DD HH:mm:ss"
                        :disabled-date="disabledDate"
                        :disabled-time="disabledTime"
                    />
                </el-form-item>
            </div>

            <div class="form-row" v-if="isManagerOrAdmin">
                <el-form-item label="Майстер (необов'язково)" prop="master_id">
                    <el-select
                        v-model="formData.master_id"
                        placeholder="Оберіть майстра"
                        filterable
                        clearable
                        @change="checkMasterSlot"
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
                    style="margin-top: 4px;"
                >{{ masterConflict }}</el-alert>
            </div>

            <div class="form-row">
                <el-form-item label="Додаткові коментарі" prop="comment">
                    <el-input
                        v-model="formData.comment"
                        :rows="3"
                        type="textarea"
                        placeholder="Введіть додаткові коментарі, якщо такі є"
                    />
                </el-form-item>
            </div>
        </el-form>

        <div class="price-collapse-container" v-if="priceForServices.total_price > 0">
            <div class="price-collapse-header" :class="{ 'open': priceCollapseOpen }" @click="priceCollapseOpen = !priceCollapseOpen">
                <div class="price-header-left">
                    <el-icon class="price-icon"><Money /></el-icon>
                    <span class="price-header-text">Повна вартість: {{ formatPrice(priceForServices.total_price) }} грн</span>
                </div>
                <el-icon class="collapse-icon" :class="{ 'rotated': priceCollapseOpen }">
                    <ArrowUp />
                </el-icon>
            </div>
            <div class="price-collapse-content" v-show="priceCollapseOpen">
                <div class="price-details-list">
                    <div 
                        class="price-detail-item" 
                        v-for="service in priceForServices.services" 
                        :key="service.id"
                    >
                        <span class="service-name">{{ service.name }}</span>
                        <span class="service-price">{{ formatPrice(service.final_price) }} грн</span>
                    </div>
                    <div class="price-detail-total">
                        <span class="total-label">Загальна вартість</span>
                        <span class="total-price">{{ formatPrice(priceForServices.total_price) }} грн</span>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="create-form-modal-footer">
                <el-button @click="handleClose">Скасувати</el-button>
                <el-button type="primary" @click="handleSubmit">{{ editingBookingId ? 'Оновити запис' : 'Створити запис' }}</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { Calendar, Money, ArrowUp } from '@element-plus/icons-vue';
import { USER_ROLES } from '../../constants/types';

export default {
    name: 'CreateCarModal',
    components: {
        Calendar,
        Money,
        ArrowUp
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        },
        editingBookingId: {
            type: Number,
            default: null
        }
    },
    emits: ['close'],
    data() {
        return {
            loading: false,
            priceCollapseOpen: false,
            priceForServices: {
                services: [],
                total_price: 0,
            },
            masterConflict: null,
            editingClientId: null,
            /** Авто з відповіді GET booking, якщо його немає в userCars (рідкісний випадок) */
            editingBookingCarOption: null,
            formData: {
                car: null,
                services: [],
                date: null,
                comment: null,
                master_id: null,
            },
            formRules: {
                car: [
                    { required: true, message: 'Виберіть автомобіль', trigger: 'change' }
                ],
                services: [
                    { required: true, message: 'Виберіть послугу', trigger: 'change' }
                ],
                date: [
                    { required: true, message: 'Виберіть дату', trigger: 'change' }
                ],
            },
            unavailableDates: new Set(),
            loadingBookings: false
        };
    },
    computed: {
        dialogVisible: {
            get() {
                return this.isOpen;
            },
            set(value) {
                if (!value) {
                    this.handleClose();
                }
            }
        },
        user() {
            return this.$store.state.user;
        },
        cars() {
            const userCars = this.$store.state.cars.userCars || [];
            const sid = (id) => (id != null ? Number(id) : null);
            const selectedId = sid(this.formData.car);

            const verifiedOnly = userCars.filter((car) => car.checked_by != null);

            if (!this.editingBookingId || selectedId == null) {
                return verifiedOnly;
            }

            const selectedFromList = userCars.find((c) => sid(c.id) === selectedId);
            const list = [...verifiedOnly];
            if (selectedFromList && !list.some((c) => sid(c.id) === selectedId)) {
                list.push(selectedFromList);
            }
            if (
                !selectedFromList &&
                this.editingBookingCarOption &&
                sid(this.editingBookingCarOption.id) === selectedId
            ) {
                list.push(this.editingBookingCarOption);
            }
            return list;
        },
        services() {
            return this.$store.state.references.services || [];
        },
        isManagerOrAdmin() {
            const u = this.$store.state.user;
            return u && (u.role_id === USER_ROLES.MANAGER || u.role_id === USER_ROLES.ADMIN);
        },
        resolvedClientId() {
            return this.editingClientId || this.user?.client_id || null;
        },
        mastersList() {
            return this.$store.state.masters.masters || [];
        },
    },
    async mounted() {
        if (this.user?.client_id) {
            this.$store.dispatch('cars/fetchUserCars', { clientId: this.user.client_id });
        }
        if (this.isManagerOrAdmin) {
            try {
                await this.$store.dispatch('masters/fetchMasters');
            } catch { /* ignore */ }
        }
    },
    watch: {
        isOpen(newVal) {
            if (newVal) {
                if (this.editingBookingId) {
                    this.initEditBooking();
                } else if (this.resolvedClientId) {
                    this.loadClientBookings();
                }
            }
            if (!newVal) {
                this.resetForm();
            }
        },
        'formData.car'() {
            this.calculatePrice();
            if (this.resolvedClientId) {
                this.loadClientBookings();
            }
        },
        'formData.services'() {
            this.calculatePrice();
        },
        'formData.date'(newDate) {
            if (newDate) {
                const selectedMoment = this.$moment(newDate, 'YYYY-MM-DD HH:mm:ss');
                const now = this.$moment();
                const minBookingTime = now.clone().subtract(5, 'minutes');
                
                if (selectedMoment.isBefore(minBookingTime)) {
                    ElMessage.warning('Мінімальний час для бронювання - через 5 хвилин від поточного часу');
                    this.$nextTick(() => {
                        this.formData.date = null;
                        this.$refs.createBookingFormRef?.validateField('date');
                    });
                } else {
                    this.checkMasterSlot();
                }
            }
        }
    },
    methods: {
        async initEditBooking() {
            try {
                const response = await this.$store.dispatch('bookings/getBooking', this.editingBookingId);
                this.editingClientId = response.client_id;
                this.editingBookingCarOption =
                    response.car && typeof response.car === 'object' ? response.car : null;

                await this.$store.dispatch('cars/fetchUserCars', {
                    clientId: response.client_id,
                    force: true,
                });

                let formattedDate = null;
                if (response.date) {
                    formattedDate = this.$moment(response.date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss');
                }

                const carId = response.car_id != null ? Number(response.car_id) : null;

                this.formData = {
                    car: carId,
                    services: (response.services || []).map((s) => Number(s.id)),
                    date: formattedDate,
                    comment: response.description,
                    master_id: response.master_id != null ? Number(response.master_id) : null,
                };
                this.calculatePrice();
            } catch {
                ElMessage.error('Не вдалося завантажити запис');
            }
        },
        carOptionLabel(car) {
            const name = car.full_name ?? '';
            const year = car.car_year ?? '';
            const plate = car.license_plate ?? '';
            return `${name} ${year} - ${plate}`.trim();
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (time.getTime() < today.getTime()) {
                return true;
            }
            
            if (this.user?.client_id && this.unavailableDates.size > 0) {
                const dateKey = this.$moment(time).format('YYYY-MM-DD');
                if (this.unavailableDates.has(dateKey)) {
                    return true;
                }
            }
            
            return false;
        },
        disabledTime(time, date) {
            const now = this.$moment();
            const today = this.$moment().startOf('day');
            const WORK_START_HOUR = 9;
            const WORK_END_HOUR = 18;
            
            if (date) {
                let selectedDate;
                
                if (date instanceof Date) {
                    selectedDate = this.$moment(date);
                } else if (typeof date === 'string') {
                    selectedDate = this.$moment(date, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD', 'DD.MM.YYYY HH:mm'], true);
                } else {
                    selectedDate = this.$moment(date);
                }
                
                if (!selectedDate.isValid()) {
                    return {};
                }
                
                const isToday = selectedDate.isSame(today, 'day');
                
                const minBookingTime = now.clone().add(1, 'hour');
                const minBookingHour = minBookingTime.hour();
                const minBookingMinute = minBookingTime.minute();
                
                return {
                    disabledHours: () => {
                        const hours = [];
                        
                        for (let i = 0; i < WORK_START_HOUR; i++) {
                            hours.push(i);
                        }
                        
                        for (let i = WORK_END_HOUR + 1; i < 24; i++) {
                            hours.push(i);
                        }
                        
                        if (isToday) {
                            for (let i = WORK_START_HOUR; i < minBookingHour; i++) {
                                if (!hours.includes(i)) {
                                    hours.push(i);
                                }
                            }
                        }
                        
                        return hours;
                    },
                    disabledMinutes: (selectedHour) => {
                        const minutes = [];
                        
                        if (isToday && selectedHour === minBookingHour) {
                            for (let i = 0; i <= minBookingMinute; i++) {
                                minutes.push(i);
                            }
                        }
                        
                        return minutes;
                    },
                };
            }
            
            return {
                disabledHours: () => {
                    const hours = [];
                    for (let i = 0; i < WORK_START_HOUR; i++) {
                        hours.push(i);
                    }
                    for (let i = WORK_END_HOUR + 1; i < 24; i++) {
                        hours.push(i);
                    }
                    return hours;
                },
                disabledMinutes: () => {
                    return [];
                },
            };
        },
        handleClose() {
            this.resetForm();
            this.$emit('close');
        },
        handleSubmit() {
            this.loading = true;
            const formRef = this.$refs.createBookingFormRef;
            
            if (!formRef) return;
            
            formRef.validate().then((valid) => {
                if (!valid) return;

                const data = {
                    car_id: this.formData.car,
                    service_ids: this.formData.services,
                    date: this.formData.date,
                    comment: this.formData.comment,
                };
                if (this.isManagerOrAdmin) {
                    data.master_id = this.formData.master_id ?? null;
                }

                const payload = {
                    data,
                    client_id: this.resolvedClientId,
                };

                if (this.editingBookingId) {
                    payload.booking_id = this.editingBookingId;
                }

                const method = this.editingBookingId ? 'updateBooking' : 'createBooking';

                this.$store.dispatch('bookings/' + method, payload)
                    .then(() => {
                        ElMessage.success(this.editingBookingId ? 'Запис успішно оновлений' : 'Запис успішно створений');
                        this.handleClose();
                    })
                    .catch((error) => {
                        console.log(error);
                        ElMessage.error(error.response?.data?.message || (this.editingBookingId ? 'Помилка при оновленні запису' : 'Помилка при створенні запису'));
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },
        calculatePrice() {
            if (!this.formData.car || !this.formData.services || this.formData.services.length === 0) {
                this.priceForServices = {
                    services: [],
                    total_price: 0,
                };
                this.priceCollapseOpen = false;
                return;
            }

            this.$store.dispatch('bookings/calculatePrice', {
                car_id: this.formData.car,
                service_ids: this.formData.services
            }).then(response => {
                this.priceForServices = response.data;
                if (response.data.total_price > 0) {
                }
            }).catch(error => {
                console.error('Помилка при розрахунку ціни:', error);
                this.priceForServices = {
                    services: [],
                    total_price: 0,
                };
            });
        },
        formatPrice(price) {
            return new Intl.NumberFormat('uk-UA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
        },
        async checkMasterSlot() {
            this.masterConflict = null;
            if (!this.formData.master_id || !this.formData.date) return;

            try {
                const data = await this.$store.dispatch('masters/checkMasterAvailability', {
                    master_id: this.formData.master_id,
                    date: this.formData.date,
                    exclude_booking_id: this.editingBookingId || null,
                });
                if (!data.available) {
                    this.masterConflict = data.message;
                }
            } catch { /* ignore */ }
        },
        async loadClientBookings() {
            const clientId = this.resolvedClientId;
            if (!clientId || this.loadingBookings) {
                return;
            }

            this.loadingBookings = true;
            try {
                const bookings = await this.$store.dispatch('bookings/fetchUserBookings', { clientId, force: true });
                
                this.unavailableDates.clear();
                
                bookings.forEach(booking => {
                    if (this.editingBookingId && booking.id === this.editingBookingId) {
                        return;
                    }
                    
                    if (booking.date) {
                        const dateKey = this.$moment(booking.date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD');
                        this.unavailableDates.add(dateKey);
                    }
                });
            } catch (error) {
                console.error('Помилка при завантаженні бронювань:', error);
            } finally {
                this.loadingBookings = false;
            }
        },
        resetForm() {
            this.loading = false;
            this.priceCollapseOpen = false;
            this.priceForServices = {
                services: [],
                total_price: 0,
            };
            this.formData = { car: null, services: [], date: null, comment: null, master_id: null };
            this.masterConflict = null;
            this.editingClientId = null;
            this.editingBookingCarOption = null;
            this.unavailableDates.clear();
            this.loadingBookings = false;
            this.$refs.createBookingFormRef?.resetFields();
        }
    },
};
</script>

<style scoped>
.input-icon :deep(.input) {
    padding-left: 2.5rem;
    padding-right: 2.5rem;
}

.w-5 {
    width: 1.25rem;
}

.h-5 {
    height: 1.25rem;
}

.mr-2 {
    margin-right: 0.5rem;
}
</style>
