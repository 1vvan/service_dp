<template>
    <el-aside :class="['dashboard-sidebar', { 'collapsed': isCollapsed }]">
        <div class="sidebar-header">
            <div v-if="!isCollapsed" class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
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
            <button class="logout-btn" @click="logout">
                <el-icon><ArrowLeftBold /></el-icon>
                Вийти
            </button>
        </div>
    </el-aside>
</template>

<script>
import {
    HomeFilled,
    Calendar,
    User,
    Setting,
    Van,
    Document,
    Expand,
    Fold,
    ArrowLeftBold
} from '@element-plus/icons-vue';

export default {
    name: 'DashboardSidebar',
    props: {
        isCollapsed: {
            type: Boolean,
            default: false
        }
    },
    components: {
        HomeFilled,
        Calendar,
        User,
        Setting,
        Van,
        Document,
        Expand,
        Fold,
        ArrowLeftBold
    },
    data() {
        return {
            clientPages: [
                {
                    name: 'Головна',
                    icon: 'HomeFilled',
                    path: '/dashboard'
                },
                {
                    name: 'Запис',
                    icon: 'Calendar',
                    path: '/dashboard/bookings'
                },
                {
                    name: 'Автомобілі',
                    icon: 'Van',
                    path: '/dashboard/cars'
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
    methods: {
        logout() {
            this.$store.dispatch('logout').then(() => {
                this.$router.push('/');
            });
        }
    }
};
</script>

