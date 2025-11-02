@extends('layouts.app') {{-- o layouts.plantilla si ese es tu master --}}

@section('title', 'Caja')

@section('content')
@php
  $role = auth()->user()->role ?? 'admin';
  $saldoCaja  = optional($cajaAbierta)->monto_total ?? optional($cajaAbierta)->monto_apertura ?? 0;
  $saldoTotal = $saldoCaja; // cámbialo si calculas total del día
@endphp

<div class="container-fluid">

  <style>
    /* Mini utilidades para el acento lila sin pelear con SB Admin */
    .text-accent{ color:#7a69f5 !important; }
    .btn-accent{ background:#7a69f5; color:#fff; border:none; }
    .btn-accent:hover{ background:#6f5ff2; color:#fff; }
    .metric{ font-size:2.25rem; font-weight:800; letter-spacing:.3px; }
    .metric-label{ text-transform:uppercase; letter-spacing:.08em; font-size:.8rem; color:#6c757d; }
    .soft{ background:#f8f9fc; border:1px solid #eaecf4; }
    .rounded-16{ border-radius:16px; }
  </style>

  {{-- Encabezado y acciones --}}
  <div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">Cierre de caja</h1>

    <div class="d-flex align-items-center">
      @if(session('ok'))
        <span class="badge badge-success mr-2">{{ session('ok') }}</span>
      @endif
      @if(session('error'))
        <span class="badge badge-danger mr-2">{{ session('error') }}</span>
      @endif

      @if($cajaAbierta)
        <span class="badge badge-success mr-2">
          <i class="fas fa-dot-circle mr-1"></i> Caja abierta por {{ $cajaAbierta->user->name ?? '—' }}
        </span>
        <button class="btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalCerrarCaja">
          <i class="fas fa-lock fa-sm text-white-50 mr-1"></i> Cerrar caja
        </button>
      @else
        <button class="btn btn-sm btn-accent shadow-sm" data-toggle="modal" data-target="#modalAbrirCaja">
          <i class="fas fa-cash-register fa-sm text-white-50 mr-1"></i> Abrir caja
        </button>
      @endif
    </div>
  </div>

  {{-- Card principal estilo SB Admin --}}
  <div class="card shadow mb-4 rounded-16">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <button class="btn btn-sm soft mr-2"><i class="fas fa-calendar-day mr-1"></i> Hoy</button>
        <div class="btn-group mr-2" role="group" aria-label="Navegación">
          <button type="button" class="btn btn-sm soft"><i class="fas fa-chevron-left"></i></button>
          <button type="button" class="btn btn-sm soft"><i class="fas fa-chevron-right"></i></button>
        </div>
        <button class="btn btn-sm soft">
          {{ now()->format('d \\de F') }} <i class="fas fa-caret-down ml-1"></i>
        </button>

        <div class="custom-control custom-checkbox ml-3">
          <input type="checkbox" class="custom-control-input" id="cajaPrincipal" checked>
          <label class="custom-control-label" for="cajaPrincipal">Caja principal</label>
        </div>
      </div>

      {{-- Filtros rápidos opcionales (se mantienen en GET) --}}
      <form class="form-inline" method="GET" action="{{ route($role.'.cajas.index') }}">
        <input type="text" name="buscador" value="{{ request('buscador') }}" class="form-control form-control-sm mr-2" placeholder="Usuario / estado">
        <input type="date" name="desde" value="{{ request('desde') }}" class="form-control form-control-sm mr-2">
        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-outline-secondary" type="submit">
          <i class="fas fa-search mr-1"></i> Buscar
        </button>
      </form>
    </div>

    <div class="card-body">

      {{-- Métricas grandes centradas --}}
      <div class="row justify-content-center text-center mb-1">
        <div class="col-6 col-md-3 mb-3">
          <div class="metric text-accent">S/ {{ number_format($saldoCaja,2) }}</div>
          <div class="metric-label">Caja</div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="metric text-accent">S/ {{ number_format($saldoTotal,2) }}</div>
          <div class="metric-label">Total</div>
        </div>
      </div>

      {{-- Link central para cerrar caja (como el mockup) --}}
      @if($cajaAbierta)
        <div class="text-center mb-4">
          <a href="#" class="font-weight-bold text-accent" data-toggle="modal" data-target="#modalCerrarCaja">
            Cerrar caja
          </a>
        </div>
      @endif

      {{-- Tabla "Movimientos y facturas" con estilo SB Admin --}}
      <h6 class="text-muted mb-2">Movimientos y facturas</h6>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="thead-light">
            <tr>
              <th style="min-width:140px">Usuario</th>                 {{-- id_user (relación user) --}}
              <th class="text-right">Monto apertura</th>              {{-- monto_apertura --}}
              <th class="text-right">Monto total</th>                 {{-- monto_total --}}
              <th class="text-right">Monto cierre</th>                {{-- monto_cierre --}}
              <th>Estado</th>                                         {{-- estado --}}
              <th style="min-width:160px">Fecha apertura</th>         {{-- created_at --}}
              <th style="min-width:160px">Fecha cierre</th>           {{-- updated_at --}}
              <th class="text-center" style="width:70px">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($cajas ?? [] as $c)
              <tr @class(['table-success' => $c->estado === 'abierta'])>
                <td>{{ $c->user->name ?? 'Usuario #'.$c->id_user }}</td>

                <td class="text-right">S/ {{ number_format((float)$c->monto_apertura, 2) }}</td>
                <td class="text-right">S/ {{ number_format((float)$c->monto_total, 2) }}</td>
                <td class="text-right">
                  @if(!is_null($c->monto_cierre))
                    S/ {{ number_format((float)$c->monto_cierre, 2) }}
                  @else
                    —
                  @endif
                </td>

                <td>
                  @switch(strtolower($c->estado))
                    @case('Abierta')
                      <span class="badge badge-success">Abierta</span>
                      @break
                    @case('Cerrada')
                      <span class="badge badge-secondary">Cerrada</span>
                      @break
                    @default
                      <span class="badge badge-light">{{ ucfirst($c->estado) }}</span>
                  @endswitch
                </td>

                <td>{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  {{ $c->estado === 'Cerrada' ? ($c->updated_at?->format('d/m/Y H:i') ?? '—') : '—' }}
                </td>

                <td class="text-center">
                  {{-- ajusta la ruta si tu PK es id_caja o id --}}
                  <a href="{{ route($role.'.cajas.show', $c->id_caja ?? $c->id) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No hay registros aún.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(isset($cajas))
        <div class="d-flex justify-content-end">
          {{ $cajas->appends(request()->query())->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ======================= MODALES ======================= --}}

{{-- Modal Abrir Caja (simple, SB Admin) --}}
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <form method="POST" action="{{ route($role.'.cajas.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-cash-register mr-2"></i> Abrir caja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label class="mb-1">Monto de apertura</label>
        <input type="number" step="0.01" min="0" name="monto_apertura" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-accent">Abrir caja</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Cerrar Caja --}}
@if(($cajaAbierta))
<div class="modal fade" id="modalCerrarCaja" tabindex="-1" role="dialog" aria-labelledby="modalCerrarCajaLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <form method="POST" action="{{ route($role.'.cajas.update', $cajaAbierta->id_caja) }}" class="modal-content">
      @csrf
      @method('PUT')

      <div class="modal-header">
        <h5 class="modal-title" id="modalCerrarCajaLabel">
          <i class="fas fa-lock mr-2"></i> Cerrar caja #{{ $cajaAbierta->user->name ?? '—' }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Monto apertura</label>
            <input type="text" class="form-control" value="S/ {{ number_format($cajaAbierta->monto_apertura,2) }}" readonly>
          </div>
          <div class="form-group col-md-6">
            <label>Fecha apertura</label>
            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d/m/Y H:i') }}" readonly>
          </div>
        </div>

        {{-- Campo de ingreso de conteo final (caja física) --}}
        <div class="form-group">
          <label for="monto_cierre">Monto de cierre (conteo físico) (S/)</label>
          <input type="number" step="0.01" min="0" class="form-control" id="monto_cierre" name="monto_cierre" required>
          <small class="form-text text-muted">
            Es el total contado en efectivo + electrónicos que declaras al cerrar.
          </small>
        </div>

        <div class="form-group">
          <label for="observacion_cierre">Observación (opcional)</label>
          <textarea class="form-control" id="observacion_cierre" name="observacion_cierre" rows="2" placeholder="Notas, diferencias, etc."></textarea>
        </div>

        <div class="alert alert-info mb-0">
          <i class="fas fa-balance-scale mr-1"></i>
          La diferencia se calculará en backend comparando movimientos vs. conteo. Podrás verla en el detalle.
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">
          <i class="fas fa-check mr-1"></i> Confirmar cierre
        </button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection
