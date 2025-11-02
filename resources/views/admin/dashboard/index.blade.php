@extends('layouts.app')
@section('title','Dashboard')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
<span class="d-none d-sm-inline-block text-muted">{{ now()->translatedFormat('d \\d\\e F, Y · H:i') }}</span>
</div>

{{-- ======= KPIs (SB Admin 2 style) ======= --}}
<div class="row">

<!-- Ventas Hoy -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas Hoy</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">S/ {{ number_format($ventasHoy,2) }}</div>
        </div>
        <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
        </div>
    </div>
    </div>
</div>

<!-- Ventas del Mes -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas del Mes</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">S/ {{ number_format($ventasMes,2) }}</div>
        </div>
        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
        </div>
    </div>
    </div>
</div>

<!-- Utilidad (Mes) -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Utilidad (Mes)</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">S/ {{ number_format($utilidad,2) }}</div>
            <div class="mt-1 small text-muted">
            Ing: S/ {{ number_format($ingresos,2) }} · Egr: S/ {{ number_format($egresos,2) }}
            </div>
        </div>
        <div class="col-auto"><i class="fas fa-balance-scale fa-2x text-gray-300"></i></div>
        </div>
    </div>
    </div>
</div>

<!-- Stock crítico -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-danger shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stock crítico</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stockCritico }}</div>
        </div>
        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
        </div>
    </div>
    </div>
</div>

</div>

{{-- ======= Gráficos (SB Admin 2 cards) ======= --}}
<div class="row">

<!-- Ventas por mes -->
<div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Ventas por mes (últimos 6)</h6>
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: 320px;">
        <canvas id="chartVentasMes"></canvas>
        </div>
    </div>
    </div>
</div>

<!-- Top productos -->
<div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-success">Productos más vendidos (Top 10)</h6>
        <span class="small text-muted">Unidades</span>
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: 320px;">
        <canvas id="chartTopProductos"></canvas>
        </div>
    </div>
    </div>
</div>

<!-- Stock donut -->
<div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger">Stock: Crítico/Bajo vs Suficiente</h6>
    </div>
    <div class="card-body">
        <div class="chart-pie pt-4 pb-2" style="height: 320px;">
        <canvas id="chartStock"></canvas>
        </div>
    </div>
    </div>
</div>

<!-- Ingresos vs Egresos -->
<div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info">Ingresos vs Egresos (últimos 6)</h6>
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: 320px;">
        <canvas id="chartIngEgr"></canvas>
        </div>
    </div>
    </div>
</div>

<!-- Estado de cajas -->
<div class="col-12">
    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-secondary">Estado de cajas</h6>
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: 260px;">
        <canvas id="chartCajas"></canvas>
        </div>
    </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
{{-- Chart.js (v4) – usa uno solo, ya lo tienes en tu layout; si no, activa esta línea: --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
const mk = (id, cfg) => { const el = document.getElementById(id); if(el){ new Chart(el, cfg); } };
const fmtSoles = v => new Intl.NumberFormat('es-PE',{style:'currency',currency:'PEN',maximumFractionDigits:2}).format(v);

  // Datos server-side
const ventasPorMes = @json($ventasPorMes);
const topProductos = @json($topProductos);
const stock        = @json($stock);
const ingEgr       = @json($ingEgr);
const cajas        = @json($cajas);

const c1='#4e73df', c2='#1cc88a', c3='#36b9cc', c4='#f6c23e', c5='#e74a3b';

  // Ventas por mes (área/linea)
mk('chartVentasMes', {
    type:'line',
    data:{ labels:ventasPorMes.labels, datasets:[{
    label:'Ventas', data:ventasPorMes.data, fill:true, tension:.35,
    borderColor:c1, backgroundColor:'rgba(78,115,223,.15)'
    }]},
    options:{ responsive:true, maintainAspectRatio:false,
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:(ctx)=>fmtSoles(ctx.parsed.y) }}},
    scales:{ x:{ grid:{display:false}}, y:{ grid:{color:'rgba(234, 236, 244, 1)'}} }
    }
});

  // Top productos (barras horizontales)
mk('chartTopProductos', {
    type:'bar',
    data:{ labels:topProductos.labels, datasets:[{ label:'Unidades', data:topProductos.data, backgroundColor:c2 }]},
    options:{ responsive:true, maintainAspectRatio:false, indexAxis:'y',
    plugins:{ legend:{display:false} }
    }
});

  // Stock (donut)
mk('chartStock', {
    type:'doughnut',
    data:{ labels:stock.labels, datasets:[{ data:stock.data, backgroundColor:[c5,c2] }] },
    options:{ responsive:true, maintainAspectRatio:false, cutout:'65%' }
});

  // Ingresos vs Egresos
mk('chartIngEgr', {
    type:'bar',
    data:{ labels:ingEgr.labels, datasets:[
    { label:'Ingresos', data:ingEgr.ingresos, backgroundColor:c1 },
    { label:'Egresos',  data:ingEgr.egresos,  backgroundColor:c4 }
    ]},
    options:{ responsive:true, maintainAspectRatio:false,
    plugins:{ tooltip:{ callbacks:{ label:(ctx)=>fmtSoles(ctx.parsed.y) }}},
    scales:{ y:{ beginAtZero:true } }
    }
});

  // Estado de cajas
mk('chartCajas', {
    type:'bar',
    data:{ labels:cajas.labels, datasets:[{ label:'Cajas', data:cajas.data, backgroundColor:[c2,c5] }]},
    options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false} } }
});

});
</script>
@endpush
