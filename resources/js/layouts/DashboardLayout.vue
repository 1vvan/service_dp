<template>
    <el-container class="dashboard-layout">
        <DashboardSidebar />
        <el-container :class="['dashboard-main', { 'sidebar-collapsed': isSidebarCollapsed }]">
            <DashboardHeader />
            <el-main class="dashboard-content">
                <slot />
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
import DashboardSidebar from '../components/layout/DashboardSidebar.vue';
import DashboardHeader from '../components/layout/DashboardHeader.vue';

export default {
    name: 'DashboardLayout',
    components: {
        DashboardSidebar,
        DashboardHeader
    },
    inject: ['getSidebarCollapsed'],
    computed: {
        isSidebarCollapsed() {
            const sidebar = this.getSidebarCollapsed?.();
            return sidebar?.isCollapsed || false;
        }
    }
};
</script>

