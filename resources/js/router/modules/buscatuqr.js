export default [
    {
        path: `/buscatuqr`,
        name: 'spa.buscatuqr',
        component: () => import('../../components/viewnoauth/qr/Buscatuqr.vue')
    },
    {
        path: `/registro`,
        name: 'spa.registro',
        component: () => import('../../components/viewnoauth/cliente/RegisterCliente.vue')
    },
    {
        path: `/encuesta`,
        name: 'spa.encuesta',
        component: () => import('../../components/viewnoauth/encuesta/EncuestaSatisfaccion.vue')
    },
]
