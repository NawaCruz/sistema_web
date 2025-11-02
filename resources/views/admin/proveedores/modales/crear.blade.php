<div class="modal fade" id="modalCrearProveedorAdmin" tabindex="-1" role="dialog" aria-labelledby="modalCrearProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form id="form-crear-proveedor-admin" method="POST">
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
                        <button type="button" class="btn btn-secondary btn-cancelar-nuevo-proveedor-admin" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>