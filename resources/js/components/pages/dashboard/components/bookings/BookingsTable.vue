<template>
    <el-table v-loading="loading" :data="bookings" class="dash-table">
        <el-table-column prop="id" label="ID" width="30">
            <template #default="scope">
                <span class="orange-text">#{{ scope.row.id }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="services" label="Послуги" width="100">
            <template #default="scope">
                <span class="white-text">{{ scope.row.services.map(service => service.name).join(', ') }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="car" label="Авто" width="150">
            <template #default="scope">
                <span>{{ scope.row.car.full_name }}</span>
                <span class="orange-text">{{ ' ' + scope.row.car.license_plate }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="date" label="Дата та час" width="100">
            <template #default="scope">
                <span>{{ scope.row.date }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="status_name" label="Статус" width="60">
            <template #default="scope">
                <span class="status-badge" :class="BOOKING_STATUS_CLASS_MAPPING[scope.row.status_id]">{{ scope.row.status_name }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="total_price" label="Вартість" width="100">
            <template #default="scope">
                <span class="white-text">{{ formatPrice(scope.row.total_price) }} грн</span>
            </template>
        </el-table-column>
        <el-table-column label="Дії" width="100" align="center">
            <template #default="scope">
                <el-dropdown placement="bottom">
                    <el-button> <el-icon><Grid /></el-icon>  </el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="downloadReceipt(scope.row.id)"><el-icon><Tickets /></el-icon> Скачати чек</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import { formatPrice } from '../../../../../lib/utils';
import { BOOKING_STATUS_CLASS_MAPPING as bookingStatusClassMapping } from '../../../../../constants/mapping';
import { Grid, Tickets } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';

export default {
    name: 'BookingsTable',
    components: {
        Tickets,
        Grid
    },
    props: {
        bookings: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    computed: {
        BOOKING_STATUS_CLASS_MAPPING() {
            return bookingStatusClassMapping;
        },
    },
    methods: {
        formatPrice(price) {
            return formatPrice(price);
        },
        async downloadReceipt(bookingId) {
            this.$store.dispatch('bookings/downloadReceipt', bookingId)
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `receipt-booking-${bookingId}.pdf`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Помилка при скачуванні чека:', error);
                    ElMessage.error('Помилка при скачуванні чека');
                });
        }
    }
}
</script>