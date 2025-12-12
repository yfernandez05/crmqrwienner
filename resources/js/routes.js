import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter)

window.appRootUrl = window.location.origin;
window.appApiUrl = `${window.appRootUrl}/rest`;
window.intranetBaseUrl = '/crm';

export default new VueRouter({
    mode: 'history',
    routes:[
        { 
            path: intranetBaseUrl,
            name: 'spa',
            component: require('./components/empresa/EmpresaIndex').default
        },
        { 
            path: `${intranetBaseUrl}/dashboard`,
            name: 'spa.dashboard',
            component: require('./components/dashboard/DashboardIndex').default
        },
        {
            path: `${intranetBaseUrl}/cuenta`,
            name: 'spa.cuenta',
            component: require('./components/cuenta/CuentaIndex').default
        },
        {
            path: `${intranetBaseUrl}/cuenta/registrar`,
            name: 'spa.cuenta.registrar',
            component: require('./components/cuenta/CuentaCreate').default
        },
        {
            path: `${intranetBaseUrl}/cuenta/editar/:id`,
            name: 'spa.cuenta.editar',
            component: require('./components/cuenta/CuentaEdit').default
        },
        {
            path: `${intranetBaseUrl}/campania`,
            name: 'spa.campania',
            component: require('./components/campania/CampaniaIndex').default
        },
        {
            path: `${intranetBaseUrl}/campania/registrar`,
            name: 'spa.campania.registrar',
            component: require('./components/campania/CampaniaCreate').default
        },
        {
            path: `${intranetBaseUrl}/campania/editar/:id`,
            name: 'spa.campania.editar',
            component: require('./components/campania/CampaniaEdit').default
        },
        {
            path: `${intranetBaseUrl}/evento`,
            name: 'spa.evento',
            component: require('./components/evento/Eventoindex').default 
        },
        {
            path: `${intranetBaseUrl}/evento/registrar`,
            name: 'spa.evento.registrar',
            component: require('./components/evento/EventoCreate').default
        },
        {
            path: `${intranetBaseUrl}/evento/editar/:id`,
            name: 'spa.evento.editar',
            component: require('./components/evento/EventoEdit').default
        },
        

        {
            path: `${intranetBaseUrl}/cliente`,
            name: 'spa.cliente',
            component: require('./components/cliente/ClienteIndex').default
        },
        {
            path: `${intranetBaseUrl}/clienteevento`,
            name: 'spa.clienteevento',
            component: require('./components/cliente/ClienteEventoIndex').default
        },
        {
            path: `${intranetBaseUrl}/user`,
            name: 'spa.user',
            component: require('./components/user/UserIndex').default
        },
        {
            path: `${intranetBaseUrl}/user/registrar`,
            name: 'spa.user.registrar',
            component: require('./components/user/UserCreate').default
        },
        {
            path: `${intranetBaseUrl}/user/editar/:id`,
            name: 'spa.user.editar',
            component: require('./components/user/UserEdit').default
        },
        {
            path: `${intranetBaseUrl}/rol`,
            name: 'spa.rol',
            component: require('./components/rol/RolIndex').default
        },
        {
            path: `${intranetBaseUrl}/rol/registrar`,
            name: 'spa.rol.registrar',
            component: require('./components/rol/RolCreate').default
        },
        {
            path: `${intranetBaseUrl}/rol/editar/:id`,
            name: 'spa.rol.editar',
            component: require('./components/rol/RolEdit').default
        },

        {
            path: `${intranetBaseUrl}/tipoAtencion`,
            name: 'spa.tipoAtencion',
            component: require('./components/tipoAtencion/TipoAtencionIndex').default
        },
        {
            path: `${intranetBaseUrl}/tipoAtencion/registrar`,
            name: 'spa.tipoAtencion.registrar',
            component: require('./components/tipoAtencion/TipoAtencionCreate').default
        },
        {
            path: `${intranetBaseUrl}/tipoAtencion/editar/:id`,
            name: 'spa.tipoAtencion.editar',
            component: require('./components/tipoAtencion/TipoAtencionEdit').default
        },

        {
            path: `${intranetBaseUrl}/atencion/atender/:id`,
            name: 'spa.atencion.atender',
            component: require('./components/atencion/AtencionCreate').default
        },
        

        {
            path: `${intranetBaseUrl}/export`,
            name: 'spa.export',
            component: require('./components/basedatos/ExportBDIndex').default
        },
        {
            path: `${intranetBaseUrl}/import`,
            name: 'spa.import',
            component: require('./components/basedatos/ImportBDIndex').default
        },
        {
            path: `${intranetBaseUrl}/administrarcliente`,
            name: 'spa.administrarcliente',
            component: require('./components/administrarcliente/ClienteIndex').default
        },
        {
            path: `${intranetBaseUrl}/administrarcliente/editar/:id`,
            name: 'spa.administrarcliente.editar',
            component: require('./components/administrarcliente/ClienteEdit').default
        },
        {
            path: `${intranetBaseUrl}/logerror`,
            name: 'spa.logerror',
            component: require('./components/logerror/LogErrorIndex').default
        },


        {
            path: `${intranetBaseUrl}/reporte`,
            name: 'spa.reporte',
            component: require('./components/reporte/ReporteIndex').default
        },


        {
            path: `${intranetBaseUrl}/lectorqr`,
            name: 'spa.lectorqr',
            component: require('./components/lectorqr/LectorQR').default
        },


        {
            path: `${intranetBaseUrl}/buttonmessage`,
            name: 'spa.buttonmessage',
            component: require('./components/buttonmessage/ButtonMessageIndex').default
        },
        {
            path: `${intranetBaseUrl}/buttonmessage/registrar`,
            name: 'spa.buttonmessage.registrar',
            component: require('./components/buttonmessage/ButtonMessageCreate').default
        },
        {
            path: `${intranetBaseUrl}/buttonmessage/editar/:id`,
            name: 'spa.buttonmessage.editar',
            component: require('./components/buttonmessage/ButtonMessageEdit').default
        },

        { path: '*', redirect: intranetBaseUrl },
        { path: '/', redirect: intranetBaseUrl },
    ]
  });