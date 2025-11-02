@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Gestión de Proveedores</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botón para abrir el modal -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalCrearProveedorAdmin">
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
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarProveedorAdmin{{ $proveedor->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Eliminar -->
                                        <!-- Botón que abre el modal -->
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmarEliminarAdmin{{ $proveedor->id }}">
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
    @foreach ($proveedores as $proveedor)
        @include('admin.proveedores.modales.crear')
        @include('admin.proveedores.modales.editar')
        @include('admin.proveedores.modales.eliminar')
    @endforeach
@endsection

@push('scripts')
    <script>
    $(document).ready(function () {

        // CREAR proveedor
        $(document).on('submit', '#form-crear-proveedor-admin', function (e) {
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
                    $('#modalCrearProveedorAdmin').modal('hide');

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

        // Cancelar creación de proveedor
        $(document).on('click', '.btn-cancelar-nuevo-proveedor-admin', function () {
            let modal = $('#modalCrearProveedorAdmin');
            modal.find('form')[0].reset(); // Limpia campos
            modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
            modal.find('.text-danger').text(''); // Limpia mensajes de error
        });
    });

    // EDITAR proveedor
    $(document).on('submit', '.form-editar-proveedor-admin', function (e) {
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
                $('#modalEditarProveedorAdmin' + id).modal('hide');

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

        // Cancelar edición de proveedor
        $(document).on('click', '.btn-cancelar-edicion-admin', function () {
            let modal = $(this).closest('.modal');
            modal.find('form')[0].reset(); // Limpia campos
            modal.find('.is-invalid').removeClass('is-invalid'); // Limpia errores
            modal.find('.text-danger').text(''); // Limpia mensajes de error
        });
    }); 


    // ELIMINAR proveedor
    $(document).ready(function () {
        // ELIMINAR proveedor con AJAX
        $(document).on('submit', '.form-eliminar-proveedor-admin', function (e) {
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
                    $('#confirmarEliminarAdmin' + id).modal('hide');

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
        $(document).on('click', '.btn-cancelar-eliminar-proveedor-admin', function () {
            let modal = $(this).closest('.modal');
            modal.find('form')[0].reset();
        });
    });
    </script>
@endpush
