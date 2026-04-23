<template>
    <form-tipoRecordatorio  cardTitle="Editar tipo de recordatorio" :tiporecordatorio="tiporecordatorio" @saveData="saveData"/>
</template>

<script>
  import FormTipoRecordatorio from './_FormTipoRecordatorio.vue';


export default {
     data() {
            return {
                tiporecordatorio: {}
            }
        },
        created() {
            this.tiporecordatorio.idtiporecordatorio = this.$route.params.id;

            if (isNaN(this.tiporecordatorio.idtiporecordatorio)) {
                this.$router.push({
                    name: 'spa.tipoRecordatorio'
                });
            }

            this.obtenerTipoAtencion(this.tiporecordatorio.idtiporecordatorio);
        },
        methods: {
            obtenerTipoAtencion(id) {
                showPreloader();

                let vm = this;
                axios.get(`${appApiUrl}/tipoRecordatorio/${id}`)
                    .then(function (response) {
                        hidePreloader();
                        if (response.data == null || response.data == '') {
                            warningMessage(`No se encontró ningúna Tipo de Recordatorio con el código ${id}`, appName);

                            vm.$router.push({
                                name: 'spa.tipoRecordatorio'
                            });
                        }
                        vm.tiporecordatorio = response.data;
                    })
                    .catch(function (error) {
                        hidePreloader();
                        errorMessage(appErrorMessage, appName);

                        vm.$router.push({
                            name: 'spa.tipoRecordatorio'
                        });
                        console.log(error);
                    })
            },
            saveData(event) {
                showPreloader();

                let vm = this;
                axios.put(`${appApiUrl}/tipoRecordatorio/${this.tiporecordatorio.idtiporecordatorio}`, event)
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

        mounted() {

        },
        components: {
            FormTipoRecordatorio
        }
}
</script>
