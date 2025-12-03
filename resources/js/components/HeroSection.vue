<template>
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-blob hero-blob-1"></div>
            <div class="hero-blob hero-blob-2"></div>
            <div class="hero-blob hero-blob-3"></div>
        </div>

        <div class="hero-grid"></div>

        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-badge animate-fade-in">
                    <span class="badge-dot"></span>
                    <span class="badge-text">Нам довіряють понад 10,000 власників автомобілів</span>
                </div>

                <h1 class="hero-title animate-slide-up">
                    Професійний
                    <span class="text-gradient">Автосервіс</span>
                    <br />
                    Якому можна довіряти
                </h1>

                <p class="hero-subtitle animate-slide-up" style="animation-delay: 0.1s">
                    Експертне обслуговування, ремонт та догляд за вашим автомобілем. 
                    Записуйтесь на прийом, відстежуйте історію обслуговування та підтримуйте свій автомобіль у відмінному стані.
                </p>

                <div class="hero-cta animate-slide-up" style="animation-delay: 0.2s">
                    <Button variant="hero" size="xl" @click="$emit('getStarted')">
                        Почати
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </Button>
                    <Button variant="hero-outline" size="xl">
                        Переглянути послуги
                    </Button>
                </div>

                <div class="hero-features animate-slide-up" style="animation-delay: 0.3s">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Сертифіковані експерти</h3>
                        <p class="feature-description">
                            Всі наші техніки є сертифікованими та висококваліфікованими професіоналами.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Швидкий сервіс</h3>
                        <p class="feature-description">
                            Швидке виконання без втрати якості.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Гарантована робота</h3>
                        <p class="feature-description">
                            Всі послуги супроводжуються нашою гарантією задоволеності.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import Button from './ui/Button.vue';

export default {
    name: 'HeroSection',
    components: {
        Button
    },
    emits: ['getStarted']
};
</script>

<style scoped>
</style>

