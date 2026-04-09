<template>
    <div class="search-page container-fluid py-5 px-3" style="background-color: #ffffff; min-height: 100vh;">
        <div class="card border-0 mx-auto rounded-lg shadow-sm" style="max-width: 1200px; border-radius: 20px;">
            <div class="card-body text-center" >
                <div class="card-body text-center p-md-5 border-2 border-dashed rounded-lg" style="border-color: #0d9fb2 !important; border-radius: 20px;">
                    <h3 class="font-weight-bold mb-3" style="color: #0d9fb2;  font-family: Poppins, sans-serif;">
                        <i class="fas fa-qrcode mr-2 "></i> Buscar tu Código QR
                    </h3>

                    <p class="text-secondary mb-4">
                        Ingresa tu <strong>correo electrónico o teléfono</strong>
                        para validar tu participación en el evento.
                    </p>

                    <div class="row justify-content-center mb-3">
                        <!-- Campo único para Email o Teléfono -->
                        <div class="col-md-5 col-12 mb-2 mb-md-0">
                            <input
                                v-model="searchValue"
                                type="text"
                                class="form-control form-control-lg text-center"
                                placeholder="Correo electrónico o teléfono"
                                @keyup.enter="buscarClientes"
                                :class="{ 'is-invalid': errorExists('search') }"
                            />
                            <small class="invalid-feedback d-block" v-if="errorExists('search')">
                                {{ showError('search').errorDetail }}
                            </small>
                        </div>

                        <!-- Botón -->
                        <div class="col-md-3 col-12">
                            <button
                                class="btn btn-lg btn-block font-weight-semibold"
                                style="background-color: #0d9fb2; border-color: #0d9fb2; color: white;"
                                @click="buscarClientes"
                                :disabled="loading"
                            >
                                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-search'"></i>
                                {{ loading ? "Buscando..." : "Buscar" }}
                            </button>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        <i class="fas fa-lock mr-1"></i> Tus datos solo se usarán para esta validación.
                    </p>

                    <!-- Resultado -->
                    <transition name="fade">
                        <div v-if="clientes && clientes.cliente"
                            class="ticket-card mx-auto mt-4 p-3 rounded bg-white" style="border-radius: 20px;">

                            <!-- Cinta superior -->
                            <div class="ticket-header text-white text-center py-2 mb-3" style="border-radius: 20px 20px 0 0;">
                                <i class="fas fa-user-check mr-2"></i> PARTICIPANTE VALIDADO
                            </div>

                            <!-- Contenido -->
                            <div class="row align-items-center">
                                <!-- QR -->
                                <div class="col-lg-4 text-center mb-3 mb-lg-0">
                                    <div class="qr-container p-3 bg-oscuro rounded d-inline-block" style="border-radius: 20px;">
                                        <img :src="clientes.qr" alt="QR del participante"
                                             class="img-fluid rounded"
                                             style="max-width: 200px" />
                                    </div>
                                    <p class="mt-3 small text-muted">
                                        <i class="fas fa-qrcode mr-1"></i>Escanea este código en el ingreso del evento
                                    </p>
                                </div>

                                <!-- Datos -->
                                <div class="col-lg-8 text-center text-lg-left">
                                    <h4 class="font-weight-bold mb-1" style="color: #0d9fb2;">
                                        {{ clientes.cliente.nombres }}
                                    </h4>
                                    <p class="text-secondary mb-3"
                                        v-if="clientes.cliente.apellidopaterno || clientes.cliente.apellidomaterno">
                                        {{ clientes.cliente.apellidopaterno }} {{ clientes.cliente.apellidomaterno }}
                                    </p>

                                    <ul class="list-unstyled mb-3 text-center text-lg-left">
                                        <li v-if="clientes.cliente.email" class="mb-2">
                                            <i class="fas fa-envelope text-secondary mr-2"></i>
                                            <strong>Correo: </strong>{{ clientes.cliente.email }}
                                        </li>
                                        <li v-if="clientes.cliente.telefono" class="mb-2">
                                            <i class="fas fa-phone text-secondary mr-2"></i>
                                            <strong>Celular: </strong>{{ clientes.cliente.telefono }}
                                        </li>
                                        <li v-if="clientes.cliente.dni" class="mb-2">
                                            <i class="fas fa-id-card text-secondary mr-2"></i>
                                            <strong>DNI: </strong>{{ clientes.cliente.dni }}
                                        </li>
                                        <li v-if="clientes.cliente.carrera" class="mb-2">
                                            <i class="fas fa-graduation-cap text-secondary mr-2"></i>
                                            <strong>Carrera: </strong>{{ clientes.cliente.carrera }}
                                        </li>
                                        <li v-if="clientes.cliente.grado" class="mb-2">
                                            <i class="fas fa-award text-secondary mr-2"></i>
                                            <strong>Grado: </strong>{{ clientes.cliente.grado }}
                                        </li>
                                        <li v-if="clientes.cliente.colegio" class="mb-2">
                                            <i class="fas fa-map-marker-alt text-secondary mr-2"></i>
                                            <strong>Sede: </strong>{{ clientes.cliente.colegio }}
                                        </li>
                                        <li v-if="clientes.cliente.coddepartamento && clientes.cliente.departamento" class="mb-2">
                                            <i class="fas fa-globe-americas text-secondary mr-2"></i>
                                            <strong>Departamento: </strong>{{ clientes.cliente.departamento.nombrecompleto }}
                                        </li>
                                        <li v-if="clientes.cliente.coddistrito && clientes.cliente.distrito" class="mb-2">
                                            <i class="fas fa-globe-americas text-secondary mr-2"></i>
                                            <strong>Distrito: </strong>{{ clientes.cliente.distrito.nombrecompleto }}
                                        </li>
                                    </ul>

                                    <div class="ticket-footer border-top pt-3 mt-3 small text-muted">
                                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                                            <div class="mr-3 mb-2" v-if="clientes.cliente.anioegreso">
                                                <i class="fas fa-calendar-check text-secondary mr-1"></i>
                                                <strong>Egreso:</strong> {{ clientes.cliente.anioegreso }}
                                            </div>
                                            <div class="mr-3 mb-2" v-if="clientes.cliente.ismatricula !== undefined">
                                                <i :class="clientes.cliente.ismatricula ? 'fas fa-user-graduate text-success' : 'fas fa-user text-muted'"
                                                    class="mr-1"></i>
                                                <strong>Matriculado:</strong> {{ clientes.cliente.ismatricula ? 'Sí' : 'No' }}
                                            </div>
                                            <div class="mb-2" v-if="clientes.cliente.fecharegistro">
                                                <i class="fas fa-clock text-secondary mr-1"></i>
                                                <strong>Inscrito:</strong> {{ formatDate(clientes.cliente.fecharegistro) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Error -->
                    <div v-if="error" class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ error }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Buscatuqr',
    data() {
        return {
            clientes: {},
            cliente: {
                telefono: "",
                email: "",
            },
            searchValue: "",
            error: null,
            errors: [],
            loading: false,
            filters: {}
        };
    },
    methods: {
        listarDatos() {
            this.loading = true;
            let vm = this;

            axios.get('/rest/buscatuqr/search', {
                params: this.filters,
            })
            .then(function (response) {
                vm.loading = false;
                vm.clientes = response.data.dataaditional;
                console.log('Datos recibidos:', response.data);
            })
            .catch(function (error) {
                vm.loading = false;
                if (error.response && error.response.data) {
                    vm.error = error.response.data.message || 'Error al buscar';
                } else {
                    vm.error = 'Error de conexión con el servidor';
                }
                console.error('Error:', error);
            });
        },

        buscarClientes() {
            this.clientes = {};
            this.error = null;

            if (this.validateFields().length > 0) {
                return;
            }

            // Detectar si es email o teléfono
            this.filters = {};
            const value = this.searchValue.trim();

            // Si contiene @ es un email
            if (value.includes('@')) {
                this.filters.email = value;
            } else {
                // Si no, es teléfono
                this.filters.telefono = value;
            }

            this.listarDatos();
        },

        validateFields() {
            this.errors = [];

            if (!this.searchValue || !this.searchValue.trim()) {
                this.setError("search", "Este campo es obligatorio");
                this.error = "Por favor, ingresa tu correo electrónico o teléfono.";
            }

            return this.errors;
        },

        setError(keyModel, errorDetail) {
            this.errors.push({
                keyModel: keyModel,
                errorDetail: errorDetail,
            });
        },

        errorExists(keyModel) {
            return this.errors.filter((err) => err.keyModel === keyModel).length;
        },

        showError(keyModel) {
            return this.errors.find((err) => err.keyModel === keyModel);
        },

        formatDate(date) {
            if (!date) return '';
            // Formatear fecha sin moment.js
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }
    },

    mounted() {
        console.log('Componente BuscaTuQR montado');
        // Leer el parámetro email de la URL y buscar automáticamente
        const params = new URLSearchParams(window.location.search);
        const email = params.get('email');
        if (email) {
            this.searchValue = email;
            this.buscarClientes();
        }
    }
};
</script>

