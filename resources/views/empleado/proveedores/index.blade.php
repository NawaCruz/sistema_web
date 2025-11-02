@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Gestión de Proveedores</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botón para abrir el modal -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalCrearProveedor">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Proveedor
        </button>
    </div>

    <!-- Tabla -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Proveedores</h6>
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
                                <th>Nombre</th>
                                <th>RUC</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proveedores as $proveedor)
                                <tr>
                                    <td>{{ $proveedor->id }}</td>
                                    <td>{{ $proveedor->nombre }}</td>
                                    <td>{{ $proveedor->ruc }}</td>
                                    <td>{{ $proveedor->telefono }}</td>
                                    <td>{{ $proveedor->correo }}</td>
                                    <td>{{ $proveedor->direccion }}</td>
                                    <td>{{ $proveedor->contacto }}</td>
                                    <td>{{ $proveedor->estado }}</td>
                                    <td>
                                        <!-- Editar -->
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarProveedor{{ $proveedor->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Eliminar -->
                                        <!-- Botón que abre el modal -->
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmarEliminar{{ $proveedor->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>   
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $proveedores->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: Crear proveedor -->
    <div class="modal fade" id="modalCrearProveedor" tabindex="-1" role="dialog" aria-labelledby="modalCrearProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="form-crear-proveedor" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Proveedor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Formulario directo -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                            <span class="text-danger error-nombre"></span>
                        </div>
                        <div class="form-group">
                            <label for="ruc">RUC</label>
                            <input type="text" name="ruc" class="form-control" required>
                            <span class="text-danger error-ruc"></span>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required>
                            <span class="text-danger error-telefono"></span>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                            <span class="text-danger error-correo"></span>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" class="form-control" required>
                            <span class="text-danger error-direccion"></span>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" name="contacto" class="form-control" required>
                            <span class="text-danger error-contacto"></span>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" class="form-control" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            <span class="text-danger error-estado"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-nuevo-proveedor" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Editar proveedor -->
    @foreach ($proveedores as $proveedor)
    <div class="modal fade" id="modalEditarProveedor{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditarProveedorLabel{{ $proveedor->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form-editar-proveedor" method="POST" data-id="{{ $proveedor->id }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarProveedorLabel{{ $proveedor->id }}">Editar Proveedor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Campos del formulario -->
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $proveedor->nombre }}" required>
                            <small class="text-danger error-nombre"></small>
                        </div>
                        <div class="form-group">
                            <label for="ruc">RUC</label>
                            <input type="text" name="ruc" class="form-control" value="{{ $proveedor->ruc }}" required>
                            <small class="text-danger error-ruc"></small>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $proveedor->telefono }}" required>
                            <small class="text-danger error-telefono"></small>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" name="correo" class="form-control" value="{{ $proveedor->correo }}" required>
                            <small class="text-danger error-correo"></small>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $proveedor->direccion }}" required>
                            <small class="text-danger error-direccion"></small>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" name="contacto" class="form-control" value="{{ $proveedor->contacto }}" required>
                            <small class="text-danger error-contacto"></small>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" class="form-control" required>
                                <option value="Activo" {{ $proveedor->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ $proveedor->estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <small class="text-danger error-estado"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Actualizar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-edicion" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmarEliminar{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelEliminar{{ $proveedor->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document" style="margin-top: 80px; transform: translateX(80px);">
            <div class="modal-content">
            <form class="form-eliminar-proveedor" data-id="{{ $proveedor->id }}">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabelEliminar{{ $proveedor->id }}">Eliminar proveedor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <p><strong>Advertencia:</strong> Este proveedor tiene productos relacionados. Si continúas, se eliminará el proveedor y los productos quedarán sin proveedor asignado.</p>
                <p>¿Estás seguro que deseas continuar?</p>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Eliminar de todas formas</button>
                <button type="button" class="btn btn-secondary btn-cancelar-eliminar-proveedor-empleado" data-dismiss="modal">Cancelar</button>

                </div>
            </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')  
    <script>
        $(document).ready(function () {
            // CREAR proveedor
            $(document).on('submit', '#form-crear-proveedor', function (e) {
                e.preventDefault();

                let form = $(this);
                let url = "{{ route(auth()->user()->role . '.proveedores.store') }}";
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
                        // ✅ Cerrar el modal
                        $('#modalCrearProveedor').modal('hide');

                        // ✅ Limpiar formulario
                        form[0].reset();

                        // ✅ Recargar solo la tabla (asumiendo que está en #main-content)
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Algo salió mal al guardar el proveedor.'
                            });
                        }
                    }
                });
            });


            // EDITAR proveedor
            $(document).on('submit', '.form-editar-proveedor', function (e) {
                e.preventDefault();

                let form = $(this);
                let id = form.data('id');
                let rol = "{{ auth()->user()->role }}";
                let url = `/${rol}/proveedores/${id}`; // PUT a esta URL
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
                        $('#modalEditarProveedor' + id).modal('hide');

                        // Recargar tabla o sección
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Proveedor actualizado',
                            showConfirmButton: false,
                            timer: 1500
                        });
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
                            Swal.fire('Error', 'Ocurrió un error al actualizar.', 'error');
                        }
                    }
                });
            });

            // Cancelar edición de proveedor
            $(document).on('click', '.btn-cancelar-edicion', function () {
                let modal = $(this).closest('.modal');
                modal.find('form')[0].reset(); // Limpia campos
                modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
                modal.find('.text-danger').text(''); // Limpia mensajes de error
            });

            // Cancelar creación de proveedor
            $(document).on('click', '.btn-cancelar-nuevo-proveedor', function () {
                let modal = $('#modalCrearProveedor');
                modal.find('form')[0].reset(); // Limpia campos
                modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
                modal.find('.text-danger').text(''); // Limpia mensajes de error
            });
        });

        // ELIMINAR proveedor
        $(document).ready(function () {
            // ELIMINAR proveedor con AJAX
            $(document).on('submit', '.form-eliminar-proveedor', function (e) {
                e.preventDefault();

                let form = $(this);
                let id = form.data('id');
                let rol = "{{ auth()->user()->role }}"; // debería ser 'empleado'
                let url = `/${rol}/proveedores/${id}`;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function () {
                        $('#confirmarEliminar' + id).modal('hide');
                        form[0].reset();
                        // Recargar solo el contenido principal
                        $.get(window.location.href, function (data) {
                            $('#main-content').html($(data).find('#main-content').html());
                        });
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo eliminar el proveedor.', 'error');
                    }
                });
            });

            // Cancelar
            $(document).on('click', '.btn-cancelar-eliminar-proveedor-empleado', function () {
                let modal = $(this).closest('.modal');
                modal.find('form')[0].reset();
            });
        });
    </script>
@endpush
