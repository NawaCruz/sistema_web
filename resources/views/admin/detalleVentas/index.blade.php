@extends('layouts.app')

@section('title', 'Detalle de Ventas')

@section('content')
<div class="container-fluid">

  {{-- Título + resumen --}}
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0 text-dark">
      <i class="fas fa-receipt mr-2 text-primary"></i> Detalle de Ventas
    </h4>
    @if(isset($totalFiltrado))
      <span class="badge badge-pill badge-primary">Registros: {{ $totalFiltrado }}</span>
    @endif
  </div>

  {{-- Filtros --}}
  <div class="card shadow-sm mb-3 border-left-primary">
    <div class="card-body py-2">
      {{-- Formulario principal de filtros --}}
      <form method="GET" action="{{ route(auth()->user()->role . '.detalleVentas.index') }}" id="formFiltros" class="form-row align-items-end">

        {{-- Cliente --}}
        <div class="form-group col-md-3">
          <label class="mb-1">Cliente</label>
          <input list="clientes" name="cliente" class="form-control"
                placeholder="Escribe nombre, apellido o DNI"
                value="{{ request('cliente') }}">
          <datalist id="clientes">
            @foreach($clientes ?? [] as $c)
              @php $dni = $c->dni ?: 'SIN-DNI'; @endphp
              <option value="{{ $dni }} - {{ $c->nombre }} {{ $c->apellido }}">
            @endforeach
          </datalist>
        </div>

        {{-- Producto --}}
        <div class="form-group col-md-3">
          <label class="mb-1">Producto</label>
          <input list="productos" name="producto" class="form-control"
                placeholder="Selecciona producto"
                value="{{ request('producto') }}">
          <datalist id="productos">
            @foreach($productos ?? [] as $producto)
              <option value="{{ $producto->nombre }}">
            @endforeach
          </datalist>
        </div>

        {{-- Usuario --}}
        <div class="form-group col-md-2">
          <label class="mb-1">Usuario</label>
          <select name="usuario" class="form-control">
            <option value="">Selecciona usuario</option>
            @foreach($usuarios ?? [] as $usr)
              <option value="{{ $usr->id }}" {{ request('usuario') == $usr->id ? 'selected' : '' }}>
                {{ $usr->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Desde --}}
        <div class="form-group col-md-2">
          <label class="mb-1">Desde</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
            </div>
            <input type="text" id="fromDate" name="from"
                  class="form-control"
                  placeholder="DD-MM-AAAA"
                  value="{{ request('from') }}">
          </div>
        </div>

        {{-- Hasta --}}
        <div class="form-group col-md-2">
          <label class="mb-1">Hasta</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
            </div>
            <input type="text" id="toDate" name="to"
                  class="form-control"
                  placeholder="DD-MM-AAAA"
                  value="{{ request('to') }}">
          </div>
        </div>

        <div class="form-group col-md-12 d-flex justify-content-between">
          <a href="{{ route(auth()->user()->role . '.detalleVentas.exportar', request()->query()) }}"
            class="btn btn-sm btn-success">
            <i class="fas fa-file-excel mr-1"></i> Exportar Excel
          </a>
          
          <div>
            <button class="btn btn-sm btn-primary mr-2" type="submit">
              <i class="fas fa-search mr-1"></i> Buscar
            </button>
            <button type="button" class="btn btn-sm btn-success shadow-sm"
                    data-toggle="modal" data-target="#modalCrearDetalleVenta">
              <i class="fas fa-plus mr-1"></i> Añadir Detalle
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>


  {{-- Tabla --}}
  <!-- Detalle de Ventas (mismo contenedor que Ventas) -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-receipt mr-2"></i> Listado de Detalle de Ventas
      </h6>
      @if(isset($totalFiltrado))
        <span class="badge badge-primary badge-pill">Registros: {{ $totalFiltrado }}</span>
      @endif
    </div>

    <div class="card-body">
      @if($detalles->isEmpty())
        <div class="text-center py-4 text-muted">
          <i class="fas fa-inbox mr-2"></i> No hay resultados para los filtros seleccionados.
        </div>
      @else
        {{-- Tabla (SIN CAMBIOS de estilo) --}}
        <div class="card shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-striped table-hover align-middle mb-0" id="tablaDetalleVentas">
                <thead class="thead-light">
                  <tr>
                    <th class="px-2 py-2">#</th>
                    <th class="px-2 py-2">Venta</th>
                    <th class="px-2 py-2">Fecha</th>
                    <th class="px-2 py-2">Producto</th>
                    <th class="px-2 py-2 text-right">Cant.</th>
                    <th class="px-2 py-2 text-right">P. Unit (S/)</th>
                    <th class="px-2 py-2 text-right">Subtotal (S/)</th>
                    <th class="px-2 py-2">Descuento</th>
                    <th class="px-2 py-2">Cliente</th>
                    <th class="px-2 py-2">Usuario</th>
                    <th class="px-2 py-2">Pago</th>
                    <th class="px-2 py-2 text-center" style="width:120px;">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($detalles as $i => $d)
                  @php
                    $venta   = $d->venta ?? null;
                    $prod    = $d->producto ?? null;
                    $cliente = $venta->cliente ?? null;
                    $user    = $venta->user ?? null;
                  @endphp
                  <tr>
                    <td class="px-2 py-2">{{ $d->id }}</td>
                    <td class="px-2 py-2">#{{ $venta->id ?? '-' }}</td>
                    <td class="px-2 py-2">{{ optional($venta->created_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-2 py-2">{{ $prod->descripcion ?? '-' }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($d->cantidad ?? 0, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($d->precio_unitario ?? 0, 2) }}</td>
                    <td class="px-2 py-2 text-right font-weight-bold">{{ number_format($d->subtotal ?? 0, 2) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($d->descuento ?? 0, 2) }}</td>
                    <td class="px-2 py-2">
                      {{ trim(($cliente->nombre ?? '').' '.($cliente->apellido ?? '')) ?: '-' }}
                      @if(!empty($cliente->dni))
                        <small class="text-muted d-block">DNI: {{ $cliente->dni }}</small>
                      @endif
                    </td>
                    <td class="px-2 py-2">{{ $user->name ?? '-' }}</td>
                    <td class="px-2 py-2">{{ $venta->metodoPago->nombre ?? '-' }}</td>
                    <td class="px-2 py-2 text-center">
                      <div class="btn-group btn-group-sm" role="group" aria-label="acciones">
                        <button type="button" class="btn btn-warning"
                                data-toggle="modal"
                                data-target="#modalEditarVenta{{ $venta->id ?? 0 }}"
                                title="Editar venta">
                          <i class="fas fa-edit"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="12" class="text-center py-4 text-muted">
                      <i class="fas fa-inbox mr-2"></i> No hay resultados para los filtros seleccionados.
                    </td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>

          {{-- Paginación (igual que el contenedor de Ventas) --}}
          @if(method_exists($detalles, 'links'))
            <div class="card-footer py-2">
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  Mostrando {{ $detalles->firstItem() }}–{{ $detalles->lastItem() }} de {{ $detalles->total() }}
                </small>
                {{ $detalles->appends(request()->query())->links() }}
              </div>
            </div>
          @endif
        </div>
      @endif
    </div>
  </div>


@endsection
@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const fromPicker = flatpickr("#fromDate", {
      dateFormat: "d-m-Y",
      allowInput: true,
      locale: "es"   // <--- aquí el idioma
    });

    const toPicker = flatpickr("#toDate", {
      dateFormat: "d-m-Y",
      allowInput: true,
      minDate: null,
      locale: "es"   // <--- también aquí
    });

    fromPicker.config.onChange.push(function(selectedDates) {
      toPicker.set("minDate", selectedDates[0]);
    });
  });
</script>
@endpush
