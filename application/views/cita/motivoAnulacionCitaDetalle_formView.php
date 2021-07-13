<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formMotivoAnulacionDet">
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Motivo Anulación </label>
			<textarea class="form-control input-sm" ng-model="fDataMAD.motivoAnulacion" placeholder="Ingrese el motivo de anulación" tabindex="200" rows="5"></textarea>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formMotivoAnulacionDet.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>
