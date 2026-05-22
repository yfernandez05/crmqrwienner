<template>
    <div>

    <!-- Contenedor principal -->
    <main-content style="padding: 0px;">
        <!-- titulo -->
        <template v-slot:card-header-title>
            LECTOR QR
        </template>
        <!-- Acciones header -->
        <template v-slot:card-header-actions>

        </template>
        <template v-slot:card-body-main>
            <div class="card border rounded shadow-sm">
                <div class="card-body p-0">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-8 p-3" v-if="!cameraOn">
                            <label class="col-12 d-none d-md-block"> </label>
                            <h5 class="card-title mb-0">Escanea el QR para validar las asistencias </h5>
                        </div>
                        <div class="form-group col-12 col-md-4 p-2" v-if="!cameraOn">
                            <div class="row">
                                <div class="form-group col-12 pt-2 mb-0">
                                    <button @click="toggleScanner(true)"  class="btn btn-primary badge-pill display-2 px-4 shadow-sm">
                                        <i class="fas fa-qrcode"></i>
                                        <span class="hide-menu">ESCANEAR QR</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-12" v-show="cameraOn">
                            <div class="qr-scanner-container">
                                <div id="video-container">
                                    <div class="form-group col-12 p-1 cont-btn-actions-qr" v-if="cameraOn">
                                        <button @click="toggleScanner(false)"  class="btn btn-secondary badge-pill display-2 px-4 shadow-sm btn-off">
                                            <i class="fas fa-eye-slash"></i>
                                            <span class="hide-menu off-text-btn">APAGAR</span>
                                        </button>
                                        <button v-if="cameras.length > 1" @click="switchCamera"
                                                class="btn badge-pill display-2 px-4 shadow-sm btn-flash">
                                                <i class="fa-solid fa-camera-rotate me-1"></i> Girar
                                        </button>
                                        <button @click="toggleFlash" class="btn badge-pill display-2 px-4 shadow-sm btn-flash" :class="flashOn ? 'btn-warning' : 'btn-secondary'">
                                            <i class="fas fa-lightbulb"></i>
                                            <span class="hide-menu flash-text-btn">{{ flashOn ? 'ON' : 'OFF' }}</span>
                                        </button>

                                    </div>
                                    <!-- Elemento de video para mostrar la transmisión de la cámara -->
                                    <video ref="videoElement" id="qr-video" width="100%" height="500"></video>
                                </div>
                                <!-- Mostrar el área de resaltado (highlight) -->
                                <div class="scan-region-highlight"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fomrulario busqueda -->
            <div class="form-row" v-show="authenticatedUser.idrol <= 2 || authenticatedUser.idrol == 6">
                <hr class="col-12 text-secondary">
                <div class="form-group col-12" v-if="!cameraOn">
                    <label class="col-12 d-none d-md-block"> </label>
                    <h6 class="card-title mb-0"><strong>¿Desea registrar la asistencia manualmente?</strong> Por favor, busque al asistente ingresando su correo electrónico:</h6>
                </div>
                <div class="form-group col-12 col-sm-8">
                    <v-select class="select-vue-customers"
                        v-model="selectedClient"
                        :filterable="false"
                        :options="clientes"
                        :searchable="true"
                        label="email"
                        :loading="loading"
                        @search="onSearch"
                        @input="onSelectClient"
                        placeholder="Escriba al menos 3 caracteres del correo, telefono ó nombres y apellidos.">
                        <template #option="data">
                            <div class="my-1">
                                <div class="d-flex no-block text-truncate">
                                    <span>Correo: </span><span class="font-weight-bolder pl-1 pr-3"
                                        v-text="data.email"></span>
                                </div>
                                <div class="d-flex no-block text-truncate">
                                    <span>Telefono: </span><span class="font-weight-bolder pl-1 pr-3"
                                        v-text="data.telefono"></span>
                                </div>
                            </div>
                        </template>
                        <template #no-options>
                            <span>No se encontraron opciones</span>
                        </template>
                    </v-select>
                </div>
                <div class="form-group col-12 col-sm-4" v-if="selectedClient" >
                    <div v-if="!selectedClient.isassistance">
                        <button class="btn btn-md btn-info waves-effect badge-pill px-4 shadow-sm" @click="markAttendance()">
                            <i class="far fa-check-circle"></i>
                            <span class="">
                                Registrar Asistencia
                            </span>
                        </button>
                    </div>
                </div>
                <div class="form-group col-12 mb-0" v-if="selectedClient">
                    <div class="col-sm-12 p-0">
                        <div class="form-row info-cont-qr-search p-2">
                            <div class="card col-12 col-md-4  cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">Nombres:</label>
                                    <p class="card-text small" v-text="selectedClient.nombres"></p>
                                </div>
                            </div>
                            <div class="card col-12 col-md-4  cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">Apellidos:</label>
                                    <p class="card-text small" v-text="selectedClient.apellidopaterno + ' '+ selectedClient.apellidomaterno"></p>
                                </div>
                            </div>
                            <div class="card col-12 col-md-4 cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">Email:</label>
                                    <p class="card-text text-info small" v-text="selectedClient.email"></p>
                                </div>
                            </div>
                            <div class="card col-12 col-md-4 cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">telefono:</label>
                                    <p class="card-text text-info small" v-text="selectedClient.telefono"></p>
                                </div>
                            </div>
                            <div class="card col-12 col-md-4 cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">Asistencia:</label><br>
                                    <span class="badge badge-pill py-1 px-3" :class="selectedClient.isassistance ? 'badge-success':'badge-warning'" v-text="selectedClient.asistencianame"></span>
                                </div>
                            </div>
                            <div class="card col-12 col-md-4 cont-details-info">
                                <div class="card-body py-0 py-md-3">
                                    <label class="h6 title-search-qr mb-1 text-info-dark">Matricula:</label><br>
                                    <span class="badge badge-pill py-1 px-3" :class="selectedClient.ismatricula ? 'badge-info':'badge-secondary'" v-text="selectedClient.matriculaname"></span>
                                </div>
                            </div>
                            <div v-if="selectedClient?.evento_cliente?.length" class="card col-12 cont-details-info">
                                <div class="d-flex flex-wrap">
                                    <div v-for="(evento, index) in selectedClient.evento_cliente" :key="index" class="evento-item p-2">
                                        <h6 class="title-search-qr mb-1 text-info-dark" v-text="evento.evento.nombreevento"></h6>
                                        <p class="card-text text-dark small" v-text="evento.fechaasistencia"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3" v-if="!selectedClient.isassistance">
                            <p class="font-italic small text-muted mt-1 text-center">
                                El asistente no ha registrado su asistencia. Haga clic en 'Registrar Asistencia'.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </template>
    </main-content>
