<template>
    <div id="external-app">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'External',
    mounted() {
        console.log('Layout External cargado');
    }
}
</script>

<style>
#external-app {
    min-height: 100vh;
}
</style>
