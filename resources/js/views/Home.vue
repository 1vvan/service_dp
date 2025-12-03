<template>
    <div class="home-page">
        <Header @loginClick="openAuthModal" />
        <HeroSection @getStarted="openAuthModal" />
        <Footer />
        <AuthModal :isOpen="isAuthModalOpen" @close="closeAuthModal" />
    </div>
</template>

<script>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
import HeroSection from '../components/HeroSection.vue';
import AuthModal from '../components/AuthModal.vue';

export default {
    name: 'Home',
    components: {
        Header,
        Footer,
        HeroSection,
        AuthModal
    },
    data() {
        return {
            isAuthModalOpen: false
        };
    },
    methods: {
        openAuthModal() {
            this.isAuthModalOpen = true;
        },
        closeAuthModal() {
            this.isAuthModalOpen = false;
        }
    }
};
</script>

<style scoped>
.home-page {
    min-height: 100vh;
    background: hsl(var(--background));
}
</style>
