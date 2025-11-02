<!-- MODAL: Crear producto (ADMIN) -->
<div class="modal fade" id="modalCrearProductoAdmin" tabindex="-1" role="dialog" aria-labelledby="modalCrearProductoAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form id="form-crear-producto-admin" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto (Admin)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="nombre_admin">Nombre</label>
                        <input type="text" name="nombre" id="nombre_admin" class="form-control" required>
                        <span class="text-danger error-nombre"></span>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label for="descripcion_admin">Descripción</label>
                        <textarea name="descripcion" id="descripcion_admin" class="form-control" rows="2" required></textarea>
                        <span class="text-danger error-descripcion"></span>
                    </div>

                    <!-- Precio Compra -->
                    <div class="form-group">
                        <label for="precio_compra_admin">Precio de Compra</label>
                        <input type="number" name="precio_compra" id="precio_compra_admin" step="0.1" class="form-control">
                        <span class="text-danger error-precio_compra"></span>
                    </div>

                    <!-- Precio Venta -->
                    <div class="form-group">
                        <label for="precio_venta_admin">Precio de Venta</label>
                        <input type="number" name="precio_venta" id="precio_venta_admin" step="0.1" class="form-control" required>
                        <span class="text-danger error-precio_venta"></span>
                    </div>

                    <!-- Stock -->
                    <div class="form-group">
                        <label for="stock_admin">Stock</label>
                        <input type="number" name="stock" id="stock_admin" class="form-control" required>
                        <span class="text-danger error-stock"></span>
                    </div>

                    <!-- Descuento -->
                    <div class="form-group">
                        <label for="descuento_admin">Descuento (S/. )</label>
                        <input type="number" name="descuento" id="descuento_admin" step="0.01" class="form-control">
                        <span class="text-danger error-descuento"></span>
                    </div>

                    <!-- Categoría -->
                    <div class="form-group">
                        <label for="categoria_id_admin">Categoría</label>
                        <select name="categoria_id" id="categoria_id_admin" class="form-control" required>
                            <option value="">-- Selecciona --</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-categoria_id"></span>
                    </div>

                    <!-- Proveedor -->
                    <div class="form-group">
                        <label for="proveedor_id_admin">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id_admin" class="form-control">
                            <option value="">-- Selecciona --</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-proveedor_id"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary btn-cancelar-crear-producto-admin" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
