<template>
    <DashboardLayout>
        <div class="car-details-container">
            <div class="top">
                <div class="header-actions">
                    <el-button @click="goBack" class="back-btn">
                        <el-icon><ArrowLeft /></el-icon>
                        <span>Назад</span>
                    </el-button>
                    <h1 class="title">Інформація про автомобіль</h1>
                </div>
                <div class="action-buttons" v-if="!loading && car && isManagerOrAdmin">
                    <template v-if="!editMode">
                        <el-button type="primary" class="edit" @click="enableEditMode">
                            <el-icon><Edit /></el-icon>
                            <span>Редагувати</span>
                        </el-button>
                        <el-button class="photo-btn" @click="openUploadPhotoModal">
                            <el-icon><Picture /></el-icon>
                            <span>Додати фото</span>
                        </el-button>
                    </template>
                    <template v-else>
                        <el-button @click="cancelEdit" class="reject">Скасувати</el-button>
                        <el-button type="primary" @click="saveCar" class="edit" :loading="saving">Зберегти</el-button>
                    </template>
                </div>
            </div>

            <UploadCarPhotoModal
                :isOpen="isUploadPhotoModalOpen"
                :carId="car?.id"
                @close="closeUploadPhotoModal"
                @uploaded="onPhotoUploaded"
            />

            <el-card v-loading="loading" class="car-details-card">
                <div v-if="!loading && car" class="car-info">
                    <div class="car-header">
                        <div class="car-brand-logo">
                            <img :src="car.brand_logo" :alt="car.brand_name" />
                        </div>
                        <div class="car-title">
                            <h2>{{ car.full_name }}</h2>
                            <span class="license-plate">{{ car.license_plate }}</span>
                        </div>
                    </div>

                    <el-form v-if="editMode" ref="editFormRef" :model="formData" :rules="formRules" label-position="top">
                        <div class="car-details-grid">
                            <el-form-item label="Марка" prop="brand_id">
                                <el-select
                                    v-model="formData.brand_id"
                                    placeholder="Виберіть марку"
                                    clearable
                                >
                                    <el-option
                                        v-for="brand in carBrands"
                                        :key="brand.id"
                                        :label="brand.name"
                                        :value="brand.id"
                                    />
                                </el-select>
                            </el-form-item>

                            <el-form-item label="Модель" prop="car_model_id">
                                <el-select
                                    v-model="formData.car_model_id"
                                    placeholder="Виберіть модель"
                                    :disabled="!formData.brand_id"
                                    clearable
                                >
                                    <el-option
                                        v-for="model in filteredCarModels"
                                        :key="model.id"
                                        :label="model.name"
                                        :value="model.id"
                                    />
                                </el-select>
                            </el-form-item>

                            <el-form-item label="Номерний знак" prop="license_plate">
                                <el-input
                                    v-model="formData.license_plate"
                                    placeholder="АА 0000 АА"
                                    maxlength="10"
                                    @input="formatLicensePlateValue"
                                />
                            </el-form-item>

                            <el-form-item label="VIN номер" prop="vin">
                                <el-input
                                    v-model="formData.vin"
                                    placeholder="ZFFKW64A690167250"
                                />
                            </el-form-item>

                            <el-form-item label="Рік випуску" prop="car_year">
                                <el-select
                                    v-model="formData.car_year"
                                    placeholder="Виберіть рік випуску"
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
                                />
                            </el-form-item>

                            <el-form-item label="Тип палива" prop="fuel_type_id">
                                <el-select
                                    v-model="formData.fuel_type_id"
                                    placeholder="Виберіть тип палива"
                                    clearable
                                >
                                    <el-option
                                        v-for="fuelType in fuelTypes"
                                        :key="fuelType.id"
                                        :label="fuelType.name"
                                        :value="fuelType.id"
                                    />
                                </el-select>
                            </el-form-item>

                            <el-form-item label="Тип двигуна" prop="engine_type_id">
                                <el-select
                                    v-model="formData.engine_type_id"
                                    placeholder="Виберіть тип двигуна"
                                    clearable
                                >
                                    <el-option
                                        v-for="engineType in engineTypes"
                                        :key="engineType.id"
                                        :label="engineType.name"
                                        :value="engineType.id"
                                    />
                                </el-select>
                            </el-form-item>

                            <el-form-item label="Тип коробки передач" prop="gearbox_type_id">
                                <el-select
                                    v-model="formData.gearbox_type_id"
                                    placeholder="Виберіть тип коробки передач"
                                    clearable
                                >
                                    <el-option
                                        v-for="gearboxType in gearboxTypes"
                                        :key="gearboxType.id"
                                        :label="gearboxType.name"
                                        :value="gearboxType.id"
                                    />
                                </el-select>
                            </el-form-item>

                            <el-form-item label="Тип приводу" prop="drive_unit_type_id">
                                <el-select
                                    v-model="formData.drive_unit_type_id"
                                    placeholder="Виберіть тип приводу"
                                    clearable
                                >
                                    <el-option
                                        v-for="driveUnitType in driveUnitTypes"
                                        :key="driveUnitType.id"
                                        :label="driveUnitType.name"
                                        :value="driveUnitType.id"
                                    />
                                </el-select>
                            </el-form-item>
                        </div>
                    </el-form>

                    <div v-else class="car-details-grid">
                        <div class="detail-item">
                            <label>Марка</label>
                            <p>{{ car.car_model.brand.name }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Модель</label>
                            <p>{{ car.car_model.name }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Номерний знак</label>
                            <p class="orange-text">{{ car.license_plate || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>VIN номер</label>
                            <p>{{ car.vin || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Рік випуску</label>
                            <p>{{ car.car_year || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Пробіг</label>
                            <p>{{ car.mileage ? car.mileage + ' км' : 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Тип палива</label>
                            <p>{{ car.fuel_type?.name || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Тип двигуна</label>
                            <p>{{ car.engine_type?.name || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Тип коробки передач</label>
                            <p>{{ car.gearbox_type?.name || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Тип приводу</label>
                            <p>{{ car.drive_unit_type?.name || 'Не вказано' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Статус перевірки</label>
                            <p>
                                <span class="status-badge" :class="car.checked_by ? 'confirmed' : 'in-progres'">{{ car.checked_by ? 'Перевірено' : 'На розгляді'}}</span>
                            </p>
                        </div>
                    </div>

                    <div v-if="car.photos && car.photos.length" class="car-photos-section">
                        <h3 class="photos-title">Фото автомобіля</h3>
                        <div class="car-photos-grid">
                            <div v-for="photo in car.photos" :key="photo.id" class="photo-item">
                                <el-image
                                    :src="photo.url"
                                    :preview-src-list="car.photos.map(p => p.url)"
                                    fit="cover"
                                    class="car-photo-img"
                                    :initial-index="car.photos.indexOf(photo)"
                                />
                                <el-button
                                    v-if="isManagerOrAdmin"
                                    type="danger"
                                    size="small"
                                    circle
                                    class="photo-delete-btn"
                                    @click="deletePhoto(photo)"
                                >
                                    <el-icon><Delete /></el-icon>
                                </el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import UploadCarPhotoModal from '../../../../modals/UploadCarPhotoModal.vue';
import { ArrowLeft, Edit, Picture, Delete } from '@element-plus/icons-vue';
import { formatLicencePlate } from '../../../../../lib/utils';

export default {
    name: 'CarDetails',
    components: {
        DashboardLayout,
        UploadCarPhotoModal,
        ArrowLeft,
        Edit,
        Picture,
        Delete
    },
    data() {
        return {
            loading: true,
            saving: false,
            car: null,
            editMode: false,
            isUploadPhotoModalOpen: false,
            formData: {
                brand_id: null,
                car_model_id: null,
                license_plate: null,
                vin: null,
                car_year: null,
                mileage: null,
                fuel_type_id: null,
                engine_type_id: null,
                gearbox_type_id: null,
                drive_unit_type_id: null
            }
        }
    },
    mounted() {
        this.loadCarDetails();
    },
    computed: {
        isManagerOrAdmin() {
            return this.$store.state.isManagerOrAdmin;
        },
        carBrands() {
            return this.$store.state.references.carBrands || [];
        },
        carModels() {
            return this.$store.state.references.carModels || [];
        },
        filteredCarModels() {
            return this.carModels.filter(model => model.brand_id === this.formData.brand_id);
        },
        engineTypes() {
            return this.$store.state.references.engineTypes || [];
        },
        fuelTypes() {
            return this.$store.state.references.fuelTypes || [];
        },
        gearboxTypes() {
            return this.$store.state.references.gearboxTypes || [];
        },
        driveUnitTypes() {
            return this.$store.state.references.driveUnitTypes || [];
        },
        carYears() {
            return Array.from({ length: new Date().getFullYear() + 1 - 1900 }, (_, i) => ({
                id: i + 1900,
                name: i + 1900
            })).reverse();
        },
        formRules() {
            return {
                brand_id: [
                    { required: true, message: 'Виберіть марку', trigger: 'change' }
                ],
                car_model_id: [
                    { required: true, message: 'Виберіть модель', trigger: 'change' }
                ],
                license_plate: [
                    { required: true, message: 'Введіть номерний знак', trigger: 'blur' }
                ],
                vin: [
                    { required: true, message: 'Введіть VIN', trigger: 'blur' }
                ],
                car_year: [
                    { required: true, message: 'Виберіть рік випуску', trigger: 'change' }
                ],
                mileage: [
                    { required: true, message: 'Введіть пробіг', trigger: 'blur' }
                ],
                fuel_type_id: [
                    { required: true, message: 'Виберіть тип палива', trigger: 'change' }
                ],
                engine_type_id: [
                    { required: true, message: 'Виберіть тип двигуна', trigger: 'change' }
                ],
                gearbox_type_id: [
                    { required: true, message: 'Виберіть тип коробки передач', trigger: 'change' }
                ],
                drive_unit_type_id: [
                    { required: true, message: 'Виберіть тип приводу', trigger: 'change' }
                ]
            };
        }
    },
    methods: {
        loadCarDetails() {
            this.loading = true;
            const carId = this.$route.params.id;
            
            this.$store.dispatch('cars/getCar', carId)
                .then((car) => {
                    this.car = car;
                    this.loading = false;
                })
                .catch(() => {
                    this.$message.error('Помилка завантаження інформації про автомобіль');
                    this.loading = false;
                    this.goBack();
                });
        },
        enableEditMode() {
            this.editMode = true;
            this.formData = {
                brand_id: this.car.car_model.brand_id,
                car_model_id: this.car.car_model_id,
                license_plate: this.car.license_plate,
                vin: this.car.vin,
                car_year: this.car.car_year,
                mileage: this.car.mileage,
                fuel_type_id: this.car.fuel_type_id,
                engine_type_id: this.car.engine_type_id,
                gearbox_type_id: this.car.gearbox_type_id,
                drive_unit_type_id: this.car.drive_unit_type_id
            };
        },
        cancelEdit() {
            this.editMode = false;
            this.$refs.editFormRef?.resetFields();
        },
        saveCar() {
            this.$refs.editFormRef.validate((valid) => {
                if (!valid) {
                    return;
                }

                this.saving = true;
                const payload = {
                    car_id: this.car.id,
                    client_id: this.car.client_id,
                    managerMode: true,
                    data: {
                        model: this.formData.car_model_id,
                        licence_plate: this.formData.license_plate,
                        vin: this.formData.vin,
                        year: this.formData.car_year,
                        mileage: this.formData.mileage,
                        brand_id: this.formData.brand_id,
                        engine_type: this.formData.engine_type_id,
                        gearbox_type: this.formData.gearbox_type_id,
                        drive_unit_type: this.formData.drive_unit_type_id,
                        fuel_type: this.formData.fuel_type_id
                    }
                };

                this.$store.dispatch('cars/updateCar', payload)
                    .then(() => {
                        this.$message.success('Автомобіль успішно оновлено');
                        this.editMode = false;
                        this.loadCarDetails();
                    })
                    .catch((error) => {
                        this.$message.error(error.response?.data?.message || 'Помилка оновлення автомобіля');
                    })
                    .finally(() => {
                        this.saving = false;
                    });
            });
        },
        formatLicensePlateValue(value) {
            this.formData.license_plate = formatLicencePlate(value);
        },
        openUploadPhotoModal() {
            this.isUploadPhotoModalOpen = true;
        },
        closeUploadPhotoModal() {
            this.isUploadPhotoModalOpen = false;
        },
        onPhotoUploaded() {
            this.loadCarDetails();
        },
        async deletePhoto(photo) {
            try {
                await this.$confirm('Видалити це фото?', 'Підтвердження', {
                    confirmButtonText: 'Так',
                    cancelButtonText: 'Скасувати',
                    type: 'warning'
                });
            } catch {
                return;
            }

            try {
                await this.$store.dispatch('cars/deleteCarPhoto', {
                    carId: this.car.id,
                    photoId: photo.id
                });
                this.$message.success('Фото видалено');
                this.loadCarDetails();
            } catch (error) {
                this.$message.error(error.response?.data?.message || 'Помилка видалення фото');
            }
        },
        goBack() {
            if (this.isManagerOrAdmin) {
                this.$router.push({ name: 'ClientCars' });
            } else {
                this.$router.push({ name: 'Cars' });
            }
        }
    }
}
</script>