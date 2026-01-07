<template>
    <form-configuracion-qr-color
        cardTitle="Editar Configuración de Color QR"
        :configuracion="configuracion"
        @saveData="saveData">
    </form-configuracion-qr-color>
</template>

<script>
import FormConfiguracionQrColor from './_FormConfiguracionQrColor';

export default {
    data() {
        return {
            configuracion: {}
        }
    },
    created() {
        this.configuracion.idconfig = this.$route.params.id;

        if (isNaN(this.configuracion.idconfig)) {
            this.$router.push({
                name: 'spa.configuracionqrcolor'
            });
        }

        this.obtenerConfiguracion(this.configuracion.idconfig);
    },
    methods: {
        obtenerConfiguracion(id) {
            showPreloader();

            let vm = this;
            axios.get(`${appApiUrl}/configuracionqrcolor/${id}/edit`)
                .then(function (response) {
                    hidePreloader();

                    if (response.data == null || response.data == '') {
                        warningMessage(`No se encontró ninguna configuración con el código ${id}`, appName);
                        vm.$router.push({
                            name: 'spa.configuracionqrcolor'
                        });
                    }

                    vm.configuracion = response.data;
                })
                .catch(function (error) {
                    hidePreloader();
                    errorMessage(appErrorMessage, appName);
                    vm.$router.push({
                        name: 'spa.configuracionqrcolor'
                    });
                    console.log(error);
                });
        },
        saveData(formData) {
            showPreloader();

            let vm = this;
            axios.put(`${appApiUrl}/configuracionqrcolor/${this.configuracion.idconfig}`, formData)
                .then(function (response) {
                    hidePreloader();

                    let result = response.data;
                    if (result.status) {
                        successMessage(result.message, appName);
                        vm.$router.push({
                            name: 'spa.configuracionqrcolor'
                        });
                    } else {
                        errorMessage(result.message, appName);
                    }
                })
                .catch(function (error) {
                    hidePreloader();
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                });
        }
    },
    components: {
        'form-configuracion-qr-color': FormConfiguracionQrColor
    }
}
</script>

