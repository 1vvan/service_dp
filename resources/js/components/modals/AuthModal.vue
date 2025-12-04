<template>
    <el-dialog
        v-model="dialogVisible"
        :title="mode === 'login' ? 'Ласкаво просимо назад' : 'Створити акаунт'"
        width="450px"
        :close-on-click-modal="false"
        :close-on-press-escape="true"
        @close="handleClose"
        class="auth-modal-dialog"
    >
        <template #header>
            <div class="auth-modal-header">
                <h2 class="auth-modal-title">
                    {{ mode === 'login' ? 'Ласкаво просимо назад' : 'Створити акаунт' }}
                </h2>
                <p class="auth-modal-subtitle">
                    {{ mode === 'login' 
                        ? 'Увійдіть, щоб отримати доступ до панелі керування' 
                        : 'Зареєструйтеся, щоб почати керувати обслуговуванням автомобіля' 
                    }}
                </p>
            </div>
        </template>

        <el-tabs v-model="mode" class="auth-modal-tabs">
            <el-tab-pane label="Увійти" name="login">
                <el-form
                    ref="loginFormRef"
                    :model="formData"
                    :rules="loginRules"
                    label-position="top"
                    @submit.prevent="handleSubmit"
                >
                    <el-form-item label="Електронна пошта" prop="email">
                        <el-input
                            v-model="formData.email"
                            type="email"
                            placeholder="ivan@example.com"
                            size="large"
                        />
                    </el-form-item>

                    <el-form-item label="Пароль" prop="password">
                        <el-input
                            v-model="formData.password"
                            :type="showPassword ? 'text' : 'password'"
                            size="large"
                            @click:append-icon="togglePassword"
                            show-password
                        />
                    </el-form-item>

                    <el-form-item>
                        <div class="text-right">
                            <el-link type="primary" :underline="false" @click="handleForgotPassword">
                                Забули пароль?
                            </el-link>
                        </div>
                    </el-form-item>

                    <el-form-item>
                        <el-button
                            type="primary"
                            size="large"
                            class="w-full"
                            native-type="submit"
                            :loading="loading"
                        >
                            Увійти
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>

            <el-tab-pane label="Зареєструватися" name="register">
                <el-form
                    ref="registerFormRef"
                    :model="formData"
                    :rules="registerRules"
                    label-position="top"
                    @submit.prevent="handleSubmit"
                >
                    <el-form-item label="Повне ім'я" prop="name">
                        <el-input
                            v-model="formData.name"
                            type="text"
                            placeholder="Іван Іванов"
                            size="large"
                        />
                    </el-form-item>

                    <el-form-item label="Електронна пошта" prop="email">
                        <el-input
                            v-model="formData.email"
                            type="email"
                            placeholder="ivan@example.com"
                            size="large"
                        />
                    </el-form-item>

                    <el-form-item label="Телефон" prop="phone">
                        <el-input
                            v-model="formData.phone"
                            type="tel"
                            placeholder="+380 (00) 000 00 00"
                            size="large"
                            @input="handlePhoneInput"
                        />
                    </el-form-item>

                    <el-form-item label="Пароль" prop="password">
                        <el-input
                            v-model="formData.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            size="large"
                            show-password
                        />
                    </el-form-item>

                    <el-form-item label="Підтвердження пароля" prop="password_confirmation">
                        <el-input
                            v-model="formData.password_confirmation"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            size="large"
                            show-password
                        />
                    </el-form-item>

                    <el-form-item>
                        <el-button
                            type="primary"
                            size="large"
                            class="w-full"
                            native-type="submit"
                            :loading="loading"
                        >
                            Створити акаунт
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { normalizePhone, formatPhone } from '../../lib/utils';

