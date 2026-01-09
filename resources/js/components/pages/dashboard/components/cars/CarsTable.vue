<template>
    <el-table v-loading="loading" :data="cars" class="dash-table" @row-click="handleRowClick" :row-style="{ cursor: 'pointer' }">
        <el-table-column prop="full_name" label="Авто" width="100">
            <template #default="scope">
                <div class="name-column">
                    <div class="name-column-icon">
                        <img :src="scope.row.brand_logo" alt="Brand Logo" />
                    </div>
                    <div class="name-column-content">
                        <span class="name-column-top">{{ scope.row.full_name.split(' ')[0] }}</span>
                        <span class="name-column-bottom">{{ scope.row.full_name.split(' ')[1] }}</span>
                    </div>
                </div>
            </template>
        </el-table-column>
        <el-table-column prop="license_plate" label="Номер" width="100">
            <template #default="scope">
                <span class="orange-text">{{ scope.row.license_plate }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="vin" label="VIN" width="100" />
        <el-table-column prop="car_year" label="Рік" width="100" />
        <el-table-column label="Дії" width="40" align="center" class-name="actions-column">
            <template #default="scope">
                <el-tooltip :content="scope.row.checked_by ? 'Перевірено' : 'Не перевірено'" placement="top">
                    <div class="indicator" :class="scope.row.checked_by ? 'green' : 'red'"></div>
                </el-tooltip>
                <el-dropdown placement="bottom">
                    <el-button> <el-icon><Grid /></el-icon>  </el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item>
                                <div @click="editCar(scope.row.id)" style="display: flex; align-items: center; gap: 8px;">
                                    <el-icon><Edit /></el-icon>
                                    <span>Редагувати</span>
                                </div>
                            </el-dropdown-item>
                            <el-dropdown-item v-if="!scope.row.checked_by && isManagerOrAdmin">
                                <div @click="confirmCar(scope.row.id)" style="display: flex; align-items: center; gap: 8px;">
                                    <el-icon><Check /></el-icon>
                                    <span>Підтвердити</span>
                                </div>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import { Grid, Tickets, Edit, Check } from '@element-plus/icons-vue';

export default {
    name: 'CarsTable',
    components: {
        Grid,
        Tickets,
        Edit,
        Check
    },
    props: {
        cars: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    computed: {
        isManagerOrAdmin() {
            return this.$store.state.isManagerOrAdmin;
        }
    },
    data() {
        return {
        }
    },
    methods: {
        handleRowClick(row) {
            this.$router.push({ name: 'CarDetails', params: { id: row.id } });
        },
        editCar(carId) {
            this.$emit('edit-car', carId);
        },
        confirmCar(carId) {
            this.$store.dispatch('cars/confirmCar', carId)
                .then(() => {
                    this.$message.success('Авто підтверджено');
                })
                .catch(() => {
                    this.$message.error('Помилка підтвердження авто');
                });
        }
    }
}
</script>