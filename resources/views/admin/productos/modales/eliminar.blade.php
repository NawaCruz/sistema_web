@foreach ($productos as $producto)
    <!-- MODAL: Eliminar producto (ADMIN) -->
    <div class="modal fade" id="modalEliminarProductoAdmin{{ $producto->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEliminarProductoAdminLabel{{ $producto->id }}" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form class="form-eliminar-producto-admin" data-id="{{ $producto->id }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalEliminarProductoAdminLabel{{ $producto->id }}">Confirmar Eliminación</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        ¿Estás seguro de eliminar el producto <strong>{{ $producto->nombre }}</strong>?
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary btn-cancelar-eliminar-admin" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
