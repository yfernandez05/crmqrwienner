<template>
    <main-content columnClass="col-12 col-md-10 col-lg-8 col-xl-10">
        <template v-slot:card-header-title>
            <span v-text="cardTitle"></span>
        </template>

        <template v-slot:card-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('idrol')}">
                    <label>Rol <small class="text-danger">(*)</small></label>
                    <select2 :options="roles" v-model="user.idrol" :selectValue="user.idrol"
                        placeholder="Seleccione Un rol" keyProperty="idrol" textProperty="rol">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('idrol')"
                        v-text="showError('idrol').errorDetail"></small>
                </div>
                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('idcuenta')}">
                    <label>Empresa <small class="text-danger">(*)</small></label>
                    <select2 :options="cuentas" v-model="user.idcuenta" @input="getcampanias(true)" :selectValue="user.idcuenta"
                        placeholder="Seleccione una empresa" keyProperty="idcuenta" textProperty="empresa">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('idcuenta')"
                        v-text="showError('idcuenta').errorDetail"></small>
                </div>
                <div v-if="user.idrol >= 6" class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('idcampania')}">
                    <label>Campaña <small class="text-danger">(*)</small></label>
                    <select2 :disabled="!campanias.length" :options="campanias" @input="buscarEvento(true)" v-model="user.idcampania" :selectValue="user.idcampania" 
                        placeholder="Seleccione una campaña" keyProperty="idcampania" textProperty="nombrecampania">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('idcampania')"
                        v-text="showError('idcampania').errorDetail"></small>
                </div>
                <div v-if="user.idrol == 7 || user.idrol == 8" class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('idevento')}">
                    <label>Evento/Stand <small class="text-danger">(*)</small></label>
                    <select2 :disabled="!eventos.length" :options="eventos" v-model="user.idevento" :selectValue="user.idevento"
                        placeholder="Seleccione un stand" keyProperty="idevento" textProperty="nombreevento">
                    </select2>
                    <small class="form-control-feedback" v-if="errorExists('idevento')"
                        v-text="showError('idevento').errorDetail"></small>
                </div>               
                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('name')}">
                    <label>Nombres <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="user.name" />
                    <small class="form-control-feedback" v-if="errorExists('name')"
                        v-text="showError('name').errorDetail"></small>
                </div>
                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('email')}">
                    <label>Email <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="user.email" />
                    <small class="form-control-feedback" v-if="errorExists('email')"
                        v-text="showError('email').errorDetail"></small>
                </div>

                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('password')}"
                    v-if="showPasswordField">
                    <label>Password <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="user.password" />
                    <small class="form-control-feedback" v-if="errorExists('password')"
                        v-text="showError('password').errorDetail"></small>
                </div>
                <div class="form-group col-12 col-sm-4" :class="{'has-danger':errorExists('password_confirmation')}"
                    v-if="showPasswordField">
                    <label>Confirmar Password <small class="text-danger">(*)</small></label>
                    <input type="text" class="form-control" v-model="user.password_confirmation" />
                    <small class="form-control-feedback" v-if="errorExists('password_confirmation')"
                        v-text="showError('password_confirmation').errorDetail"></small>
                </div>
            </div>

            <hr class="mt-2">
        </template>

        <template v-slot:card-body-actions>
            <router-link :to="{name: 'spa.user'}" class="btn waves-effect waves-light btn-info mr-2">
                <i class="fas fa-reply"></i> <span class="button-text">Atrás</span>
            </router-link>

            <div>
                <button type="button" class="btn btn-success waves-effect waves-light" @click="doSaveData">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
                <button type="reset" class="btn waves-effect waves-light btn-outline-secondary ml-2">
                    <i class="fa fa-window-close"></i> <span class="button-text">Cancelar</span>
                </button>
            </div>
        </template>

    </main-content>
</template>

