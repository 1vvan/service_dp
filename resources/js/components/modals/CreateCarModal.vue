<template>
    <el-dialog
        v-model="dialogVisible"
        title="Додати автомобіль"
        width="450px"
        :close-on-click-modal="false"
        :close-on-press-escape="true"
        @close="handleClose"
        class="create-car-modal-dialog"
    >
        <template #header>
            <div class="create-car-modal-header">
                <h2 class="create-car-modal-title">
                    <div class="icon">
                        <el-icon><Van /></el-icon>
                    </div>
                    Додати автомобіль
                </h2>
                <p class="create-car-modal-description">
                    Додати новий авто до вашого гаражу.
                </p>
            </div>
        </template>

        <el-form
            ref="createCarFormRef"
            :model="formData"
            :rules="formRules"
            label-position="top"
            @submit.prevent="handleSubmit"
        >
            <el-form-item label="Марка" prop="brand">
                <el-select
                    v-model="formData.brand"
                    placeholder="Виберіть марку"
                    :options="carBrands"
                    :props="{ label: 'name', value: 'id' }"
                    clearable
                />
            </el-form-item>
            <el-form-item label="Модель" prop="model">
                <el-select
                    v-model="formData.model"
                    placeholder="Виберіть модель"
                    :options="filteredCarModels"
                    :disabled="!formData.brand"
                    :props="{ label: 'name', value: 'id' }"
                    clearable
                />
            </el-form-item>
        </el-form>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { Van } from '@element-plus/icons-vue';

export default {
    name: 'CreateCarModal',
    components: {
        Van
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        }
    },
    emits: ['close'],
    data() {
        return {
            loading: false,
            formData: {
                brand: null,
                model: null,
                licence_plate: null,
                vin: null,
                year: null
            },
            formRules: {
                brand: [
                    { required: true, message: 'Виберіть марку', trigger: 'change' }
                ],
                model: [
                    { required: true, message: 'Виберіть модель', trigger: 'change' }
                ],
                licence_plate: [
                    { required: true, message: 'Введіть номерний знак', trigger: 'change' }
                ],
                vin: [
                    { required: true, message: 'Введіть VIN', trigger: 'change' }
                ],
                year: [
                    { required: true, message: 'Введіть рік випуску', trigger: 'change' }
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
        },
        carModels() {
            return this.$store.state.references.carModels;
        },
        carBrands() {
            return this.$store.state.references.carBrands;
        },
        filteredCarModels() {
            return this.carModels.filter(model => model.brand_id === this.formData.brand) || [];
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
        handleSubmit() {
            const formRef = this.$refs.createCarFormRef;
            
            if (!formRef) return;
            
            formRef.validate().then((valid) => {
                if (!valid) return;

                const payload = {
                    model: this.formData.model,
                    licence_plate: this.formData.licence_plate,
                    vin: this.formData.vin,
                    year: this.formData.year
                };
    
                console.log(payload);
            });
        },
        resetForm() {
            this.formData = { model: null, licence_plate: null, vin: null, year: null };
            this.$refs.createCarFormRef?.resetFields();
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
