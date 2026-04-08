<template>
    <el-dialog
        v-model="dialogVisible"
        :title="editingServiceId ? 'Редагувати послугу' : 'Додати послугу'"
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
                        <el-icon><Setting /></el-icon>
                    </div>
                    {{ editingServiceId ? 'Редагувати послугу' : 'Додати послугу' }}
                </h2>
                <p class="create-form-modal-description">
                    {{ editingServiceId ? 'Редагуйте інформацію про послугу.' : 'Додайте нову послугу до списку.' }}
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
                <el-form-item label="Назва послуги" prop="name">
                    <el-input
                        v-model="formData.name"
                        placeholder="Наприклад: Заміна масла"
                        maxlength="255"
                        show-word-limit
                        clearable
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Категорія" prop="category_id">
                    <el-select
                        v-model="formData.category_id"
                        placeholder="Оберіть категорію"
                        clearable
                        style="width: 100%"
                    >
                        <el-option
                            v-for="cat in categories"
                            :key="cat.id"
                            :label="cat.name"
                            :value="cat.id"
                        />
                    </el-select>
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Базова вартість (грн)" prop="base_price">
                    <el-input-number
                        v-model="formData.base_price"
                        :min="0"
                        :step="50"
                        :precision="2"
                        placeholder="0.00"
                        style="width: 100%"
                    />
                </el-form-item>
            </div>
        </el-form>

        <template #footer>
            <div class="create-form-modal-footer">
                <el-button @click="handleClose">Скасувати</el-button>
                <el-button type="primary" :loading="saving" @click="handleSubmit">
                    {{ editingServiceId ? 'Зберегти' : 'Додати' }}
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { Setting } from '@element-plus/icons-vue';

export default {
    name: 'CreateServiceModal',
    components: {
        Setting
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        },
        editingServiceId: {
            type: [Number, String],
            default: null
        },
        services: {
            type: Array,
            default: () => []
        }
    },
    emits: ['close', 'saved'],
    data() {
        return {
            categories: [],
            formData: {
                name: '',
                base_price: 0,
                category_id: null,
            },
            formRules: {
                name: [
                    { required: true, message: 'Введіть назву послуги', trigger: 'blur' },
                    { max: 255, message: 'Максимум 255 символів', trigger: 'blur' }
                ],
                base_price: [
                    { required: true, message: 'Введіть вартість', trigger: 'blur' },
                    { type: 'number', min: 0, message: 'Вартість не може бути від\'ємною', trigger: 'blur' }
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
    async mounted() {
        try {
            const res = await import('axios').then(m => m.default.get('/api/references/service-categories'));
            this.categories = res.data || [];
        } catch { /* ignore */ }
    },
    watch: {
        isOpen(val) {
            if (val) {
                this.initForm();
            }
        },
        editingServiceId: {
            immediate: true,
            handler(val) {
                if (this.isOpen && val) {
                    this.initForm();
                }
            }
        }
    },
    methods: {
        initForm() {
            if (this.editingServiceId) {
                const service = this.services.find(s => s.id === this.editingServiceId);
                if (service) {
                    this.formData = {
                        name: service.name,
                        base_price: parseFloat(service.base_price),
                        category_id: service.category_id || null,
                    };
                }
            } else {
                this.formData = {
                    name: '',
                    base_price: 0,
                    category_id: null,
                };
            }
            this.$nextTick(() => {
                this.$refs.formRef?.clearValidate();
            });
        },
        handleSubmit() {
            this.$refs.formRef.validate((valid) => {
                if (!valid) return;

                this.saving = true;

                const payload = {
                    name: this.formData.name.trim(),
                    base_price: this.formData.base_price,
                    category_id: this.formData.category_id || null,
                };

                const action = this.editingServiceId
                    ? this.$store.dispatch('services/updateService', { id: this.editingServiceId, data: payload })
                    : this.$store.dispatch('services/createService', payload);

                action
                    .then(() => {
                        ElMessage.success(this.editingServiceId ? 'Послугу оновлено' : 'Послугу створено');
                        this.$emit('saved');
                        this.handleClose();
                    })
                    .catch((error) => {
                        ElMessage.error(error.response?.data?.message || 'Помилка збереження');
                    })
                    .finally(() => {
                        this.saving = false;
                    });
            });
        },
        handleClose() {
            this.formData = { name: '', base_price: 0, category_id: null };
            this.$refs.formRef?.resetFields();
            this.$emit('close');
        }
    }
};
</script>
