@extends('layouts.app')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">Gestión de Ventas</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="card shadow-sm mb-3 border-left-primary">
        <div class="card-body py-2">
            <form method="GET" action="{{ route(auth()->user()->role . '.detalleVentas.index') }}" id="formFiltros" class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label class="mb-1">Buscar</label>
                    <input type="text" name="q" class="form-control form-control-sm"
                           placeholder="Venta ID, código, descripción, cliente, usuario..."
                           value="{{ request('q') }}">
                </div>
                <div class="form-group col-md-2">
                    <label class="mb-1">Desde</label>
                    <input type="date" name="from" class="form-control form-control-sm"
                           value="{{ request('from') }}">
                </div>
                <div class="form-group col-md-2">
                    <label class="mb-1">Hasta</label>
                    <input type="date" name="to" class="form-control form-control-sm"
                           value="{{ request('to') }}">
                </div>
                <div class="form-group col-md-2">
                    <label class="mb-1">Método de pago</label>
                    <select name="metodo" class="form-control form-control-sm">
                        <option value="">— Todos —</option>
                        @foreach($metodosPago ?? [] as $mp)
                            <option value="{{ $mp->id }}" {{ request('metodo') == $mp->id ? 'selected' : '' }}>
                                {{ $mp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 d-flex justify-content-between">
                    {{-- Grupo Filtrar/Limpiar --}}
                    <div>
                        <button class="btn btn-sm btn-primary mr-2" type="submit">
                            <i class="fas fa-search mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route(auth()->user()->role . '.detalleVentas.index') }}" class="btn btn-sm btn-light border">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </a>
                    </div>

                    {{-- Botón añadir Venta --}}
                    <button class="btn btn-sm btn-success shadow-sm" type="button"
                            data-toggle="modal" data-target="#modalCrearVenta">
                        <i class="fas fa-plus mr-1"></i> Añadir Venta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <datalist id="listaProductos">
        @foreach($productos as $producto)
            <option
                value="{{ ($producto->codigo ? $producto->codigo.' - ' : '') . $producto->descripcion }}"
                data-id="{{ $producto->id }}"
                data-precio="{{ number_format($producto->precio_venta ?? 0, 2, '.', '') }}"
                data-stock="{{ (int)($producto->stock ?? 0) }}">
            </option>
        @endforeach
    </datalist>

    <!-- Tabla -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Ventas</h6>
        </div>
        <div class="card-body">
            @if($ventas->isEmpty())
                <p class="text-center text-muted">No hay ventas registradas.</p>
            @else
                {{-- Tabla de Ventas --}}
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        @if($ventas->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox mr-2"></i> No hay ventas registradas.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                        <th class="px-2 py-2 text-center" style="width:50px;">#</th>
                                        <th class="px-2 py-2" style="width:200px;">Cliente</th>
                                        <th class="px-2 py-2 text-center" style="width:100px;">DNI</th>
                                        <th class="px-2 py-2" style="width:150px;">Usuario</th>
                                        <th class="px-2 py-2" style="width:150px;">Método de Pago</th>
                                        <th class="px-2 py-2 text-right" style="width:120px;">Total (S/)</th>
                                        <th class="px-2 py-2 text-center" style="width:150px;">Fecha</th>
                                        <th class="px-2 py-2 text-center" style="width:130px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ventas as $venta)
                                        <tr>
                                            <td class="px-2 py-2 text-center">{{ $venta->id }}</td>
                                            <td class="px-2 py-2">{{ $venta->cliente->nombre ?? 'Público general' }}</td>
                                            <td class="px-2 py-2 text-center">{{ $venta->cliente->dni ?? '-' }}</td>
                                            <td class="px-2 py-2">{{ $venta->user->name ?? '-' }}</td>
                                            <td class="px-2 py-2">{{ $venta->metodoPago->nombre ?? '-' }}</td>
                                            <td class="px-2 py-2 text-right font-weight-bold">
                                            S/ {{ number_format($venta->total, 2) }}
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                            {{ $venta->created_at->format('d/m/Y h:i A') }}
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="acciones">
                                                {{-- Ver Detalles --}}
                                                <a href="{{ route(auth()->user()->role . '.detalleVentas.index') }}?id_venta={{ $venta->id }}"
                                                class="btn btn-info" title="Ver detalles de venta">
                                                <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- Editar --}}
                                                <button type="button" class="btn btn-warning"
                                                        data-toggle="modal"
                                                        data-target="#modalEditarVenta{{ $venta->id }}"
                                                        title="Editar venta">
                                                <i class="fas fa-edit"></i>
                                                </button>
                                                {{-- Eliminar --}}
                                                <button type="button"
                                                        class="btn btn-danger btnEliminarVenta"
                                                        data-url="{{ route(auth()->user()->role . '.ventas.destroy', $venta->id) }}"
                                                        data-id="{{ $venta->id }}"
                                                        title="Eliminar venta">
                                                <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-3">
                    {{ $ventas->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: Crear Venta (BS4) -->
    <div class="modal fade" id="modalCrearVenta" tabindex="-1" role="dialog" aria-labelledby="modalCrearVentaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCrearVentaLabel">
                        <i class="fas fa-cash-register mr-2"></i> Registrar nueva venta
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formCrearVenta" method="POST" action="{{ route(auth()->user()->role . '.ventas.store') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <div class="modal-body">

                        {{-- CABECERA: Datos del cliente --}}
                        <div class="card shadow-sm mb-3 border-left-primary">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user mr-2"></i>Datos del cliente
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-row">

                                    {{-- Cliente con datalist --}}
                                    <div class="form-group col-md-6">
                                        <label for="cliente_busqueda" class="mb-1">Cliente <span class="text-danger">*</span></label>
                                        <input list="clientes" class="form-control" id="cliente_busqueda" name="cliente_busqueda" placeholder="Escribe DNI o nombre..." autocomplete="off" required value="">
                                        <input type="hidden" id="cliente_id" name="cliente_id" value="">
                                        <datalist id="clientes">
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->dni }} — {{ $cliente->nombre }} {{ $cliente->apellido }}" data-id="{{ $cliente->id }}"></option>
                                            @endforeach
                                        </datalist>
                                        <small class="text-danger d-block" id="error_cliente"></small>
                                    </div>

                                    {{-- Método de pago --}}
                                    <div class="form-group col-md-3">
                                        <label for="metodo_pago_id" class="mb-1">Método de pago <span class="text-danger">*</span></label>
                                        <select class="form-control" id="metodo_pago_id" name="metodo_pago_id" required>
                                            <option value="">— Seleccione —</option>
                                            @foreach($metodosPago as $metodoPago)
                                                <option value="{{ $metodoPago->id }}">{{ $metodoPago->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger d-block" id="error-metodo_pago_id"></small>
                                    </div>

                                    {{-- Vendedor --}}
                                    <div class="form-group col-md-3">
                                        <label class="mb-1">Vendedor</label>
                                        <input type="text" class="form-control bg-light" value="{{ auth()->user()->name ?? '—' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CAPTURA: Agregar producto -->
                        <div class="card shadow-sm mb-3 border-left-success">
                            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-box-open mr-2"></i>Agregar producto
                                </h6>
                                <span class="text-muted small">Usa esta fila para añadir ítems</span>
                            </div>

                            <div class="card-body">
                                <div class="form-row align-items-end">

                                    <div class="form-group col-lg-6">
                                        <label class="mb-1" for="producto_busqueda">Producto</label>
                                        <div class="input-group">
                                            <input type="text" id="producto_busqueda" class="form-control" list="listaProductos"
                                                placeholder="Escribe código o nombre…" autocomplete="off">
                                            <input type="hidden" id="producto_id">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Stock: <span id="producto_stock_lbl" class="ml-1">—</span></span>
                                                <span class="input-group-text">S/ <span id="producto_precio_lbl" class="ml-1">0.00</span></span>
                                            </div>
                                        </div>
                                        <small class="text-danger d-block" id="error_producto"></small>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-4">
                                        <label class="mb-1" for="producto_cantidad">Cantidad</label>
                                        <input type="number" min="1" step="1" id="producto_cantidad" class="form-control" value="1">
                                        <small class="text-danger d-block" id="error_cantidad"></small>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-4">
                                        <label class="mb-1" for="producto_descuento">Descuento (S/)</label>
                                        <input type="number" min="0" step="0.1" id="producto_descuento" class="form-control" value="0.00">
                                    </div>

                                    <div class="form-group col-lg-1 col-md-4">
                                        <label class="mb-1 d-block">Subtotal.</label>
                                        <input type="text" id="producto_subtotal" class="form-control bg-light" value="0.00" readonly>
                                    </div>

                                    <div class="form-group col-lg-1 col-md-12 text-right">
                                        <button type="button" class="btn btn-success" id="producto_btn_agregar" title="Agregar a la lista">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DETALLE (editable: cantidad y descuento) --}}
                        <div class="card shadow-sm">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list mr-2"></i>Detalle de venta
                                </h6>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover align-middle mb-0" id="tabla_detalle_venta">
                                        <thead class="thead-light">
                                        <tr>
                                            <th style="width: 35%">Producto</th>
                                            <th style="width: 9%"  class="text-center">Stock</th>
                                            <th style="width: 10%" class="text-center">Cantidad</th>
                                            <th style="width: 12%" class="text-right">Precio Unit. (S/)</th>
                                            <th style="width: 12%" class="text-right">Desc. (S/)</th>
                                            <th style="width: 12%" class="text-right">Subtotal (S/)</th>
                                            <th style="width: 10%" class="text-center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                        <tr class="bg-light font-weight-bold">
                                            <td colspan="5" class="text-right pr-3">Total (S/)</td>
                                            <td class="text-right pr-3" id="total_venta_lbl">0.00</td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="p-3 text-right">
                                <input type="hidden" name="total" id="CapturarTotalGeneral" value="0.00">
                                <button type="submit" class="btn btn-primary" id="btnGuardarVenta">
                                    <i class="fas fa-save mr-1"></i> Guardar venta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <datalist id="clientesEditar">
        @foreach($clientes as $cliente)
            <option value="{{ $cliente->dni }} — {{ $cliente->nombre }} {{ $cliente->apellido }}" data-id="{{ $cliente->id }}"></option>
        @endforeach
    </datalist>

    <!-- MODAL: Editar Venta (BS4) -->
    @foreach ($ventas as $venta)
        <div class="modal fade" id="modalEditarVenta{{ $venta->id }}" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="tituloEditarVenta{{ $venta->id }}">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded">

                    <!-- HEADER -->
                    <div class="modal-header bg-warning text-white border-0">
                        <h5 class="modal-title d-flex align-items-center" id="tituloEditarVenta{{ $venta->id }}">
                            <i class="fas fa-edit mr-2"></i> Editar venta #{{ $venta->id }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="formEditarVenta{{ $venta->id }}" method="POST" action="{{ route(auth()->user()->role . '.ventas.update', $venta->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            {{-- CABECERA: Datos del cliente --}}
                            <div class="card shadow-sm mb-3 border-left-warning">
                                <div class="card-header py-2 d-flex align-items-center">
                                    <h6 class="m-0 font-weight-bold text-warning">
                                        <i class="fas fa-user-edit mr-2"></i>Datos del cliente
                                    </h6>
                                </div>
                                <div class="card-body pt-3 pb-2">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="mb-1">Cliente <span class="text-danger">*</span></label>
                                            <input list="clientesEditar" class="form-control"
                                                   id="cliente_busqueda_editar{{ $venta->id }}"
                                                   name="cliente_busqueda"
                                                   placeholder="Escribe DNI o nombre..."
                                                   autocomplete="off" required
                                                   value="{{ $venta->cliente->dni }} — {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}">
                                            <input type="hidden" id="cliente_busqueda_editar_id{{ $venta->id }}" name="cliente_id" value="{{ $venta->cliente->id }}">
                                            <small class="text-danger d-block" id="error_cliente_editar{{ $venta->id }}"></small>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="mb-1">Método de pago <span class="text-danger">*</span></label>
                                            <select class="form-control" id="metodo_pago_id_editar{{ $venta->id }}" name="metodo_pago_id" required>
                                                <option value="">— Seleccione —</option>
                                                @foreach($metodosPago as $metodoPago)
                                                    <option value="{{ $metodoPago->id }}" {{ $venta->metodo_pago_id == $metodoPago->id ? 'selected' : '' }}>
                                                        {{ $metodoPago->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger d-block" id="error-metodo_pago_id_editar{{ $venta->id }}"></small>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="mb-1">Vendedor</label>
                                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->name ?? '—' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CAPTURA: Agregar producto (igual a crear) --}}
                            <div class="card shadow-sm mb-3 border-left-success">
                                <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-success">
                                        <i class="fas fa-box-open mr-2"></i>Agregar producto
                                    </h6>
                                    <span class="text-muted small">Usa esta fila para añadir ítems</span>
                                </div>

                                <div class="card-body pt-3 pb-2">
                                    <div class="form-row align-items-end">
                                        <!-- Producto (6) -->
                                        <div class="form-group col-lg-6 col-md-12 mb-3 mb-lg-0">
                                            <label class="mb-1">Producto</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       id="producto_busqueda{{ $venta->id }}"
                                                       class="form-control"
                                                       list="listaProductos"
                                                       placeholder="Escribe código o nombre…"
                                                       autocomplete="off">
                                                <input type="hidden" id="producto_id{{ $venta->id }}">
                                                <input type="hidden" id="producto_precio{{ $venta->id }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Stock: <span id="producto_stock_lbl{{ $venta->id }}" class="ml-1">—</span></span>
                                                    <span class="input-group-text">S/ <span id="producto_precio_lbl{{ $venta->id }}" class="ml-1">0.00</span></span>
                                                </div>
                                            </div>
                                            <small class="text-danger d-block" id="error_producto{{ $venta->id }}"></small>
                                        </div>

                                        <!-- Cantidad (2) -->
                                        <div class="form-group col-lg-2 col-md-4 mb-3 mb-lg-0">
                                            <label class="mb-1">Cantidad</label>
                                            <input type="number" min="1" step="1"
                                                   id="producto_cantidad{{ $venta->id }}"
                                                   class="form-control" value="1">
                                            <small class="text-danger d-block" id="error_cantidad{{ $venta->id }}"></small>
                                        </div>

                                        <!-- Descuento (2) -->
                                        <div class="form-group col-lg-2 col-md-4 mb-3 mb-lg-0">
                                            <label class="mb-1">Descuento (S/)</label>
                                            <input type="number" min="0" step="0.01"
                                                   id="producto_descuento{{ $venta->id }}"
                                                   class="form-control" value="0.00">
                                        </div>

                                        <!-- Subtotal (1) -->
                                        <div class="form-group col-lg-1 col-md-4 mb-3 mb-lg-0">
                                            <label class="mb-1">Subtotal.</label>
                                            <input type="text"
                                                   id="producto_subtotal{{ $venta->id }}"
                                                   class="form-control bg-light" value="0.00" readonly>
                                        </div>
                                        <!-- Botón (1) -->
                                        <div class="form-group col-lg-1 col-md-12 mb-0 d-flex align-items-end justify-content-end">
                                            <button type="button" class="btn btn-success"
                                                    id="producto_btn_agregar{{ $venta->id }}" title="Agregar a la lista">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DETALLE (editable: cantidad y descuento) --}}
                            <div class="card shadow-sm">
                                <div class="card-header py-2">
                                    <h6 class="m-0 font-weight-bold text-warning">
                                        <i class="fas fa-list mr-2"></i>Detalle de venta
                                    </h6>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" id="tabla_detalle_venta_editar{{ $venta->id }}">
                                            <thead class="thead-light">
                                            <tr>
                                                <th style="width: 40%" class="text-left">Producto</th>
                                                <th style="width: 10%" class="text-center">Stock</th>
                                                <th style="width: 12%" class="text-center">Cantidad</th>
                                                <th style="width: 13%" class="text-right">Precio Unit. (S/)</th>
                                                <th style="width: 12%" class="text-right">Desc. (S/)</th>
                                                <th style="width: 13%" class="text-right">Subtotal (S/)</th>
                                                <th style="width: 0%"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($venta->detalles as $detalle)
                                                <tr data-item="1" data-id="{{ $detalle->id_producto }}" 
                                                    data-stock="{{ (int)($detalle->producto->stock ?? 0) }}"
                                                    data-precio="{{ number_format($detalle->precio_unitario, 2, '.', '') }}">
                                                    {{-- Producto (texto) + inputs ocultos --}}
                                                    <td class="text-left">
                                                        {{ $detalle->producto->descripcion }}
                                                        <input type="hidden" name="productos[]" value="{{ $detalle->id_producto }}">
                                                        <input type="hidden" name="detalle_ids[]" value="{{ $detalle->id }}">
                                                    </td>
                                                    {{-- STOCK (solo ver) --}}
                                                    <td class="text-center">
                                                        {{ (int)($detalle->producto->stock ?? 0) }}
                                                    </td>
                                                    {{-- CANTIDAD (editable) --}}
                                                    <td class="text-center">
                                                        <input type="number" name="cantidades[]" value="{{ (int)$detalle->cantidad }}" min="0" step="1" class="form-control form-control-sm">
                                                        <small class="error-cantidad text-danger d-block"></small>
                                                    </td>
                                                    {{-- PRECIO unitario --}}
                                                    <td class="text-right">
                                                        S/ {{ number_format($detalle->precio_unitario, 2) }}
                                                        <input type="hidden" name="precios[]" value="{{ number_format($detalle->precio_unitario, 2, '.', '') }}">
                                                    </td>
                                                    {{-- DESCUENTO (editable) --}}
                                                    <td class="text-right">
                                                        <input type="number" name="descuentos[]" value="{{ number_format($detalle->descuento ?? 0, 2, '.', '') }}" min="0" step="0.0" class="form-control form-control-sm">
                                                        <small class="error-descuento text-danger d-block"></small>
                                                    </td>
                                                    {{-- SUBTOTAL (auto) --}}
                                                    <td class="text-right align-middle">
                                                        S/ {{ number_format($detalle->subtotal, 2, '.', '') }}
                                                        <input type="hidden" name="subtotales[]" value="{{ number_format($detalle->subtotal, 2, '.', '') }}">
                                                    </td>
                                                    {{-- Botón eliminar --}}
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm btnEliminarFila" title="Quitar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr class="bg-light font-weight-bold">
                                                <td colspan="5" class="text-right pr-3">Total (S/)</td>
                                                <td class="text-right pr-3" id="total_venta_editar{{ $venta->id }}">
                                                    {{ number_format($venta->total, 2) }}
                                                    <input type="hidden" name="total" id="total_input_editar{{ $venta->id }}"
                                                           value="{{ number_format($venta->total, 2, '.', '') }}">
                                                </td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <small id="error_detalle{{ $venta->id }}" class="text-danger d-block text-center mt-2"></small>
                                    </div>
                                </div>

                                <!-- BOTONES -->
                                <div class="p-3 text-right">
                                    <button type="submit" class="btn btn-warning" id="actualizar_venta{{ $venta->id }}">
                                        <i class="fas fa-save mr-1"></i> Actualizar venta
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="cancelar_editar_venta{{ $venta->id }}" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </button>
                                </div>
                            </div>

                        </div> {{-- /modal-body --}}
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        // 1) Refs del DOM
        const formVenta        = document.getElementById('formCrearVenta');
        const cuerpoTabla      = document.querySelector('#tabla_detalle_venta tbody');
        const totalVisible     = document.getElementById('total_venta_lbl');
        const totalOculto      = document.getElementById('CapturarTotalGeneral');
        const btnGuardar       = document.getElementById('btnGuardarVenta');
        // Captura (fila superior)
        const inputProducto    = document.getElementById('producto_busqueda');
        const inputProductoId  = document.getElementById('producto_id');
        const etiquetaStock    = document.getElementById('producto_stock_lbl');
        const etiquetaPrecio   = document.getElementById('producto_precio_lbl');
        const inputCantidad    = document.getElementById('producto_cantidad');
        const inputDescuento    = document.getElementById('producto_descuento');
        const inputSubtotal    = document.getElementById('producto_subtotal');
        const botonAgregar     = document.getElementById('producto_btn_agregar');
        const listaProductos   = document.getElementById('listaProductos');
        const inputUserId      = document.querySelector('input[name="user_id"]');
        // Cliente / Pago
        const inputClienteBusqueda = document.getElementById('cliente_busqueda');
        const inputClienteId       = document.getElementById('cliente_id');
        const listaClientes        = document.getElementById('clientes');
        const selectMetodoPago     = document.getElementById('metodo_pago_id');
        
        // 2) Helpers y constantes
        const aNumero = (v) => parseFloat((v || '0').toString().replace(',', '.')) || 0;
        const moneda  = (n) => aNumero(n).toFixed(2);
        const ALLOW_NEGATIVE_STOCK = false;

        const mostrarError = (idSpan, mensaje) => {
            const el = document.getElementById(idSpan);
            if (el) el.textContent = mensaje || '';
        };
        const limpiarErrores = () => {
            ['error_cliente', 'error-metodo_pago_id', 'error_producto', 'error_cantidad'].forEach(id => mostrarError(id, ''));
        };
        const obtenerOpcionPorValor = (datalist, valor) => {
            if (!valor || !datalist) return null;
            return Array.from(datalist.querySelectorAll('option')).find(op => op.value === valor) || null;
        };

        // 3) Cálculo de totales
        function recalcularTotalGeneral() {
            let total = 0;
            cuerpoTabla.querySelectorAll('input[name="subtotales[]"]').forEach(inp => {
                total += aNumero(inp.value);
            });
            totalVisible.textContent = moneda(total);
            if (totalOculto) totalOculto.value = moneda(total);
        }

        // 5) Construcción de filas e items[] del detalle
        function crearFilaDetalle({ id, descripcion, stock, cantidad, precio, descuento }) {
            const subtotal = Math.max(0, cantidad * precio - descuento);
            const tr = document.createElement('tr');
            tr.className = 'align-middle';
            tr.dataset.item     = '1';
            tr.dataset.id       = id;
            tr.dataset.cantidad = cantidad;
            tr.dataset.precio   = precio;
            tr.dataset.descuento = descuento;
            tr.dataset.stock    = stock;

            tr.innerHTML = `
            <td>
                ${descripcion}
                <input type="hidden" name="productos[]"   value="${id}">
            </td>
            <td>${Number.isFinite(stock) ? stock : 0}</td>
            <td>
                ${cantidad}
                <input type="hidden" name="cantidades[]"  value="${cantidad}">
            </td>
            <td class="text-right">
                ${moneda(precio)}
                <input type="hidden" name="precios[]"     value="${moneda(precio)}">
            </td>
            <td class="text-right">
                ${moneda(descuento)}
                <input type="hidden" name="descuentos[]"  value="${moneda(descuento)}">
            </td>
            <td class="text-right">
                ${moneda(subtotal)}
                <input type="hidden" name="subtotales[]"  value="${moneda(subtotal)}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" data-accion="eliminar-item" title="Quitar">
                <i class="fas fa-trash"></i>
                </button>
            </td>`;
            return tr;
        }

        // Construye items[] desde la tabla de detalle (id_producto, cantidad, descuento, subtotal)
        function buildItemsFromTable() {
            const filas = [...cuerpoTabla.querySelectorAll('tr[data-item="1"]')];
            return filas.map(tr => {
                const idProd   = tr.querySelector('input[name="productos[]"]')?.value;
                const cant     = tr.querySelector('input[name="cantidades[]"]')?.value;
                const descuento = tr.querySelector('input[name="descuentos[]"]')?.value;
                const subtotal = tr.querySelector('input[name="subtotales[]"]')?.value;
                let stock = 0;
                if (tr.dataset.stock !== undefined) stock = parseInt(tr.dataset.stock || '0', 10) || 0;
                else stock = parseInt(tr.children[1]?.textContent?.trim() || '0', 10) || 0;

                return {
                    id_producto: parseInt(idProd, 10),
                    cantidad: parseInt(cant, 10),
                    descuento: aNumero(descuento),
                    subtotal: aNumero(subtotal),
                    _stock: stock
                };
            });
        }

        // 6) UI de captura (limpiar/actualizar)
        function limpiarFilaCaptura() {
            if (inputProducto) inputProducto.value = '';
            if (inputProductoId) inputProductoId.value = '';
            if (etiquetaStock) etiquetaStock.textContent = '—';
            if (etiquetaPrecio) etiquetaPrecio.textContent = '0.00';
            if (inputCantidad) inputCantidad.value = '1';
            if (inputDescuento) inputDescuento.value = '0.00';
            if (inputSubtotal) inputSubtotal.value = '0.00';
            inputProducto?.focus();
        }

        function actualizarSubtotal(precioUnitario) {
            const cantidad = parseInt(inputCantidad?.value || '0', 10) || 0;
            const precio   = Number.isFinite(precioUnitario)
                ? precioUnitario
                : aNumero(etiquetaPrecio?.textContent);
            const descuento = aNumero(inputDescuento?.value || 0);
            let subtotal = cantidad * precio - descuento;
            if (subtotal < 0) subtotal = 0;
            if (inputSubtotal) inputSubtotal.value = moneda(subtotal);
        }

        // 7) Handlers de cambios (producto/cantidad/cliente/descuento)
        function alCambiarProducto() {
            const op = obtenerOpcionPorValor(listaProductos, inputProducto?.value);
            if (op) {
            const id     = op.dataset?.id || '';
            const precio = aNumero(op.dataset?.precio);
            const stock  = parseInt(op.dataset?.stock || '0', 10) || 0;

            inputProductoId.value      = id;
            etiquetaStock.textContent  = String(stock);
            etiquetaPrecio.textContent = moneda(precio);
            actualizarSubtotal(precio);
            mostrarError('error_producto', '');
            } else {
            inputProductoId.value      = '';
            etiquetaStock.textContent  = '—';
            etiquetaPrecio.textContent = '0.00';
            if (inputSubtotal) inputSubtotal.value = '0.00';
            }
        }
        function alCambiarCantidad() {
            actualizarSubtotal();
            mostrarError('error_cantidad', '');
        }
        function alCambiarDescuento() {
            actualizarSubtotal();
        }
        function alCambiarCliente() {
            const op = obtenerOpcionPorValor(listaClientes, inputClienteBusqueda?.value);
            if (op) {
            inputClienteId.value = op.dataset.id || '';
            mostrarError('error_cliente', '');
            } else {
            inputClienteId.value = '';
            }
        }

        // 8) Agregar / Eliminar ítems
        function manejarAgregar() {
            limpiarErrores();

            // 1) Cliente requerido
            if (!inputClienteId?.value) {
            mostrarError('error_cliente', 'Selecciona un cliente de la lista antes de agregar ítems.');
            inputClienteBusqueda?.focus();
            return;
            }

            // 2) Lectura de captura
            const id          = (inputProductoId?.value || '').trim();
            const descripcion = (inputProducto?.value   || '').trim();
            const stockTxt    = (etiquetaStock?.textContent  || '').trim();
            const precioTxt   = (etiquetaPrecio?.textContent || '').trim();
            const cantTxt     = (inputCantidad?.value        || '').trim();
            const descuentoTxt = (inputDescuento?.value || '').trim();

            if (!id || !descripcion) {
            mostrarError('error_producto', 'Selecciona un producto de la lista.');
            inputProducto?.focus();
            return;
            }
            const cantidad = parseInt(cantTxt, 10);
            const descuento = aNumero(descuentoTxt);
            if (descuento < 0) {
                mostrarError('error_producto', 'El descuento no puede ser negativo.');
                inputDescuento?.focus();
                return;
            }
            const precio = aNumero(precioTxt);
            if (precio <= 0) {
            mostrarError('error_producto', 'Este producto no tiene precio de venta válido.');
            return;
            }
            const stock = Number.isFinite(parseInt(stockTxt, 10)) ? parseInt(stockTxt, 10) : 0;
            if (cantidad > stock) {
            mostrarError('error_cantidad', `No hay stock suficiente. Stock disponible: ${stock}.`);
            return;
            }
            if (descuento > cantidad * precio) {
                mostrarError('error_producto', 'El descuento no puede ser mayor al total de la fila.');
                inputDescuento?.focus();
                return;
            }

            // 3) Agregar
            const fila = crearFilaDetalle({ id, descripcion, stock, cantidad, precio, descuento });
            cuerpoTabla.appendChild(fila);

            // 4) Total + bloquear cliente
            recalcularTotalGeneral();
            limpiarFilaCaptura();
        }

        function manejarEliminar(ev) {
            const btn = ev.target.closest('[data-accion="eliminar-item"]');
            if (!btn) return;
            const tr = btn.closest('tr');
            if (tr) tr.remove();
            recalcularTotalGeneral();
        }

        // 9) Validación y envío (fetch)
        formVenta?.addEventListener('submit', (e) => {
            limpiarErrores();
            limpiarFilaCaptura();
            // Asegura total sincronizado
            recalcularTotalGeneral();
            // Validaciones de cabecera
            if (!inputClienteId?.value) {
            e.preventDefault();
            mostrarError('error_cliente', 'Selecciona un cliente válido.');
            return;
            }
            if (!selectMetodoPago?.value) {
            e.preventDefault();
            mostrarError('error-metodo_pago_id', 'Selecciona un método de pago.');
            selectMetodoPago?.focus();
            return;
            }

            // Validación de detalle
            const productos  = cuerpoTabla.querySelectorAll('input[name="productos[]"]');
            const cantidades = cuerpoTabla.querySelectorAll('input[name="cantidades[]"]');
            const precios    = cuerpoTabla.querySelectorAll('input[name="precios[]"]');

            if (productos.length === 0) {
            e.preventDefault();
            mostrarError('error_producto', 'Agrega al menos un producto al detalle.');
            inputProducto?.focus();
            return;
            }

            // Stock por fila (usa dataset.stock si existe o la celda)
            const filas = cuerpoTabla.querySelectorAll('tr[data-item="1"]');
            for (const tr of filas) {
            const cant = aNumero(tr.querySelector('input[name="cantidades[]"]')?.value);
            let stock  = 0;

            if (tr.dataset.stock !== undefined) {
                stock = parseInt(tr.dataset.stock || '0', 10) || 0;
            } else {
                const tdStock = tr.children[1]?.textContent?.trim();
                stock = parseInt(tdStock || '0', 10) || 0;
            }
            if (cant > stock) {
                e.preventDefault();
                mostrarError('error_cantidad', 'Una fila del detalle supera el stock disponible.');
                return;
            }
            }

            // Total > 0
            const total = aNumero(totalOculto?.value);
            if (total <= 0) {
            e.preventDefault();
            mostrarError('error_producto', 'El total debe ser mayor que 0.');
            return;
            }

            // Evitar doble envío
            btnGuardar?.setAttribute('disabled', 'disabled');
            btnGuardar && (btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Guardando…');

            // === ENVÍO POR AJAX (fetch) ===
            e.preventDefault(); // Evita el submit normal

            // Armar items desde la tabla
            const items = buildItemsFromTable();
            if (items.length === 0) {
            mostrarError('error_producto', 'Agrega al menos un producto al detalle.');
            inputProducto?.focus();
            // Rehabilitar botón si fuese necesario
            if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
            }
            return;
            }

            // Validaciones por fila (usa tu flag ALLOW_NEGATIVE_STOCK)
            for (const it of items) {
            if (!Number.isFinite(it.id_producto) || it.id_producto <= 0) {
                mostrarError('error_producto', 'Producto inválido en el detalle.');
                if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
                }
                return;
            }
            if (!Number.isFinite(it.cantidad) || it.cantidad < 1) {
                mostrarError('error_cantidad', 'Cantidad inválida en una fila del detalle.');
                if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
                }
                return;
            }
            if (!ALLOW_NEGATIVE_STOCK && it.cantidad > it._stock) {
                mostrarError('error_cantidad', `Una fila supera el stock disponible (stock: ${it._stock}).`);
                if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
                }
                return;
            }
            delete it._stock; // limpiar aux antes de enviar
            }

            // CSRF desde el <form>
            const csrf = formVenta.querySelector('input[name="_token"]')?.value;

            // Payload que espera tu store()
            const payload = {
            cliente_id: parseInt(inputClienteId.value, 10),
            user_id: parseInt(inputUserId?.value || '0', 10),
            metodo_pago_id: parseInt(selectMetodoPago.value, 10),
            total: Number(moneda(total)),
            items
            };

            fetch(formVenta.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
            })
            .then(async (res) => {
            if (res.ok) {
                const ct = (res.headers.get('content-type') || '').toLowerCase();
                if (res.ok && !ct.includes('application/json')) {
                    const html = await res.text();
                    console.error('Respuesta no JSON:', html.slice(0, 500));
                    Swal.fire({icon:'error', title:'Respuesta no válida', text:'El servidor no devolvió JSON.'});
                    return;
                }
                const data = await res.json();

                // Notificación
                if (window.Swal) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Venta creada',
                    text: `ID: ${data.venta_id} | Total: S/ ${data.total}`,
                    timer: 1600,
                    showConfirmButton: false
                });
                }

                // Cerrar modal
                $('#modalCrearVenta').modal('hide');

                // Limpiar UI
                formVenta.reset();
                cuerpoTabla.innerHTML = '';
                totalVisible.textContent = '0.00';
                totalOculto.value = '0.00';

                // Restaurar botón
                if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
                }
                $('#modalCrearVenta').modal('hide');
                $("#main-content").load(window.location.href + " #main-content > *");
                return;
            }

            // 422 Validación
            if (res.status === 422) {
                const { errors } = await res.json();
                if (errors?.cliente_id)      mostrarError('error_cliente', errors.cliente_id[0]);
                if (errors?.metodo_pago_id)  mostrarError('error-metodo_pago_id', errors.metodo_pago_id[0]);
                if (errors?.items)           mostrarError('error_producto', errors.items[0]);
                if (errors?.total)           mostrarError('error_producto', errors.total[0]);

                const indexedErrs = Object.keys(errors || {}).filter(k => k.startsWith('items.'));
                if (indexedErrs.length && window.Swal) {
                const msg = indexedErrs.slice(0, 6).map(k => `• ${k}: ${errors[k][0]}`).join('\n');
                Swal.fire({ icon: 'error', title: 'Errores en ítems', text: msg });
                }
            } else {
                const txt = await res.text();
                console.error('Error no controlado:', txt);
                if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo crear la venta.' });
            }
            })
            .catch(err => {
            console.error(err);
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Error inesperado', text: 'no se ha podido crear la venta.'});
            })
            .finally(() => {
            if (btnGuardar?.disabled) {
                btnGuardar.removeAttribute('disabled');
                btnGuardar.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar venta';
            }
            });
        });

        // 10) Listeners
        inputProducto?.addEventListener('input',  alCambiarProducto);
        inputProducto?.addEventListener('change', alCambiarProducto);
        inputProducto?.addEventListener('blur',   alCambiarProducto);
        inputProducto?.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });

        inputCantidad?.addEventListener('input', alCambiarCantidad);
        inputDescuento?.addEventListener('input', alCambiarDescuento);

        inputClienteBusqueda?.addEventListener('input',  alCambiarCliente);
        inputClienteBusqueda?.addEventListener('change', alCambiarCliente);
        inputClienteBusqueda?.addEventListener('blur',   alCambiarCliente);

        botonAgregar?.addEventListener('click', manejarAgregar);
        cuerpoTabla.addEventListener('click', manejarEliminar);

        // Para la tabla de creación (tabla_detalle_venta)
        cuerpoTabla.addEventListener('click', function(ev) {
            const btn = ev.target.closest('.btnEliminarFila, [data-accion="eliminar-item"]');
            if (!btn) return;
            const tr = btn.closest('tr[data-item="1"]');
            if (tr) tr.remove();
            recalcularTotalGeneral();
        });

        // Para cada tabla de edición (tabla_detalle_venta_editar{vid})
        document.querySelectorAll('table[id^="tabla_detalle_venta_editar"]').forEach(tabla => {
            tabla.addEventListener('click', function(ev) {
                const btn = ev.target.closest('.btnEliminarFila, [data-accion="eliminar-item"]');
                if (!btn) return;
                const tr = btn.closest('tr[data-item="1"]');
                if (tr) tr.remove();
                // Si tienes función para recalcular el total de edición, llámala aquí:
                // recalcTotal(vid); // si tienes esta función definida
            });
        });

        // 11) Estado inicial
        alCambiarCliente();
        recalcularTotalGeneral();
        });
    </script>
    <script>
        (function () {
            // --- Helpers ---
            const toNum  = v => { const n = parseFloat(String(v).replace(',', '.')); return Number.isFinite(n) ? n : 0; };
            const money  = n => toNum(n).toFixed(2);
            const getTxt = el => (el && el.textContent ? el.textContent.trim() : '');

            // --- Producto UI (stock/precio) ---
            window.initProductoUI = function(vid) {
                const $busq  = document.getElementById(`producto_busqueda${vid}`);
                const $id    = document.getElementById(`producto_id${vid}`);
                const $pHid  = document.getElementById(`producto_precio${vid}`);
                const $stk   = document.getElementById(`producto_stock_lbl${vid}`);
                const $precio= document.getElementById(`producto_precio_lbl${vid}`);
                if (!($busq && $id && $pHid && $stk && $precio)) return;

                const listId = $busq.getAttribute('list') || '';
                const $dl = document.getElementById(listId) ||
                            document.getElementById(`listaProductos${vid}`) ||
                            document.getElementById('listaProductos');
                if (!$dl) return;

                const index = new Map();
                Array.from($dl.options).forEach(opt => {
                    index.set(opt.value, {
                        id: opt.dataset.id,
                        stock: toNum(opt.dataset.stock || 0),
                        precio: toNum(opt.dataset.precio || 0)
                    });
                });

                function limpiar() {
                    $stk.textContent = '—';
                    $precio.textContent = '0.00';
                    $id.value = '';
                    $pHid.value = '';
                }
                function actualizar(valor) {
                    const d = index.get(valor);
                    if (!d) { limpiar(); return; }
                    $stk.textContent = d.stock;
                    $precio.textContent = money(d.precio);
                    $id.value = d.id;
                    $pHid.value = d.precio;
                }

                $busq.addEventListener('input', e => {
                    const v = e.target.value.trim();
                    if (!v) return limpiar();
                    actualizar(v);
                });
                $busq.addEventListener('change', e => {
                    const v = e.target.value.trim();
                    if (!v) return limpiar();
                    actualizar(v);
                });

                limpiar();
            };

            // --- Subtotal y validaciones ---
            window.initProductoSubtotal = function(vid) {
                const ensureErrorSmall = (el, id) => {
                    let sm = document.getElementById(id);
                    if (!sm) {
                        sm = document.createElement('small');
                        sm.id = id;
                        sm.className = 'text-danger d-block';
                        el.parentElement.appendChild(sm);
                    }
                    return sm;
                };

                const $busq   = document.getElementById(`producto_busqueda${vid}`);
                const $id     = document.getElementById(`producto_id${vid}`);
                const $pHid   = document.getElementById(`producto_precio${vid}`);
                const $stkLbl = document.getElementById(`producto_stock_lbl${vid}`);
                const $pLbl   = document.getElementById(`producto_precio_lbl${vid}`);
                const $cant   = document.getElementById(`producto_cantidad${vid}`);
                const $desc   = document.getElementById(`producto_descuento${vid}`);
                const $subt   = document.getElementById(`producto_subtotal${vid}`);

                const $errCant = document.getElementById(`error_cantidad${vid}`) || ensureErrorSmall($cant, `error_cantidad${vid}`);
                const $errDesc = ensureErrorSmall($desc, `error_descuento${vid}`);

                if (!($busq && $id && $pHid && $stkLbl && $pLbl && $cant && $desc && $subt)) return;

                const listId = $busq.getAttribute('list') || '';
                const $dl = document.getElementById(listId) ||
                            document.getElementById(`listaProductos${vid}`) ||
                            document.getElementById('listaProductos');
                if (!$dl) return;

                const index = new Map();
                Array.from($dl.options).forEach(opt => {
                    index.set(opt.value, {
                        id: opt.dataset.id,
                        stock: toNum(opt.dataset.stock || 0),
                        precio: toNum(opt.dataset.precio || 0)
                    });
                });

                function clearErrors() {
                    $errCant.textContent = '';
                    $errDesc.textContent = '';
                    $cant.classList.remove('is-invalid');
                    $desc.classList.remove('is-invalid');
                }

                function limpiarTodo() {
                    $stkLbl.textContent = '—';
                    $pLbl.textContent   = '0.00';
                    $id.value = '';
                    $pHid.value = '';
                    $subt.value = '0.00';
                    $cant.value = '1';
                    $desc.value = '0.00';
                    clearErrors();
                }

                function actualizarDesdeProducto(valor) {
                    const d = index.get(valor);
                    if (!d) { limpiarTodo(); return; }
                    $stkLbl.textContent = d.stock;
                    $pLbl.textContent   = money(d.precio);
                    $id.value  = d.id;
                    $pHid.value = d.precio;
                    $cant.value = '1';
                    $desc.value = '0.00';
                    clearErrors();
                    recalcular();
                    $cant.focus();
                }

                function recalcular() {
                    clearErrors();

                    const stock  = toNum($stkLbl.textContent || 0);
                    const precio = toNum($pHid.value || 0);

                    let cant = toNum($cant.value);
                    let desc = toNum($desc.value);

                    if (!$id.value || precio <= 0) {
                        $subt.value = '0.00';
                        return;
                    }

                    if (!Number.isFinite(cant) || $cant.value === '') cant = 0;
                    if (!Number.isFinite(desc) || $desc.value === '') desc = 0;

                    const cantCalc = (stock > 0) ? Math.min(cant, stock) : cant;
                    const bruto = Math.max(0, cantCalc * precio);

                    if (stock > 0 && cant > stock) {
                        $errCant.textContent = `La cantidad no puede superar el stock (${stock}).`;
                        $cant.classList.add('is-invalid');
                    }
                    if (desc > bruto) {
                        $errDesc.textContent = `El descuento no puede ser mayor que el subtotal bruto (S/ ${money(bruto)}).`;
                        $desc.classList.add('is-invalid');
                    }

                    const subtotal = Math.max(0, bruto - Math.min(desc, bruto));
                    $subt.value = money(subtotal);
                }

                $busq.addEventListener('input',  e => {
                    const v = e.target.value.trim();
                    if (!v) return limpiarTodo();
                    actualizarDesdeProducto(v);
                });
                $busq.addEventListener('change', e => {
                    const v = e.target.value.trim();
                    if (!v) return limpiarTodo();
                    actualizarDesdeProducto(v);
                });

                ['input','change'].forEach(evt => {
                    $cant.addEventListener(evt, recalcular);
                    $desc.addEventListener(evt, recalcular);
                });

                limpiarTodo();
            };

            window.initBotonAgregar = function(vid) {
                function updateSubtotalText(td, valorStr) {
                    const hidden = td.querySelector('input[name="subtotales[]"]');
                    if (!hidden) return;
                    const texto = `S/ ${valorStr} `;
                    const first = td.firstChild;
                    if (first && first.nodeType === Node.TEXT_NODE) first.textContent = texto;
                }

                function recalcFila(tr, opts = {}) {
                    const cantInput = tr.querySelector('input[name="cantidades[]"]');
                    const descInput = tr.querySelector('input[name="descuentos[]"]');
                    const precioHid = tr.querySelector('input[name="precios[]"]');
                    const subtHid   = tr.querySelector('input[name="subtotales[]"]');

                    const stock = toNum(tr.dataset.stock || 0);
                    const prec  = toNum(precioHid.value || tr.dataset.precio || 0);

                    const errCant = tr.querySelector('.error-cantidad');
                    const errDesc = tr.querySelector('.error-descuento');

                    if (errCant) { errCant.textContent = ''; cantInput.classList.remove('is-invalid'); }
                    if (errDesc) { errDesc.textContent = ''; descInput.classList.remove('is-invalid'); }

                    let cant = cantInput.value === '' ? '' : parseInt(cantInput.value, 10);
                    let desc = descInput.value === '' ? '' : toNum(descInput.value);

                    let error = false;

                    if (cantInput.value === '') {
                        if (errCant) { errCant.textContent = 'La cantidad es obligatoria.'; cantInput.classList.add('is-invalid'); }
                        error = true;
                    } else if (!Number.isFinite(cant) || cant < 1) {
                        if (errCant) { errCant.textContent = 'La cantidad debe ser al menos 1.'; cantInput.classList.add('is-invalid'); }
                        error = true;
                    } else if (stock > 0 && cant > stock) {
                        if (errCant) { errCant.textContent = `La cantidad no puede superar el stock (${stock}).`; cantInput.classList.add('is-invalid'); }
                        error = true;
                    }

                    let bruto = 0;
                    if (cantInput.value !== '' && Number.isFinite(cant) && cant >= 1) {
                        const cantCalc = (stock > 0) ? Math.min(cant, stock) : cant;
                        bruto = Math.max(0, cantCalc * prec);
                    }
                    if (descInput.value === '') {
                        if (errDesc) { errDesc.textContent = 'El descuento es obligatorio.'; descInput.classList.add('is-invalid'); }
                        error = true;
                    } else if (!Number.isFinite(desc) || desc < 0) {
                        if (errDesc) { errDesc.textContent = 'El descuento no puede ser negativo.'; descInput.classList.add('is-invalid'); }
                        error = true;
                    } else if (desc > bruto) {
                        if (errDesc) { errDesc.textContent = `El descuento no puede ser mayor que el subtotal bruto (S/ ${money(bruto)}).`; descInput.classList.add('is-invalid'); }
                        error = true;
                    }

                    let neto = 0;
                    if (!error && cantInput.value !== '' && descInput.value !== '') {
                        const cantCalc = (stock > 0) ? Math.min(cant, stock) : cant;
                        const brutoCalc = Math.max(0, cantCalc * prec);
                        const descCalc = Math.min(desc, brutoCalc);
                        neto = Math.max(0, brutoCalc - descCalc);

                        if (opts.format) {
                            cantInput.value = cant;
                            descInput.value = money(descCalc);
                        }
                        if (subtHid) subtHid.value = money(neto);
                    } else {
                        if (subtHid) subtHid.value = '0.00';
                    }

                    const tdSubtotal = subtHid ? subtHid.parentElement : null;
                    if (tdSubtotal) updateSubtotalText(tdSubtotal, money(neto));
                }

                function recalcTotal(vid) {
                    const tbody = document.querySelector(`#tabla_detalle_venta_editar${vid} tbody`);
                    const totalCell = document.getElementById(`total_venta_editar${vid}`);
                    const totalHid  = document.getElementById(`total_input_editar${vid}`);
                    if (!(tbody && totalCell && totalHid)) return;

                    let total = 0;
                    tbody.querySelectorAll('input[name="subtotales[]"]').forEach(h => total += toNum(h.value || 0));
                    totalHid.value = money(total);

                    const first = totalCell.firstChild;
                    if (first && first.nodeType === Node.TEXT_NODE) {
                        first.textContent = `${money(total)} `;
                    } else {
                        totalCell.insertBefore(document.createTextNode(`${money(total)} `), totalCell.firstChild);
                    }
                }

                function filaHTML(payload) {
                    return `
                    <tr data-item="1" data-id="${payload.producto_id}"
                        data-stock="${payload.stock}" data-precio="${money(payload.precio_unit)}">
                        <input type="hidden" name="detalle_ids[]" value="">
                        <td class="text-left">
                        ${payload.rotulo}
                        <input type="hidden" name="productos[]" value="${payload.producto_id}">
                        </td>
                        <td class="text-center">${payload.stock}</td>
                        <td class="text-center">
                        <input type="number" name="cantidades[]" value="${payload.cantidad}" min="0" step="1"
                                class="form-control form-control-sm">
                        <small class="error-cantidad text-danger d-block"></small>
                        </td>
                        <td class="text-right">
                        S/ ${money(payload.precio_unit)}
                        <input type="hidden" name="precios[]" value="${money(payload.precio_unit)}">
                        </td>
                        <td class="text-right">
                        <input type="number" name="descuentos[]" value="${money(payload.descuento)}" min="0" step="0.01"
                                class="form-control form-control-sm">
                        <small class="error-descuento text-danger d-block"></small>
                        </td>
                        <td class="text-right align-middle">
                        S/ ${money(payload.subtotal)}
                        <input type="hidden" name="subtotales[]" value="${money(payload.subtotal)}">
                        </td>
                        <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btnEliminarFila" title="Quitar">
                            <i class="fas fa-trash"></i>
                        </button>
                        </td>
                    </tr>`;
                }

                const $btn   = document.getElementById(`producto_btn_agregar${vid}`);
                const $tabla = document.getElementById(`tabla_detalle_venta_editar${vid}`);
                if (!($btn && $tabla)) return;

                const $busq   = document.getElementById(`producto_busqueda${vid}`);
                const $idHid  = document.getElementById(`producto_id${vid}`);
                const $precioHid = document.getElementById(`producto_precio${vid}`);
                const $stkLbl = document.getElementById(`producto_stock_lbl${vid}`);
                const $cant   = document.getElementById(`producto_cantidad${vid}`);
                const $desc   = document.getElementById(`producto_descuento${vid}`);
                const $subt   = document.getElementById(`producto_subtotal${vid}`);

                const $errProd = document.getElementById(`error_producto${vid}`);
                const $errCant = document.getElementById(`error_cantidad${vid}`);
                let $errDesc = document.getElementById(`error_descuento${vid}`);
                if (!$errDesc) {
                    $errDesc = document.createElement('small');
                    $errDesc.id = `error_descuento${vid}`;
                    $errDesc.className = 'text-danger d-block';
                    $desc.parentElement.appendChild($errDesc);
                }

                $tabla.addEventListener('input', (ev) => {
                    const t = ev.target;
                    if (!(t instanceof HTMLInputElement)) return;
                    if (t.name === 'cantidades[]' || t.name === 'descuentos[]') {
                        const tr = t.closest('tr[data-item="1"]');
                        if (!tr) return;
                        recalcFila(tr, { format: false });
                        recalcTotal(vid);
                    }
                });

                $tabla.addEventListener('change', (ev) => {
                    const t = ev.target;
                    if (!(t instanceof HTMLInputElement)) return;
                    if (t.name === 'cantidades[]' || t.name === 'descuentos[]') {
                        const tr = t.closest('tr[data-item="1"]');
                        if (!tr) return;
                        recalcFila(tr, { format: true });
                        recalcTotal(vid);
                    }
                });

                $tabla.addEventListener('blur', (ev) => {
                    const t = ev.target;
                    if (!(t instanceof HTMLInputElement)) return;
                    if (t.name === 'cantidades[]' || t.name === 'descuentos[]') {
                        const tr = t.closest('tr[data-item="1"]');
                        if (!tr) return;
                        recalcFila(tr, { format: true });
                        recalcTotal(vid);
                    }
                }, true);

                $tabla.addEventListener('click', (ev) => {
                    const btn = ev.target.closest('.btnEliminarFila');
                    if (!btn) return;

                    const tr = btn.closest('tr[data-item="1"]');
                    if (!tr) return;

                    // sacar el vid desde la tabla o el form
                    const vid =
                        tr.closest('table[id^="tabla_detalle_venta_editar"]')?.id.replace('tabla_detalle_venta_editar','') ||
                        tr.closest('form[id^="formEditarVenta"]')?.id.replace('formEditarVenta','') ||
                        '';

                    // inicializa el bucket
                    (window.eliminadosPorVenta[vid] ||= []);

                    // guarda el detalle_id si es una fila existente
                    const detalleId = tr.querySelector('input[name="detalle_ids[]"]')?.value;
                    if (detalleId) window.eliminadosPorVenta[vid].push(detalleId);

                    // ahora sí, quita la fila y recalcula
                    tr.remove();
                    recalcTotal(vid);
                });

                $btn.addEventListener('click', () => {
                    const hasErr = (
                        ($errProd && getTxt($errProd)) ||
                        ($errCant && getTxt($errCant)) ||
                        ($errDesc && getTxt($errDesc))
                    );
                    if (!$idHid.value || hasErr) return;

                    const stock = toNum(getTxt($stkLbl) || 0);
                    const cant  = toNum($cant.value || 0);
                    const desc  = toNum($desc.value || 0);
                    const prec  = toNum($precioHid.value || 0);
                    const cantCalc = stock > 0 ? Math.min(cant, stock) : cant;
                    const bruto = Math.max(0, cantCalc * prec);
                    const descCalc = Math.min(desc, bruto);
                    const subtotal = Math.max(0, bruto - descCalc);

                    if (cantCalc <= 0 || prec <= 0) return;

                    const payload = {
                        producto_id: $idHid.value,
                        rotulo: $busq.value ? $busq.value : `ID:${$idHid.value}`,
                        stock: stock,
                        cantidad: cant,
                        precio_unit: prec,
                        descuento: desc,
                        subtotal: subtotal
                    };

                    const tbody = $tabla.querySelector('tbody');
                    tbody.insertAdjacentHTML('beforeend', filaHTML(payload));
                    const tr = tbody.lastElementChild;
                    recalcFila(tr);
                    recalcTotal(vid);

                    $busq.value = '';
                    $idHid.value = '';
                    $precioHid.value = '';
                    $stkLbl.textContent = '—';
                    document.getElementById(`producto_precio_lbl${vid}`).textContent = '0.00';
                    $cant.value = '1';
                    $desc.value = '0.00';
                    $subt.value = '0.00';
                    if ($errProd) $errProd.textContent = '';
                    if ($errCant) $errCant.textContent = '';
                    if ($errDesc) $errDesc.textContent = '';
                });

                // Sincroniza la tabla al cargar
                (function syncTabla() {
                    const tbody = document.querySelector(`#tabla_detalle_venta_editar${vid} tbody`);
                    if (!tbody) return;
                    tbody.querySelectorAll('tr[data-item="1"]').forEach(tr => recalcFila(tr, { format: true }));
                    recalcTotal(vid);
                })();
            };

            // --- Restaurar modal de edición al cerrar ---
            const modalStates = {};
            document.querySelectorAll('div[id^="modalEditarVenta"]').forEach(modal => {
                modalStates[modal.id] = modal.innerHTML;
            });

            function restoreModal(modal) {
                if (!modal || !modal.id || !modalStates[modal.id]) return;
                modal.innerHTML = modalStates[modal.id];
            }

            document.querySelectorAll('div[id^="modalEditarVenta"]').forEach(modal => {
                $(modal).on('hidden.bs.modal', function () {
                    restoreModal(modal);
                    setTimeout(() => {
                        const vid = modal.id.replace('modalEditarVenta', '');
                        if (window.initProductoUI) window.initProductoUI(vid);
                        if (window.initProductoSubtotal) window.initProductoSubtotal(vid);
                        if (window.initBotonAgregar) window.initBotonAgregar(vid);
                    }, 10);
                });
                // Inicialización al cargar
                const vid = modal.id.replace('modalEditarVenta', '');
                if (window.initProductoUI) window.initProductoUI(vid);
                if (window.initProductoSubtotal) window.initProductoSubtotal(vid);
                if (window.initBotonAgregar) window.initBotonAgregar(vid);
            });
        })();
    </script>
    <script>
        (function () {
        // ===== Estado + helpers =====
        window.idsOriginalesVenta ??= {};
        window.eliminadosPorVenta ??= {};
        const toNum = (v) => Number(String(v ?? '').replace(/[^\d.-]/g, '')) || 0;

        // Snapshot SOLO de IDs al abrir el modal + reset del bucket
        document.addEventListener('show.bs.modal', (ev) => {
            const modal = ev.target;
            if (!modal.id || !modal.id.startsWith('modalEditarVenta')) return;

            const vid   = modal.id.replace('modalEditarVenta','');
            const tabla = document.getElementById(`tabla_detalle_venta_editar${vid}`);
            if (!tabla) return;

            const ids = [...tabla.querySelectorAll('tbody input[name="detalle_ids[]"]')]
            .map(i => i.value)
            .filter(Boolean);

            window.idsOriginalesVenta[vid] = ids;
            window.eliminadosPorVenta[vid] = []; // reset eliminados al abrir
            // console.log('SNAPSHOT ids:', ids);
        });

        // Reset bucket al cerrar modal
        document.addEventListener('hidden.bs.modal', (ev) => {
            const modal = ev.target;
            if (!modal.id || !modal.id.startsWith('modalEditarVenta')) return;
            const vid = modal.id.replace('modalEditarVenta','');
            window.eliminadosPorVenta[vid] = [];
        });

        // Captura de ELIMINADOS al pulsar el bote rojo (no removemos aquí; tu otro handler ya lo hace)
        document.addEventListener('click', (ev) => {
            const btn = ev.target.closest('.btnEliminarFila');
            if (!btn) return;

            const tr = btn.closest('tr[data-item="1"]');
            if (!tr) return;

            // obtener vid desde form o tabla
            let vid = '';
            const form = tr.closest('form[id^="formEditarVenta"]');
            if (form) vid = form.id.replace('formEditarVenta','');
            if (!vid) {
            const tbl = tr.closest('table[id^="tabla_detalle_venta_editar"]');
            if (tbl) vid = tbl.id.replace('tabla_detalle_venta_editar','');
            }
            if (!vid) return;

            (window.eliminadosPorVenta[vid] ||= []);
            const detalleId = tr.querySelector('input[name="detalle_ids[]"]')?.value;
            if (detalleId) window.eliminadosPorVenta[vid].push(detalleId);
        });

        // ===== Submit del formulario =====
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            if (!form.id || !form.id.startsWith('formEditarVenta')) return;
            e.preventDefault();

            const vid   = form.id.replace('formEditarVenta', '');
            const tabla = document.getElementById(`tabla_detalle_venta_editar${vid}`);
            const tbody = tabla?.querySelector('tbody');
            const errDetalle = document.getElementById(`error_detalle${vid}`);
            if (!tbody) return;

            // Al menos una fila
            const filas = tbody.querySelectorAll('tr[data-item="1"]');
            if (errDetalle) errDetalle.textContent = (filas.length === 0)
            ? 'Debe agregar al menos un producto a la venta.' : '';
            tabla?.classList.toggle('table-danger', filas.length === 0);
            if (filas.length === 0) return;

            const totalInput = document.getElementById(`total_input_editar${vid}`);
            const btnSubmit  = document.getElementById(`actualizar_venta${vid}`);

            // 1) Estado actual de filas
            const detallesActuales = [];
            tbody.querySelectorAll('tr[data-item="1"]').forEach(tr => {
            const cantInput = tr.querySelector('input[name="cantidades[]"]');
            const precInput = tr.querySelector('input[name="precios[]"]');     // hidden
            const descInput = tr.querySelector('input[name="descuentos[]"]');

            detallesActuales.push({
                detalle_id: tr.querySelector('input[name="detalle_ids[]"]')?.value || '',
                producto_id: tr.querySelector('input[name="productos[]"]')?.value || '',
                cantidad: toNum(cantInput?.value),
                precio:   toNum(precInput?.value),
                descuento:toNum(descInput?.value),
                subtotal: toNum(tr.querySelector('input[name="subtotales[]"]')?.value),

                // Originales del HTML inicial (defaultValue no cambia cuando el usuario escribe)
                _orig_cantidad: toNum(cantInput?.defaultValue),
                _orig_precio:   toNum(precInput?.defaultValue),
                _orig_desc:     toNum(descInput?.defaultValue),
            });
            });

            // 2) Diffs
            // a) nuevos → sin detalle_id
            const nuevos = detallesActuales
            .filter(d => !d.detalle_id)
            .map(d => ({
                producto_id: d.producto_id,
                cantidad: d.cantidad,
                precio: d.precio,
                descuento: d.descuento,
                subtotal: d.subtotal // el backend lo recalcula igual
            }));

            // b) editados → cambió cantidad || precio || descuento
            const editados = detallesActuales
            .filter(d => d.detalle_id)
            .filter(d => (d.cantidad !== d._orig_cantidad) || (d.precio !== d._orig_precio) || (d.descuento !== d._orig_desc))
            .map(d => ({
                detalle_id: d.detalle_id,
                producto_id: d.producto_id,
                cantidad_nueva: d.cantidad,
                cantidad_original: d._orig_cantidad,
                precio: d.precio,
                descuento: d.descuento,
                subtotal: d.subtotal
            }));

            // c) eliminados → unión de snapshot DIFF ∪ bucket por click
            const idsOriginales = window.idsOriginalesVenta?.[vid] ?? [];
            const idsActuales   = detallesActuales.map(d => d.detalle_id).filter(Boolean);
            const eliminadosDiff  = idsOriginales.filter(id => !idsActuales.includes(id));
            const eliminadosClick = Array.isArray(window.eliminadosPorVenta[vid]) ? window.eliminadosPorVenta[vid] : [];
            const eliminados = [...new Set([...eliminadosDiff, ...eliminadosClick])];

            // 3) Generales + CSRF
            const cliente_id     = form.querySelector('input[name="cliente_id"]')?.value;
            const metodo_pago_id = form.querySelector('select[name="metodo_pago_id"]')?.value;
            const total          = totalInput?.value || '0.00';
            const csrf           = document.querySelector('meta[name="csrf-token"]')?.content
                                ?? form.querySelector('input[name="_token"]')?.value
                                ?? '';
            if (!csrf) { console.warn('CSRF vacío'); return; }

            const payload = { cliente_id, metodo_pago_id, total, nuevos, editados, eliminados };
            console.log('PAYLOAD →', JSON.stringify(payload, null, 2));

            // 4) AJAX
            const originalHTML = btnSubmit?.innerHTML;
            if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Actualizando…';
            }

            try {
            const res = await fetch(form.action, {
                method: 'PUT',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                const msg = data?.message || 'No se pudo actualizar la venta.';
                throw new Error(msg);
            }

            const cambios = (data.creados|0) + (data.actualizados|0) + (data.eliminados|0);
            if (cambios === 0) {
                await (window.Swal?.fire({ icon:'info', title:'Sin cambios', text:'No se detectaron modificaciones.' }));
                return;
            }

            await (window.Swal?.fire({ icon:'success', title:'Venta actualizada', timer: 1400, showConfirmButton: false }));
            $(`#modalEditarVenta${vid}`).modal('hide');
            $("#main-content").load(window.location.href + " #main-content > *");

            // limpiar buckets después del éxito
            window.eliminadosPorVenta[vid] = [];
            window.idsOriginalesVenta[vid] = idsActuales;

            } catch (err) {
            console.error(err);
            window.Swal?.fire({ icon:'error', title:'Error', text: err.message || 'No se pudo actualizar la venta.' });
            } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalHTML || '<i class="fas fa-save mr-1"></i> Actualizar venta';
            }
            }
        });
        })();
    </script>
    <script>
        (function () {
        const getCsrf = () =>
            document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('input[name="_token"]')?.value || '';

        // Recarga el listado y, si la página quedó vacía, navega a la anterior.
        function reloadListFixingPagination(removedRow) {
            const tbody = removedRow?.closest('tbody')
            || document.querySelector('#tablaVentas tbody')
            || document.querySelector('table[id*="ventas"] tbody');

            const rowsAfter = tbody ? tbody.querySelectorAll('tr').length : 0;

            const urlObj = new URL(window.location.href);
            let page = parseInt(urlObj.searchParams.get('page') || '1', 10);

            if (rowsAfter === 0 && page > 1) {
            page -= 1;
            urlObj.searchParams.set('page', String(page));
            }

            const target = urlObj.toString();

            // Si hay jQuery y contenedor parcial, hacemos fragment reload + fix de URL
            if (window.jQuery && document.getElementById('main-content')) {
            $("#main-content").load(target + " #main-content > *", function () {
                // refleja la URL correcta en la barra
                window.history.replaceState({}, '', target);
                // re-init de scripts internos (define esta función en tu página)
                if (window.initVentasPage) window.initVentasPage();
            });
            } else {
            // fallback: recarga completa
            window.location.href = target;
            }
        }

        let deleting = false;

        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.btn.btn-danger.btnEliminarVenta, .btnEliminarVenta');
            if (!btn || deleting) return;

            const url = btn.dataset.url;
            const id  = btn.dataset.id;
            if (!url || !id) return;

            const confirmed = await (window.Swal?.fire({
            icon: 'warning',
            title: '¿Eliminar venta?',
            html: 'Se eliminarán todos los <b>detalles</b> y se <b>repondrá el stock</b>.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            }).then(r => r.isConfirmed));
            if (!confirmed) return;

            deleting = true;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const ctrl = new AbortController();
            const timer = setTimeout(() => ctrl.abort(), 10000);

            try {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrf(),
                },
                credentials: 'same-origin',
                signal: ctrl.signal
            });

            let data = null;
            try { data = await res.clone().json(); } catch {}

            if (!res.ok || data?.ok === false) {
                const msg = (data && data.message) || `Error ${res.status}`;
                throw new Error(msg);
            }

            await window.Swal?.fire({
                icon: 'success',
                title: 'Venta eliminada',
                timer: 1200,
                showConfirmButton: false
            });

            const row = btn.closest('tr');
            if (row) row.remove();
            reloadListFixingPagination(row);

            } catch (err) {
            const msg = err?.name === 'AbortError'
                ? 'Se agotó el tiempo de espera.'
                : (err?.message || 'No se pudo eliminar la venta.');
            window.Swal?.fire({ icon: 'error', title: 'Error', text: msg });
            } finally {
            clearTimeout(timer);
            deleting = false;
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            }
        });
        })();
    </script>
    <script>
        //iniciador global para que no se quede sin funciones los modales
        window.initVentasPage = function () {
            // re-bind / re-init lo necesario dentro de #main-content
            document.querySelectorAll('div[id^="modalEditarVenta"]').forEach(modal => {
            const vid = modal.id.replace('modalEditarVenta','');
            // tus inicializadores existentes:
            if (window.initProductoUI)        window.initProductoUI(vid);
            if (window.initProductoSubtotal)  window.initProductoSubtotal(vid);
            if (window.initBotonAgregar)      window.initBotonAgregar(vid);
            });
        };

        // al cargar la página por primera vez
        document.addEventListener('DOMContentLoaded', () => {
            window.initVentasPage?.();
        });
    </script>
@endpush