</div>
</template>

<script>

import MainContent from './../../utils/MainContent';
import PaginationLinks from './../../utils/PaginationLinks';
import RowActions from './../../utils/RowActions';
import QrScanner from 'qr-scanner';
import vSelect from 'vue-select';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            qrResult: null,
            scanner: null,
            highlightStyle: "example-style-1",
            cameraOn: false,
            flashOn: false,
            requestCompleted: false,
            flashSupported: false,
            users: {},

            selectedClient: null,
            clientes: [],
            loading: false,

            cameras: [],
            currentCameraIndex: 0,
        };
    },
    methods: {

        toggleScanner(turnOn) {
            if (turnOn) {
                this.startScanner();
            } else {
                this.stopScanner();
            }
        },

        setupScanner(){

            const videoElement = this.$refs.videoElement;
            this.isProcessing = false;

            QrScanner.listCameras(true).then(cameras => {

                console.log('cámaras disponibles:', cameras);
                //alert("Cámaras disponibles:\n" + JSON.stringify(cameras, null, 2));

                // Filtrar solo los que parecen cámaras reales
                this.cameras = cameras;

                if (this.cameras.length === 0) {
                    console.warn("No se detectaron cámaras válidas.");
                    return;
                }

                // Buscar primero cámara frontal
                let preferredCamera = this.cameras.find(cam => {
                    const label = (cam.label || "").toLowerCase();
                    return label.includes("front") || label.includes("facing front") || label.includes("user");
                });

                // Si no hay frontal, usar la trasera (primera disponible)
                if (!preferredCamera) {
                    preferredCamera = this.cameras[0];
                }

                this.currentCameraIndex = this.cameras.indexOf(preferredCamera);

                this.scanner = new QrScanner(videoElement, result => {
                console.log('Código QR escaneado:', result);


                if (this.isProcessing) {
                    console.log('Lectura bloqueada. Procesando petición anterior.');
                    return;
                }

                this.qrResult = result;
                if (this.qrResult) {
                    this.isProcessing = true;
                    this.turnOffFlash();
                    this.stopScanner();
                    this.sendQRResult();
                    playSoundScanner();
                }
            },{
                preferredCamera: preferredCamera.id,
                highlightScanRegion: true,
                highlightCodeOutline: true
            });

             this.scanner.start(preferredCamera.id).then(() => {
                    this.cameraOn = true;
                    console.log('Scanner iniciado con:', preferredCamera.label);
                });
                 }).catch(err => {
                console.error('Error listando cámaras:', err);
            });
        },

        switchCamera() {
            if (this.cameras.length <= 1) return; // seguridad

            this.currentCameraIndex = (this.currentCameraIndex + 1) % this.cameras.length;
            const nextCamera = this.cameras[this.currentCameraIndex];

            this.scanner.setCamera(nextCamera.id).then(() => {
                console.log("Cambiado a cámara:", nextCamera.label);
            }).catch(err => {
                console.error("Error al cambiar cámara:", err);
            });
        },

        startScanner() {
            if (!this.scanner) {
                this.setupScanner();
            } else {
                this.scanner.start();
                this.cameraOn = true;
            }
        },

        stopScanner() {
            if (this.scanner) {
                this.scanner.stop();
                // Liberar el Stream
                const videoEl = this.$refs.videoElement;
                const stream = videoEl?.srcObject;
                if (stream) {
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop()); // apaga cámara/micrófono
                    videoEl.srcObject = null;
                }

                this.cameraOn = false;
                this.flashOn = false;
            }
            this.cameraOn = false;
            console.log('Apagado', this.scanner);
        },

        toggleFlash() {
            if (this.flashOn) {
                this.turnOffFlash();
            } else {
                this.turnOnFlash();
            }
        },

        async turnOnFlash() {
            this.checkFlashSupport();
            if (!this.flashSupported) {
                console.warn('Flash no compatible con este dispositivo');
                alert('Error al encender el flash');
                return;
            }

            try {
                const track = this.scanner.$video.srcObject.getVideoTracks()[0];
                await track.applyConstraints({
                    advanced: [{ torch: true }]
                });
                this.flashOn = true;
            } catch (error) {
                console.error('Error al encender el flash:', error);
                alert('Error al encender el flash');
            }
        },

        async turnOffFlash() {
            if (!this.flashSupported) {
                return;
            }

            try {
                const track = this.scanner.$video.srcObject.getVideoTracks()[0];
                await track.applyConstraints({
                        advanced: [{ torch: false }]
                });
                this.flashOn = false;
            } catch (error) {
                console.error('Error al apagar el flash:', error);
            }
        },

        async checkFlashSupport() {
            const track = this.scanner.$video.srcObject.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            this.flashSupported = !!capabilities.torch;
        },

        sendQRResult() {
            if (!this.qrResult) {
                console.warn('Error al leer el qr intentelo nuevamente');
                warningMessage('Error al leer el qr intentelo nuevamente');
                this.isProcessing = false;
                return;
            }

            let vm = this;
            axios.post(`${appApiUrl}/validateqr`, { qrCode: this.qrResult.data })
            .then(function (response) {
                hidePreloader();
                let result = response.data;
                let messageResult = 'EL QR ES VALIDO';
                let matriculaText = '';
                let mensajeExitoso = '';
                if(result.message && result.message.trim() !== '') messageResult = result.message;

                if(result.dataaditional && result.dataaditional.ismatricula == 1){
                    matriculaText = `<h5 class=\"text-dark mt-2 name-qr-response\">Matriculado</h5>`;
                }

                if(result.dataaditional && result.dataaditional.mensaje_exito){
                    mensajeExitoso = `<div class=\"cont-alert-main\">${result.dataaditional.mensaje_exito}</div>`;
                }

                let infodatahtml = '';
                if (result.dataaditional && result.dataaditional.config) {
                    const config = result.dataaditional.config;
                    let textColor = '#000';
                    if (config.color && config.color.startsWith('#')) {
                        const color = config.color.replace('#', '');
                        if (color.length === 3 || color.length === 6) {
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
                            textColor = luminance > 0.5 ? '#000' : '#fff';
                        }
                    } else if (config.color === 'white') {
                        textColor = '#000';
                    } else {
                        textColor = '#fff';
                    }
                    const borderColor = config.color === 'white' ? '#ddd' : config.color;
                    infodatahtml = `<h4 class=\"name-qr-response anio-qr-response\" style=\"
                                        text-transform: uppercase;
                                        background-color: ${config.color};
                                        padding: 10px;
                                        margin-top: 1em;
                                        color: ${textColor};
                                        border: 2px solid ${borderColor};
                                        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);\">
                                        ${config.description}
                                    </h4>`;
                }

                if (result.status) {
                    // Solo mostrar el checkbox de autorización si NO es lectura de stand
                    let autorizacionHtml = '';
                    if (!result.dataaditional?.isStand) {
                        autorizacionHtml = `
                            <div class=\"mt-3\">
                                <div class=\"form-check d-flex justify-content-center\">
                                    <input class=\"form-check-input\" type=\"checkbox\" name=\"autorizo_uso_imagen\" id=\"autorizo_si\" value=\"1\" checked>
                                    <label class=\"form-check-label ml-2\" for=\"autorizo_si\" style=\"font-size: 0.95em; margin-left: 0.5em;\">
                                        Al ingresar autorizo el uso de mi imagen y/o mi hijo para fines promocionales de la UNW.
                                    </label>
                                </div>
                            </div>
                        `;
                    }
                    const swalOptions = {
                        title: messageResult,
                        html: `
                            ${mensajeExitoso}
                            Asistente: <br>
                            <h3 class=\"name-qr-response mt-1 mb-0 text-primary\">${result.dataaditional.nombre}</h3>
                            <p class=\"email-qr-response mb-1\">${result.dataaditional.correo}</p>
                            ${matriculaText}
                            ${infodatahtml}
                            ${autorizacionHtml}
                        `,
                        showCancelButton: false,
                        confirmButtonText: result.dataaditional?.isStand ? 'Cerrar' : 'Guardar respuesta',
                        allowOutsideClick: false
                    };
                    if (!result.dataaditional?.isStand) {
                        swalOptions.preConfirm = () => {
                            let valor = '1';
                            const check = document.getElementById('autorizo_si');
                            if (check) {
                                valor = check.checked ? '1' : '0';
                            }
                            if (!result.dataaditional.idcliente) {
                                alert('Error: idcliente vacío. No se puede guardar la respuesta.');
                                return Promise.reject('idcliente vacío');
                            }
                            return axios.post(`${appApiUrl}/cliente/update-autorizacion-imagen`, {
                                idcliente: result.dataaditional.idcliente,
                                autorizo_uso_imagen: valor
                            })
                            .then(function (resp) {
                                vm.startScanner();
                                successMessage('Respuesta guardada', appName);
                            })
                            .catch(function () {
                                errorMessage('No se pudo guardar la respuesta', appName);
                            });
                        };
                    } else {
                        swalOptions.preConfirm = () => {
                            vm.startScanner();
                            return Promise.resolve();
                        };
                    }
                    Swal.fire(swalOptions);
                    vm.users = result.dataaditional;
                }  else if (result.warning){
                    swalAlertWarningQR(`<div class=\"cont-alert-main\">${result.message}</div>`, appName)
                    .then(function(optionSelected){
                        if(optionSelected.value){
                            vm.startScanner();
                        }
                    });
                } else{
                    errorMessage(result.message, appName);
                    vm.startScanner();
                }
            })
            .catch(function (error) {
                hidePreloader();
                errorMessage(appErrorMessage, appName);
                console.log(error);
            })
            .finally(function () {
                vm.isProcessing = false;
            });
        },

        onSearch: debounce(function (search) {
            if (search.length < 3) {
                return;
            }
            this.loading = true;
            this.fetchClients(search);
        }, 500),

        fetchClients(search) {
            axios.get(`${appApiUrl}/cliente/selectsearch`, { params: { search } })
                .then(response => {
                    this.clientes = response.data;
                    this.loading = false;
                })
                .catch(error => {
                console.error(error);
                this.loading = false;
            });
        },
        onSelectClient(client) {
            this.selectedClient = client;
        },
        markAttendance() {
            console.log(this.selectedClient);
            if (!this.selectedClient) {
                console.warn('No selecciono un asistente');
                warningMessage('Debe Seleccionar un asistente de los resultados para registrar su asistencia.');
                return;
            }

            let vm = this;
            let clientData = {
                idcliente : this.selectedClient.idcliente,
                idcampania : this.selectedClient.idcampania,
                email : this.selectedClient.email,
            };

            axios.post(`${appApiUrl}/markattendanceentry`, clientData)
            .then(function (response) {

                let result = response.data;
                console.log('respuesta: ',result);

                if (result.status) {
                    vm.selectedClient = result.dataaditional;
                    vm.replaceClientInArray(vm.selectedClient);
                    successMessage(result.message, appName);
                } else if(result.warning) {
                    warningMessage(result.message, appName);
                }else{
                    errorMessage(result.message, appName);
                }
            })
            .catch(function (error) {
                errorMessage(appErrorMessage, appName);
                console.log(error);
            });
        },

        replaceClientInArray(updatedClient) {
            let index = this.clientes.findIndex(c => c.idcliente === updatedClient.idcliente);
            if (index !== -1) {
                this.clientes.splice(index, 1, updatedClient);
            }
        }
    },

    beforeDestroy() {
        // Detener el escáner y limpiar recursos cuando el componente se destruye
        if (this.scanner) {
            this.scanner.stop();
            this.scanner = null;
            this.cameraOn = false;
            this.flashOn= false;
        }
    },
    components:{
        MainContent,
        PaginationLinks,
        RowActions,
        vSelect,
    }
}
</script>

