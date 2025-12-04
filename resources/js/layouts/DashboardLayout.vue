<template>
    <el-container class="dashboard-layout">
        <DashboardSidebar :is-collapsed="isSidebarCollapsed" />
        <el-container :class="['dashboard-main', { 'sidebar-collapsed': isSidebarCollapsed }]">
            <DashboardHeader :is-collapsed="isSidebarCollapsed" @toggle-sidebar="toggleSidebar" />
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
    data() {
        return {
            isSidebarCollapsed: false
        }
    },
    mounted() {
        this.$store.dispatch('references/fetchAllReferences');
    },
    methods: {
        toggleSidebar() {
            this.isSidebarCollapsed = !this.isSidebarCollapsed;
        }
    }
};
</script>

