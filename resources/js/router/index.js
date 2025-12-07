import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../components/pages/home/Home.vue')
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('../components/pages/dashboard/Dashboard.vue'),
        meta: {
            title: 'Панель керування',
            subtitle: 'Ласкаво просимо',
            requiresAuth: true
        }
    },
    {
        path: '/dashboard/bookings',
        name: 'Bookings',
        component: () => import('../components/pages/dashboard/components/bookings/Bookings.vue'),
        meta: {
            title: 'Записи',
            subtitle: 'Керування записами',
            requiresAuth: true
        }
    },
    {
        path: '/dashboard/clients',
        name: 'Clients',
        component: () => import('../components/pages/dashboard/components/Clients.vue'),
        meta: {
            title: 'Клієнти',
            subtitle: 'Керування клієнтами',
            requiresAuth: true,
        }
    },
    {
        path: '/dashboard/services',
        name: 'Services',
        component: () => import('../components/pages/dashboard/components/Services.vue'),
        meta: {
            title: 'Послуги',
            subtitle: 'Керування послугами',
            requiresAuth: true,
        }
    },
    {
        path: '/dashboard/cars',
        name: 'Cars',
        component: () => import('../components/pages/dashboard/components/cars/Cars.vue'),
        meta: {
            title: 'Автомобілі',
            subtitle: 'Керування автомобілями',
            requiresAuth: true,
        }
    },
    {
        path: '/dashboard/reports',
        name: 'Reports',
        component: () => import('../components/pages/dashboard/components/Reports.vue'),
        meta: {
            title: 'Звіти',
            subtitle: 'Керування звітами',
            requiresAuth: true
        }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('../components/pages/NotFound.vue')
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    // Проверяем, требуется ли авторизация для маршрута
    if (to.meta.requiresAuth) {
        // Проверяем наличие токена
        if (!store.getters.isAuthenticated) {
            // Если токена нет, перенаправляем на главную
            next({ name: 'Home' });
            return;
        }
        
        // Если токен есть, но пользователь не загружен, пытаемся загрузить
        if (!store.getters.user) {
            try {
                await store.dispatch('getUser');
            } catch (error) {
                // Если не удалось загрузить пользователя, очищаем токен и перенаправляем
                store.dispatch('logout');
                next({ name: 'Home' });
                return;
            }
        }
    }
    
    next();
});

export default router;

