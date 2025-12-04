<template>
    <DashboardLayout>
        <div class="page-container">
            <h1>Бронювання</h1>
            <p>Керуйте своїми бронюваннями</p>
        </div>

        <BookingsTable />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import BookingsTable from './BookingsTable.vue';
export default {
    name: 'Bookings',
    components: {
        DashboardLayout,
        BookingsTable
    }
};
</script>

<style>
.page-container {
    padding: 24px;
}
</style>

