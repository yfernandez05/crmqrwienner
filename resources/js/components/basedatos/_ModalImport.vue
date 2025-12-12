<template>
    <modal :showModal="showModal" @closeModal="closeModal">
        <template v-slot:modal-header-title>
            Importar Participantes desde Excel
        </template>
        <template v-slot:modal-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-6 col-xl-6" :class="{'has-danger':errorExists('tipoImportacion')}">
                    <label class="mb-1">¿Qué desea importar? <small class="text-danger">(*)</small></label>
                    <select2 :options="tiposImportacion" v-model="cliente.tipoImportacion"
                        :selectValue="cliente.tipoImportacion" placeholder="Seleccione una opción"
                        keyProperty="id" textProperty="nombre">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('tipoImportacion')" v-text="showError('tipoImportacion').errorDetail"></small>
                </div>
                <div class="form-group col-12 col-sm-6 col-xl-6" :class="{'has-danger':errorExists('idcampania')}">
                    <label class="mb-1">Campaña  <small class="text-danger">(*)</small></label>
                    <select2 :options="campania" @input="buscarEvento()" v-model="cliente.idcampania"
                        :selectValue="cliente.idcampania" placeholder="Seleccione una campaña"
                        keyProperty="idcampania" textProperty="nombrecampania">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('idcampania')" v-text="showError('idcampania').errorDetail"></small>
                </div>
                <!-- <div class="form-group col-12 col-sm-6 col-xl-6">
                    <label class="mb-1">Evento</label>
                    <select2 :options="eventos" v-model="cliente.idevento"
                        :selectValue="cliente.idevento" placeholder="Seleccione un Evento"
                        keyProperty="idevento" textProperty="nombreevento" id="selectevento">
                    </select2>
                </div> -->
                <div class="form-group col-12">
                    <label>Archivo Excel</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" ref="fileexcel" @change="handleFileUpload"
                                accept=".XLSX, .CSV" id="fileexcel">
                            <label class="custom-file-label" for="fileexcel" v-text="fileName">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button"
                                @click="importarExcel()">
                                    <i class="fa fa-save"></i>
                                    Guardar
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </modal>
</template>
<script>
    import Modal from './../../utils/Modal';
    import Select2 from './../../utils/Select2';

    export default {
        data() {
            return {
                showModal: false,
                fileName: '',
                cliente:{
                    tipoImportacion: 0,
                    idcampania:0,
                    idevento:0,
                },
                
                campania:[],
                eventos:[],
                tiposImportacion: [
                    { id: 1, nombre: 'Importar registro' },
                    { id: 2, nombre: 'Importar matrícula' }
                ],
                errors:[]
            }
        },
        methods: {
            closeModal(event) {
                this.showModal = false;
                this.fileName = '';
                this.cliente={
                    idcampania:0,
                    idevento:0,
                };
            },
            openModal() {
                this.showModal = true;
                this.fileName = '';
            },
            handleFileUpload(event) {
                if (event.target.files[0]) {
                    this.fileName = event.target.files[0].name;
                }else
                    this.fileName = '';
            },
            importarExcel() {                
                let vm = this;

                if (this.validateFields().length > 0) {
                    return;
                }

                if (this.$refs.fileexcel.files[0]) {
                    let ruta = '';
                    if(this.cliente.tipoImportacion == "1"){
                        ruta = 'import';
                        this.saveimportdata(ruta);
                    }else{
                        ruta = 'import/storematricula';
                        this.saveimportdata(ruta);
                    }

                }else{
                    warningMessage('Seleccione un archivo Excel', appName);
                }
            },

            saveimportdata(rute){
                let vm = this;
                
                let formData = new FormData();
                formData.append('file', this.$refs.fileexcel.files[0]);
                formData.append('idcampania', this.cliente.idcampania);
                formData.append('idevento', this.cliente.idevento);

                axios.post(`${appApiUrl}/${rute}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(function (response) {
                    hidePreloader();
                    let result = response.data;
                        console.log(result);
                    if (result.status) {
                        successMessage(result.message, appName);
                        vm.showModal = false;
                        vm.fileName = '';
                        vm.$parent.listarImportaciones(); 
                    } else
                        warningMessage(result.message, appName);
                })
                .catch(function (error) {
                    hidePreloader();
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
            },

            buscarEvento(){
                this.cliente.idevento = 0; 
                this.eventos = [];

                let vm = this;
                axios.get(`${appApiUrl}/evento/getevento`, {params: {idcampania:this.cliente.idcampania}})
                .then(function (response) {

                    /* if (response.data == null || response.data == '') {
                        warningMessage(`Esta campaña no tiene ningun evento`, appName);
                    } */
                    
                    vm.eventos = response.data;
                    
                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
            },

            listarCampania() {
                let vm = this;
                axios.get(`${appApiUrl}/campania/select`)
                    .then(function (response) {
                        vm.campania = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            validateFields() {
                this.errors = [];

                if (!this.cliente.tipoImportacion) {
                    this.setError('tipoImportacion', 'El campo es obligatorio');
                }
                if (!this.cliente.idcampania) {
                    this.setError('idcampania', 'El campo campaña es obligatorio');
                }

                return this.errors;
            },
            setError(keyModel, errorDetail) {
                this.errors.push({
                    keyModel: keyModel,
                    errorDetail: errorDetail
                });
            },
            errorExists(keyModel){
                return this.errors.filter(err => err.keyModel === keyModel).length;
            },
            showError(keyModel){
                return this.errors.find(err => err.keyModel === keyModel);
            },
        },
        mounted() {
            this.listarCampania();
        },
        components: {
            Modal,
            Select2
        }
    }

</script>
