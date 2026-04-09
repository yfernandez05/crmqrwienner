<template>
    <main-content>
        <template v-slot:card-header-title>
            Configuración de Colores QR
        </template>
        <template v-slot:card-header-actions>
            <button class="btn btn-sm btn-info waves-effect waves-light" @click="buscarConfiguracion()">
                <i class="fas fa-search"></i>
                <span class="d-none d-sm-inline-block">
                    Buscar
                </span>
            </button>
            <button class="btn btn-sm btn-danger waves-effect waves-light" @click="limpiarFiltros()">
                <i class="fas fa-times"></i>
                <span class="d-none d-sm-inline-block">
                    Limpiar
                </span>
            </button>
            <router-link :to="{name:'spa.configuracionqrcolor.registrar'}" class="btn btn-sm btn-success waves-effect waves-light">
                 <i class="fas fa-plus"></i>
                <span class="d-none d-sm-inline-block">
                    Nuevo
                </span>
            </router-link>
        </template>
        <template v-slot:card-body-main>
            <div class="form-row">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>Grado</label>
                    <input type="text" class="form-control" v-model="filtros.grado"
                           @keyup.enter="buscarConfiguracion()" placeholder="Ej: 2026">
                </div>
                <!-- Eliminado filtro ¿Es Beca18? -->
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <label>&nbsp;</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="estado"
                            v-model="filtros.incluirInactivos" @change="buscarConfiguracion()">
                        <label class="custom-control-label" for="estado">Incluir Inactivos</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="p-2">Acciones</th>
                            <th class="p-2">Cod</th>
                            <th class="p-2">Orden</th>
                            <th class="p-2 text-nowrap">Grado</th>
                            <!-- Eliminada columna Beca18 -->
                            <th class="p-2">Color</th>
                            <th class="p-2">Descripción</th>
                            <th class="p-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!configuraciones || configuraciones.length === 0">
                            <td colspan="8" class="text-center">No hay configuraciones registradas</td>
                        </tr>
                        <tr v-for="config in configuraciones" :key="config.idconfig">
                            <td>
                                <row-actions :rowData="config" @rowItemActions="rowItemActions">
                                </row-actions>
                            </td>
                            <td v-text="config.idconfig"></td>
                            <td v-text="config.orden"></td>
                            <td v-text="config.grado || 'Por defecto'"></td>
                            <!-- Eliminada celda Beca18 -->
                            <td>
                                <div :style="`background-color: ${config.color}; color: ${getTextColor(config.color)}; padding: 8px 15px; border-radius: 4px; border: 1px solid #ddd; font-weight: 500; display: inline-block;`">
                                    {{ config.color }}
                                </div>
                            </td>
                            <td v-text="config.descripcion"></td>
                            <td>
                                <span class="badge badge-pill py-1 px-3"
                                    :class="config.estado === 'A' ? 'badge-success':'badge-danger'"
                                     v-text="config.estado === 'A' ? 'ACTIVO' : 'INACTIVO'">
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination-links :pagination="pagination" @changePerPage="changePerPage">
            </pagination-links>
        </template>
    </main-content>
</template>

<script>
    import MainContent from './../../utils/MainContent';
    import RowActions from './../../utils/RowActions';
    import PaginationLinks from './../../utils/PaginationLinks';

    export default {
        data() {
            return {
                configuraciones: [],
                filtros: {
                    grado: '',
                    incluirInactivos: false
                },
                pagination: {
                    current_page: 1,
                    per_page: 10,
                    total: 0,
                    last_page: 1
                }
            }
        },
        created() {
            this.buscarConfiguracion();
        },
        methods: {
            buscarConfiguracion(page = 1) {
                showPreloader();

                let vm = this;
                let params = {
                    page: page,
                    perpage: this.pagination.per_page
                };

                if (this.filtros.grado) {
                    params.grado = this.filtros.grado;
                }
                // Eliminada lógica de filtro es_beca18
                if (!this.filtros.incluirInactivos) {
                    params.estado = 'A';
                }

                axios.get(`${appApiUrl}/configuracionqrcolor`, { params: params })
                    .then(function (response) {
                        hidePreloader();
                        vm.configuraciones = response.data.data;
                        vm.pagination = {
                            current_page: response.data.current_page,
                            per_page: response.data.per_page,
                            total: response.data.total,
                            last_page: response.data.last_page
                        };
                    })
                    .catch(function (error) {
                        hidePreloader();
                        errorMessage(appErrorMessage, appName);
                        console.log(error);
                    });
            },
            limpiarFiltros() {
                this.filtros = {
                    grado: '',
                    incluirInactivos: false
                };
                this.buscarConfiguracion();
            },
            changePerPage(perPage) {
                this.pagination.per_page = perPage;
                this.buscarConfiguracion(1);
            },
            rowItemActions(event) {
                switch (event.action) {
                    case 'edit':
                        this.$router.push({
                            name: 'spa.configuracionqrcolor.editar',
                            params: { id: event.data.idconfig }
                        });
                        break;
                    case 'delete':
                        this.deleteConfig(event.data.idconfig);
                        break;
                }
            },
            deleteConfig(id) {
                let vm = this;

                swal({
                    title: "¿Está seguro?",
                    text: "Esta configuración se marcará como inactiva",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancelar",
                            value: null,
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Sí, desactivar",
                            value: true,
                            visible: true,
                            className: "bg-danger",
                            closeModal: true
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        showPreloader();

                        axios.delete(`${appApiUrl}/configuracionqrcolor/${id}`)
                            .then(function (response) {
                                hidePreloader();

                                let result = response.data;
                                if (result.status) {
                                    successMessage(result.message, appName);
                                    vm.buscarConfiguracion(vm.pagination.current_page);
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
                });
            },
            getTextColor(bgColor) {
                if (!bgColor) return '#000';

                let color = bgColor.replace('#', '');

                if (color.length !== 3 && color.length !== 6) {
                    return '#000';
                }

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

                return luminance > 0.5 ? '#000' : '#fff';
            }
        },
        components: {
            MainContent,
            RowActions,
            PaginationLinks
        }
    }
</script>

