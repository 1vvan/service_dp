<template>
    <el-dialog
        v-model="dialogVisible"
        title="Додати автомобіль"
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
                        <el-icon><Van /></el-icon>
                    </div>
                    Додати автомобіль
                </h2>
                <p class="create-form-modal-description">
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
            <div class="form-row">
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
            </div>

            <div class="form-row">
                <el-form-item label="Рік випуску" prop="year">
                    <el-select
                        v-model="formData.year"
                        placeholder="Виберіть рік випуску"
                        :options="carYears"
                        :props="{ label: 'name', value: 'id' }"
                        clearable
                    >
                        <el-option
                            v-for="year in carYears"
                            :key="year.id"
                            :label="year.name"
                            :value="year.id"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="Пробіг" prop="mileage">
                    <el-input
                        v-model="formData.mileage"
                        placeholder="Введіть пробіг"
                        type="number"
                        min="0"
                        max="1000000"
                        clearable
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="Номерний знак" prop="licence_plate">
                    <el-input
                        v-model="formData.licence_plate"
                        placeholder="АА 0000 АА"
                        type="text"
                        clearable
                        maxlength="10"
                        @input="formatLicencePlateValue"
                        style="text-transform: uppercase;"
                    />
                </el-form-item>
            </div>

            <div class="form-row">
                <el-form-item label="VIN" prop="vin">
                    <el-input
                        v-model="formData.vin"
                        placeholder="ZFFKW64A690167250"
                        type="text"
                        clearable
                    />
                </el-form-item>
            </div>
        </el-form>

        <template #footer>
            <div class="create-form-modal-footer">
                <el-button @click="handleClose">Скасувати</el-button>
                <el-button type="primary" @click="handleSubmit">Додати автомобіль</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { Van } from '@element-plus/icons-vue';
import { formatLicencePlate } from '../../lib/utils';

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
                year: null,
                mileage: null
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
                mileage: [
                    { required: true, message: 'Введіть пробіг', trigger: 'change' }
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
        user() {
            return this.$store.state.user;
        },
        carModels() {
            return this.$store.state.references.carModels;
        },
        carBrands() {
            return this.$store.state.references.carBrands;
        },
        filteredCarModels() {
            return this.carModels.filter(model => model.brand_id === this.formData.brand) || [];
        },
        carYears() {
            return Array.from({ length: new Date().getFullYear() + 1 - 1900 }, (_, i) => ({
                id: i + 1900,
                name: i + 1900
            })).reverse();
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
            this.resetForm();
            this.$emit('close');
        },
        handleSubmit() {
            this.loading = true;
            const formRef = this.$refs.createCarFormRef;
            
            if (!formRef) return;
            
            formRef.validate().then((valid) => {
                if (!valid) return;

                const payload = {
                    data: {
                        model: this.formData.model,
                        licence_plate: this.formData.licence_plate,
                        vin: this.formData.vin,
                        year: this.formData.year,
                        mileage: this.formData.mileage,
                        brand_id: this.formData.brand,
                    },
                    client_id: this.user.client_id,
                };

                this.$store.dispatch('cars/createCar', payload)
                    .then(() => {
                        ElMessage.success('Автомобіль успішно додано');
                        this.handleClose();
                    })
                    .catch((error) => {
                        console.log(error);
                        ElMessage.error(error.response?.data?.message || 'Помилка при додаванні автомобіля');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },
        resetForm() {
            this.loading = false;
            this.formData = { brand: null, model: null, licence_plate: null, vin: null, year: null, mileage: null };
            this.$refs.createCarFormRef?.resetFields();
        },
        formatLicencePlateValue(value) {
            this.formData.licence_plate = formatLicencePlate(value);
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
