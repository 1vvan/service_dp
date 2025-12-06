<template>
    <el-table v-loading="loading" :data="cars" class="dash-table">
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
    </el-table>
</template>

<script>
export default {
    name: 'CarsTable',
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
    data() {
        return {
        }
    },
}
</script>