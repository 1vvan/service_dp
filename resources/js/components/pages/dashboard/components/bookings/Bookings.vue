<template>
    <DashboardLayout>
        <div class="bookings-container">
            <div class="top">
                <h1 class="title">Ви маєте <span class="count">{{ bookings.length }}</span> бронювань</h1>

                <button class="create-booking-btn" @click="openCreateBookingModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Створити бронювання</span>
                </button>
            </div>
            <BookingsTable :bookings="bookings" :loading="loading" />
        </div>

        <CreateBookingModal :isOpen="isCreateBookingModalOpen" @close="closeCreateBookingModal" />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import CreateBookingModal from '../../../../modals/CreateBookingModal.vue';
import BookingsTable from './BookingsTable.vue';

export default {
    name: 'Bookings',
    components: {
        DashboardLayout,
        BookingsTable,
        CreateBookingModal
    },
    data() {
        return {
            isCreateBookingModalOpen: false,
            loading: false,
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
            this.loading = true;
            this.$store.dispatch('bookings/fetchUserBookings', this.user.client_id, true)
                .then(() => {
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        openCreateBookingModal() {
            this.isCreateBookingModalOpen = true;
        },
        closeCreateBookingModal() {
            this.isCreateBookingModalOpen = false;
        }
    }
};
</script>

