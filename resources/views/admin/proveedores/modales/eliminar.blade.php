<!-- Modal de confirmación -->
<div class="modal fade" id="confirmarEliminarAdmin{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelEliminar{{ $proveedor->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin-top: 80px; transform: translateX(80px);">
        <div class="modal-content">
        <form class="form-eliminar-proveedor-admin" data-id="{{ $proveedor->id }}">
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
            <button type="button" class="btn btn-secondary btn-cancelar-eliminar-proveedor-admin" data-dismiss="modal">Cancelar</button>

            </div>
        </form>
        </div>
    </div>
</div>