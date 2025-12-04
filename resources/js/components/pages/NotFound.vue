<template>
    <div class="not-found">
        <div class="container">
            <h1>404</h1>
            <p>Сторінку не знайдено</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NotFound'
};
</script>

<style scoped>
.not-found {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.not-found h1 {
    font-size: 4rem;
    margin-bottom: 1rem;
}
</style>

