<template>
    <el-table :data="bookings" style="width: 100%">
        <el-table-column prop="id" label="ID" width="100" />
        <el-table-column prop="client_name" label="Клієнт" width="100" />
        <el-table-column prop="car_name" label="Автомобіль" width="100" />
        <el-table-column prop="date" label="Дата" width="100" />
        <el-table-column prop="status" label="Статус" width="100" />
        <el-table-column prop="actions" label="Дії" width="100" />
    </el-table>
</template>

<script>
export default {
    name: 'BookingsTable',
    data() {
        return {
            bookings: []
        }
    },
    mounted() {
        this.fetchBookings();
    },
    computed: {
        user() {
            return this.$store.state.user;
        }
    },
    methods: {
        fetchBookings() {
            this.$store.dispatch('bookings/fetchUserBookings', this.user.id).then(response => {
                this.bookings = response.data;
            });
        }
    }
}
</script>