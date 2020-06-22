<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form name="formBusquedaCliente" class="">
		<div class="row">
			<div class="col-xs-12">
				<div ui-grid="gridOptionsBC" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="mySelectionGrid.length != 1">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div>