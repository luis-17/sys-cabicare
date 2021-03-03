<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formDocumento">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> Año <small class="text-danger">*</small>: </label>
			<select class="form-control input-sm" ng-model="fDataDoc.anio" required ng-options="item as item.descripcion for item in fArr.listaAnio"></select>
		</div>
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> Mes <small class="text-danger">*</small>: </label>
			<select class="form-control input-sm" ng-model="fDataDoc.mes" required ng-options="item as item.descripcion for item in fArr.listaMes"></select>
		</div>
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> Categoria <small class="text-danger">*</small>: </label>
			<select class="form-control input-sm" ng-model="fDataDoc.categoria" required ng-options="item as item.descripcion for item in fArr.listaCategoria"></select>
		</div>
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> Moneda <small class="text-danger">*</small>: </label>
			<select class="form-control input-sm" ng-model="fDataDoc.moneda" required ng-options="item as item.descripcion for item in fArr.listaMoneda"></select>
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° Serie / Factura </label>
			<div class="group-bloque" style="display:flex;">
				<input type="text" class="form-control input-sm" ng-model="fDataDoc.numSerie" placeholder="Ingrese N° Serie" 
					style="width: 100px; margin-right: 4px;" />
				<input type="text" class="form-control input-sm" ng-model="fDataDoc.numDoc" placeholder="Ingrese N° Doc." 
					style="" />
			</div>
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> RUC </label>
			<input type="text" class="form-control input-sm" ng-model="fDataDoc.ruc" placeholder="Ingrese RUC" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Código Interno </label>
			<input type="text" class="form-control input-sm" ng-model="fDataDoc.codigoExterno" placeholder="Ingrese cod. externo" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Monto </label>
			<input type="text" class="form-control input-sm" ng-model="fDataDoc.monto" placeholder="Ingrese monto" />
		</div>
		<div class="form-group mb-md col-sm-6">
			<label class="control-label minotaur-label mb-xs"> Cargar Documento </label>
			<div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
				<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; text-align: center;">
					<img ng-if="fDataDoc.nombreArchivo" ng-src="{{ app.name + 'assets/dinamic/documentos/' + fDataDoc.nombreArchivo }}" />
				</div>
				<div>
					<a id="quitarImg" href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a> 
					<span class="btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span> 
						<span class="fileinput-exists">Cambiar</span> 
						<input type="file" name="file" file-model="fDataDoc.nombreArchivo_blob" /> 
					</span>
				</div>
			</div>
		</div>
		<div class="form-group mb-md col-sm-12">
			<label class="control-label minotaur-label mb-xs"> Descripción </label>
			<textarea class="form-control" rows="3" ng-model="fDataDoc.observaciones"></textarea>	
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formDocumento.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>
