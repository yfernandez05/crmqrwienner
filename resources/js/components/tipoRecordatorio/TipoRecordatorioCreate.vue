<template>
    <form-tipoRecordatorio cardTitle="Registrar tipo de recordatorio" @saveData="saveData" :tiporecordatorio="tiporecordatorio"/>
</template>

<script>
    import FormTipoRecordatorio from './_FormTipoRecordatorio.vue';

    export default {
        data() {
            return {
                tiporecordatorio: {
                    tiporecordatorio: '',
                    backgroundColor: '#d3d3d3',
                    textColor: '#000',
                }
            };
        },
        methods: {
            saveData(event) {
                showPreloader();
                console.log('save data', event);
                let vm = this;
                axios.post(`${appApiUrl}/tipoRecordatorio`, event)
                    .then(function (response) {
                        hidePreloader();
                        let result = response.data;

                        if (result.status) {
                            successMessage(result.message, appName);

                            vm.$router.push({
                                name: 'spa.tipoRecordatorio'
                            });
                        } else
                            errorMessage(result.message, appName);
                    })
                    .catch(function (error) {
                        hidePreloader();
                        errorMessage(appErrorMessage, appName);
                        console.log(error);
                    })
            }
        },
        components: {
            FormTipoRecordatorio
        }
    }

</script>
