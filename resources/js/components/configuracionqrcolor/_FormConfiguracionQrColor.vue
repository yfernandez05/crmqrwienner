<template>
    <main-content columnClass="col-12 col-xl-10">
        <template v-slot:card-header-title>
            <span v-text="cardTitle"></span>
        </template>

        <template v-slot:card-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-6 col-md-4" :class="{'has-danger':errorExists('anio_egreso')}">
                    <label>Año de Egreso</label>
                    <input type="text" class="form-control" v-model="configuracion.anio_egreso"
                           @keyup.enter="doSaveData" placeholder="Ej: 2026 (vacío = por defecto)" maxlength="10" />
                    <small class="form-control-feedback" v-if="errorExists('anio_egreso')"
                           v-text="showError('anio_egreso').errorDetail"></small>
                    <small class="form-text text-muted">Dejar vacío para configuración por defecto</small>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4" :class="{'has-danger':errorExists('es_beca18')}">
                    <label>¿Es Beca18? <small class="text-danger">(*)</small></label>
                    <select class="form-control" v-model="configuracion.es_beca18" @change="doSaveData">
                        <option :value="0">No</option>
                        <option :value="1">Sí</option>
                    </select>
                    <small class="form-control-feedback" v-if="errorExists('es_beca18')"
                           v-text="showError('es_beca18').errorDetail"></small>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4" :class="{'has-danger':errorExists('orden')}">
                    <label>Orden de Prioridad <small class="text-danger">(*)</small></label>
                    <vue-numeric class="form-control" ref="orden"
                        @keypress.native.enter.prevent="doSaveData"
                        thousand-separator="" v-model="configuracion.orden" v-bind:precision="0" >
                    </vue-numeric>
                    <small class="form-control-feedback" v-if="errorExists('orden')"
                           v-text="showError('orden').errorDetail"></small>
                    <small class="form-text text-muted">Menor número = mayor prioridad</small>
                </div>

                <div class="form-group col-12 col-sm-8 col-md-8" :class="{'has-danger':errorExists('color')}">
                    <label>Color <small class="text-danger">(*)</small></label>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" v-model="configuracion.color"
                                       @input="syncColorPicker" placeholder="Ej: #ff0000 o orange" maxlength="50" />
                                <div class="input-group-append">
                                    <label class="btn btn-outline-secondary mb-0" style="cursor: pointer; padding: 0; width: 100px; height: 38px; position: relative; overflow: hidden;">
                                        <input type="color" v-model="colorPicker" @input="updateColorFromPicker"
                                               style="position: absolute; width: 100%; height: 100%; border: none; cursor: pointer; opacity: 0;">
                                        <span class="d-flex align-items-center justify-content-center h-100"
                                              :style="`background-color: ${colorPicker}; border: 2px solid #fff; border-radius: 3px; margin: 3px;`">
                                            <i class="fas fa-palette"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <small class="form-control-feedback" v-if="errorExists('color')"
                                   v-text="showError('color').errorDetail"></small>
                            <small class="form-text text-muted">Formato: #ff0000 o nombre (orange, blue, white)</small>
                        </div>
                        <div class="col-md-4">
                            <div v-if="configuracion.color"
                                 class="d-flex align-items-center justify-content-center h-100"
                                 :style="`background-color: ${configuracion.color}; border: 2px solid #ddd; border-radius: 4px; min-height: 80px;`">
                                <span :style="`color: ${getTextColor(configuracion.color)}; font-weight: bold; font-size: 14px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);`">
                                    Vista previa
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Colores predefinidos -->
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1">Colores comunes:</small>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" v-for="presetColor in colorPresets" :key="presetColor.value"
                                    @click="setPresetColor(presetColor.value)"
                                    class="btn btn-sm"
                                    :style="`background-color: ${presetColor.value}; color: ${getTextColor(presetColor.value)}; border: 2px solid ${configuracion.color === presetColor.value ? '#000' : '#ddd'}; min-width: 80px;`"
                                    :title="presetColor.name">
                                {{ presetColor.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-4 col-md-4" :class="{'has-danger':errorExists('estado')}">
                    <label>Estado <small class="text-danger">(*)</small></label>
                    <select class="form-control" v-model="configuracion.estado">
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                    <small class="form-control-feedback" v-if="errorExists('estado')"
                           v-text="showError('estado').errorDetail"></small>
                </div>

                <div class="form-group col-12" :class="{'has-danger':errorExists('descripcion')}">
                    <label>Descripción <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="configuracion.descripcion"
                           @keyup.enter="doSaveData" placeholder="Ej: Quinto año de secundaria" maxlength="200" />
                    <small class="form-control-feedback" v-if="errorExists('descripcion')"
                           v-text="showError('descripcion').errorDetail"></small>
                    <small class="form-text text-muted">Texto que se mostrará al escanear el QR</small>
                </div>
            </div>
            <hr class="mt-2">
        </template>

        <template v-slot:card-body-actions>
            <router-link :to="{name: 'spa.configuracionqrcolor'}" class="btn waves-effect waves-light btn-info mr-2">
                <i class="fas fa-reply"></i> <span class="button-text">Atrás</span>
            </router-link>

            <div>
                <button type="button" class="btn btn-success waves-effect waves-light" @click="doSaveData">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
                <button type="reset" class="btn waves-effect waves-light btn-outline-secondary ml-2" @click="resetForm">
                    <i class="fa fa-window-close"></i> <span class="button-text">Cancelar</span>
                </button>
            </div>
        </template>

    </main-content>
</template>

<script>
    import MainContent from './../../utils/MainContent';
    import VueNumeric from 'vue-numeric';

    export default {
        props: {
            cardTitle: {
                type: String,
                default: 'Configuración QR Color'
            },
            configuracion: {
                type: Object,
                default() {
                    return {
                        anio_egreso: null,
                        es_beca18: 0,
                        color: '#000000',
                        descripcion: '',
                        orden: 999,
                        estado: 'A'
                    }
                }
            }
        },
        data() {
            return {
                colorPicker: '#000000',
                errors: [],
                colorPresets: [
                    { name: 'Naranja', value: 'orange' },
                    { name: 'Azul', value: 'blue' },
                    { name: 'Celeste', value: '#00bfff' },
                    { name: 'Blanco', value: 'white' },
                    { name: 'Rojo', value: '#ff0000' },
                    { name: 'Verde', value: '#28a745' },
                    { name: 'Amarillo', value: '#ffc107' },
                    { name: 'Morado', value: '#6f42c1' },
                    { name: 'Negro', value: '#000000' }
                ]
            }
        },
        watch: {
            configuracion: {
                handler(newVal) {
                    if (newVal && Object.keys(newVal).length > 0) {
                        // Intentar convertir el color a hex para el picker
                        if (this.configuracion.color && this.configuracion.color.startsWith('#')) {
                            this.colorPicker = this.configuracion.color;
                        }
                    }
                },
                deep: true,
                immediate: true
            }
        },
        methods: {
            doSaveData() {
                if (this.validateFields().length > 0) {
                    return;
                }

                // Limpiar anio_egreso si está vacío
                let configData = {
                    anio_egreso: this.configuracion.anio_egreso || null,
                    es_beca18: this.configuracion.es_beca18,
                    color: this.configuracion.color,
                    descripcion: this.configuracion.descripcion,
                    orden: this.configuracion.orden,
                    estado: this.configuracion.estado
                };

                this.$emit('saveData', configData);
            },
            validateFields() {
                this.errors = [];

                if (!this.configuracion.color) {
                    this.setError('color', 'El campo Color es obligatorio');
                }
                if (!this.configuracion.descripcion) {
                    this.setError('descripcion', 'El campo Descripción es obligatorio');
                }
                if (!this.configuracion.orden) {
                    this.setError('orden', 'El campo Orden es obligatorio');
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
            },
            updateColorFromPicker() {
                this.configuracion.color = this.colorPicker;
            },
            syncColorPicker() {
                // Sincronizar el color picker si es un hex válido
                if (this.configuracion.color && this.configuracion.color.startsWith('#') &&
                    (this.configuracion.color.length === 4 || this.configuracion.color.length === 7)) {
                    this.colorPicker = this.configuracion.color;
                }
            },
            setPresetColor(color) {
                this.configuracion.color = color;
                if (color.startsWith('#')) {
                    this.colorPicker = color;
                }
            },
            resetForm() {
                this.errors = [];
            },
            getTextColor(bgColor) {
                if (!bgColor) return '#000';

                let color = bgColor.replace('#', '');

                if (color.length !== 3 && color.length !== 6) {
                    return '#000';
                }

                let r, g, b;
                if (color.length === 3) {
                    r = parseInt(color.charAt(0) + color.charAt(0), 16);
                    g = parseInt(color.charAt(1) + color.charAt(1), 16);
                    b = parseInt(color.charAt(2) + color.charAt(2), 16);
                } else {
                    r = parseInt(color.substring(0, 2), 16);
                    g = parseInt(color.substring(2, 4), 16);
                    b = parseInt(color.substring(4, 6), 16);
                }

                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

                return luminance > 0.5 ? '#000' : '#fff';
            }
        },
        components: {
            'main-content': MainContent,
            'vue-numeric': VueNumeric
        }
    }
</script>
