@foreach ($productos as $producto)
<div class="modal fade" id="modalEliminarProducto{{ $producto->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelEliminar{{ $producto->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin-top: 80px; transform: translateX(80px);">
        <div class="modal-content">
            <form class="form-eliminar-producto-empleado" data-id="{{ $producto->id }}">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalLabelEliminar{{ $producto->id }}">Eliminar producto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p><strong>Advertencia:</strong> Estás a punto de eliminar el producto <strong>{{ $producto->nombre }}</strong>.</p>
                    <p>¿Estás seguro que deseas continuar?</p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Eliminar de todas formas</button>
                    <button type="button" class="btn btn-secondary btn-cancelar-eliminar-producto-empleado" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
