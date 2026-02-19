<template>
    <el-dialog
        v-model="dialogVisible"
        title="Додати клієнта"
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
                        <el-icon><User /></el-icon>
                    </div>
                    Додати клієнта
                </h2>
                <p class="create-form-modal-description">
                    Заповніть дані для нової картки клієнта.
                </p>
            </div>
        </template>

        <el-form
            ref="formRef"
            :model="formData"
            :rules="formRules"
            label-position="top"
            @submit.prevent="handleSubmit"
        >
            <div class="form-row">
                <el-form-item label="ПІБ" prop="full_name">
                    <el-input
                        v-model="formData.full_name"
                        placeholder="Повне ім'я клієнта"
                        maxlength="255"
                        show-word-limit
                        clearable
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Email" prop="email">
                    <el-input
                        v-model="formData.email"
                        type="email"
                        placeholder="email@example.com"
                        maxlength="255"
                        clearable
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Телефон" prop="phone">
                    <el-input
                        v-model="formData.phone"
                        placeholder="+380 (XX) XXX-XX-XX"
                        maxlength="255"
                        clearable
                    />
                </el-form-item>
            </div>
        </el-form>

        <template #footer>
            <div class="create-form-modal-footer">
                <el-button @click="handleClose">Скасувати</el-button>
                <el-button type="primary" :loading="saving" @click="handleSubmit">
                    Додати
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { User } from '@element-plus/icons-vue';

export default {
    name: 'CreateClientModal',
    components: {
        User
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close', 'saved'],
    data() {
        return {
            formData: {
                full_name: '',
                email: '',
                phone: ''
            },
            formRules: {
                full_name: [
                    { required: true, message: 'Введіть ПІБ клієнта', trigger: 'blur' },
                    { max: 255, message: 'Максимум 255 символів', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: 'Введіть email', trigger: 'blur' },
                    { type: 'email', message: 'Введіть коректний email', trigger: 'blur' },
                    { max: 255, message: 'Максимум 255 символів', trigger: 'blur' }
                ],
                phone: [
                    { max: 255, message: 'Максимум 255 символів', trigger: 'blur' }
                ]
            },
            saving: false
        };
    },
    computed: {
        dialogVisible: {
            get() {
                return this.isOpen;
            },
            set(value) {
                if (!value) {
                    this.$emit('close');
                }
            }
        }
    },
    watch: {
        isOpen(val) {
            if (val) {
                this.formData = { full_name: '', email: '', phone: '' };
                this.$nextTick(() => this.$refs.formRef?.clearValidate());
            }
        }
    },
    methods: {
        handleSubmit() {
            this.$refs.formRef.validate((valid) => {
                if (!valid) return;

                this.saving = true;

                this.$store.dispatch('clients/createClient', {
                    full_name: this.formData.full_name.trim(),
                    email: this.formData.email.trim(),
                    phone: (this.formData.phone || '').trim() || ''
                })
                    .then(() => {
                        ElMessage.success('Клієнта створено');
                        this.$emit('saved');
                        this.handleClose();
                    })
                    .catch((error) => {
                        ElMessage.error(error.response?.data?.message || 'Помилка створення клієнта');
                    })
                    .finally(() => {
                        this.saving = false;
                    });
            });
        },
        handleClose() {
            this.formData = { full_name: '', email: '', phone: '' };
            this.$refs.formRef?.resetFields();
            this.$emit('close');
        }
    }
};
</script>
