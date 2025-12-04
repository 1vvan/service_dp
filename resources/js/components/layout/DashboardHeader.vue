<template>
    <el-header :class="['dashboard-header', { 'sidebar-collapsed': isCollapsed }]">

        <button class="toggle-sidebar-btn" @click="toggleSidebar">
            <el-icon v-if="!isCollapsed"><Fold /></el-icon>
            <el-icon v-else><Expand /></el-icon>
        </button>

        <div class="header-left">
            <p class="title">{{pageBreadcrumbs.title}}</p>
            <p class="subtitle">{{pageBreadcrumbs.subtitle}}</p>
        </div>
        
        <div class="header-right">
            <div class="user-info">
                <el-avatar :size="32" class="user-avatar">
                    {{ userInitials }}
                </el-avatar>
                <span class="user-name">{{ userName }}</span>
            </div>
        </div>
    </el-header>
</template>

<script>
import {
    Fold,
    Expand
} from '@element-plus/icons-vue';

export default {
    name: 'DashboardHeader',
    props: {
        isCollapsed: {
            type: Boolean,
            default: false
        }
    },
    components: {
        Fold,
        Expand
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
        pageBreadcrumbs() {
            let title = this.$route.meta.title;
            let subtitle = this.$route.meta.subtitle;
            if (this.$route.name === 'Dashboard') {
                subtitle = subtitle + ', ' + this.user?.full_name;
            }
            return {
                title,
                subtitle
            };
        }
    },
    methods: {
        toggleSidebar() {
            this.$emit('toggle-sidebar');
        }
    }
};
</script>

