@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Gestión de Compras</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botón para abrir el modal -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalCrearCompraAdmin">
            <i class="fas fa-plus fa-sm text-white-50"></i> Registrar Nueva Compra
        </button>
    </div>

        <!-- Tabla -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Listado de Compras</h6>
            </div>
            <div class="card-body">
                @if($proveedores->isEmpty())
                    <p class="text-center text-muted">No hay proveedores registrados.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Proveedor</th>
                                    <th>Usuario</th>
                                    <th>Método de pago</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($compras as $compra)
                                    <tr>
                                        <td>{{ $compra->id }}</td>
                                        <td>{{ $compra->proveedor->nombre ?? '-'}}</td>
                                        <td>{{ $compra->user->name ?? '-'}}</td>
                                        <td>{{ $compra->metodoPago->nombre ?? '-' }}</td>
                                        <td>S/ {{ number_format($compra->total, 2) }}</td>
                                        <td>{{ $compra->created_at->format('d/m/Y h:i A') }}</td>
                                        <td>
                                            <!-- Ver Detalles -->
                                            <a href="{{ route(auth()->user()->role . '.detalleCompras.index') }}?id_compra={{ $compra->id }}"
                                                class="btn btn-sm btn-info" title="Ver detalles de compra">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Editar -->
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarCompraAdmin{{ $compra->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <!-- Eliminar -->
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmarEliminarCompraAdmin{{ $compra->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>   
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $compras->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>

    <!-- MODAL: Crear nueva compra -->
    <div class="modal fade" id="modalCrearCompraAdmin" tabindex="-1" role="dialog" aria-labelledby="modalCrearCompraLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="form-crear-compra-admin" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Compra</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Formulario directo -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select name="proveedor_id" class="form-control" required>
                                <option value="">-- Selecciona un proveedor --</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-proveedor"></span>
                        </div>
                        <div class="form-group">
                            <label for="usuario_visible">Usuario</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <span class="text-danger error-usuario"></span>
                        </div>
                        <div class="form-group">
                            <label for="metodo_pago_id">Método de Pago</label>
                            <select name="metodo_pago_id" id="metodo_pago_id" class="form-control" required>
                                <option value="">-- Selecciona un método de pago --</option>
                                @foreach ($metodosPago as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-metodo_pago_id"></span>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="total" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-nueva-compra-admin" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Editar compra -->
    @foreach ($compras as $compra)
    <div class="modal fade" id="modalEditarCompraAdmin{{ $compra->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditarCompraLabel{{ $compra->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form-editar-compra-admin" method="POST" data-id="{{ $compra->id }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Compra #{{ $compra->id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select name="proveedor_id" class="form-control" required>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ $compra->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-proveedor_id"></small>
                        </div>
                        <div class="form-group">
                            <label for="usuario_visible">Usuario</label>
                            <input type="text" class="form-control" value="{{ $compra->user->name ?? 'Sin usuario' }}" disabled>
                            <input type="hidden" name="user_id" value="{{ $compra->user_id }}">
                            <small class="text-danger error-user_id"></small>
                        </div>
                        <div class="form-group">
                            <label for="tipo_pago">Tipo de Pago</label>
                            <select name="metodo_pago_id" id="metodo_pago_id" class="form-control" required>
                                <option value="">-- Selecciona un método de pago --</option>
                                @foreach ($metodosPago as $metodo)
                                    <option value="{{ $metodo->id }}"
                                        {{ $compra->metodo_pago_id == $metodo->id ? 'selected' : '' }}>
                                        {{ $metodo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-tipo_pago"></small>
                        </div>
                        <div class="form-group">
                            <label for="total">Total de Compra</label>
                            <input type="number" name="total" class="form-control" step="0.1" min="0" value="{{ $compra->total }}" required>
                            <small class="text-danger error-total"></small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Actualizar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-edicion-compra-admin" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmarEliminarCompraAdmin{{ $compra->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelEliminar{{ $compra->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document" style="margin-top: 80px; transform: translateX(80px);">
            <div class="modal-content">
            <form class="form-eliminar-compra-admin" data-id="{{ $compra->id }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalLabelEliminar{{ $compra->id }}">Eliminar compra</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                    <p><strong>Advertencia:</strong> Esta acción eliminará el detalle de compra relacionado.</p>
                    <p>¿Estás seguro que deseas continuar?</p>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Eliminar de todas formas</button>
                <button type="button" class="btn btn-secondary btn-cancelar-eliminar-compra-admin" data-dismiss="modal">Cancelar</button>

                </div>
            </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
    <script>
        $(document).on('submit', '#form-crear-compra-admin', function (e) {
            e.preventDefault();

            let form = $(this);
            let url = "{{ route(auth()->user()->role . '.compras.store') }}";
            let formData = new FormData(this);

            // Limpiar errores anteriores
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.text-danger').text('');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function (response) {
                    // Cerrar el modal
                    $('#modalCrearCompraAdmin').modal('hide');
                    // Limpiar formulario
                    form[0].reset();
                    // Recargar solo la tabla (asumiendo que está en #main-content)
                    $.get(window.location.href, function (data) {
                        $('#main-content').html($(data).find('#main-content').html());
                        swal.fire({
                            title: 'Éxito',
                            text: 'Compra creada correctamente.',
                            icon: 'success',
                        });// Mostrar mensaje de éxito
                    });                    
                },

                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // Recorrer y mostrar errores por campo
                        $.each(errors, function (key, value) {
                            let input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            form.find('.error-' + key).text(value[0]);
                        });
                    } else {
                        // Otro tipo de error
                        swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al crear la compra. Por favor, inténtalo de nuevo.',
                            icon: 'error',
                        });
                    }
                }
            });
        });

        // EDITAR compra
        $(document).on('submit', '.form-editar-compra-admin', function (e) {
            e.preventDefault();

            let form = $(this);
            let id = form.data('id');
            let rol = "{{ auth()->user()->role }}";
            let url = `/${rol}/compras/${id}`; // PUT a esta URL
            let formData = new FormData(this);

            // Limpiar errores previos
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.text-danger').text('');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },

                success: function (response) {
                    // Cerrar modal
                    $('#modalEditarCompraAdmin' + id).modal('hide');
                    // Limpiar formulario
                    form[0].reset();
                    // Recargar tabla o sección
                    $.get(window.location.href, function (data) {
                        $('#main-content').html($(data).find('#main-content').html());
                    });
                    swal.fire({
                        title: 'Éxito',
                        text: 'Compra actualizada correctamente.',
                        icon: 'success',
                    });// Mostrar mensaje de éxito
                },

                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            let input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            form.find('.error-' + key).text(value[0]);
                        });
                    } else {
                        swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al actualizar la compra. Por favor, inténtalo de nuevo.',
                            icon: 'error',
                        });
                    }
                }
            });
        });

        // Cancelar edición de compra
        $(document).on('click', '.btn-cancelar-edicion-compra-admin', function () {
            let modal = $(this).closest('.modal');
            modal.find('form')[0].reset(); // Limpia campos
            modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
            modal.find('.text-danger').text(''); // Limpia mensajes de error
        });

        // Cancelar creación de compra
        $(document).on('click', '.btn-cancelar-nueva-compra-admin', function () {
            let modal = $('#modalCrearCompraAdmin');
            modal.find('form')[0].reset(); // Limpia campos
            modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
            modal.find('.text-danger').text(''); // Limpia mensajes de error
        });

        // ELIMINAR compra
        $(document).ready(function () {
            // ELIMINAR compra con AJAX
            $(document).on('submit', '.form-eliminar-compra-admin', function (e) {
                e.preventDefault();

                let form = $(this);
                let id = form.data('id');
                let rol = "{{ auth()->user()->role }}"; // debería ser 'empleado'
                let url = `/${rol}/compras/${id}`;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function () {
                        $('#confirmarEliminarCompraAdmin' + id).modal('hide');

                        // Recargar solo el contenido principal
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
                        });
                    },
                    error: function () {
                        alert('No se pudo eliminar la compra.');
                    }
                });
            });

            // Cancelar
            $(document).on('click', '.btn-cancelar-eliminar-proveedor-admin', function () {
                let modal = $(this).closest('.modal');
                modal.find('form')[0].reset();
            });
        });

    </script>
@endpush
