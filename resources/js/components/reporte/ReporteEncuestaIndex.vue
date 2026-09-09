<template>
    <div>
        <main-content columnClass="col-12">
            <template v-slot:card-header-title>
                Reporte de Encuesta
            </template>

            <template v-slot:card-header-actions>
                <button class="btn btn-sm btn-info waves-effect waves-light" @click.prevent="buscarReporte">
                    <i class="fas fa-sync-alt"></i>
                    <span class="d-none d-sm-inline-block">Actualizar</span>
                </button>
            </template>

            <template v-slot:card-body-main>
                <div class="alert alert-info mb-3" v-if="campaniaFija">
                    <strong>Campaña:</strong>
                    <span v-text="campaniaFija.nombrecampania"></span>
                </div>

                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card border-left-primary shadow-sm h-100">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-2">Total encuestas</h6>
                                <h2 class="mb-0" v-text="resumen.total_encuestas"></h2>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-12 col-md-4 mb-3">
                        <div class="card border-left-success shadow-sm h-100">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-2">Respondieron taller</h6>
                                <h2 class="mb-0" v-text="resumen.respondieron_taller"></h2>
                            </div>
                        </div>
                    </div> -->

                    <!-- <div class="col-12 col-md-4 mb-3">
                        <div class="card border-left-danger shadow-sm h-100">
                            <div class="card-body py-3">
                                <h6 class="text-uppercase text-muted mb-2">NPS score</h6>
                                <h2 class="mb-0" v-text="nps.score"></h2>
                            </div>
                        </div>
                    </div> -->
                </div>

                <div class="row mt-2">
                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 1: Conocimiento antes</h5>
                        <simple-table :rows="preguntas.q1_conocimiento_antes" :label-map="q1Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ1" :datos="preguntas.q1_conocimiento_antes" :label-map="q1Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 2: Conocimiento despues</h5>
                        <simple-table :rows="preguntas.q2_conocimiento_despues" :label-map="q2Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ2" :datos="preguntas.q2_conocimiento_despues" :label-map="q2Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 3: Lo que mas gusto (Top)</h5>
                        <kv-table :rows="preguntas.q3_gusto_mas" :label-map="q3Map"></kv-table>
                        <div class="chart-wrap mt-2">
                            <chart-bar-encuesta-top-q3 ref="chartQ3" :datos="preguntas.q3_gusto_mas" :label-map="q3Map"></chart-bar-encuesta-top-q3>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 4: Sentimiento general</h5>
                        <simple-table :rows="preguntas.q4_sentimiento_general" :label-map="q4Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ4" :datos="preguntas.q4_sentimiento_general" :label-map="q4Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 5A: El taller en general</h5>
                        <simple-table :rows="preguntas.q5_taller_general"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ5General" :datos="preguntas.q5_taller_general"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 5B: El/La tallerista (dominio, claridad, energia)</h5>
                        <simple-table :rows="preguntas.q5_tallerista"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ5Tallerista" :datos="preguntas.q5_tallerista"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 5C: Aprendiste algo nuevo</h5>
                        <simple-table :rows="preguntas.q5_aprendio_nuevo"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ5Aprendio" :datos="preguntas.q5_aprendio_nuevo"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 6: NPS (0-10)</h5>
                        <kv-table :rows="preguntas.q6_nps_distribucion" key-label="Puntaje"></kv-table>
                        <div class="chart-wrap mt-2">
                            <chart-bar-encuesta-nps ref="chartQ6" :datos="preguntas.q6_nps_distribucion"></chart-bar-encuesta-nps>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 7: Intencion de estudiar</h5>
                        <simple-table :rows="preguntas.q7_intencion_estudiar" :label-map="q7Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ7" :datos="preguntas.q7_intencion_estudiar" :label-map="q7Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 9: Como se entero</h5>
                        <simple-table :rows="preguntas.q9_como_se_entero" :label-map="q9Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ9" :datos="preguntas.q9_como_se_entero" :label-map="q9Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4">
                        <h5 class="title_report">Pregunta 10: Situacion actual</h5>
                        <simple-table :rows="preguntas.q10_situacion_actual" :label-map="q10Map"></simple-table>
                        <div class="chart-wrap mt-2">
                            <chart-pie-encuesta-opciones ref="chartQ10" :datos="preguntas.q10_situacion_actual" :label-map="q10Map"></chart-pie-encuesta-opciones>
                        </div>
                    </div>

                    <!-- <div class="col-12 mb-4">
                        <h5 class="title_report">Pregunta 8: Comentarios recientes</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="p-2">ID</th>
                                        <th class="p-2">Comentario</th>
                                        <th class="p-2">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in comentarios" :key="row.idencuesta">
                                        <td class="p-2" v-text="row.idencuesta"></td>
                                        <td class="p-2" v-text="row.q8_que_cambiarias"></td>
                                        <td class="p-2" v-text="formatDate(row.dateinsert)"></td>
                                    </tr>
                                    <tr v-if="!comentarios.length">
                                        <td colspan="3" class="text-center p-2">Sin comentarios registrados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->
                </div>
            </template>
        </main-content>
    </div>
