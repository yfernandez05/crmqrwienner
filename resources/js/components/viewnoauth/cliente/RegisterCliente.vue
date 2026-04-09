<template>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-11 wiener-border">
                <div class="card wiener-card">
                    <div class="card-header wiener-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-user-plus"></i>
                            <h5 class="mb-0">Registro de participación</h5>
                        </div>
                        <small class="text-black">Completa los datos para ser parte del evento.</small>
                    </div>

                    <div class="card-body">
                        <!-- ALERTAS (éxito/error) -->
                        <div v-if="alert.show" class="alert shadow-sm border-1 d-flex align-items-center gap-3 p-3 mb-4"
                            :class="alertClass" role="alert">

                            <!-- Icono Grande dinámico -->
                            <i :class="alertIcon" class="fs-2"></i>

                            <!-- Contenido del mensaje -->
                            <div class="w-100">
                                <!-- Título dinámico -->
                                <h5 class="fw-bold mb-1">{{ alert.title }}</h5>
                                <!-- AQUÍ SE MOSTRARÁ: "Los datos ingresados se guardaron correctamente." -->
                                <p class="mb-0 small" v-html="alert.message"></p>
                            </div>

                            <!-- Botón cerrar -->
                            <button type="button" class="btn-close align-self-start" @click="closeAlert"
                                aria-label="Close"></button>
                        </div>

                        <form @submit.prevent="submitForm" novalidate>
                            <div class="row g-3">
                                <!-- Nombres -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Nombres*</label>
                                    <input v-model.trim="form.nombres" type="text" class="form-control"
                                        :class="{ 'is-invalid': errorExists('nombres') }"
                                        placeholder="Ej: Yanet Camila" />
                                    <div class="invalid-feedback">
                                        {{ showError('nombres')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- Apellidos -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Apellidos*</label>
                                    <input v-model.trim="form.apellidopaterno" type="text" class="form-control"
                                        :class="{ 'is-invalid': errorExists('apellidopaterno') }"
                                        placeholder="Ej: Pérez García" />
                                    <div class="invalid-feedback">
                                        {{ showError('apellidopaterno')?.errorDetail }}
                                    </div>
                                </div>

                <!-- Celular -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Celular*</label>
                                    <input v-model.trim="form.telefono" type="tel" class="form-control"
                                        :class="{ 'is-invalid': errorExists('telefono') }" placeholder="999999999"
                                        inputmode="numeric" />
                                    <div class="invalid-feedback">
                                        {{ showError('telefono')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- Correo electrónico -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Correo electrónico*</label>
                                    <input v-model.trim="form.email" type="email" class="form-control"
                                        :class="{ 'is-invalid': errorExists('email') }"
                                        placeholder="correo@ejemplo.com" autocomplete="email" />
                                    <div class="invalid-feedback">
                                        {{ showError('email')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- Carrera -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Carrera de interés*</label>
                                    <select v-model="form.carrera" class="form-select"
                                        :class="{ 'is-invalid': errorExists('carrera') }">
                                        <option value="" disabled>Selecciona una carrera</option>
                                        <option v-for="c in carreraOptions" :key="c" :value="c">
                                            {{ c }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ showError('carrera')?.errorDetail }}
                                    </div>
                                </div>

                               <!-- Escoge tu campus -->
                                <div class="col-12 col-md-6 mb-3" v-if="form.carrera && sedesFiltradas.length > 1">
                                    <label class="form-label">Escoge tu campus*</label>
                                    <select v-model="form.sedes" class="form-select"
                                        :class="{ 'is-invalid': errorExists('sedes') }">
                                        <option value="" disabled>Selecciona un campus</option>
                                        <option v-for="s in sedesFiltradas" :key="s" :value="s">
                                            {{ s }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ showError('sedes')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- Grado de estudios -->
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Grado de estudios*</label>
                                    <select v-model="form.grado" class="form-select"
                                        :class="{ 'is-invalid': errorExists('grado') }">
                                        <option value="" disabled>Selecciona grado de estudios</option>
                                        <option v-for="g in anioEgresoOptions" :key="g" :value="g">
                                            {{ g }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ showError('grado')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- Año de egreso (solo si selecciona "Ya terminé colegio") -->
                                <div class="col-12 col-md-6 mb-3" v-if="form.grado === 'Ya terminé colegio'">
                                    <label class="form-label">Año de egreso*</label>
                                    <input v-model="form.anioegreso" type="number" min="1950" max="2099" class="form-control"
                                        :class="{ 'is-invalid': errorExists('anioegreso') }"
                                        placeholder="Ej: 2024" />
                                    <div class="invalid-feedback">
                                        {{ showError('anioegreso')?.errorDetail }}
                                    </div>
                                </div>

                                <!-- CHECKBOXES -->
                                <div class="col-12">
                                    <hr class="my-2" />

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="chkPrivacidad"
                                            v-model="checks.privacidad"
                                            :class="{ 'is-invalid': errorExists('privacidad') }" />
                                        <label class="form-check-label" for="chkPrivacidad">
                                            Declaro expresamente haber leído y aceptado las <a href="https://continuatuformacion.uwiener.edu.pe/wp-content/uploads/2024/12/POLITICA-DE-PROTECCION-DE-DATOS-PERSONALES.pdf?_gl=1*1jj8d1*_gcl_au*NTk0MDM2NS4xNzY2MDA2NjE2LjczMDkyNzg2OC4xNzY3NzEzMzgzLjE3Njc3MTMzODg.*_ga*NTMxOTkzODQ3LjE3NjYwMDY2MTY.*_ga_WLR48FCJRD*czE3Njk3MjAxMjEkbzMkZzEkdDE3Njk3MjEzMjUkajU2JGwwJGg3MDQ5MTY3ODI." target="_blank">Políticas de Privacidad.</a>
                                            <em class="zf-important">*</em>
                                        </label>
                                        <div class="invalid-feedback">
                                            {{ showError('privacidad')?.errorDetail }}
                                        </div>
                                    </div>
                                </div>

                                <!-- BOTONES -->
                                <div class="col-12 d-flex flex-wrap gap-3 justify-content-end mt-4">
                                    <button type="button" class="btn btn-wiener-outline" @click="resetForm"
                                        :disabled="loading">
                                        <i class="fa-solid fa-eraser me-1"></i> Limpiar
                                    </button>

                                    <button type="submit" class="btn btn-wiener-primary" :disabled="loading">
                                        <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"
                                            aria-hidden="true"></span>
                                        <i v-else class="fa-solid fa-paper-plane me-1"></i>
                                        Enviar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer wiener-footer">
                        UNIVERSIDAD NORBERT WIENER - Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RegisterCliente",
    data() {
        return {
            endpoint: "/rest/cliente/saveClientQr",
            loading: false,
            error: null,
            errors: [],
            alert: {
                show: false,
                type: "success",
                title: "",
                message: "",
                debug: "",
            },
            form: {
                 anioegreso: "",
                 apellidomaterno: "GENERICO",
                 apellidopaterno: "",
                 carrera: "",
                 sedes: "",
                 grado: "",
                 edad: null,
                 email: "",
                 nombres: "",
                 telefono: "",
                 coddepartamento: "15",
                 prefijo: "Perú (+51)",
                 tipodocumento: "DNI",
                 dni: "7777777",
            },
            checks: {
                privacidad: true,
            },
            carreraSedeMap: {
                'ENFERMERÍA': ["Lima Centro", "Lima Norte"],
                'FARMACIA Y BIOQUÍMICA': ["Lima Centro", "Lima Norte"],
                'MEDICINA HUMANA': ["Lima Centro", "Lima Norte"],
                'MEDICINA VETERINARIA': ["Lima Norte"],// no hay sede Lima Centro ni Lima Norte
                'NUTRICIÓN Y DIETÉTICA': ["Lima Centro", "Lima Norte"],
                'OBSTETRICIA': ["Lima Centro", "Lima Norte"],
                'ODONTOLOGÍA': ["Lima Centro", "Lima Norte"],
                'PSICOLOGÍA':   ["Lima Centro", "Lima Norte"],
                'TECNOLOGÍA MÉDICA EN LABORATORIO CLÍNICO Y ANATOMÍA PATOLÓGICA': ["Lima Centro", "Lima Norte"],
                'TECNOLOGÍA MÉDICA EN TERAPIA FÍSICA Y REHABILITACIÓN': ["Lima Centro", "Lima Norte"],
                'DERECHO Y CIENCIA POLÍTICA': ["Lima Centro", "Lima Norte"],
                'ARQUITECTURA': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte

                'INGENIERÍA CIVIL': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte
                'INGENIERÍA DE SISTEMAS E INFORMÁTICA': ["Lima Centro", "Lima Norte"],
                'INGENIERÍA INDUSTRIAL Y DE GESTIÓN EMPRESARIAL': ["Lima Centro", "Lima Norte"],
                'INGENIERÍA BIOMÉDICA': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte

                'ADMINISTRACIÓN EN TURISMO Y HOTELERÍA': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte
                'ADMINISTRACIÓN Y DIRECCIÓN DE EMPRESAS': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte
                'ADMINISTRACIÓN Y MARKETING': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte
                'ADMINISTRACIÓN Y NEGOCIOS INTERNACIONALES': ["Lima Centro", "Lima Norte"],
                'COMUNICACIÓN EN MEDIOS DIGITALES': ["Lima Centro"], // no hay sede Lima Centro ni Lima Norte
                'CONTABILIDAD Y AUDITORÍA': ["Lima Centro"],// no hay sede Lima Centro ni Lima Norte
            },
            anioEgresoOptions: ["4to", "5to", "Ya terminé colegio"],
            departamentosOptions: [
                { "coddepartamento": "01", "departamento": "Amazonas", "nombrecompleto": "Amazonas" },
                { "coddepartamento": "02", "departamento": "Áncash", "nombrecompleto": "Áncash" },
                { "coddepartamento": "03", "departamento": "Apurímac", "nombrecompleto": "Apurímac" },
                { "coddepartamento": "04", "departamento": "Arequipa", "nombrecompleto": "Arequipa" },
                { "coddepartamento": "05", "departamento": "Ayacucho", "nombrecompleto": "Ayacucho" },
                { "coddepartamento": "06", "departamento": "Cajamarca", "nombrecompleto": "Cajamarca" },
                { "coddepartamento": "07", "departamento": "Callao", "nombrecompleto": "Callao" },
                { "coddepartamento": "08", "departamento": "Cusco", "nombrecompleto": "Cusco" },
                { "coddepartamento": "09", "departamento": "Huancavelica", "nombrecompleto": "Huancavelica" },
                { "coddepartamento": "10", "departamento": "Huánuco", "nombrecompleto": "Huánuco" },
                { "coddepartamento": "11", "departamento": "Ica", "nombrecompleto": "Ica" },
                { "coddepartamento": "12", "departamento": "Junín", "nombrecompleto": "Junín" },
                { "coddepartamento": "13", "departamento": "La Libertad", "nombrecompleto": "La Libertad" },
                { "coddepartamento": "14", "departamento": "Lambayeque", "nombrecompleto": "Lambayeque" },
                { "coddepartamento": "15", "departamento": "Lima", "nombrecompleto": "Lima" },
                { "coddepartamento": "16", "departamento": "Loreto", "nombrecompleto": "Loreto" },
                { "coddepartamento": "17", "departamento": "Madre de Dios", "nombrecompleto": "Madre de Dios" },
                { "coddepartamento": "18", "departamento": "Moquegua", "nombrecompleto": "Moquegua" },
                { "coddepartamento": "19", "departamento": "Pasco", "nombrecompleto": "Pasco" },
                { "coddepartamento": "20", "departamento": "Piura", "nombrecompleto": "Piura" },
                { "coddepartamento": "21", "departamento": "Puno", "nombrecompleto": "Puno" },
                { "coddepartamento": "22", "departamento": "San Martín", "nombrecompleto": "San Martín" },
                { "coddepartamento": "23", "departamento": "Tacna", "nombrecompleto": "Tacna" },
                { "coddepartamento": "24", "departamento": "Tumbes", "nombrecompleto": "Tumbes" },
                { "coddepartamento": "25", "departamento": "Ucayali", "nombrecompleto": "Ucayali" }
            ],
            sedesOptions: ["Lima Centro", "Lima Norte"],
            conf_pag_registro: null
        };
    },

    computed: {
        alertClass() {
            return {
                'alert-success': this.alert.type === 'success',
                'alert-danger': this.alert.type === 'danger',
                'alert-warning': this.alert.type === 'warning'
            };
        },
        alertIcon() {
            switch (this.alert.type) {
                case 'success': return 'fa-solid fa-circle-check text-success';
                case 'danger': return 'fa-solid fa-circle-xmark text-danger';
                case 'warning': return 'fa-solid fa-triangle-exclamation text-warning';
                default: return 'fa-solid fa-info-circle';
            }
        },
        carreraOptions() {
            return Object.keys(this.carreraSedeMap);
        },
        sedesFiltradas() {
            if (!this.form.carrera) return [];
            return this.carreraSedeMap[this.form.carrera] || this.sedesOptions;
        },
    },

    methods: {
        showPreloader() {
            this.loading = true;
        },
        hidePreloader() {
            this.loading = false;
        },
        setAlert({ type, title, message }) {
            this.alert.show = true;
            this.alert.type = type;
            this.alert.title = title;
            this.alert.message = message;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeAlert() {
            this.alert.show = false;
        },
        setError(keyModel, errorDetail) {
            this.errors.push({ keyModel, errorDetail });
        },
        errorExists(keyModel) {
            return this.errors.some((err) => err.keyModel === keyModel);
        },
        showError(keyModel) {
            return this.errors.find((err) => err.keyModel === keyModel);
        },
        validateFields() {
            this.errors = [];
            this.error = null;

            if (!this.form.nombres) this.setError("nombres", "Nombres es obligatorio.");
            if (!this.form.apellidopaterno) this.setError("apellidopaterno", "Apellidos es obligatorio.");

            if (!this.form.email) {
                this.setError("email", "El correo es obligatorio.");
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                this.setError("email", "Correo no válido.");
            }

            if (!this.form.telefono) {
                this.setError("telefono", "El teléfono es obligatorio.");
            } else if (!/^\d{9}$/.test(this.form.telefono)) {
                this.setError("telefono", "Teléfono no válido (9 dígitos).");
            }

            if (!this.form.carrera) this.setError("carrera", "Selecciona una carrera.");
            if (!this.form.sedes) this.setError("sedes", "Selecciona un campus.");
            if (!this.form.grado) this.setError("grado", "Selecciona tu grado de estudios.");
            if (this.form.grado === 'Ya terminé colegio') {
                if (!this.form.anioegreso) {
                this.setError("anioegreso", "El año de egreso es obligatorio.");
                } else if (!/^\d{4}$/.test(this.form.anioegreso) || this.form.anioegreso < 1950 || this.form.anioegreso > 2099) {
                    this.setError("anioegreso", "Año de egreso no válido.");
                }
            }

            if (!this.checks.privacidad) this.setError("privacidad", "Debes aceptar la política de privacidad.");

            if (this.errors.length) {
                this.error = "Revisa los campos marcados.";
            }

            return this.errors;
        },
        buildPayload() {
            return {
                anioegreso: String(this.form.anioegreso),
                apellidomaterno: String(this.form.apellidomaterno),
                apellidopaterno: String(this.form.apellidopaterno),
                carrera: String(this.form.carrera),
                colegio: String(this.form.sedes),
                grado: String(this.form.grado),
                edad: Number(this.form.edad) || 0,
                email: String(this.form.email),
                idcampania: Number(this.conf_pag_registro.idcampania),
                nombres: String(this.form.nombres),
                telefono: String(this.form.telefono),
                prefijo: String(this.form.prefijo),
                tipodocumento: String(this.form.tipodocumento),
                dni: String(this.form.dni),
                // isbeca18 eliminado
                coddepartamento: String(this.form.coddepartamento) || '15',
                coddistrito: null,
                procedencia: 'Orgánico',
                campaign_source: 'Web',
                campaign_medium: 'Formulario',
            };
        },
        async submitForm() {
            this.closeAlert();
            this.validateFields();

            if (this.errors.length) {
                this.setAlert({
                    type: 'warning',
                    title: 'Campos incompletos',
                    message: 'Por favor, completa los campos obligatorios marcados en rojo.'
                });
                return;
            }

            const token = this.conf_pag_registro.api_token;
            if (!token) {
                this.setAlert({
                    type: 'danger',
                    title: 'Error de sesión',
                    message: 'No se encontró el token de seguridad.'
                });
                return;
            }

            const url = `${window.appRootUrl}${this.endpoint}`;
            const payload = this.buildPayload();

            this.showPreloader();
            try {
                const resp = await axios.post(url, payload, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                console.log('Respuesta del servidor:', resp);
                const apiData = resp.data;
                console.log('API Data:', apiData);

                if (apiData.status === true) {
                    this.setAlert({
                        type: 'success',
                        title: '¡Registro Exitoso! - En breve te mostraremos tu código QR.',
                        message: `Recuerda que el código QR también será enviado a tu correo electrónico.<br> Serás redirigido a tu código QR en unos segundos.`
                    });

                    setTimeout(() => {
                        const email = encodeURIComponent(this.form.email);
                        window.location.href = `/buscatuqr?email=${email}`;
                    }, 3000);

                } else if (apiData.warning === true) {
                    // Caso de correo duplicado (warning)
                    const email = encodeURIComponent(this.form.email);
                    let urlemailduplicado = `/buscatuqr?email=${email}`;

                    this.setAlert({
                        type: 'warning',
                        title: 'Atención',
                        message: `${apiData.message}<br> Tu código QR ya existe y puedes acceder a él aquí: <a href="${urlemailduplicado}" class="fw-bold text-decoration-underline">Ver mi código QR</a>`
                    });
                } else {
                    // Error real del servidor
                    this.setAlert({
                        type: 'danger',
                        title: 'Error',
                        message: apiData.message || 'Ocurrió un error inesperado. Intente nuevamente más tarde'
                    });
                }

            } catch (err) {
                console.log('Error capturado:', err);
                console.log('Error response:', err.response);

                let msgError = "Ocurrió un error.";
                if (err.response && err.response.data && err.response.data.message) {
                    msgError = err.response.data.message;
                }

                this.setAlert({
                    type: 'danger',
                    title: 'Error',
                    message: msgError
                });
            } finally {
                this.hidePreloader();
            }
        },
        resetForm() {
            this.errors = [];
            this.error = null;
            this.closeAlert();

            this.form = {
                anioegreso: "2024",
                apellidomaterno: "-",
                apellidopaterno: "",
                carrera: "",
                sedes: "",
                grado: "",
                edad: null,
                email: "",
                nombres: "",
                telefono: "",
                // isbeca18 eliminado
                coddepartamento: "15",
                prefijo: "Perú (+51)",
                tipodocumento: "DNI",
                dni: "7777777",
            };

            this.checks = { privacidad: false };
        },
    },

    mounted() {
        // Cargar configuración desde window
        if (window.__CONFIG_PAG_REGISTRO__) {
            this.conf_pag_registro = window.__CONFIG_PAG_REGISTRO__;
            delete window.__CONFIG_PAG_REGISTRO__;
        }
    },
    watch: {
        'form.carrera'(newCarrera) {
            const sedes = this.carreraSedeMap[newCarrera] || [];
            // Si solo hay una sede, la asigna automáticamente
            if (sedes.length === 1) {
                this.form.sedes = sedes[0];
            } else {
                this.form.sedes = '';
            }
        },
        'form.grado'(newGrado) {
        if (newGrado !== 'Ya terminé colegio') {
            this.form.anioegreso = '';
        }
    }
    },

};
</script>

<style scoped>
/* Colores corporativos Universidad Norbert Wiener */
:root {
    --wiener-celeste: #08e1e6;
    --wiener-azul: #00669e;
    --wiener-amarillo: #fac93d;
}

/* Card principal */
.wiener-card {
    box-shadow: 0 4px 6px rgba(0, 102, 158, 0.1);
    border: none;
    border-radius: 8px;
    overflow: hidden;
}

.wiener-border {
    border: 3px solid #08e1e6 !important;
    border-radius: 8px;
    overflow: hidden;
    padding: 0px;
}

/* Header */
.wiener-header {
    background: linear-gradient(135deg, #08e1e6 0%, #00669e 100%);
    color: white;
    border-bottom: 3px solid #fac93d;
    padding: 1.5rem;
}

.wiener-header i {
    color: #fff;
    font-size: 1.5rem;
    padding-right: 13px;
}

.wiener-header h5 {
    color: white;
    font-weight: 600;
    margin: 0;
}

.wiener-header small {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

/* Inputs y selects */
.form-control:focus,
.form-select:focus {
    border-color: #08e1e6;
    box-shadow: 0 0 0 0.25rem rgba(8, 225, 230, 0.25);
}

.btn-wiener-outline {
    margin-right: 16px; /* Puedes ajustar el valor */
}

.form-select,
.form-control {
    width: 100%;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.6rem 0.75rem;
    transition: all 0.3s ease;
}

.form-select:hover,
.form-control:hover {
    border-color: #08e1e6;
}

.form-label {
    font-weight: 600;
    color: #00669e;
    margin-bottom: 0.5rem;
}

/* Botones */
.btn-wiener-primary {
    background: linear-gradient(135deg, #00669e 0%, #08e1e6 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.6rem 2rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 102, 158, 0.3);
}

.btn-wiener-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #005580 0%, #06b8bd 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 102, 158, 0.4);
}

.btn-wiener-outline {
    background: white;
    border: 2px solid #00669e;
    color: #00669e;
    font-weight: 600;
    padding: 0.6rem 2rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-wiener-outline:hover:not(:disabled) {
    background: #00669e;
    color: white;
    transform: translateY(-2px);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Checkboxes */
.form-check-input:checked {
    background-color: #00669e;
    border-color: #00669e;
}

.form-check-input:focus {
    border-color: #08e1e6;
    box-shadow: 0 0 0 0.25rem rgba(8, 225, 230, 0.25);
}

.form-check-label {
    color: #333;
    font-size: 0.9rem;
}

.form-check-label a {
    color: #00669e;
    font-weight: 600;
    text-decoration: none;
}

.form-check-label a:hover {
    color: #08e1e6;
    text-decoration: underline;
}

.zf-important {
    color: #dc3545;
    font-style: normal;
}

/* Alertas */
.alert {
    background-color: #fff;
    border-left: 5px solid;
    border-radius: 6px;
}

.alert-success {
    border-color: #08e1e6;
    background-color: rgba(8, 225, 230, 0.1);
}

.alert-success .fa-circle-check {
    color: #08e1e6;
}

.alert-danger {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

.alert-warning {
    border-color: #fac93d;
    background-color: rgba(250, 201, 61, 0.1);
}

.alert-warning .fa-triangle-exclamation {
    color: #fac93d;
}

/* Footer */
.wiener-footer {
    background-color: #f8f9fa;
    color: #00669e;
    font-weight: 500;
    text-align: center;
    padding: 1rem;
    border-top: 3px solid #08e1e6;
}

/* Separador */
hr {
    border-color: #08e1e6;
    opacity: 0.3;
}

/* Responsive */
@media (max-width: 768px) {
    .row.justify-content-center {
        padding: 15px;
    }

    .wiener-header {
        padding: 1rem;
    }

    .wiener-card .card-body {
        padding: 15px;
    }

    .btn-wiener-primary,
    .btn-wiener-outline {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
}
</style>
