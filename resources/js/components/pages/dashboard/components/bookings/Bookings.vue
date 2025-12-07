<template>
    <DashboardLayout>
        <div class="bookings-container">
            <div class="top">
                <h1 class="title">Ви маєте <span class="count">{{ bookings.length }}</span> {{ bookingsWord }}</h1>

                <button class="create-booking-btn" @click="openCreateBookingModal">
                    <el-icon><Plus /></el-icon>
                    <span class="text">Створити бронювання</span>
                </button>
            </div>
            <BookingsTable :bookings="bookings" :loading="loading" @edit-booking="editBooking" />
        </div>

        <CreateBookingModal :isOpen="isCreateBookingModalOpen" :editingBookingId="editingBookingId" @close="closeCreateBookingModal" />
    </DashboardLayout>
</template>

<script>
import DashboardLayout from '../../../../../layouts/DashboardLayout.vue';
import { pluralize } from '../../../../../lib/utils';
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
            editingBookingId: null,
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        bookings() {
            return this.$store.state.bookings.userBookings || [];
        },
        bookingsWord() {
            return pluralize(
                this.bookings.length,
                'запис',
                'запис',
                'записів'
            );
        }
    },
    mounted() {
        this.getBookings();
    },
    methods: {
        getBookings() {
            this.loading = true;
            this.$store.dispatch('bookings/fetchUserBookings', { clientId: this.user.client_id, force: true })
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
            this.editingBookingId = null;
        },
        editBooking(bookingId) {
            this.isCreateBookingModalOpen = true;
            this.editingBookingId = bookingId;
        }
    }
};
</script>
