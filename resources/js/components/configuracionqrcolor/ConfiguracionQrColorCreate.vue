<template>
    <form-configuracion-qr-color
        cardTitle="Nueva Configuración de Color QR"
        :configuracion="configuracion"
        @saveData="saveData">
    </form-configuracion-qr-color>
</template>

<script>
import FormConfiguracionQrColor from './_FormConfiguracionQrColor';

export default {
    data() {
        return {
            configuracion: {
                anio_egreso: null,
                es_beca18: 0,
                color: '#000000',
                descripcion: '',
                orden: 999,
                estado: 'A'
            }
        }
    },
    methods: {
        saveData(formData) {
            showPreloader();

            let vm = this;
            axios.post(`${appApiUrl}/configuracionqrcolor`, formData)
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