export default {
    name: 'AuthModal',
    props: {
        isOpen: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close'],
    data() {
        return {
            mode: 'login',
            showPassword: false,
            loading: false,
            formData: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                phone: ''
            },
            loginRules: {
                email: [
                    { required: true, message: 'Будь ласка, введіть електронну пошту', trigger: 'blur' },
                    { type: 'email', message: 'Будь ласка, введіть коректну електронну пошту', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: 'Будь ласка, введіть пароль', trigger: 'blur' },
                    { min: 6, message: 'Пароль повинен містити мінімум 6 символів', trigger: 'blur' }
                ]
            },
            registerRules: {
                name: [
                    { required: true, message: 'Будь ласка, введіть ваше ім\'я', trigger: 'blur' },
                    { min: 2, message: 'Ім\'я повинно містити мінімум 2 символи', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: 'Будь ласка, введіть електронну пошту', trigger: 'blur' },
                    { type: 'email', message: 'Будь ласка, введіть коректну електронну пошту', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: 'Будь ласка, введіть пароль', trigger: 'blur' },
                    { min: 6, message: 'Пароль повинен містити мінімум 6 символів', trigger: 'blur' }
                ],
                password_confirmation: [
                    { required: true, message: 'Будь ласка, підтвердіть пароль', trigger: 'blur' },
                    {
                        validator: (rule, value, callback) => {
                            if (value !== this.formData.password) {
                                callback(new Error('Паролі не співпадають'));
                            } else {
                                callback();
                            }
                        },
                        trigger: 'blur'
                    }
                ],
                phone: [
                    { required: true, message: 'Будь ласка, введіть ваш телефон', trigger: 'blur' },
                    {
                        validator: (rule, value, callback) => {
                            if (!value) {
                                callback();
                                return;
                            }
                            const phonePattern = /^\+380\s\(\d{2}\)\s\d{3}\s\d{2}\s\d{2}$/;
                            if (!phonePattern.test(value)) {
                                callback(new Error('Будь ласка, введіть коректний телефон у форматі +380 (00) 000 00 00'));
                            } else {
                                callback();
                            }
                        },
                        trigger: 'blur'
                    }
                ]
            }
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
        }
    },
    watch: {
        isOpen(newVal) {
            if (!newVal) {
                this.resetForm();
            }
        }
    },
    methods: {
        handleClose() {
            this.$emit('close');
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        handlePhoneInput(value) {
            this.formData.phone = formatPhone(value);
        },
        handleSubmit() {
            const formRef = this.mode === 'login' ? this.$refs.loginFormRef : this.$refs.registerFormRef;
            
            if (!formRef) return;
            
            formRef.validate().then((valid) => {
                if (!valid) return;

                const payload = this.mode === 'login' 
                    ? {
                        email: this.formData.email,
                        password: this.formData.password
                    }
                    : {
                        name: this.formData.name,
                        email: this.formData.email,
                        password: this.formData.password,
                        password_confirmation: this.formData.password_confirmation,
                        phone: normalizePhone(this.formData.phone)
                    };
    
                const action = this.mode === 'login' ? 'login' : 'register';
                this.loading = true;
                this.$store.dispatch(action, payload).then(() => {
                    ElMessage.success(
                        this.mode === 'login' 
                            ? 'Ласкаво просимо назад!' 
                            : 'Акаунт створено!'
                    );
                    this.handleClose();
                    this.resetForm();
                    this.$router.push('/dashboard');
                }).catch((error) => {
                    const errorMessage = error.response?.data?.message || error.message || 'Сталася помилка';
                    ElMessage.error(errorMessage);
                }).finally(() => {
                    this.loading = false;
                });
            });

        },
        handleForgotPassword() {
            ElMessage.info('Функція відновлення пароля буде доступна найближчим часом');
        },
        resetForm() {
            this.formData = { name: '', email: '', password: '', password_confirmation: '', phone: '' };
            this.showPassword = false;
            this.mode = 'login';
            this.$refs.loginFormRef?.resetFields();
            this.$refs.registerFormRef?.resetFields();
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
