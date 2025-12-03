<template>
    <header class="header">
        <div class="header-container">
            <div class="header-logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
                <span class="logo-text">
                    Auto<span class="highlight">Care</span>
                </span>
            </div>

            <nav class="header-nav">
                <a href="#services" class="nav-link">Послуги</a>
                <a href="#about" class="nav-link">Про нас</a>
                <a href="#contact" class="nav-link">Контакти</a>
            </nav>

            <Button variant="hero" @click="$emit('loginClick')">
                Увійти
            </Button>
        </div>
    </header>
</template>

<script>
import Button from './ui/Button.vue';

export default {
    name: 'Header',
    components: {
        Button
    },
    emits: ['loginClick']
};
</script>