<style scoped>
  .qr-scanner-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    /* height: 100vh; */
  }


  #video-container.example-style-2 {
    position: relative;
    width: max-content;
    height: max-content;
    overflow: hidden;
  }
  #video-container.example-style-2 .scan-region-highlight {
    border-radius: 30px;
    outline: rgba(0, 0, 0, .25) solid 50vmax;
  }
  #video-container.example-style-2 .scan-region-highlight-svg {
    display: none;
  }
  #video-container.example-style-2 .code-outline-highlight {
    stroke: rgba(255, 255, 255, .5) !important;
    stroke-width: 15 !important;
    stroke-dasharray: none !important;
  }

  .cont-btn-actions-qr{
    width: max-content;
    position: absolute;
    z-index: 5;
    right: 1em;
    border-radius: 12px;
    top: 0px;
  }

  .btn-off,.btn-flash{
    height: 65px;
    width: 65px;
    padding: 8px !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px #ffffff solid;
    background-color: #e2e6e9b6;
    margin-bottom: 8px;
  }
  .btn-off .off-text-btn , .btn-flash .flash-text-btn{
    font-size: 0.7em;
    font-weight: 600;
    margin-top: 2px;
  }
  .title-search-qr{
    font-weight: 600;
  }

  .info-cont-qr-search{
    background-color: #f8f8fa;
    border-radius: 1em;
    border: 1px solid #f8f9fa;
  }
  .info-cont-qr-search .card{
    background-color: transparent !important;
    margin-bottom: 10px;
  }
  .text-info-dark{
    color: #1D428A;
  }

  .cont-details-info .card-body{
    padding: 5px 0.8rem !important;
  }
  .evento-item {
    flex: 1 1 30%; /* Ajusta el tamaño según tus necesidades */
    margin: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
}
  </style>
  <style>
 .select-vue-customers .vs__dropdown-toggle .vs__selected-options .vs__search{
    line-height: 2 !important;
  }
  </style>
