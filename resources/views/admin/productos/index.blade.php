@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Gestión de Productos</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    {{-- boton para abrir el modal de creación de producto --}}
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalCrearProductoAdmin">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Producto
        </button>
    </div>
    {{-- tabla --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Productos</h6>
        </div>
        <div class="card-body">
            @if($productos->isEmpty())
                <p class="text-center text-muted">No hay productos registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                                <th>Descuento (S/. )</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr>
                                    <td>{{ $producto->id }}</td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                                    <td>{{ $producto->proveedor->nombre ?? 'Sin proveedor' }}</td>
                                    <td>S/ {{ number_format($producto->precio_compra, 2) }}</td>
                                    <td>S/ {{ number_format($producto->precio_venta, 2) }}</td>
                                    <td>{{ $producto->stock }}</td>
                                    <td>S/ {{ number_format($producto->descuento, 2) }}</td>
                                    <td>
                                        {{-- boton editar y eliminar --}}
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditarProductoAdmin{{ $producto->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalEliminarProductoAdmin{{ $producto->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $productos->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    @include('admin.productos.modales.crear')
    @include('admin.productos.modales.editar')
    @include('admin.productos.modales.eliminar')
@endsection

@push('scripts')
  <script>
      $(document).ready(function () {
          // CREAR PRODUCTO
          $(document).on('submit', '#form-crear-producto-admin', function (e) {
              e.preventDefault();

              let form = $(this);
              let url = "{{ route(auth()->user()->role . '.productos.store') }}";
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
                      $('#modalCrearProductoAdmin').modal('hide');

                      // ✅ Limpiar formulario
                      form[0].reset();

                      // ✅ Recargar solo el content
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
                          Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: 'Ocurrió un error al guardar el producto.'
                          });
                      }
                  }
              });
          });

          // CANCELAR CREACIÓN
          $(document).on('click', '.btn-cancelar-crear-producto-admin', function () {
              let modal = $('#modalCrearProductoAdmin');
              modal.find('form')[0].reset(); // Limpiar campos
              modal.find('.is-invalid').removeClass('is-invalid'); // Limpiar errores
              modal.find('.text-danger').text(''); // Limpiar mensajes
          });
      });

    $(document).ready(function () {

        // EDITAR PRODUCTO
        $(document).on('submit', '.form-editar-producto-admin', function (e) {
            e.preventDefault();

            let form = $(this);
            let id = form.data('id');
            let rol = "{{ auth()->user()->role }}";
            let url = `/${rol}/productos/${id}`;
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
                    // Cerrar el modal
                    $('#modalEditarProductoAdmin' + id).modal('hide');

                    // Recargar sección actual
                    $.get(window.location.href, function (data) {
                        $('#main-content').html($(data).find('#main-content').html());
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

        // CANCELAR EDICIÓN
        $(document).on('click', '.btn-cancelar-edicion-admin', function () {
            let modal = $(this).closest('.modal');
            modal.find('form')[0].reset(); // Limpiar campos
            modal.find('.is-invalid').removeClass('is-invalid'); // Limpiar errores
            modal.find('.text-danger').text(''); // Limpiar mensajes
        });

    });

    $(document).ready(function () {
        // ELIMINAR PRODUCTO (ADMIN)
        $(document).on('submit', '.form-eliminar-producto-admin', function (e) {
            e.preventDefault();

            let form = $(this);
            let id = form.data('id');
            let rol = "{{ auth()->user()->role }}";
            let url = `/${rol}/productos/${id}`;

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-HTTP-Method-Override': 'DELETE'
                },
                success: function (response) {
                    // ✅ Cerrar el modal específico
                    $('#modalEliminarProductoAdmin' + id).modal('hide');

                    // ✅ Recargar el #main-content sin refrescar toda la página
                    $.get(window.location.href, function (data) {
                        $('#main-content').html($(data).find('#main-content').html());
                    });

                    // ✅ Mensaje opcional de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'Producto eliminado correctamente.'
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el producto.'
                    });
                }
            });
        });

        // CANCELAR ELIMINACIÓN
        $(document).on('click', '.btn-cancelar-eliminar-admin', function () {
            let modal = $(this).closest('.modal');
            modal.find('form')[0].reset(); // por si acaso
        });
    });

  </script>

@endpush
