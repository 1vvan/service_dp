<template>
    <div class="home-page">
        <LandingHeader @loginClick="openAuthModal" />
        <HeroSection @getStarted="openAuthModal" />
        <LandingFooter />
        <AuthModal :isOpen="isAuthModalOpen" @close="closeAuthModal" />
    </div>
</template>

<script>
import LandingHeader from '../../layout/LandingHeader.vue';
import LandingFooter from '../../layout/LandingFooter.vue';
import HeroSection from './HeroSection.vue';
import AuthModal from '../../modals/AuthModal.vue';

export default {
    name: 'Home',
    components: {
        LandingHeader,
        LandingFooter,
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