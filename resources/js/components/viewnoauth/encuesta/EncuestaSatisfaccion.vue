<template>
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-sm survey-card">
                    <div class="card-header survey-header">
                        <h4 class="mb-1">Encuesta de Satisfaccion Wiener Fest</h4>
                        <p class="mb-0 small">¿Cómo te fue hoy? Cuéntanos en menos de 3 minutos
⭐  10 preguntas · No hay respuestas correctas ni incorrectas — solo tu opinión real. </p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div v-if="alert.show" class="alert mb-4" :class="alertClass" role="alert">
                            <strong>{{ alert.title }}</strong>
                            <div class="small mt-1" v-html="alert.message"></div>
                        </div>

                        <form @submit.prevent="submitForm" novalidate>
                            <div class="mb-4">
                                <label class="question-title">1. Cuanto conocias la Universidad Wiener antes de venir hoy?</label>
                                <div class="mt-2" :class="{ 'is-invalid-block': errorExists('q1') }">
                                    <div class="form-check" v-for="opt in q1Options" :key="`q1-${opt.value}`">
                                        <input class="form-check-input" type="radio" name="q1" :id="`q1-${opt.value}`" :value="opt.value" v-model.number="form.q1_conocimiento_antes">
                                        <label class="form-check-label" :for="`q1-${opt.value}`">{{ opt.label }}</label>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q1')">{{ showError('q1').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">2. Despues del Wiener Fest, cuanto conoces ahora la Universidad Wiener?</label>
                                <div class="mt-2" :class="{ 'is-invalid-block': errorExists('q2') }">
                                    <div class="form-check" v-for="opt in q2Options" :key="`q2-${opt.value}`">
                                        <input class="form-check-input" type="radio" name="q2" :id="`q2-${opt.value}`" :value="opt.value" v-model.number="form.q2_conocimiento_despues">
                                        <label class="form-check-label" :for="`q2-${opt.value}`">{{ opt.label }}</label>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q2')">{{ showError('q2').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">3. Que fue lo que mas te gusto? (Puedes marcar mas de uno)</label>
                                <div class="row mt-2" :class="{ 'is-invalid-block': errorExists('q3') }">
                                    <div class="col-12 col-md-6" v-for="opt in q3Options" :key="`q3-${opt.value}`">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" :id="`q3-${opt.value}`" :value="opt.value" v-model="form.q3_gusto_mas">
                                            <label class="form-check-label" :for="`q3-${opt.value}`">{{ opt.label }}</label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q3')">{{ showError('q3').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">4. Como te sientes con el Wiener Fest en general?</label>
                                <div class="nps-grid-q4 mt-2" :class="{ 'is-invalid-block': errorExists('q4') }">
                                    <label class="score-item" v-for="opt in q4Options" :key="`q4-${opt.value}`">
                                        <input type="radio" name="q4" :value="opt.value" v-model.number="form.q4_sentimiento_general">
                                        <span>{{ opt.label }}</span>
                                    </label>
                                </div>
                                <small class="text-danger" v-if="errorExists('q4')">{{ showError('q4').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <label class="question-title mb-0">5. Participaste en un taller? Calificalo</label>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="participoTaller" v-model="form.participo_taller">
                                        <label class="form-check-label" for="participoTaller">Si participe en taller</label>
                                    </div>
                                </div>

                                <div class="mt-3" v-if="form.participo_taller" :class="{ 'is-invalid-block': errorExists('q5') }">
                                    <div class="mb-3">
                                        <label class="small font-weight-bold">El taller en general</label>
                                        <div class="score-line">
                                            <label v-for="n in 5" :key="`q5a-${n}`"><input type="radio" name="q5a" :value="n" v-model.number="form.q5_taller_general"> {{ n }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small font-weight-bold">El/La tallerista (dominio, claridad, energia)</label>
                                        <div class="score-line">
                                            <label v-for="n in 5" :key="`q5b-${n}`"><input type="radio" name="q5b" :value="n" v-model.number="form.q5_tallerista"> {{ n }}</label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="small font-weight-bold">Aprendiste algo nuevo?</label>
                                        <div class="score-line">
                                            <label v-for="n in 5" :key="`q5c-${n}`"><input type="radio" name="q5c" :value="n" v-model.number="form.q5_aprendio_nuevo"> {{ n }}</label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q5')">{{ showError('q5').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">6. Cuanto recomendarias la Universidad Wiener a un amigo? (0 al 10)</label>
                                <div class="nps-grid mt-2" :class="{ 'is-invalid-block': errorExists('q6') }">
                                    <label class="score-item" v-for="n in 11" :key="`q6-${n - 1}`">
                                        <input type="radio" name="q6" :value="n - 1" v-model.number="form.q6_nps">
                                        <span>{{ n - 1 }}</span>
                                    </label>
                                </div>
                                <small class="text-danger" v-if="errorExists('q6')">{{ showError('q6').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">7. Estas pensando en estudiar en la Universidad Wiener?</label>
                                <div class="mt-2" :class="{ 'is-invalid-block': errorExists('q7') }">
                                    <div class="form-check" v-for="opt in q7Options" :key="`q7-${opt.value}`">
                                        <input class="form-check-input" type="radio" name="q7" :id="`q7-${opt.value}`" :value="opt.value" v-model.number="form.q7_intencion_estudiar">
                                        <label class="form-check-label" :for="`q7-${opt.value}`">{{ opt.label }}</label>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q7')">{{ showError('q7').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">8. Que cambiarias para que la proxima edicion sea aun mejor?</label>
                                <textarea rows="4" class="form-control mt-2" :class="{ 'is-invalid': errorExists('q8') }" v-model.trim="form.q8_que_cambiarias" placeholder="Escribe tu respuesta"></textarea>
                                <small class="text-danger" v-if="errorExists('q8')">{{ showError('q8').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">9. Como supiste del Wiener Fest?</label>
                                <div class="mt-2" :class="{ 'is-invalid-block': errorExists('q9') }">
                                    <div class="form-check" v-for="opt in q9Options" :key="`q9-${opt.value}`">
                                        <input class="form-check-input" type="radio" name="q9" :id="`q9-${opt.value}`" :value="opt.value" v-model.number="form.q9_como_se_entero">
                                        <label class="form-check-label" :for="`q9-${opt.value}`">{{ opt.label }}</label>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q9')">{{ showError('q9').errorDetail }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="question-title">10. Cual es tu situacion ahora?</label>
                                <div class="mt-2" :class="{ 'is-invalid-block': errorExists('q10') }">
                                    <div class="form-check" v-for="opt in q10Options" :key="`q10-${opt.value}`">
                                        <input class="form-check-input" type="radio" name="q10" :id="`q10-${opt.value}`" :value="opt.value" v-model.number="form.q10_situacion_actual">
                                        <label class="form-check-label" :for="`q10-${opt.value}`">{{ opt.label }}</label>
                                    </div>
                                </div>
                                <small class="text-danger" v-if="errorExists('q10')">{{ showError('q10').errorDetail }}</small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                    {{ loading ? 'Enviando...' : 'Enviar encuesta' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'EncuestaSatisfaccion',
    data() {
        return {
            endpoint: '/rest/encuesta',
            loading: false,
            errors: [],
            confPagEncuesta: null,
            alert: {
                show: false,
                type: 'success',
                title: '',
                message: '',
            },
            form: {
                idcampania: null,
                idevento: null,
                q1_conocimiento_antes: null,
                q2_conocimiento_despues: null,
                q3_gusto_mas: [],
                q4_sentimiento_general: null,
                participo_taller: false,
                q5_taller_general: null,
                q5_tallerista: null,
                q5_aprendio_nuevo: null,
                q6_nps: null,
                q7_intencion_estudiar: null,
                q8_que_cambiarias: '',
                q9_como_se_entero: null,
                q10_situacion_actual: null,
                origen_qr: 'qr_general_encuesta',
            },
            q1Options: [
                { value: 1, label: '🤷 No la conocía para nada' },
                { value: 2, label: '👂 La había escuchado pero no sabía mucho' },
                { value: 3, label: '🙂 La conocía más o menos' },
                { value: 4, label: '🎓La conocía muy bien, ya la estaba considerando' },
            ],
            q2Options: [
                { value: 1, label: '😐 Igual que antes' },
                { value: 2, label: '📖 La conozco un poco más' },
                { value: 3, label: '✅ La conozco bastante mejor' },
                { value: 4, label: '🔥 ¡Ahora la conozco muy bien!' },
            ],
            q3Options: [
                { value: 'stands', label: 'Los stands por carrera' },
                { value: 'docentes', label: 'Hablar con docentes' },
                { value: 'instalaciones', label: 'Las instalaciones' },
                { value: 'info_carreras', label: 'Info sobre carreras' },
                { value: 'talleres', label: 'Los talleres' },
                { value: 'alumnos', label: 'Hablar con alumnos' },
                { value: 'ambiente', label: 'El ambiente y dinamica' },
                { value: 'organizacion', label: 'La organizacion' },
            ],
            q4Options: [
                { value: 1, label: 'Malo 😞' },
                { value: 2, label: 'Regular 😕' },
                { value: 3, label: 'Bien 🙂' },
                { value: 4, label: 'Muy Bien 😄' },
                { value: 5, label: '¡Genial! 🤩' },
            ],
            q7Options: [
                { value: 1, label: '✅ Sí, ya tomé la decisión' },
                { value: 2, label: '🤔 Sí, es una de mis opciones' },
                { value: 3, label: ' 👀 Lo estoy evaluando' },
                { value: 4, label: '❌ Por ahora no' },
                { value: 5, label: '🎓 Ya soy alumno/a o egresado/a' },
            ],
            q9Options: [
                { value: 1, label: '📱 Redes sociales' },
                { value: 2, label: '💬 WhatsApp / mensaje' },
                { value: 3, label: '👫 Un amigo o familiar' },
                { value: 4, label: '🏫 Mi colegio / instituto' },
                { value: 5, label: '📧 Email' },
                { value: 6, label: '📢 Me avisaron en la U' },
            ],
            q10Options: [
                { value: 1, label: '📚 En el colegio (4.° o 5.°)' },
                { value: 2, label: '🎒 Egresé, aún no estudio' },
                { value: 3, label: '🏛️ Estudio en otra universidad' },
                { value: 4, label: '💼 Trabajo / soy profesional' },
                { value: 5, label: '🎓 Soy alumno/a de Wiener' },
                { value: 6, label: '👪 Soy padre / madre' },
            ],
        };
    },
    computed: {
        alertClass() {
            return {
                'alert-success': this.alert.type === 'success',
                'alert-warning': this.alert.type === 'warning',
                'alert-danger': this.alert.type === 'danger',
            };
        },
    },
    methods: {
        setError(keyModel, errorDetail) {
            this.errors.push({ keyModel, errorDetail });
        },
        errorExists(keyModel) {
            return this.errors.some((err) => err.keyModel === keyModel);
        },
        showError(keyModel) {
            return this.errors.find((err) => err.keyModel === keyModel);
        },
        setAlert(type, title, message) {
            this.alert = { show: true, type, title, message };
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        validateFields() {
            this.errors = [];

            if (!this.form.q1_conocimiento_antes) this.setError('q1', 'Selecciona una opcion.');
            if (!this.form.q2_conocimiento_despues) this.setError('q2', 'Selecciona una opcion.');
            if (!this.form.q3_gusto_mas.length) this.setError('q3', 'Selecciona al menos una opcion.');
            if (!this.form.q4_sentimiento_general) this.setError('q4', 'Selecciona una opcion.');

            if (this.form.participo_taller) {
                if (!this.form.q5_taller_general || !this.form.q5_tallerista || !this.form.q5_aprendio_nuevo) {
                    this.setError('q5', 'Completa las 3 calificaciones de taller.');
                }
            }

            if (this.form.q6_nps === null || this.form.q6_nps === undefined) this.setError('q6', 'Selecciona un valor de 0 a 10.');
            if (!this.form.q7_intencion_estudiar) this.setError('q7', 'Selecciona una opcion.');
            if (!this.form.q8_que_cambiarias || !this.form.q8_que_cambiarias.trim()) this.setError('q8', 'Esta respuesta es obligatoria.');
            if (!this.form.q9_como_se_entero) this.setError('q9', 'Selecciona una opcion.');
            if (!this.form.q10_situacion_actual) this.setError('q10', 'Selecciona una opcion.');

            return this.errors;
        },
        buildPayload() {
            return {
                idcampania: this.form.idcampania,
                idevento: this.form.idevento,
                q1_conocimiento_antes: this.form.q1_conocimiento_antes,
                q2_conocimiento_despues: this.form.q2_conocimiento_despues,
                q3_gusto_mas: this.form.q3_gusto_mas.join(','),
                q4_sentimiento_general: this.form.q4_sentimiento_general,
                q5_taller_general: this.form.participo_taller ? this.form.q5_taller_general : null,
                q5_tallerista: this.form.participo_taller ? this.form.q5_tallerista : null,
                q5_aprendio_nuevo: this.form.participo_taller ? this.form.q5_aprendio_nuevo : null,
                q6_nps: this.form.q6_nps,
                q7_intencion_estudiar: this.form.q7_intencion_estudiar,
                q8_que_cambiarias: this.form.q8_que_cambiarias.trim(),
                q9_como_se_entero: this.form.q9_como_se_entero,
                q10_situacion_actual: this.form.q10_situacion_actual,
                origen_qr: this.form.origen_qr,
            };
        },
        resetForm() {
            this.form.q1_conocimiento_antes = null;
            this.form.q2_conocimiento_despues = null;
            this.form.q3_gusto_mas = [];
            this.form.q4_sentimiento_general = null;
            this.form.participo_taller = false;
            this.form.q5_taller_general = null;
            this.form.q5_tallerista = null;
            this.form.q5_aprendio_nuevo = null;
            this.form.q6_nps = null;
            this.form.q7_intencion_estudiar = null;
            this.form.q8_que_cambiarias = '';
            this.form.q9_como_se_entero = null;
            this.form.q10_situacion_actual = null;
            this.errors = [];
        },
        async submitForm() {
            this.alert.show = false;
            this.validateFields();

            if (this.errors.length) {
                this.setAlert('warning', 'Faltan respuestas', 'Completa los campos obligatorios para enviar la encuesta.');
                return;
            }

            this.loading = true;
            try {
                const url = `${window.appRootUrl}${this.endpoint}`;
                const payload = this.buildPayload();
                const response = await axios.post(url, payload, {
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                if (response.data.status === true) {
                    this.setAlert('success', 'Gracias por tu respuesta', response.data.message || 'Encuesta registrada correctamente.');
                    this.resetForm();
                } else if (response.data.warning) {
                    this.setAlert('warning', 'Atencion', response.data.message || 'Revisa las respuestas obligatorias.');
                } else {
                    this.setAlert('danger', 'Error', response.data.message || 'No se pudo guardar la encuesta.');
                }
            } catch (error) {
                const message = error?.response?.data?.message || 'No se pudo guardar la encuesta, intenta nuevamente.';
                this.setAlert('danger', 'Error', message);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        if (window.__CONFIG_PAG_ENCUESTA__) {
            this.confPagEncuesta = window.__CONFIG_PAG_ENCUESTA__;
            this.form.idcampania = this.confPagEncuesta?.idcampania || null;
            delete window.__CONFIG_PAG_ENCUESTA__;
        }
    },
};
</script>

<style scoped>
.survey-card {
    border-radius: 14px;
}

.survey-header {
    background: linear-gradient(135deg, #0d9fb2 0%, #056e7a 100%);
    color: #fff;
    border-radius: 14px 14px 0 0;
}

.question-title {
    font-weight: 700;
    color: #17384a;
    display: block;
}

.nps-grid {
    display: grid;
    grid-template-columns: repeat(11, minmax(0, 1fr));
    gap: 8px;
}

.nps-grid-q4{
 display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 8px;
}

.score-item {
    border: 1px solid #cfd8df;
    border-radius: 8px;
    padding: 8px 4px;
    text-align: center;
    cursor: pointer;
    margin: 0;
}

.score-item input {
    margin-right: 4px;
}

.score-line {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 8px;
}

.score-line label {
    border: 1px solid #cfd8df;
    border-radius: 6px;
    padding: 6px;
    text-align: center;
    margin: 0;
    cursor: pointer;
}

.is-invalid-block {
    border: 1px solid #dc3545;
    border-radius: 8px;
    padding: 10px;
}

@media (max-width: 992px) {
    .nps-grid {
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }
}

@media (max-width: 576px) {
    .nps-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .score-line {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .nps-grid-q4{
 display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}
}
</style>