<style scoped>
.ticket-header {
    background: linear-gradient(90deg, #0d9fb2, #0a7f8f);
    font-weight: 600;
    letter-spacing: 0.5px;
}

.qr-container {
    border: 2px dashed #0d9fb2;
}

.ticket-card {
    max-width: 950px;
    position: relative;
    overflow: hidden;
}

.ticket-cutout {
    position: absolute;
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 50%;
    left: -20px;
    border: 2px solid #e9ecef;
}

.ticket-cutout.top {
    top: 30%;
}

.ticket-cutout.bottom {
    bottom: 30%;
}

/* Animación suave */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@media (max-width: 768px) {
    .ticket-card {
        padding: 1.5rem;
    }

    .qr-container img {
        max-width: 160px;
    }
}
.search-page {
    background-color: #ffffff;
}

.card {
    background-color: #f8f9fa;
    border-radius: 15px;
}

@media (min-width: 1200px) {
    .container-xl, .container-lg, .container-md, .container-sm, .container {
        max-width: 1400px;
    }
}
.ribbon {
    position: absolute;
    top: 0px;
    left: 0;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}

input::placeholder {
    color: #00000089 !important;
    opacity: 1;
}

::-webkit-input-placeholder {
    color: #00000089;
}

::-moz-placeholder {
    color: #00000089;
    opacity: 1;
}

:-ms-input-placeholder {
    color: #00000089;
}

:-moz-placeholder {
    color: #00000089;
}

</style>