</template>

<script>
import MainContent from '../../utils/MainContent';
import ChartPieEncuestaOpciones from './_ChartpieEncuestaOpciones.vue';
import ChartBarEncuestaNps from './_ChartbarEncuestaNps.vue';
import ChartBarEncuestaTopQ3 from './_ChartbarEncuestaTopQ3.vue';

const SimpleTable = {
    props: {
        rows: { type: Array, default: () => [] },
        labelMap: { type: Object, default: () => ({}) },
    },
    methods: {
        resolveLabel(value) {
            if (Object.prototype.hasOwnProperty.call(this.labelMap, value)) {
                return this.labelMap[value];
            }
            return value;
        },
    },
    template: `
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="p-2">Opcion</th>
                        <th class="p-2">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, idx) in rows" :key="idx">
                        <td class="p-2">{{ resolveLabel(row.opcion) }}</td>
                        <td class="p-2">{{ row.total }}</td>
                    </tr>
                    <tr v-if="!rows.length">
                        <td colspan="2" class="text-center p-2">Sin datos</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `,
};

const KvTable = {
    props: {
        rows: { type: [Object, Array], default: () => ({}) },
        labelMap: { type: Object, default: () => ({}) },
        keyLabel: { type: String, default: 'Opcion' },
    },
    computed: {
        normalizedRows() {
            if (Array.isArray(this.rows)) {
                return this.rows.map((x, idx) => {
                    if (x !== null && typeof x === 'object') {
                        const key = Object.prototype.hasOwnProperty.call(x, 'opcion') ? x.opcion
                            : (Object.prototype.hasOwnProperty.call(x, 'key') ? x.key : idx);
                        const total = Object.prototype.hasOwnProperty.call(x, 'total') ? x.total
                            : (Object.prototype.hasOwnProperty.call(x, 'cantidad') ? x.cantidad : 0);

                        return { key, total };
                    }

                    // Soporta distribuciones serializadas como arreglo simple: [0, 2, 1, ...]
                    return {
                        key: idx,
                        total: Number(x) || 0,
                    };
                });
            }

            return Object.keys(this.rows || {}).map((key) => ({
                key,
                total: this.rows[key],
            }));
        },
    },
    methods: {
        resolveLabel(value) {
            if (Object.prototype.hasOwnProperty.call(this.labelMap, value)) {
                return this.labelMap[value];
            }
            return value;
        },
    },
    template: `
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="p-2">{{ keyLabel }}</th>
                        <th class="p-2">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, idx) in normalizedRows" :key="idx">
                        <td class="p-2">{{ resolveLabel(row.key) }}</td>
                        <td class="p-2">{{ row.total }}</td>
                    </tr>
                    <tr v-if="!normalizedRows.length">
                        <td colspan="2" class="text-center p-2">Sin datos</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `,
};

