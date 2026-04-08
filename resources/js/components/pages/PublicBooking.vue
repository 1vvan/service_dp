<template>
    <div class="public-booking-page">
        <div class="public-booking-background">
            <div class="public-booking-blob public-booking-blob-1"></div>
            <div class="public-booking-blob public-booking-blob-2"></div>
        </div>

        <header class="public-booking-header">
            <div class="public-booking-header-inner">
                <router-link to="/" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    На головну
                </router-link>
            </div>
        </header>

        <div class="public-booking-container" v-if="step === 'form'">
            <div class="public-booking-intro animate-slide-up">
                <div class="intro-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Онлайн-запис</span>
                </div>
                <h1 class="public-booking-title">
                    Записатися на <span class="text-gradient">прийом</span>
                </h1>
                <p class="public-booking-subtitle">
                    Заповніть форму — ми передзвонимо для підтвердження запису.
                </p>
            </div>

            <form class="public-booking-form glass animate-slide-up" @submit.prevent="handleSubmit">
                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Контактні дані
                    </h3>
                    <div class="form-row form-row-2">
                        <el-form-item label="ПІБ" prop="full_name" :error="firstError('full_name')">
                            <el-input v-model="form.full_name" placeholder="Іван Петренко" maxlength="255" show-word-limit />
                        </el-form-item>
                        <el-form-item label="Телефон" prop="phone" :error="firstError('phone')">
                            <PhoneInput v-model="form.phone" placeholder="+380 (00) 000 00 00" />
                        </el-form-item>
                    </div>
                    <el-form-item label="Email (необов'язково)" prop="email">
                        <el-input v-model="form.email" type="email" placeholder="email@example.com" maxlength="255" />
                    </el-form-item>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9-.6 1.3-.6 2.7-.6 2.7s-.6 1.3-1.4 2.2C1.2 15 0 16.2 0 17.5V19c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        Автомобіль
                    </h3>
                    <div class="form-row form-row-2">
                        <el-form-item label="Марка" prop="car.brand_id">
                            <el-select v-model="form.car.brand_id" placeholder="Оберіть марку" filterable clearable @change="onBrandChange" class="full-width">
                                <el-option v-for="b in carBrands" :key="b.id" :label="b.name" :value="b.id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Модель" prop="car.car_model_id">
                            <el-select v-model="form.car.car_model_id" placeholder="Спочатку оберіть марку" :disabled="!form.car.brand_id" filterable clearable class="full-width">
                                <el-option v-for="m in carModelsFiltered" :key="m.id" :label="m.name" :value="m.id" />
                            </el-select>
                        </el-form-item>
                    </div>
                    <div class="form-row form-row-2">
                        <el-form-item label="Рік випуску" prop="car.year">
                            <el-input-number v-model="form.car.year" :min="1990" :max="currentYear" placeholder="Рік" class="full-width" />
                        </el-form-item>
                        <el-form-item label="Номерний знак" prop="car.licence_plate">
                            <el-input v-model="form.car.licence_plate" placeholder="АА 1234 ВВ" maxlength="10" />
                        </el-form-item>
                    </div>
                    <el-form-item label="VIN-код" prop="car.vin">
                        <el-input v-model="form.car.vin" placeholder="17 символів (наприклад, WBADT43452G123456)" maxlength="17" show-word-limit />
                    </el-form-item>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        Послуги
                    </h3>
                    <el-form-item label="Оберіть послуги" prop="service_ids">
                        <el-select v-model="form.service_ids" multiple placeholder="Додайте послуги..." class="full-width">
                            <el-option v-for="s in services" :key="s.id" :label="s.name" :value="s.id" />
                        </el-select>
                    </el-form-item>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Дата та час
                    </h3>
                    <el-form-item label="Зручна дата та час" prop="date">
                        <el-date-picker
                            v-model="form.date"
                            type="datetime"
                            placeholder="Оберіть дату і час"
                            format="DD.MM.YYYY HH:mm"
                            value-format="YYYY-MM-DD HH:mm:ss"
                            :disabled-date="disabledDate"
                            :disabled-time="disabledTime"
                            class="full-width"
                        />
                    </el-form-item>
                </div>

                <el-form-item label="Коментар (необов'язково)" prop="comment">
                    <el-input v-model="form.comment" type="textarea" :rows="3" placeholder="Опишіть проблему або побажання..." maxlength="1000" show-word-limit />
                </el-form-item>

                <div class="form-actions">
                    <Button type="submit" variant="hero" size="lg" :disabled="submitDisabled" class="submit-btn">
                        <span v-if="loading">Відправка...</span>
                        <span v-else>Записатися</span>
                    </Button>
                </div>
            </form>
        </div>

        <!-- Step: prompt -->
        <div class="public-booking-success animate-slide-up" v-else-if="step === 'prompt'">
            <div class="success-card glass">
                <div class="success-icon prompt-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/></svg>
                </div>
                <h2 class="success-title">Запис прийнято!</h2>
                <p class="success-text">
                    Хочете стежити за статусом запису та історією обслуговування вашого авто в особистому кабінеті? Реєстрація займе менше хвилини — усі дані вже заповнено.
                </p>
                <div class="prompt-actions">
                    <Button variant="hero" size="lg" @click="step = 'register'" style="width: 100%;">
                        Створити акаунт
                    </Button>
                    <Button variant="hero-outline" size="lg" @click="$router.push('/')" style="width: 100%;">
                        Без реєстрації
                    </Button>
                </div>
            </div>
        </div>

        <!-- Step: register -->
        <div class="public-booking-container animate-slide-up" v-else-if="step === 'register'">
            <div class="public-booking-intro">
                <div class="intro-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Реєстрація</span>
                </div>
                <h1 class="public-booking-title">Залишився <span class="text-gradient">один крок</span></h1>
                <p class="public-booking-subtitle">
                    Дані з форми запису вже заповнено — придумайте лише пароль.
                </p>
            </div>

            <form class="public-booking-form glass" @submit.prevent="handleRegister">
                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Ваші дані
                    </h3>
                    <div class="form-row form-row-2">
                        <el-form-item label="ПІБ" :error="regError('name')">
                            <el-input v-model="regForm.name" placeholder="Іван Петренко" maxlength="255" />
                        </el-form-item>
                        <el-form-item label="Телефон">
                            <el-input v-model="regForm.phoneDisplay" disabled />
                        </el-form-item>
                    </div>
                    <el-form-item label="Email" :error="regError('email')">
                        <el-input v-model="regForm.email" type="email" placeholder="email@example.com" maxlength="255" />
                    </el-form-item>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Пароль
                    </h3>
                    <div class="form-row form-row-2">
                        <el-form-item label="Пароль" :error="regError('password')">
                            <el-input v-model="regForm.password" type="password" placeholder="••••••••" show-password />
                        </el-form-item>
                        <el-form-item label="Підтвердження пароля" :error="regError('password_confirmation')">
                            <el-input v-model="regForm.password_confirmation" type="password" placeholder="••••••••" show-password />
                        </el-form-item>
                    </div>
                </div>

                <div class="form-actions">
                    <Button type="submit" variant="hero" size="lg" :disabled="regLoading" class="submit-btn">
                        <span v-if="regLoading">Реєстрація...</span>
                        <span v-else>Зареєструватися</span>
                    </Button>
                    <button type="button" class="skip-link" @click="$router.push('/')">
                        Пропустити
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus';
import axios from 'axios';
import Button from '../ui/Button.vue';
import PhoneInput from '../ui/PhoneInput.vue';
import { normalizePhone, isUaMobileComplete, PHONE_INPUT_PREFIX } from '../../lib/utils';
import { USER_ROLES } from '../../constants/types';

