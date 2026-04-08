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
        <el-table-column v-if="isManagerOrAdmin" label="Дії" width="56" align="center" class-name="actions-column">
            <template #default="scope">
                <div class="actions-cell" @click.stop @mousedown.stop>
                    <el-tooltip
                        v-if="!scope.row.checked_by"
                        content="Підтвердити авто"
                        placement="top"
                    >
                        <el-button
                            type="success"
                            circle
                            size="small"
                            class="confirm-car-btn"
                            @click.stop="confirmCar(scope.row.id)"
                        >
                            <el-icon><Check /></el-icon>
                        </el-button>
                    </el-tooltip>
                </div>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import { Check } from '@element-plus/icons-vue';

export default {
    name: 'CarsTable',
    components: {
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
        handleRowClick(row, _column, event) {
            if (event?.target?.closest?.('.actions-cell')) {
                return;
            }
            this.$router.push({ name: 'CarDetails', params: { id: row.id } });
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

<style scoped>
.actions-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
}

.confirm-car-btn {
    flex-shrink: 0;
}
</style>