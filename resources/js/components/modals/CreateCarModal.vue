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
                        <CarIcon :size="32" />
                    </div>
                    {{ editingCarId ? 'Редагувати автомобіль' : 'Додати автомобіль' }}
                </h2>
                <p class="create-form-modal-description" v-if="!editingCarId && managerMode">
                    Додати новий авто.
                </p>
                <p class="create-form-modal-description">
                    {{ editingCarId ? 'Редагувати автомобіль' : 'Додати новий авто до вашого гаражу.' }}
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
            <div class="form-row" v-if="managerMode">
                <el-form-item label="Клієнт" prop="client_id">
                    <el-select
                        v-model="formData.client_id"
                        placeholder="Виберіть клієнта"
                        clearable
                        filterable
                    >
                        <el-option
                            v-for="client in clients"
                            :key="client.id"
                            :label="getClientLabel(client)"
                            :value="client.id"
                        >
                            <span>{{ getClientLabel(client) }}</span>
                        </el-option>
                    </el-select>
                </el-form-item>
            </div>

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

            <template v-if="isAddInfoAvailable">
                <div class="form-row">
                    <el-form-item label="Тип двигуна" prop="engine_type">
                        <el-select
                            v-model="formData.engine_type"
                            placeholder="Виберіть тип двигуна"
                            :options="engineTypes"
                            :props="{ label: 'name', value: 'id' }"
                        />
                    </el-form-item>

                    <el-form-item label="Тип палива" prop="fuel_type">
                        <el-select
                            v-model="formData.fuel_type"
                            placeholder="Виберіть тип палива"
                            :options="fuelTypes"
                            :props="{ label: 'name', value: 'id' }"
                        />
                    </el-form-item>
                </div>

                <div class="form-row">
                    <el-form-item label="Тип приводу" prop="drive_unit_type">
                        <el-select
                            v-model="formData.drive_unit_type"
                            placeholder="Виберіть тип приводу"
                            :options="driveUnitTypes"
                            :props="{ label: 'name', value: 'id' }"
                        />
                    </el-form-item>

                    <el-form-item label="Тип коробки передач" prop="gearbox_type">
                        <el-select
                            v-model="formData.gearbox_type"
                            placeholder="Виберіть тип коробки передач"
                            :options="gearboxTypes"
                            :props="{ label: 'name', value: 'id' }"
                        />
                    </el-form-item>
                </div>
            </template>
        </el-form>

        <template #footer>
            <div class="create-form-modal-footer">
                <el-button @click="handleClose">Скасувати</el-button>
                <el-button type="primary" @click="handleSubmit">{{ editingCarId ? 'Оновити автомобіль' : 'Додати автомобіль' }}</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import { ElMessage } from 'element-plus';
import { formatLicencePlate, formatPhone } from '../../lib/utils';
import CarIcon from '../ui/CarIcon.vue';

export default {
    name: 'CreateCarModal',
    components: {
        CarIcon
    },
    props: {
        isOpen: {
            type: Boolean,
            default: false
        },
        managerMode: {
            type: Boolean,
            default: false
        },
        editingCarId: {
            type: Number,
            default: null
        }
    },
    emits: ['close'],
    data() {
        return {
            loading: false,
            formData: {
                client_id: null,
                brand: null,
                model: null,
                licence_plate: null,
                vin: null,
                year: null,
                mileage: null,
                engine_type: null,
                gearbox_type: null,
                drive_unit_type: null,
                fuel_type: null
            },
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
        isAddInfoAvailable() {
            return this.editingCarId || this.managerMode;
        },
        formRules() {
            return {
                client_id: [
                    { required: this.managerMode, message: 'Виберіть клієнта', trigger: 'change' }
                ],
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
                ],
                engine_type: [
                    { required: this.isAddInfoAvailable, message: 'Виберіть тип двигуна', trigger: 'change' }
                ],
                gearbox_type: [
                    { required: this.isAddInfoAvailable, message: 'Виберіть тип коробки передач', trigger: 'change' }
                ],
                drive_unit_type: [
                    { required: this.isAddInfoAvailable, message: 'Виберіть тип приводу', trigger: 'change' }
                ],
                fuel_type: [
                    { required: this.isAddInfoAvailable, message: 'Виберіть тип палива', trigger: 'change' }
                ]
            };
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
        clients() {
            return this.$store.state.references.clients;
        },
        filteredCarModels() {
            return this.carModels.filter(model => model.brand_id === this.formData.brand) || [];
        },
        engineTypes() {
            return this.$store.state.references.engineTypes;
        },
        gearboxTypes() {
            return this.$store.state.references.gearboxTypes;
        },
        driveUnitTypes() {
            return this.$store.state.references.driveUnitTypes;
        },
        fuelTypes() {
            return this.$store.state.references.fuelTypes;
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
            if (newVal) {
                if (this.editingCarId) {
                    this.initEditCar();
                }
            }
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
                        engine_type: this.formData.engine_type,
                        gearbox_type: this.formData.gearbox_type,
                        drive_unit_type: this.formData.drive_unit_type,
                        fuel_type: this.formData.fuel_type,
                    },
                    car_id: this.editingCarId,
                    client_id: this.formData.client_id || this.user.client_id,
                    managerMode: this.managerMode,
                };

                const action = this.editingCarId ? 'updateCar' : 'createCar';

                this.$store.dispatch(`cars/${action}`, payload)
                    .then(() => {
                        ElMessage.success(this.editingCarId ? 'Автомобіль успішно оновлено' : 'Автомобіль успішно додано');
                        this.handleClose();
                    })
                    .catch((error) => {
                        console.log(error);
                        ElMessage.error(error.response?.data?.message || (this.editingCarId ? 'Помилка при оновленні автомобіля' : 'Помилка при додаванні автомобіля'));
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            });
        },
        resetForm() {
            this.loading = false;
            this.formData = { brand: null, model: null, licence_plate: null, vin: null, year: null, mileage: null, engine_type: null, gearbox_type: null, drive_unit_type: null, fuel_type: null };
            this.$refs.createCarFormRef?.resetFields();
        },
        formatLicencePlateValue(value) {
            this.formData.licence_plate = formatLicencePlate(value);
        },
        getClientLabel(client) {
            const name = client.full_name || 'Без імені';
            const phone = client.phone ? formatPhone(client.phone) : 'Немає телефону';
            return `${name} | ${phone}`;
        },
        initEditCar() {
            this.$store.dispatch('cars/getCar', this.editingCarId).then(response => {
                this.formData = {
                    brand: response.car_model.brand_id,
                    model: response.car_model_id,
                    licence_plate: formatLicencePlate(response.license_plate),
                    vin: response.vin,
                    year: response.car_year,
                    mileage: response.mileage,
                    engine_type: response.engine_type_id,
                    gearbox_type: response.gearbox_type_id,
                    drive_unit_type: response.drive_unit_type_id,
                    fuel_type: response.fuel_type_id,
                };

                if (this.managerMode) {
                    this.formData.client_id = response.client_id;
                }
            }).catch((error) => {
                console.log(error);
                ElMessage.error('Помилка при завантаженні автомобіля');
            });
        },
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
