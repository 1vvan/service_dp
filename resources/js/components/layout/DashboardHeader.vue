<template>
    <el-header :class="['dashboard-header', { 'sidebar-collapsed': isSidebarCollapsed }]">
        <div class="header-left">
            <p class="title">Панель керування</p>
            <p class="subtitle">Ласкаво просимо, {{ userName }}</p>
        </div>
        
        <div class="header-right">
            <el-dropdown @command="handleCommand" class="user-dropdown">
                <div class="user-info">
                    <el-avatar :size="32" class="user-avatar">
                        {{ userInitials }}
                    </el-avatar>
                    <span class="user-name">{{ userName }}</span>
                    <el-icon class="dropdown-icon"><ArrowDown /></el-icon>
                </div>
                <template #dropdown>
                    <el-dropdown-menu class="user-dropdown-menu">
                        <el-dropdown-item command="profile">
                            <el-icon><User /></el-icon>
                            Профіль
                        </el-dropdown-item>
                        <el-dropdown-item command="settings">
                            <el-icon><Setting /></el-icon>
                            Налаштування
                        </el-dropdown-item>
                        <el-dropdown-item divided command="logout">
                            <el-icon><SwitchButton /></el-icon>
                            Вийти
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </el-header>
</template>

<script>
import {
    ArrowDown,
    User,
    Setting,
    SwitchButton
} from '@element-plus/icons-vue';

export default {
    name: 'DashboardHeader',
    components: {
        ArrowDown,
        User,
        Setting,
        SwitchButton
    },
    inject: {
        getSidebarCollapsed: {
            default: () => () => ({ isCollapsed: false })
        }
    },
    computed: {
        user() {
            return this.$store.getters.user;
        },
        userName() {
            return this.user?.full_name || this.user?.email || 'Користувач';
        },
        userInitials() {
            if (this.user?.full_name) {
                const names = this.user.full_name.split(' ');
                return names.map(n => n[0]).join('').toUpperCase().slice(0, 2);
            }
            return this.user?.email?.[0].toUpperCase() || 'U';
        },
        breadcrumbs() {
            const matched = this.$route.matched.filter(item => item.meta && item.meta.title);
            return matched.map(item => ({
                path: item.path,
                title: item.meta.title
            }));
        },
        isSidebarCollapsed() {
            const sidebar = this.getSidebarCollapsed?.();
            return sidebar?.isCollapsed || false;
        }
    },
    methods: {
        async handleCommand(command) {
            if (command === 'logout') {
                try {
                    await this.$store.dispatch('logout');
                    this.$router.push('/');
                } catch (error) {
                    console.error('Помилка виходу:', error);
                }
            } else if (command === 'profile') {
                this.$router.push('/dashboard/profile');
            } else if (command === 'settings') {
                this.$router.push('/dashboard/settings');
            }
        }
    }
};
</script>

