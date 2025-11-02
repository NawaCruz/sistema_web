<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    {{-- <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider">

    {{-- <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div> --}}

    {{-- <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Components</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Components:</h6>
                <a class="collapse-item" href="buttons.html">Buttons</a>
                <a class="collapse-item" href="cards.html">Cards</a>
            </div>
        </div>
    </li> --}}

    {{-- <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Utilities</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="utilities-color.html">Colors</a>
                <a class="collapse-item" href="utilities-border.html">Borders</a>
                <a class="collapse-item" href="utilities-animation.html">Animations</a>
                <a class="collapse-item" href="utilities-other.html">Other</a>
            </div>
        </div>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider">

    {{-- <!-- Heading -->
    <div class="sidebar-heading">
        Addons
    </div> --}}

    <!-- Nav Item - graficos -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Gráficos</span>
        </a>
    </li>

    <!-- Nav Item - Productos -->
    <li class="nav-item">
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link" href="{{ route('admin.productos.index') }}">
                <i class="fas fa-boxes"></i>
                <span>Productos</span>
            </a>
        @elseif (auth()->check() && auth()->user()->role === 'empleado')
            <a class="nav-link" href="{{ route('empleado.productos.index') }}">
                <i class="fas fa-boxes"></i>
                <span>Productos</span>
            </a>
        @endif
    </li>

    <!-- Nav Item - proveedores -->
    <li class="nav-item">
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link" href="{{ route('admin.proveedores.index') }}">
                <i class="fas fa-truck"></i>
                <span>Proveedores</span>
            </a>
        @elseif (auth()->check() && auth()->user()->role === 'empleado')
            <a class="nav-link" href="{{ route('empleado.proveedores.index') }}">
                <i class="fas fa-truck"></i>
                <span>Proveedores</span>
            </a>
        @endif
    </li>

    <!-- Nav Item - Compras -->
    <li class="nav-item">
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link" href="{{ route('admin.compras.index') }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Compras</span>
            </a>
        @elseif (auth()->check() && auth()->user()->role === 'empleado')
            <a class="nav-link" href="{{ route('empleado.compras.index') }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Compras</span>
            </a>
        @endif
    </li>

    <!-- Nav Item - Ventas (Bootstrap 4 / SBAdmin2) -->
    <li class="nav-item">
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVentas"
            aria-expanded="false" aria-controls="collapseVentas">
                <i class="fas fa-cash-register"></i>
                <span>Ventas</span>
            </a>

            <div id="collapseVentas" class="collapse" aria-labelledby="headingVentas" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('admin.ventas.index') }}">
                        <i class="fas fa-list me-2"></i> Gestión de Ventas
                    </a>
                    <a class="collapse-item" href="{{ route('admin.detalleVentas.index') }}">
                        <i class="fas fa-file-invoice me-2"></i> Detalle de Ventas
                    </a>
                </div>
            </div>
        @elseif (auth()->check() && auth()->user()->role === 'empleado')
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVentas"
            aria-expanded="false" aria-controls="collapseVentas">
                <i class="fas fa-cash-register"></i>
                <span>Ventas</span>
            </a>

            <div id="collapseVentas" class="collapse" aria-labelledby="headingVentas" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('empleado.ventas.index') }}">
                        <i class="fas fa-list me-2"></i> Gestión de Ventas
                    </a>
                    <a class="collapse-item" href="{{ route('empleado.detalleVentas.index') }}">
                        <i class="fas fa-file-invoice me-2"></i> Detalle de Ventas
                    </a>
                </div>
            </div>
        @endif
    </li>

    <!-- Nav Item - Caja -->
    <li class="nav-item">
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a class="nav-link" href="{{ route('admin.cajas.index') }}">
                <i class="fas fa-cash-register"></i>
                <span>Caja</span>
            </a>
        @elseif (auth()->check() && auth()->user()->role === 'empleado')
            <a class="nav-link" href="{{ route('empleado.cajas.index') }}">
                <i class="fas fa-cash-register"></i>
                <span>Caja</span>
            </a>
        @endif
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>