export default {
    name: 'ReporteEncuestaIndex',
    components: {
        MainContent,
        ChartPieEncuestaOpciones,
        ChartBarEncuestaNps,
        ChartBarEncuestaTopQ3,
        SimpleTable,
        KvTable,
    },
    data() {
        return {
            campaniaFija: null,
            resumen: {
                total_encuestas: 0,
                respondieron_taller: 0,
                no_respondieron_taller: 0,
            },
            preguntas: {
                q1_conocimiento_antes: [],
                q2_conocimiento_despues: [],
                q3_gusto_mas: {},
                q4_sentimiento_general: [],
                q6_nps_distribucion: {},
                q7_intencion_estudiar: [],
                q9_como_se_entero: [],
                q10_situacion_actual: [],
            },
            nps: {
                promotores: 0,
                pasivos: 0,
                detractores: 0,
                score: 0,
            },
            comentarios: [],
            q1Map: {
                1: 'No la conocia para nada',
                2: 'La habia escuchado pero no sabia mucho',
                3: 'La conocia mas o menos',
                4: 'La conocia muy bien, ya la estaba considerando',
            },
            q2Map: {
                1: 'Igual que antes',
                2: 'La conozco un poco mas',
                3: 'La conozco bastante mejor',
                4: 'Ahora la conozco muy bien',
            },
            q3Map: {
                stands: 'Los stands por carrera',
                docentes: 'Hablar con docentes',
                instalaciones: 'Las instalaciones',
                info_carreras: 'Info sobre carreras',
                talleres: 'Los talleres',
                alumnos: 'Hablar con alumnos',
                ambiente: 'El ambiente y dinamica',
                organizacion: 'La organizacion',
            },
            q4Map: {
                1: 'Mala',
                2: 'Regular',
                3: 'Bien',
                4: 'Muy bien',
                5: 'Genial',
            },
            q7Map: {
                1: 'Si, ya tome la decision',
                2: 'Si, es una de mis opciones',
                3: 'Lo estoy evaluando',
                4: 'Por ahora no',
                5: 'Ya soy alumno/a o egresado/a',
            },
            q9Map: {
                1: 'Redes sociales',
                2: 'WhatsApp o mensaje',
                3: 'Amigo o familiar',
                4: 'Colegio o instituto',
                5: 'Email',
                6: 'Me avisaron en la U',
            },
            q10Map: {
                1: 'En el colegio (4to o 5to)',
                2: 'Egrese, aun no estudio',
                3: 'Estudio en otra universidad',
                4: 'Trabajo o soy profesional',
                5: 'Soy alumno/a de Wiener',
                6: 'Soy padre o madre',
            },
        };
    },
    methods: {
        buscarReporte() {
            let vm = this;
            showPreloader();
            axios.get(`${appApiUrl}/reporte/encuesta/preguntas`)
                .then(function (response) {
                    const result = response.data || {};
                    vm.campaniaFija = result.campania_fija || null;
                    vm.resumen = result.resumen || vm.resumen;
                    vm.preguntas = {
                        ...vm.preguntas,
                        ...(result.preguntas || {}),
                    };
                    vm.nps = result.nps || vm.nps;
                    vm.comentarios = result.comentarios_q8 || [];
                    vm.$nextTick(() => {
                        vm.renderCharts();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    errorMessage(appErrorMessage, appName);
                })
                .then(() => {
                    hidePreloader();
                });
        },
        renderCharts() {
            const refs = ['chartQ1', 'chartQ2', 'chartQ3', 'chartQ4', 'chartQ5General', 'chartQ5Tallerista', 'chartQ5Aprendio', 'chartQ6', 'chartQ7', 'chartQ9', 'chartQ10'];
            refs.forEach((name) => {
                if (this.$refs[name] && typeof this.$refs[name].mostrar === 'function') {
                    this.$refs[name].mostrar();
                }
            });
        },
        resetReporte() {
            this.resumen = {
                total_encuestas: 0,
                respondieron_taller: 0,
                no_respondieron_taller: 0,
            };
            this.preguntas = {
                q1_conocimiento_antes: [],
                q2_conocimiento_despues: [],
                q3_gusto_mas: {},
                q4_sentimiento_general: [],
                q6_nps_distribucion: {},
                q7_intencion_estudiar: [],
                q9_como_se_entero: [],
                q10_situacion_actual: [],
            };
            this.nps = {
                promotores: 0,
                pasivos: 0,
                detractores: 0,
                score: 0,
            };
            this.comentarios = [];
        },
        formatDate(value) {
            if (!value) return '';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return value;
            const dd = String(date.getDate()).padStart(2, '0');
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const yyyy = date.getFullYear();
            const hh = String(date.getHours()).padStart(2, '0');
            const mi = String(date.getMinutes()).padStart(2, '0');
            return `${dd}-${mm}-${yyyy} ${hh}:${mi}`;
        },
    },
    created() {
        this.buscarReporte();
    },
};
</script>

<style scoped>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.chart-wrap {
    min-height: 280px;
}
</style>