export default {
    name: 'PublicBooking',
    components: { Button, PhoneInput },
    data() {
        const currentYear = new Date().getFullYear();
        return {
            step: 'form', // 'form' | 'prompt' | 'register'
            loading: false,
            currentYear,
            carBrands: [],
            carModels: [],
            services: [],
            form: {
                full_name: '',
                phone: PHONE_INPUT_PREFIX,
                email: '',
                car: {
                    brand_id: null,
                    car_model_id: null,
                    year: currentYear,
                    licence_plate: '',
                    vin: '',
                },
                service_ids: [],
                date: null,
                comment: '',
            },
            formErrors: {},
            regForm: {
                name: '',
                email: '',
                phone: '',
                phoneDisplay: '',
                password: '',
                password_confirmation: '',
            },
            regErrors: {},
            regLoading: false,
        };
    },
    computed: {
        carModelsFiltered() {
            if (!this.form.car.brand_id) return [];
            return this.carModels.filter(m => m.brand_id === this.form.car.brand_id);
        },
        submitDisabled() {
            return this.loading ||
                !this.form.full_name?.trim() ||
                !isUaMobileComplete(normalizePhone(this.form.phone)) ||
                !this.form.car.brand_id ||
                !this.form.car.car_model_id ||
                !this.form.car.year ||
                !this.form.car.licence_plate?.trim() ||
                !this.form.car.vin?.trim() ||
                this.form.service_ids.length === 0 ||
                !this.form.date;
        },
    },
    async mounted() {
        await Promise.all([
            this.fetchCarBrands(),
            this.fetchCarModels(),
            this.fetchServices(),
        ]);
    },
    methods: {
        async fetchCarBrands() {
            const res = await axios.get('/api/references/car-brands');
            this.carBrands = res.data || [];
        },
        async fetchCarModels() {
            const res = await axios.get('/api/references/car-models');
            this.carModels = res.data || [];
        },
        async fetchServices() {
            const res = await axios.get('/api/references/services');
            this.services = res.data || [];
        },
        onBrandChange() {
            this.form.car.car_model_id = null;
        },
        firstError(field) {
            const v = this.formErrors[field];
            return Array.isArray(v) ? v[0] : (v || '');
        },
        removeService(id) {
            this.form.service_ids = this.form.service_ids.filter(s => s !== id);
        },
        serviceName(id) {
            const s = this.services.find(x => x.id === id);
            return s ? s.name : '';
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() < today.getTime();
        },
        disabledTime(date) {
            const hours = () => {
                const h = [];
                for (let i = 0; i < 9; i++) h.push(i);
                for (let i = 19; i < 24; i++) h.push(i);
                return h;
            };
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const selectedDay = date ? new Date(date.getFullYear(), date.getMonth(), date.getDate()) : null;
            const isToday = selectedDay && selectedDay.getTime() === today.getTime();
            const minutes = (selectedHour) => {
                if (!isToday || selectedHour < 9 || selectedHour > 18) return [];
                const m = [];
                const min = now.getHours() === selectedHour ? now.getMinutes() + 1 : 0;
                for (let i = 0; i <= min; i++) m.push(i);
                return m;
            };
            return { disabledHours: hours, disabledMinutes: minutes };
        },
        async handleSubmit() {
            this.formErrors = {};
            if (this.submitDisabled) return;
            this.loading = true;
            try {
                const normalizedPhone = normalizePhone(this.form.phone);
                const payload = {
                    full_name: this.form.full_name.trim(),
                    phone: normalizedPhone,
                    email: this.form.email?.trim() || null,
                    car: {
                        brand_id: this.form.car.brand_id,
                        car_model_id: this.form.car.car_model_id,
                        year: this.form.car.year,
                        licence_plate: this.form.car.licence_plate.trim(),
                        mileage: 0,
                        vin: this.form.car.vin?.trim() || null,
                    },
                    service_ids: this.form.service_ids,
                    date: this.form.date,
                    comment: this.form.comment?.trim() || null,
                };
                await axios.post('/api/public/booking', payload);

                // Pre-fill registration form with booking data
                this.regForm.name = this.form.full_name.trim();
                this.regForm.email = this.form.email?.trim() || '';
                this.regForm.phone = normalizedPhone;
                this.regForm.phoneDisplay = normalizedPhone;

                this.step = 'prompt';
            } catch (err) {
                const data = err.response?.data;
                if (data?.errors) {
                    this.formErrors = data.errors;
                }
                ElMessage.error(data?.message || 'Помилка при створенні запису');
            } finally {
                this.loading = false;
            }
        },

        async handleRegister() {
            this.regErrors = {};

            if (!this.regForm.name.trim()) {
                this.regErrors.name = ["Будь ласка, введіть ваше ім'я"];
            }
            if (!this.regForm.email.trim()) {
                this.regErrors.email = ['Будь ласка, введіть електронну пошту'];
            }
            if (!this.regForm.password) {
                this.regErrors.password = ['Будь ласка, введіть пароль'];
            } else if (this.regForm.password.length < 6) {
                this.regErrors.password = ['Пароль повинен містити мінімум 6 символів'];
            }
            if (this.regForm.password !== this.regForm.password_confirmation) {
                this.regErrors.password_confirmation = ['Паролі не співпадають'];
            }
            if (Object.keys(this.regErrors).length > 0) return;

            this.regLoading = true;
            try {
                await this.$store.dispatch('register', {
                    name: this.regForm.name,
                    email: this.regForm.email,
                    phone: this.regForm.phone,
                    password: this.regForm.password,
                    password_confirmation: this.regForm.password_confirmation,
                });
                ElMessage.success('Акаунт створено! Ласкаво просимо!');
                const user = this.$store.state.user;
                if (user?.role_id === USER_ROLES.MASTER) {
                    this.$router.push('/dashboard/master');
                } else {
                    this.$router.push('/dashboard');
                }
            } catch (err) {
                const data = err.response?.data;
                if (data?.errors) {
                    this.regErrors = data.errors;
                }
                ElMessage.error(data?.message || err.message || 'Помилка реєстрації');
            } finally {
                this.regLoading = false;
            }
        },

        regError(field) {
            const v = this.regErrors[field];
            return Array.isArray(v) ? v[0] : (v || '');
        },
    },
};
</script>

<style scoped>
.prompt-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 0 auto;
    margin-top: 8px;
    width: 100%;
    max-width: 320px;
}

.prompt-icon {
    color: var(--color-primary, #6366f1);
}

.skip-link {
    background: none;
    border: none;
    color: var(--color-text-muted, #9ca3af);
    font-size: 0.875rem;
    cursor: pointer;
    padding: 8px;
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color 0.2s;
}

.skip-link:hover {
    color: var(--color-text, #374151);
}
</style>
