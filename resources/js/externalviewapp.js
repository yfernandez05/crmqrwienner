// Bootstrap y dependencias (external version without CSRF requirement)
require('./bootstrap-external');

// Vue
import Vue from 'vue';
import VueRouter from 'vue-router';

// Componentes
import App from './components/layout/External.vue';

// Rutas
import buscatuqrRoutes from './router/modules/buscatuqr.js';

// Configuración global
Vue.use(VueRouter);

// Router
const router = new VueRouter({
    mode: 'history',
    routes: [
        ...buscatuqrRoutes,
    ],
});

// Instancia Vue
const app = new Vue({
    el: '#app',
    router,
    components: {
        App
    },
    template: '<App/>'
});
