@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Detalle de compras</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route(auth()->user()->role . '.compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalCrearDetalleCompraEmpleado">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Detalle de Compra
        </button>
    </div>

    <!-- Tabla -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Detalles de Compra</h6>
        </div>
        <div class="card-body">
            @if($detalles->isEmpty())
                <p class="text-center text-muted">No hay detalles de compra registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Compra ID</th>
                                <th>Proveedor</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->id }}</td>
                                    <td>{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                                    <td>{{ $detalle->id_compra }}</td>
                                    <td>{{ $detalle->compra->proveedor->nombre ?? 'N/A' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td>{{ $detalle->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <!-- Editar -->
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarDetalleCompraEmpleado{{ $detalle->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Eliminar -->
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalEliminarDetalleCompraEmpleado{{ $detalle->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>   
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $detalles->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: Crear detalle de compra -->
    <div class="modal fade" id="modalCrearDetalleCompraEmpleado" tabindex="-1" role="dialog" aria-labelledby="modalCrearProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="form-crear-detalle-compra-empleado" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo detalle de compra</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Formulario directo -->
                    <div class="modal-body">
                        <input type="hidden" name="id_compra" value="{{ request('id_compra') }}">
                        <div class="form-group">
                            <label for="id_producto">Producto</label>
                            <select name="id_producto" class="form-control" required>
                                <option value="">-- Selecciona un producto --</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-producto"></span>
                        </div>
                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="1" step="1" required>
                            <span class="text-danger error-cantidad"></span>
                        </div>
                        <div class="form-group">
                            <label for="precio_unitario">Precio Unitario</label>
                            <input type="number" name="precio_unitario" class="form-control" step="0.1" min="0" required>
                            <span class="text-danger error-precio_unitario"></span>
                        </div>
                        <div class="form-group">
                            <label>Subtotal</label>
                            <p class="form-control bg-light mb-2" id="crear-subtotal-mostrar-empleado">S/ 0.00</p>
                            <input type="hidden" name="subtotal" id="crear-subtotal-enviar-empleado">
                            <span class="text-danger error-subtotal"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-nuevo-detalle-compra-empleado" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Editar detalle de compras -->
    @foreach ($detalles as $detalle)
    <div class="modal fade" id="modalEditarDetalleCompraEmpleado{{ $detalle->id}}" tabindex="-1" role="dialog" aria-labelledby="modalEditarDetalleLabel{{ $detalle->id}}" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form-editar-detalle-compra-empleado" method="POST" data-id="{{ $detalle->id }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id_detalle_compra" value="{{ $detalle->id }}">
                    <input type="hidden" name="id_compra" value="{{ $detalle->id_compra }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar detalle de compra</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="id_producto">Producto</label>
                            <select name="id_producto" class="form-control id_producto" required>
                                <option value="">-- Selecciona un producto --</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ $producto->id === $detalle->id_producto ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-id_producto"></span>
                        </div>

                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control cantidad" value="{{ $detalle->cantidad }}" min="1" step="1" required>
                            <span class="text-danger error-cantidad"></span>
                        </div>

                        <div class="form-group">
                            <label for="precio_unitario">Precio Unitario</label>
                            <input type="number" name="precio_unitario" class="form-control precio_unitario" value="{{ $detalle->precio_unitario }}" step="0.1" min="0" required>
                            <span class="text-danger error-precio_unitario"></span>
                        </div>

                        <div class="form-group">
                            <label>Subtotal</label>
                            <p class="form-control bg-light mb-2" id="editar-subtotal-mostrar-empleado-{{ $detalle->id }}">S/ {{ number_format($detalle->subtotal, 2) }}</p>
                            <input type="hidden" name="subtotal" id="editar-subtotal-enviar-empleado-{{ $detalle->id }}" value="{{ $detalle->subtotal }}">
                            <span class="text-danger error-subtotal"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación: ELIMINAR DETALLE -->
    <div class="modal fade" id="modalEliminarDetalleCompraEmpleado{{ $detalle->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelEliminarDetalle{{ $detalle->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document" style="margin-top: 80px; transform: translateX(80px);">
            <div class="modal-content">
            <form class="form-eliminar-detalle-compra-empleado" data-id="{{ $detalle->id }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalLabelEliminarDetalle{{ $detalle->id }}">Eliminar detalle</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Advertencia:</strong> Esta acción eliminará el detalle de la compra.</p>
                    <p>¿Estás seguro que deseas continuar?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@push('scripts')  
    <script>
        $(document).on('input', '#form-crear-detalle-compra-empleado input[name="cantidad"], #form-crear-detalle-compra-empleado input[name="precio_unitario"]', function () {
            let cantidad = parseFloat($('#form-crear-detalle-compra-empleado input[name="cantidad"]').val()) || 0;
            let precio = parseFloat($('#form-crear-detalle-compra-empleado input[name="precio_unitario"]').val()) || 0;
            let subtotal = (cantidad * precio).toFixed(2)

            $('#crear-subtotal-mostrar-empleado').text('S/ ' + subtotal);
            $('#crear-subtotal-enviar-empleado').val(subtotal);
        });

        $(document).on('input', '.form-editar-detalle-compra-empleado input[name="cantidad"], .form-editar-detalle-compra-empleado input[name="precio_unitario"]', function () {
            let form = $(this).closest('form');
            let id = form.data('id');
            let cantidad = parseFloat(form.find('[name="cantidad"]').val()) || 0;
            let precio = parseFloat(form.find('[name="precio_unitario"]').val()) || 0;
            let subtotal = (cantidad * precio).toFixed(2);

            $('#editar-subtotal-mostrar-empleado-' + id).text('S/ ' + subtotal);
            $('#editar-subtotal-enviar-empleado-' + id).val(subtotal);
        });

     
        // CREAR detalle de compra
        $(document).ready(function () {
            // Envío AJAX
            $(document).on('submit', '#form-crear-detalle-compra-empleado', function (e) {
                e.preventDefault();

                let form = $(this);
                let url = "{{ route(auth()->user()->role . '.detalleCompras.store') }}";

                // 👇 Esto asegura que si el usuario no tocó nada, se calcule de todas formas
                let cantidad = parseFloat(form.find('[name="cantidad"]').val()) || 0;
                let precio = parseFloat(form.find('[name="precio_unitario"]').val()) || 0;
                let subtotal = (cantidad * precio).toFixed(2);
                $('#subtotal-mostrar').text('S/ ' + subtotal);
                $('#subtotal-enviar').val(subtotal);

                let formData = new FormData(this);

                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.text-danger').text('');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#modalCrearDetalleCompraEmpleado').modal('hide');

                        // Recargar contenido dinámico
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;
                            $.each(errores, function (key, value) {
                                let input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                form.find('.error-' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo registrar el detalle de compra.'
                            });
                        }
                    }
                });
            });
        });

        // EDITAR detalle de compra
        $(document).ready(function () {
            // Evento al editar campos de cantidad o precio
            $(document).on('input', '.form-editar-detalle-compra-empleado input[name="cantidad"], .form-editar-detalle-compra-empleado input[name="precio_unitario"]', function () {
                let form = $(this).closest('form');
                let id = form.find('input[name="id_detalle_compra"]').val();
                let url = `/empleado/detalleCompras/${id}`;

                let cantidad = parseFloat(form.find('[name="cantidad"]').val()) || 0;
                let precio = parseFloat(form.find('[name="precio_unitario"]').val()) || 0;
                let subtotal = (cantidad * precio).toFixed(2);

                // Actualiza visual y valor oculto
                $('#editar-subtotal-mostrar-' + id).text('S/ ' + subtotal);
                $('#editar-subtotal-enviar-' + id).val(subtotal);
            });

            // Evento submit AJAX del formulario
            $(document).on('submit', '.form-editar-detalle-compra-empleado', function (e) {
                e.preventDefault();

                let form = $(this);
                let id = form.data('id');

                // Detectar si estás en 'admin' o 'empleado'
                let role = window.location.pathname.includes('/admin') ? 'admin' : 'empleado';
                let url = `/${role}/detalleCompras/${id}`;
                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                // Calcular subtotal por seguridad
                let cantidad = parseFloat(form.find('[name="cantidad"]').val()) || 0;
                let precio = parseFloat(form.find('[name="precio_unitario"]').val()) || 0;
                let subtotal = (cantidad * precio).toFixed(2);
                $('#editar-subtotal-mostrar-' + id).text('S/ ' + subtotal);
                $('#editar-subtotal-enviar-' + id).val(subtotal);

                // Limpiar errores anteriores
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.text-danger').text('');

                // Enviar AJAX
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function () {
                        $('.modal').modal('hide');
                        Swal.fire('¡Éxito!', 'Detalle actualizado correctamente', 'success');

                        // Recargar sección #main-content
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;
                            $.each(errores, function (key, value) {
                                let input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                form.find('.error-' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire('Error', 'No se pudo actualizar el detalle de compra.', 'error');
                        }
                    }
                });
            });
        });

        //Cancelar Crear Detalle de Compra
        $(document).on('click', '.btn-cancelar-nuevo-detalle-compra-empleado', function () {
            let form = $('#form-crear-detalle-compra-empleado');
            form[0].reset();
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.text-danger').text('');
            $('#crear-subtotal-mostrar-empleado').text('S/ 0.00');
            $('#crear-subtotal-enviar-empleado').val('');
        });

        // Cancelar edición de detalle de compra
        $(document).on('click', '.modal[id^="modalEditarDetalleCompraEmpleado"] .btn.btn-secondary[data-dismiss="modal"]', function () {
            const $modal = $(this).closest('.modal');
            const $form  = $modal.find('.form-editar-detalle-compra-empleado');
            const id     = $form.data('id');
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.text-danger').text('');
            const cantidadDef = parseFloat($form.find('[name="cantidad"]')[0].defaultValue || 0);
            const precioDef   = parseFloat($form.find('[name="precio_unitario"]')[0].defaultValue || 0);
            const subtotalDef = (cantidadDef * precioDef).toFixed(2);

            $('#editar-subtotal-mostrar-empleado-' + id).text('S/ ' + subtotalDef);
            $('#editar-subtotal-enviar-empleado-' + id).val(subtotalDef);
        });

        // Eliminar DETALLE de compra (empleado/admin) por AJAX
        $(document).on('submit', '.form-eliminar-detalle-compra-empleado', function (e) {
        e.preventDefault();

        const $form = $(this);
        const id    = $form.data('id');
        const role  = "{{ auth()->user()->role }}";
        const url   = `/${role}/detalleCompras/${id}`;

            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize(),
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'DELETE'
                },
                success: function () {
                $(`#modalEliminarDetalleCompraEmpleado${id}`).modal('hide');
                // Recargar solo el contenido principal (o usa location.reload(); si prefieres)
                $.get(window.location.href, function (data) {
                    $('#main-content').html($(data).find('#main-content').html());
                });
                },
                error: function () {
                Swal.fire('Error', 'No se pudo eliminar el detalle de compra.', 'error');
                },
            });
        });

</script>
@endpush
