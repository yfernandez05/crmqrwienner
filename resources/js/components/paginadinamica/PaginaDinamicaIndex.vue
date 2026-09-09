<template>
    <main-content>
        <template v-slot:card-header-title>
            Paginas Dinamicas
        </template>

        <template v-slot:card-header-actions>
            <button class="btn btn-sm btn-info waves-effect waves-light" @click="buscarPaginas()">
                <i class="fas fa-search"></i>
                <span class="d-none d-sm-inline-block">Buscar</span>
            </button>
            <button class="btn btn-sm btn-danger waves-effect waves-light" @click="limpiarFiltros()">
                <i class="fas fa-times"></i>
                <span class="d-none d-sm-inline-block">Limpiar</span>
            </button>
        </template>

        <template v-slot:card-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-4">
                    <label>Slug</label>
                    <input type="text" class="form-control" v-model.trim="filtros.slug" @keyup.enter="buscarPaginas()" placeholder="Ej: encuesta">
                </div>
                <div class="form-group col-12 col-sm-4">
                    <label>Campana</label>
                    <select2 :options="campanias" v-model="filtros.idcampania" :selectValue="filtros.idcampania"
                        placeholder="Todas" keyProperty="idcampania" textProperty="nombrecampania" @input="buscarPaginas()">
                    </select2>
                </div>
                <div class="form-group col-12 col-sm-4">
                    <label>&nbsp;</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="incluirInactivos" v-model="filtros.incluirInactivos" @change="buscarPaginas()">
                        <label class="custom-control-label" for="incluirInactivos">Incluir inactivos</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="p-2 text-nowrap">Acciones</th>
                            <th class="p-2">ID</th>
                            <th class="p-2">Slug</th>
                            <th class="p-2">Titulo</th>
                            <th class="p-2">Descripcion</th>
                            <th class="p-2">Campana</th>
                            <th class="p-2">Token API</th>
                            <th class="p-2">Estado General</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!paginas.length">
                            <td colspan="8" class="text-center">No hay paginas dinamicas registradas</td>
                        </tr>

                        <tr v-for="item in paginas" :key="item.idpagina">
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-primary mr-1" @click="iniciarEdicion(item)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-warning mr-1" @click="toggleEstado(item)" title="Activar/Desactivar">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                <button class="btn btn-sm btn-secondary" @click="regenerarToken(item)" title="Regenerar token">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </td>
                            <td v-text="item.idpagina"></td>
                            <td v-text="item.slug"></td>
                            <td v-text="item.titulo"></td>
                            <td v-text="item.descripcion"></td>
                            <td v-text="item.campania ? item.campania.nombrecampania : '-'"></td>
                            <td class="token-col" :title="item.api_token" v-text="item.api_token"></td>
                            <td>
                                <span class="badge badge-pill py-1 px-3" :class="item.estado_general === 'A' ? 'badge-success' : 'badge-danger'"
                                    v-text="item.estado_general === 'A' ? 'ACTIVO' : 'INACTIVO'"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <pagination-links :pagination="pagination" @changePerPage="changePerPage"></pagination-links>

            <div class="modal fade" id="modalEditarPaginaDinamica" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar pagina dinamica</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-12 col-md-4">
                                    <label>Slug</label>
                                    <input type="text" class="form-control" v-model.trim="editForm.slug" disabled>
                                </div>
                                <div class="form-group col-12 col-md-8">
                                    <label>Titulo</label>
                                    <input type="text" class="form-control" v-model.trim="editForm.titulo">
                                </div>
                                <div class="form-group col-12">
                                    <label>Descripcion</label>
                                    <textarea class="form-control" rows="3" v-model.trim="editForm.descripcion"></textarea>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Campana fija</label>
                                    <select2 :options="campanias" v-model="editForm.idcampania" :selectValue="editForm.idcampania"
                                        placeholder="Seleccione una campana" keyProperty="idcampania" textProperty="nombrecampania">
                                    </select2>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" @click="guardarEdicion">Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </main-content>
</template>

<script>
import MainContent from './../../utils/MainContent';
import Select2 from './../../utils/Select2';
import PaginationLinks from './../../utils/PaginationLinks';

