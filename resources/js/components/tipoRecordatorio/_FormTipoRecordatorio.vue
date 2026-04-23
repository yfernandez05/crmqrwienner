<template>
    <main-content columnClass="col-12 col-md-10 col-lg-8 col-xl-7">
        <template v-slot:card-header-title>
            <span v-text="cardTitle"></span>
        </template>

        <template v-slot:card-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-12" :class="{'has-danger':errorExists('tipoRecordatorio')}">
                    <label>Estado de Recordatorio <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="tiporecordatorio.tiporecordatorio"/>
                    <small class="form-control-feedback" v-if="errorExists('tipoRecordatorio')" v-text="showError('tipoRecordatorio').errorDetail"></small>
                </div>
                <hr class="mt-2">
                <div class="form-group col-12 col-sm-12" >
                    <label><small>Opciones adicionales:</small></label><br>

                    <label>Seleccione color de fondo: &nbsp;</label>
                    <v-input-colorpicker v-model="tiporecordatorio.backgroundColor"/> &nbsp; &nbsp;

                    <label>Seleccione color de texto: &nbsp;</label>
                    <v-input-colorpicker v-model="tiporecordatorio.textColor"/>
                </div>

                <div class="form-group col-12 col-sm-12" >
                    <label><small>Vista previa:</small></label><br>
                    <h4><span v-bind:style="{background: tiporecordatorio.backgroundColor, color:tiporecordatorio.textColor}" class="badge badge-pill badge-lg mb-3" v-text="tiporecordatorio.tiporecordatorio"></span></h4>
                </div>
            </div>
            <hr class="mt-2">
        </template>

        <template v-slot:card-body-actions>
            <router-link :to="{name: 'spa.tipoRecordatorio'}" class="btn waves-effect waves-light btn-info mr-2">
                <i class="fas fa-reply"></i> <span class="button-text">Atrás</span>
            </router-link>

            <div>
                <button type="button" class="btn btn-success waves-effect waves-light" @click="doSaveData">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
                <button type="reset" class="btn waves-effect waves-light btn-outline-secondary ml-2">
                    <i class="fa fa-window-close"></i> <span class="button-text">Cancelar</span>
                </button>
            </div>
        </template>

    </main-content>
</template>


<script>
    import MainContent from './../../utils/MainContent';
    import VInputColorpicker from 'vue-native-color-picker'

export default {
    props: {
        cardTitle: {
            default: 'TipoAtencion'
        },
        tiporecordatorio: {
            type: Object,
            default: function () {
                return {
                    tiporecordatorio: '',
                    backgroundColor: '',
                    textColor: '',
                }
            }
        }
    },
    data() {
        return {
            errors: []
        }
    },
    methods: {
        doSaveData() {

            if (this.validateFields().length > 0) {
                return;
            }

            let TipoAtencionData = {
                tiporecordatorio: this.tiporecordatorio.tiporecordatorio,
                backgroundColor: this.tiporecordatorio.backgroundColor,
                textColor: this.tiporecordatorio.textColor,
            }

            this.$emit('saveData', TipoAtencionData);
        },

        validateFields() {
            this.errors = [];

            if (!this.tiporecordatorio.tiporecordatorio) {
                this.setError('tipoRecordatorio', 'El campo Tipo de Dato es obligatorio');
            }

            return this.errors;
        },
        setError(keyModel, errorDetail) {
            this.errors.push({
                keyModel: keyModel,
                errorDetail: errorDetail
            });
        },
        errorExists(keyModel) {
            return this.errors.filter(err => err.keyModel === keyModel).length;
        },
        showError(keyModel) {
            return this.errors.find(err => err.keyModel === keyModel);
        }
    },
    components: {
        MainContent,
        VInputColorpicker
    }
}

</script>

<style scoped>
input.icp__input {
    border: 2px solid #333a40 !important;
}
</style>