<script>
    import MainContent from './../../utils/MainContent';
    import Select2 from './../../utils/Select2';


    export default {
        props: {
            cardTitle: {
                default: 'user'
            },
            user: {
                type: Object,
                // a factory function
                default: function () {
                    return {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        idcampania:'',

                    }
                }
            },
            showPasswordField: {
                type: Boolean,
                default: true
            }

        },
        data() {
            return {
                roles: [],
                cuentas: [],
                campanias: [],
                eventos: [],
                errors: [],

            }
        },
        methods: {
            listarRoles() {
                let vm = this;
                axios.get(`${appApiUrl}/rol/select`)
                    .then(function (response) {
                        vm.roles = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            listarCuentas() {
                let vm = this;
                axios.get(`${appApiUrl}/cuenta/select`)
                    .then(function (response) {
                        vm.cuentas = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            getcampanias(newsearch) {
                if(newsearch){this.user.idcampania = '';}     
                let vm = this;
                axios.get(`${appApiUrl}/cuenta/getecampania`,{params: {idcuenta:this.user.idcuenta}})
                    .then(function (response) {

                        vm.campanias = response.data;
                                              
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            buscarEvento(newsearch){
                if(newsearch){this.user.idevento = '';}                
                this.eventos = [];

                let vm = this;
                axios.get(`${appApiUrl}/evento/getevento`, {params: {idcampania:this.user.idcampania}})
                .then(function (response) {                    
                    
                    vm.eventos = response.data;

                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
            },
            listarEventos() {
                let vm = this;
                axios.get(`${appApiUrl}/evento/select`)
                    .then(function (response) {
                        vm.eventos = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            doSaveData() {

                if (this.validateFields().length > 0) {
                    return;
                }

                let userData = {
                    name: this.user.name,
                    email: this.user.email,
                    //password:this.user.password,
                    idrol: this.user.idrol,
                    idcuenta: this.user.idcuenta,
                    idcampania: this.user.idcampania,
                    idevento: this.user.idevento
                };

                if (this.showPasswordField)
                    userData.password = this.user.password;

                this.$emit('saveData', userData);
            },
            validatedatalength(campain, stand){
                this.errors = [];

                    if (this.user.idrol >= 6) {
                        if(campain){
                            if (!campain.length) {
                                this.setError('idcampania', 'No hay campañas, primero cree una nueva');
                            }
                        }                        
                    }

                    if (this.user.idrol > 6) {
                        if(stand){
                            if (!stand.length) {
                                this.setError('idevento', 'No hay stand, primero cree una nueva');
                            }
                        }                        
                    }

                return this.errors;
            },
            validateFields() {
                this.errors = [];

                if (!this.user.idrol) {
                    this.setError('idrol', 'El campo Rol es obligatorio');
                }

                if (!this.user.idcuenta) {
                    this.setError('idcuenta', 'El campo Empresa es obligatorio');
                }

                if (this.user.idrol >= 6){
                    if (!this.user.idcampania) {
                        this.setError('idcampania', 'El campo campaña es obligatorio');
                    }
                }
                
                if(this.user.idrol > 6){
                    if (!this.user.idevento) {
                        this.setError('idevento', 'El campo stand es obligatorio');
                    }
                }

                if (!this.user.name) {
                    this.setError('name', 'El campo Nombre es obligatorio');
                }
                if (!this.user.email) {
                    this.setError('email', 'El campo Email es obligatorio');
                }


                if (this.showPasswordField) {

                    if (!this.user.password) {
                        this.setError('password', 'El campo password es obligatorio');
                    }

                    if (!this.user.password_confirmation) {
                        this.setError('password_confirmation', 'El campo confirmar password es obligatorio');
                    }

                    if (this.user.password != this.user.password_confirmation) {
                        this.setError('password_confirmation', 'Los passwords no coinciden');
                    }
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
        },
        mounted() {
            this.listarRoles();
            this.listarCuentas();
            this.listarEventos();
        },
        watch: {
            user: {
                immediate: true, // Ejecutar al montarse el componente
                handler(newUser) {
                    if (newUser.idcampania) {
                        this.getcampanias();  // Cargar las campañas
                        this.buscarEvento();  // Cargar los eventos basados en la campaña seleccionada
                    }
                }
            }
        },
        components: {
            MainContent,
            Select2,

        }
    }

</script>
