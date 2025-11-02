@foreach ($productos as $producto)
    <!-- MODAL: Editar producto (ADMIN) -->
    <div class="modal fade" id="modalEditarProductoAdmin{{ $producto->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditarProductoAdminLabel{{ $producto->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form-editar-producto-admin" data-id="{{ $producto->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Producto (Admin)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="nombre_admin_{{ $producto->id }}">Nombre</label>
                            <input type="text" name="nombre" id="nombre_admin_{{ $producto->id }}" class="form-control" value="{{ $producto->nombre }}" required>
                            <span class="text-danger error-nombre"></span>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="descripcion_admin_{{ $producto->id }}">Descripción</label>
                            <textarea name="descripcion" id="descripcion_admin_{{ $producto->id }}" class="form-control" rows="2" required>{{ $producto->descripcion }}</textarea>
                            <span class="text-danger error-descripcion"></span>
                        </div>

                        <!-- Precio Compra -->
                        <div class="form-group">
                            <label for="precio_compra_admin_{{ $producto->id }}">Precio de Compra</label>
                            <input type="number" name="precio_compra" id="precio_compra_admin_{{ $producto->id }}" step="0.1" class="form-control" value="{{ $producto->precio_compra }}">
                            <span class="text-danger error-precio_compra"></span>
                        </div>

                        <!-- Precio Venta -->
                        <div class="form-group">
                            <label for="precio_venta_admin_{{ $producto->id }}">Precio de Venta</label>
                            <input type="number" name="precio_venta" id="precio_venta_admin_{{ $producto->id }}" step="0.1" class="form-control" value="{{ $producto->precio_venta }}" required>
                            <span class="text-danger error-precio_venta"></span>
                        </div>

                        <!-- Stock -->
                        <div class="form-group">
                            <label for="stock_admin_{{ $producto->id }}">Stock</label>
                            <input type="number" name="stock" id="stock_admin_{{ $producto->id }}" class="form-control" value="{{ $producto->stock }}" required>
                            <span class="text-danger error-stock"></span>
                        </div>

                        <!-- Descuento -->
                        <div class="form-group">
                            <label for="descuento_admin_{{ $producto->id }}">Descuento (S/. )</label>
                            <input type="number" name="descuento" id="descuento_admin_{{ $producto->id }}" step="0.01" class="form-control" value="{{ $producto->descuento }}">
                            <span class="text-danger error-descuento"></span>
                        </div>

                        <!-- Categoría -->
                        <div class="form-group">
                            <label for="categoria_id_admin_{{ $producto->id }}">Categoría</label>
                            <select name="categoria_id" id="categoria_id_admin_{{ $producto->id }}" class="form-control" required>
                                <option value="">-- Selecciona --</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-categoria_id"></span>
                        </div>

                        <!-- Proveedor -->
                        <div class="form-group">
                            <label for="proveedor_id_admin_{{ $producto->id }}">Proveedor</label>
                            <select name="proveedor_id" id="proveedor_id_admin_{{ $producto->id }}" class="form-control">
                                <option value="">-- Selecciona --</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ $producto->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-proveedor_id"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-edicion-admin" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
