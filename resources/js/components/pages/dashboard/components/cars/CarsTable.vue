<template>
    <el-table :data="cars" class="dash-table">
        <el-table-column prop="car_model" label="Авто" width="100" />
        <el-table-column prop="licence_plate" label="Номер" width="100" />
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
        }
    },
    data() {
        return {
        }
    },
}
</script>