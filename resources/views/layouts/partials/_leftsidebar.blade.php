<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-pro"> 
                    <a class="has-arrow waves-effect waves-dark d-flex no-block" href="javascript:void(0)" aria-expanded="false">
                        <img src="{{asset('images/users/1.jpg')}}" alt="user-img" class="img-circle">
                        <span class="hide-menu text-truncate ">{{ Auth::user()->name }}</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <router-link :to="{name: 'spa'}"><i class="ti-user"></i> Perfil</router-link>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form-menu').submit();">
                                <i class="fa fa-power-off"></i> {{ __('Logout') }}
                            </a>
                            <!-- text-->
                            <form id="logout-form-menu" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
                @if (Auth::user()->idrol == Rule::PERSONAL_ATTENDANCE_ACCESS || Auth::user()->idrol == Rule::PERSONAL_STAND_ACCESS || in_array(Auth::user()->idrol, Rule::ADMINISTRATORS_ACCESS))
                <div class="dropdown-divider"></div>
                <li>
                    <router-link class="waves-effect waves-dark" :to="{ name: 'spa.lectorqr'}" aria-expanded="false">
                        <i class="fas fa-qrcode"></i>
                        <span class="hide-menu">Lector QR</span>
                    </router-link>
                </li>
                <div class="dropdown-divider"></div>
                @endif
                
                @if (Auth::user()->idrol == Rule::SYSTEM_ADMIN_ROLE && Auth::user()->idcuenta == Rule::SYSTEM_COMPANY_ACCOUNT)
                <li>
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="ti-panel"></i>
                        <span class="hide-menu">Sistema</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link :to="{name: 'spa.rol'}">Rol</router-link></li>
                        <li><router-link :to="{name: 'spa.cuenta'}">Cuentas</router-link></li>
                        <li><router-link :to="{name: 'spa.administrarcliente'}">Administrar Cliente</router-link></li>
                        <li><router-link :to="{name: 'spa.logerror'}">Log Error</router-link></li>  
                    </ul>
                </li>
                @endif
                
                @if (in_array(Auth::user()->idrol, Rule::ADMINISTRATORS_ACCESS))
                <li> 
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-user-lock"></i>
                        <span class="hide-menu">Administrador</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link :to="{name: 'spa.user'}">Usuario</router-link></li>                          
                        <li><router-link :to="{name: 'spa.campania'}">Campañas</router-link></li>
                        <li><router-link :to="{name: 'spa.evento'}">Stands</router-link></li>
                        <li><router-link :to="{name: 'spa.tipoAtencion'}">Tipo de atención</router-link></li>
                        <li><router-link :to="{name: 'spa.buttonmessage'}">Botones directos</router-link></li>
                    </ul>
                </li>
                <li><router-link :to="{name: 'spa.import'}"><i class="fas fa-file-import"></i><span class="hide-menu">Importar Registros</span></li></router-link>  
                <li> 
                    <router-link class="waves-effect waves-dark" :to="{name: 'spa.cliente'}" aria-expanded="false">
                        <i class="fas fa-user-edit"></i>
                        <span class="hide-menu">Registros</span>
                    </router-link>
                </li>
                <li>
                    <router-link class="waves-effect waves-dark" :to="{name: 'spa.reporte'}" aria-expanded="false">
                        <i class="fas fa-chart-pie"></i>
                        <span class="hide-menu">Reporte</span>
                    </router-link>
                </li>
                @endif

                @if  (in_array(Auth::user()->idrol, Rule::STANDARD_ACCESS))
                <li> 
                    <router-link class="has-arrow waves-effect waves-dark" :to="{name: 'spa.cliente'}" aria-expanded="false">
                        <i class="fas fa-user-edit"></i>
                        <span class="hide-menu">Registros</span>
                    </router-link>
                </li> 
                @endif    
                
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>