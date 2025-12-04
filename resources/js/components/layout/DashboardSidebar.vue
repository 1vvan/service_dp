<template>
    <el-aside :class="['dashboard-sidebar', { 'collapsed': isCollapsed }]">
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
            <button class="logout-btn" @click="logout">
                <el-icon><ArrowLeftBold /></el-icon>
                Вийти
            </button>
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
        Tools,
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
                    name: 'Бронювання',
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

