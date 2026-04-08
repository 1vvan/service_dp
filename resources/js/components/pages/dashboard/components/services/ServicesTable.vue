<template>
    <el-table v-loading="loading" :data="services" class="dash-table">
        <el-table-column prop="id" label="ID" width="80" sortable>
            <template #default="scope">
                <span class="orange-text">#{{ scope.row.id }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="name" label="Назва" min-width="200" sortable>
            <template #default="scope">
                <span class="white-text">{{ scope.row.name }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="category" label="Категорія" width="160" sortable :sort-method="sortByCategory">
            <template #default="scope">
                <span v-if="scope.row.category" class="category-badge">{{ scope.row.category.name }}</span>
                <span v-else class="muted-text">—</span>
            </template>
        </el-table-column>
        <el-table-column prop="base_price" label="Вартість (грн)" width="200" sortable :sort-method="sortByPrice">
            <template #default="scope">
                <span class="white-text">{{ formatPrice(scope.row.base_price) }}</span>
            </template>
        </el-table-column>
        <el-table-column label="Дії" width="120" align="center" fixed="right">
            <template #default="scope">
                <el-dropdown placement="bottom">
                    <el-button><el-icon><Grid /></el-icon></el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="editService(scope.row.id)">
                                <el-icon><Edit /></el-icon>
                                Редагувати
                            </el-dropdown-item>
                            <el-dropdown-item @click="deleteService(scope.row)" class="danger-item">
                                <el-icon><Delete /></el-icon>
                                Видалити
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import { formatPrice } from '../../../../../lib/utils';
import { Grid, Edit, Delete } from '@element-plus/icons-vue';
import { ElMessage, ElMessageBox } from 'element-plus';

export default {
    name: 'ServicesTable',
    components: {
        Grid,
        Edit,
        Delete
    },
    props: {
        services: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        sortByCategory(a, b) {
            const catA = a.category?.name || '';
            const catB = b.category?.name || '';
            return catA.localeCompare(catB, 'uk');
        },
        sortByPrice(a, b) {
            const priceA = parseFloat(a.base_price) || 0;
            const priceB = parseFloat(b.base_price) || 0;
            return priceA - priceB;
        },
        formatPrice(price) {
            return formatPrice(price);
        },
        editService(serviceId) {
            this.$emit('edit-service', serviceId);
        },
        async deleteService(service) {
            try {
                await ElMessageBox.confirm(
                    `Видалити послугу "${service.name}"?`,
                    'Підтвердження видалення',
                    {
                        confirmButtonText: 'Видалити',
                        cancelButtonText: 'Скасувати',
                        type: 'warning'
                    }
                );

                await this.$store.dispatch('services/deleteService', service.id);
                ElMessage.success('Послугу видалено');
            } catch (error) {
                if (error !== 'cancel') {
                    ElMessage.error(error.response?.data?.message || 'Помилка видалення');
                }
            }
        }
    }
};
</script>

<style scoped>
.danger-item {
    color: var(--el-color-danger);
}
</style>
