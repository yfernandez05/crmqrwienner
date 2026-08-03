<template>
    <div id="cont-report-export">
        <!-- contenedor principal -->
        <main-content columnClass="col-12">
            <!-- titulo -->
            <template v-slot:card-header-title>
                Reportes
            </template>
            <!-- acciones head -->
            <template v-slot:card-header-actions>
                <div class="col-12 d-flex align-items-center">
                    <h4 class="text-white m-0 mr-3 font-weight-bold" v-text="titleReporte"></h4>
                    <button class="btn btn-sm btn-info waves-effect waves-light" @click.prevent="printme">
                        <i class="fas fa-file"></i>
                        <span class="d-none d-sm-inline-block">
                            Generar PDF
                        </span>
                    </button>
                </div>
            </template>
            <!-- Contenido reporte  -->
            <template v-slot:card-body-main>
                <div class="form-row">
                    <!-- <div class="form-group col-12 col-sm-6 col-md-4">
                        <label class="mb-1">ASISTENTES TOTALES</label><br>
                        <span class="form-control form-control-md d-block text-truncate" v-text="totalprospectos"></span>
                    </div>  -->
                    <div class="form-group col-12 col-sm-6 col-md-4">
                        <label class="mb-1">Campaña</label>
                        <select2 :options="campanias" @input="buscarEvento()" v-model="reporte.idcampania"
                            :selectValue="reporte.idcampania" placeholder="Seleccione una campaña"
                            keyProperty="idcampania" textProperty="nombrecampania" id="campaniaChart">
                        </select2>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-md-4">
                        <label class="mb-1">Sedes</label>
                        <select2 :options="sedes" @input="buscarEvento()" v-model="reporte.idcolegio"
                        :selectValue="reporte.idcolegio" placeholder="Seleccione una Sede" :disabled="!reporte.idcampania"
                            textProperty="sedesalumno">
                        </select2>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-md-4">
                        <label class="mb-1">Stands</label>
                        <select2 :options="eventos" @input="buscarReport()" v-model="reporte.idevento"
                            :selectValue="reporte.idevento" placeholder="Seleccione un stands"
                            keyProperty="idevento" textProperty="nombreevento" id="eventoChart" :disabled="!reporte.idcampania">
                        </select2>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-md-4 col-xl-3">
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="asistencia" v-model="reporte.asistencia"
                                @change="buscarReport()" :disabled="!reporte.idcampania">
                            <label class="custom-control-label" for="asistencia">Asistente</label>
                        </div>
                    </div>
                    <div v-if="!reporte.asistencia && !reporte.idevento" class="form-group col-12 col-sm-6 col-md-4 col-xl-3">
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="import" v-model="reporte.import"
                                @change="buscarReport()" :disabled="!reporte.idcampania">
                            <label class="custom-control-label" for="import">Excluir Importados</label>
                        </div>
                    </div>
                </div>
                <div class="form-row border_report">
                    <!-- chartr lienal clientes registros -->
                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">Participantes registrados por horas:</label>
                        <label v-else class="title_report">Participantes ASISTIDOS por horas:</label>
                        <label class="text-end h3 title_report" style=" right: 9px !important; position: absolute;" v-if="totalprospectos > 0" v-text="totalprospectos"></label>
                        <div class="d-flex align-items-center">
                            <div class="col-12">
                                <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                                <chart-line-cliente ref="chartlineCliente" :datos="reporte.clientedata"></chart-line-cliente>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantos registrados por Sedes?</label>
                        <label v-else class="title_report">¿Cuantas ASISTENCIAS por Sedes?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12 col-md-5">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Sedes:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registrosededata" :key="index">
                                            <td v-text="asist.colegio"></td>
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-7">
                                <chart-pie-regsitrosede ref="chartPieRegsitrosede" :datos="reporte.registrosededata"
                                    titulo="¿Cuántos registrados por Sedes?"></chart-pie-regsitrosede>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantos registros ADS?</label>
                        <label v-else class="title_report">¿Cuantas ASISTENCIAS ADS?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12 col-md-5">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Procedencia:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registroprocedenciadedata" :key="index">
                                            <td v-text="asist.procedencia"></td>
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-7">
                                <chart-pie-registroprocedencia ref="chartPieRegsitroprocedencia" :datos="reporte.registroprocedenciadedata"></chart-pie-registroprocedencia>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantos Registrados por Grado?</label>
                        <label v-else class="title_report">¿Cuantas ASISTENCIAS por Grado?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12 col-md-5">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Grado:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registrogradodata" :key="index">
                                            <td v-text="asist.grado"></td>
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-7">
                                <chart-pie-registrogrado ref="chartPieRegistrogrado" :datos="reporte.registrogradodata"></chart-pie-registrogrado>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantos Registrados por Año de Egreso?</label>
                        <label v-else class="title_report">¿Cuantas ASISTENCIAS por Año de Egreso?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12 col-md-5" style="max-height: 250px;">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Año Egreso:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registroanioegresodata" :key="index">
                                            <td v-text="asist.anioegreso"></td>
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-7">
                                <chart-pie-regsitro-anioegreso ref="chartPieRegsitroAnioegreso" :datos="reporte.registroanioegresodata"></chart-pie-regsitro-anioegreso>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantos registros Matriculados?</label>
                        <label v-else class="title_report">¿Cuantas Matriculados Asistieron?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12 col-md-5">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Cantidad Matriculados:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registromatriculadodata" :key="index">
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 col-md-7">
                                <chart-pie-registrobeca ref="chartPieRegsitromatriculado" :datos="reporte.registromatriculadodata"></chart-pie-registrobeca>
                            </div>
                        </div>
                    </div> -->

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label v-if="!reporte.asistencia" class="title_report">¿Cuantas Registros por Carreras?</label>
                        <label v-else class="title_report">¿Cuantas ASISTENCIAS por Carreras?</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12" style="max-height: 250px;">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Carreras:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registrocarreradata.slice().sort((a, b) => b.total - a.total)" :key="index">
                                            <td v-text="asist.carrera"></td>
                                            <td v-text="asist.total"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12">
                                <chart-bar-registrocarrera ref="chartBarRegistroCarrera" :datos="reporte.registrocarreradata"></chart-bar-registrocarrera>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-0 pt-2 pb-5 px-2 col-12 ">
                        <label class="title_report">¿Cuantas invitaciones se realizarón?</label>
                        <label class="bg-secondary p-2">Cantidad total mostrado solo por campaña - no aplica otros filtros</label>
                        <div class="movil-flex d-flex align-items-center">
                            <span v-if="!reporte.idcampania" class="title-informative"><h2>Seleccione una campaña</h2></span>
                            <div class="table-responsive p-0 col-12" style="max-height: 250px;">
                                <table v-if="reporte.idcampania" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="p-2">Fecha Envío:</th>
                                            <th class="p-2">Invitacion:</th>
                                            <th class="p-2">Cantidad:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(asist, index) in reporte.registrosendnotificationdata.slice().reverse()" :key="index">
                                            <td v-text="asist.fecha_atencion"></td>
                                            <td v-text="asist.tipoatencion"></td>
                                            <td v-text="asist.total_atenciones"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12">
                                <chart-bar-send-invitation ref="chartBarSendInvitation" :datos="reporte.registrosendnotificationdata"></chart-bar-send-invitation>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </main-content>
    </div>
