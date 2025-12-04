<template>
    <DashboardLayout>
        <div class="bookings-container">
            <div class="top">
                <h1 class="title">Ви маєте <span class="count">{{ bookings.length }}</span> бронювань</h1>

                <button class="create-booking-btn">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Створити бронювання</span>
                </button>
            </div>
            <!-- <BookingsTable /> -->
        </div>
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
    },
    data() {
        return {
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        bookings() {
            return this.$store.state.bookings.userBookings || [];
        }
    },
    mounted() {
        this.getBookings();
    },
    methods: {
        getBookings() {
            this.$store.dispatch('bookings/fetchUserBookings', this.user.id);
        }
    }
};
</script>

