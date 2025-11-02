<!-- MODAL: Editar proveedor -->
<div class="modal fade" id="modalEditarProveedorAdmin{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditarProveedorLabel{{ $proveedor->id }}" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form class="form-editar-proveedor-admin" method="POST" data-id="{{ $proveedor->id }}">
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
                    <button type="button" class="btn btn-secondary btn-cancelar-edicion-admin" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