</template>

<script>
//importamos librerias y utilerias
import MainContent from '../../utils/MainContent';
import Select2 from './../../utils/Select2';
import chartGroup from './../../utils/ChartGroup';

import ChartLineCliente from './_ChartlineCliente';
import ChartPieRegistroprocedencia from './_ChartpieProcedencia';
import ChartPieRegsitrosede from './_ChartpieRegistroSede';
import ChartPieRegistrogrado from './_ChartpieGrado';
import ChartPieRegistrobeca from './_ChartpieRegistroSede';
import chartPieRegsitroAnioegreso from './_ChartpieRegistroAnioegreso';
import ChartBarRegistrocarrera from './_ChartbarRegistroCarrera';
import ChartBarSendInvitation from './_ChartbarSendInvitation';

import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

export default {
    data() {
        return {
            reporte:{
                idcampania: 0,
                idevento: 0,
                idcolegio: '',
                asistencia: false,
                asistenciastatus: 0,
                import: false,
                importstatus: 0,

                clientedata:[],

                registroprocedenciadedata: [],
                registrosededata: [],

                registroanioegresodata:[],
                registrocarreradata:[],
                registrosendnotificationdata:[],
                registrogradodata:[],
                registromatriculadodata:[],

                eventos:[],
                procedenciadata:[],

            },
            totalprospectos:'Seleccione una campaña',
            campanias:[],
            titleReporte:'',
            idevento: 0,
            eventos:[],
            sedes:[],
            filters:[],

        }
    },

    //metodos
    methods:{

        // Show Data select
        listarCampania(){
            showPreloader();
            let vm = this;
            axios.get(`${appApiUrl}/campania/select`)
            .then(function (response){
                hidePreloader();
                vm.campanias = response.data;
            })
            .catch(function (error) {
                hidePreloader();
                console.log(error);
            })
        },

        listarSede(){
            let vm = this;
            axios.get(`${appApiUrl}/cliente/sedesUni`)
            .then(function (response) {
                vm.sedes = response.data;
            })
            .catch(function (error) {
                console.log(error);
            })
        },


        // Render
        renderizarReporteline(){
            this.$refs.chartlineCliente.mostrar();
        },
        renderizarRegistroProcedencia(){
            this.$refs.chartPieRegsitroprocedencia.mostrar();
        },
        renderizarRegistroSede(){
            this.$refs.chartPieRegsitrosede.mostrar();
        },
        renderizarRegistroAnioegreso(){
            this.$refs.chartPieRegsitroAnioegreso.mostrar();
        },
        renderizarRegistroCarrera(){
            this.$refs.chartBarRegistroCarrera.mostrar();
        },
        // renderizarRegistroMatriculado(){
        //     this.$refs.chartPieRegsitromatriculado.mostrar();
        // },
        renderizarEnvionoNotificacion(){
            this.$refs.chartBarSendInvitation.mostrar();
        },
        renderizarRegistroGrado(){
            this.$refs.chartPieRegistrogrado.mostrar();
        },


        // Get Data
        buscarEvento(){
            if(!this.reporte.idcampania){
                this.reporte.idevento = 0;
                this.reporte.idcolegio = '';
                this.reporte.asistencia = false;
                this.reporte.import = false;
            }

            this.eventos = [];

            let vm = this;
            axios.get(`${appApiUrl}/evento/getevento`, {params: {idcampania: this.reporte.idcampania}})
            .then(function (response) {

                if(vm.reporte.idcampania.length){
                    if (response.data == null || response.data == '') {
                        warningMessage(`Esta campaña no tiene ningun stand`, appName);
                    }
                }

                vm.eventos = response.data;
                vm.buscarReport();
            })
            .catch(function (error) {
                errorMessage(appErrorMessage, appName);
                console.log(error);
            })
        },

        registrosClientes(){
            showPreloader();
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registroclientes`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.datasets.length > 0) {
                    vm.reporte.clientedata = result.datasets;
                    vm.totalprospectos = result.cantidadprospectos;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarReporteline();
            })
            .catch(function (error){
                hidePreloader();
                console.log(error);
            })
        },

        registroprocedencias(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registroprocedencia`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registroprocedenciadedata = result;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarRegistroProcedencia();
            })
            .catch(function (error){
                console.log(error);
            })
        },
        registrosedes() {
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registrosede`, { params: { idevento: this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus } })
                .then(function (response) {

                    let result = response.data;

                    if (result && result.length > 0) {
                        vm.reporte.registrosededata = result;
                    }

                }).then(() => {
                    hidePreloader();
                    vm.renderizarRegistroSede();
                })
                .catch(function (error) {
                    console.log(error);
                })
        },

        registroanioegreso(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registroanioegreso`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registroanioegresodata = result;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarRegistroAnioegreso();
            })
            .catch(function (error){
                console.log(error);
            })
        },



        registrocarreras(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registrocarrera`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registrocarreradata = result;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarRegistroCarrera();
            })
            .catch(function (error){
                console.log(error);
            })
        },

        enviosnotificacion(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/enviosinvitacionwtsp`,{params: {idcampania: this.reporte.idcampania}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registrosendnotificationdata = result;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarEnvionoNotificacion();
            })
            .catch(function (error){
                console.log(error);
            })
        },
        asistenciasedes() {
            let vm = this;
            axios.get(`${appApiUrl}/reporte/asistenciasedes`, { params: { idevento: this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio } })
                .then(function (response) {

                    let result = response.data; //

                    if (result && result.length > 0) {
                        vm.reporte.asistenciasdata = result;
                    }

                }).then(() => {
                    hidePreloader();
                })
                .catch(function (error) {
                    console.log(error);
                })
        },

        registrosmatriculados(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registrosmatriculado`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registromatriculadodata = result;
                }

            }).then(() => {
                hidePreloader();
            })
            .catch(function (error){
                console.log(error);
            })
        },

        repogrados(){
            let vm = this;
            axios.get(`${appApiUrl}/reporte/registrogrado`,{params: {idevento:this.reporte.idevento, idcampania: this.reporte.idcampania, idcolegio: this.reporte.idcolegio, asistencia: this.reporte.asistenciastatus, import: this.reporte.importstatus}})
            .then(function (response){

                let result = response.data;

                if (result && result.length > 0) {
                    vm.reporte.registrogradodata = result;
                }

            }).then(() => {
                hidePreloader();
                vm.renderizarRegistroGrado();
            })
            .catch(function (error){
                console.log(error);
            })
        },


        // Clear data
        limpiarDataRepor(){
            this.reporte.clientedata = [];
            this.reporte.registroprocedenciadedata = [];
             this.reporte.registrosededata = [];
            this.reporte.registroanioegresodata = [];
            this.reporte.registrocarreradata = [];
            // this.reporte.registromatriculadodata = [];
            this.reporte.registrogradodata = [];
            this.reporte.registrosendnotificationdata = [];
        },


        // Search report
        buscarReport(){
            if(this.reporte.campanias == null)
                this.totalprospectos = 'Seleccione una campaña';

            this.limpiarDataRepor();

            this.reporte.asistencia ? this.reporte.asistenciastatus = 1 : this.reporte.asistenciastatus = 0
            this.reporte.import ? this.reporte.importstatus = 1 : this.reporte.importstatus = 0

            this.registrosClientes();
             this.registrosedes();
            this.registroprocedencias();
            this.registrosmatriculados();
            this.registroanioegreso();
            this.registrocarreras();
            this.enviosnotificacion();
            this.repogrados();
        },

        printme() {
            showPreloader(); // Mostrar preloader al inicio

            const element = document.getElementById('cont-report-export'); // ID del div que se va a imprimir

            // Guardar estilos originales para poder revertirlos después
            const originalStyles = {
            width: element.style.width,
            margin: element.style.margin,
            };

            // Aplicar estilos para simular cómo se vería en una pantalla de PC estándar
            element.style.width = '1400px'; // Ancho que simula una pantalla de PC
            element.style.margin = 'auto'; // Centrar el contenido en el viewport

            // Esperar un breve tiempo para que se apliquen los estilos antes de capturar
            setTimeout(() => {
            // Capturar el contenido con html2canvas y ajustar la escala para mejorar la calidad
            html2canvas(element, { scale: 2 }).then(canvas => { // Aumenta la escala (2 veces) para mejorar la calidad

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                orientation: 'portrait', // Orientación del PDF (portrait para vertical)
                unit: 'px', // Unidades del PDF (píxeles)
                format: [1800, 800], // Formato del PDF (tamaño deseado)
                });

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight, '', 'FAST'); // 'FAST' para alta calidad

                // Guardar el PDF y revertir los estilos al tamaño original después de la captura
                pdf.save('documento.pdf');
                element.style.width = originalStyles.width;
                element.style.margin = originalStyles.margin;

                hidePreloader(); // Ocultar preloader al final

            }).catch(error => {
                console.error('Error al generar PDF:', error);
                hidePreloader(); // Ocultar preloader en caso de error
            });
            }, 900);
        }

    },
    mounted(){
        let vm = this;
        var $select = $("#campaniaChart").change(function() {
        // obtengo el value
        var value = $(this).val();
        // obtengo el texto segun el value
        var text = $select.find('option[value=' + value + ']').text();

        value == null ? vm.titleReporte = 'Registros Totales' : vm.titleReporte = text;

      });
    },

    created(){
        this.listarCampania();
        this.listarSede();
    },

    //iniciamos componentes
    components:{
        MainContent,
        Select2,
        chartGroup,
        ChartLineCliente,
        ChartPieRegistrobeca,
        ChartPieRegistrogrado,
        ChartPieRegistroprocedencia,
        ChartPieRegsitrosede,
        chartPieRegsitroAnioegreso,
        ChartBarRegistrocarrera,
        ChartBarSendInvitation
    },
}
</script>

<style scoped>
.movil-flex{
    display: flex !important;
    flex-wrap: wrap;
}
.title-informative{
    width: 100%;
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}
</style>