export default {
    name: 'PaginaDinamicaIndex',
    data() {
        return {
            paginas: [],
            campanias: [],
            filtros: {
                slug: '',
                idcampania: '',
                incluirInactivos: false,
            },
            pagination: {
                current_page: 1,
                per_page: 10,
                total: 0,
                last_page: 1,
            },
            editForm: {
                idpagina: 0,
                slug: '',
                titulo: '',
                descripcion: '',
                idcampania: '',
            },
        };
    },
    created() {
        this.listarCampanias();
        this.buscarPaginas();
    },
    methods: {
        listarCampanias() {
            let vm = this;
            axios.get(`${appApiUrl}/campania/select`)
                .then(function (response) {
                    vm.campanias = response.data || [];
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        buscarPaginas(filters = null) {
            showPreloader();
            let vm = this;

            const params = {
                page: filters?.page || this.pagination.current_page || 1,
                perpage: filters?.perpage || this.pagination.per_page || 10,
            };

            if (this.filtros.slug) {
                params.slug = this.filtros.slug;
            }

            if (this.filtros.idcampania) {
                params.idcampania = this.filtros.idcampania;
            }

            if (!this.filtros.incluirInactivos) {
                params.estado_general = 'A';
            }

            axios.get(`${appApiUrl}/paginadinamica`, { params })
                .then(function (response) {
                    vm.paginas = response.data.data || [];
                    vm.pagination = {
                        current_page: response.data.current_page,
                        per_page: response.data.per_page,
                        total: response.data.total,
                        last_page: response.data.last_page,
                        from: response.data.from,
                        to: response.data.to,
                    };
                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
                .then(() => {
                    hidePreloader();
                });
        },
        limpiarFiltros() {
            this.filtros = {
                slug: '',
                idcampania: '',
                incluirInactivos: false,
            };
            this.pagination.current_page = 1;
            this.buscarPaginas({ page: 1, perpage: this.pagination.per_page });
        },
        changePerPage(filters) {
            this.pagination.current_page = filters.page;
            this.pagination.per_page = filters.perpage;
            this.buscarPaginas(filters);
        },
        iniciarEdicion(item) {
            this.editForm = {
                idpagina: item.idpagina,
                slug: item.slug || '',
                titulo: item.titulo || '',
                descripcion: item.descripcion || '',
                idcampania: item.idcampania || '',
            };
            window.$('#modalEditarPaginaDinamica').modal('show');
        },
        guardarEdicion() {
            if (!this.editForm.idcampania) {
                warningMessage('Selecciona una campana para guardar.', appName);
                return;
            }

            showPreloader();
            let vm = this;
            axios.put(`${appApiUrl}/paginadinamica/${this.editForm.idpagina}`, {
                slug: this.editForm.slug,
                titulo: this.editForm.titulo,
                descripcion: this.editForm.descripcion,
                idcampania: this.editForm.idcampania,
            })
                .then(function (response) {
                    const result = response.data;
                    if (result.status) {
                        successMessage(result.message, appName);
                        window.$('#modalEditarPaginaDinamica').modal('hide');
                        vm.buscarPaginas();
                    } else {
                        warningMessage(result.message, appName);
                    }
                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
                .then(() => {
                    hidePreloader();
                });
        },
        toggleEstado(item) {
            let vm = this;
            showPreloader();
            axios.post(`${appApiUrl}/paginadinamica/${item.idpagina}/toggle`, {
                slug: item.slug,
                titulo: item.titulo,
                descripcion: item.descripcion,
                idcampania: item.idcampania,
            })
                .then(function (response) {
                    const result = response.data;
                    if (result.status || result.warning) {
                        successMessage(result.message, appName);
                        vm.buscarPaginas();
                    } else {
                        warningMessage(result.message, appName);
                    }
                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
                .then(() => {
                    hidePreloader();
                });
        },
        regenerarToken(item) {
            let vm = this;
            showPreloader();
            axios.post(`${appApiUrl}/paginadinamica/${item.idpagina}/regenerar-token`)
                .then(function (response) {
                    const result = response.data;
                    if (result.status) {
                        successMessage(result.message, appName);
                        vm.buscarPaginas();
                    } else {
                        warningMessage(result.message, appName);
                    }
                })
                .catch(function (error) {
                    errorMessage(appErrorMessage, appName);
                    console.log(error);
                })
                .then(() => {
                    hidePreloader();
                });
        },
    },
    components: {
        MainContent,
        Select2,
        PaginationLinks,
    },
};
</script>

<style scoped>
.token-col {
    max-width: 220px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
