<template>
    <div id="app">
        <router-view />
    </div>
</template>

<script>
export default {
    name: 'App',
    async mounted() {
        if (this.$store.getters.isAuthenticated && !this.$store.getters.user) {
            try {
                await this.$store.dispatch('getUser');
            } catch (error) {
                console.error('Помилка завантаження користувача:', error);
                this.$store.dispatch('logout');
            }
        }
    }
}
</script>

<style>
#app {
    min-height: 100vh;
}
</style>

