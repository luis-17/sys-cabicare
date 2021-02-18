<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formMant">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> TipoNota <small class="text-danger">*</small>: </label>
			<select class="form-control input-sm" ng-model="fDataNota.tipoNota" required ng-options="item as item.descripcion for item in fArr.listaTipoNota"></select>
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° Serie / Factura </label>
			<div class="group-bloque" style="display:flex;">
				<input type="text" class="form-control input-sm" ng-model="fDataNota.numSerie" placeholder="Ingrese N° Serie" 
					style="width: 100px; margin-right: 4px;" />
				<input type="text" class="form-control input-sm" ng-model="fDataNota.numDoc" placeholder="Ingrese N° Doc." 
					style="" />
			</div>
        </div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° Serie / Factura Asociada</label>
			<div class="group-bloque" style="display:flex;">
				<input type="text" class="form-control input-sm" ng-model="fDataNota.numSerieAsoc" placeholder="Ingrese N° Serie" 
					style="width: 100px; margin-right: 4px;" />
				<input type="text" class="form-control input-sm" ng-model="fDataNota.numDocAsoc" placeholder="Ingrese N° Doc." 
					style="" />
			</div>
        </div>
        <div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Fecha Emisión </label>
			<input type="text" class="form-control input-sm" ng-model="fDataNota.fechaNota" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
        </div>
        <div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Monto </label>
			<input type="text" class="form-control input-sm" ng-model="fDataNota.total" placeholder="Ingrese monto" />
        </div>
		<div class="form-group mb-md col-sm-12">
			<label class="control-label minotaur-label mb-xs"> Anotaciones </label>
			<textarea class="form-control" rows="3" ng-model="fDataNota.anotaciones"></textarea>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formMant.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>
