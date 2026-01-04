<template>
    <el-table v-loading="loading" :data="clients" class="dash-table">
        <el-table-column prop="id" label="ID" width="60">
            <template #default="scope">
                <span class="orange-text">#{{ scope.row.id }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="full_name" label="ПІБ" width="200">
            <template #default="scope">
                <span class="white-text">{{ scope.row.full_name }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="email" label="Email" width="200">
            <template #default="scope">
                <span>{{ scope.row.email }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="phone" label="Телефон" width="150">
            <template #default="scope">
                <span>{{ scope.row.phone || '-' }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="cars_count" label="Автомобілів" width="120" align="center">
            <template #default="scope">
                <span class="white-text">{{ scope.row.cars_count || 0 }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="bookings_count" label="Записів" width="100" align="center">
            <template #default="scope">
                <span class="white-text">{{ scope.row.bookings_count || 0 }}</span>
            </template>
        </el-table-column>
        <el-table-column label="Дії" width="100" align="center">
            <template #default="scope">
                <el-dropdown placement="bottom">
                    <el-button> <el-icon><Grid /></el-icon>  </el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="viewClient(scope.row.id)"><el-icon><View /></el-icon> Переглянути</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import { Grid, Edit, View } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';

export default {
    name: 'ClientsTable',
    components: {
        Grid,
        Edit,
        View
    },
    props: {
        clients: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        viewClient(clientId) {
            this.$emit('view-client', clientId);
        },
    }
}
</script>

