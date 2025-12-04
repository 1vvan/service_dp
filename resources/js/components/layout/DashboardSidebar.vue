<template>
    <el-aside :width="isCollapsed ? '64px' : '240px'" class="dashboard-sidebar">
        <div class="sidebar-header">
            <div v-if="!isCollapsed" class="logo">
                <el-icon :size="24" class="logo-icon">
                    <Tools />
                </el-icon>
                <span class="logo-text">Auto<span class="logo-text-primary">Care</span></span>
            </div>
            <el-icon v-else :size="24" class="logo-icon">
                <Tools />
            </el-icon>
        </div>
        
        <el-menu
            :default-active="activeMenu"
            :collapse="isCollapsed"
            :collapse-transition="false"
            class="sidebar-menu"
            router
        >
            <el-menu-item v-for="page in pages" :key="page.path" :index="page.path">
                <el-icon><component :is="page.icon" /></el-icon>
                <template #title>{{ page.name }}</template>
            </el-menu-item>
        </el-menu>
        
        <div class="sidebar-footer">
            <el-button
                :icon="isCollapsed ? Expand : Fold"
                circle
                class="collapse-btn"
                @click="toggleCollapse"
            />
        </div>
    </el-aside>
</template>

<script>
import {
    Tools,
    HomeFilled,
    Calendar,
    User,
    Setting,
    Van,
    Document,
    Expand,
    Fold
} from '@element-plus/icons-vue';

export default {
    name: 'DashboardSidebar',
    components: {
        Tools,
        HomeFilled,
        Calendar,
        User,
        Setting,
        Van,
        Document,
        Expand,
        Fold
    },
    data() {
        return {
            isCollapsed: false,
            clientPages: [
                {
                    name: 'Головна',
                    icon: 'HomeFilled',
                    path: '/dashboard'
                },
                {
                    name: 'Бронювання',
                    icon: 'Calendar',
                    path: '/dashboard/bookings'
                },
            ],
            managerPages: [
                {
                    name: 'Головна',
                    icon: 'HomeFilled',
                    path: '/dashboard'
                },
                
            ],
            adminPages: [
                {
                    name: 'Головна',
                    icon: 'HomeFilled',
                    path: '/dashboard'
                },
                
            ]
        };
    },
    computed: {
        pages() {
            if (this.$store.state.isAdmin) {
                return this.adminPages;
            } else if (this.$store.state.isManager) {
                return this.managerPages;
            } else {
                return this.clientPages;
            }
        },
        activeMenu() {
            return this.$route.path;
        }
    },
    provide() {
        return {
            getSidebarCollapsed: () => this
        };
    },
    methods: {
        toggleCollapse() {
            this.isCollapsed = !this.isCollapsed;
        }
    }
};
</script>

