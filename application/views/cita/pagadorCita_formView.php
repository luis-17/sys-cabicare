<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formPagador">
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Tipo Documento: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.pagador.tipo_documento" ng-options="item as item.descripcion for item in fArr.listaTipoDocumentoPag" required tabindex="10" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° de Documento: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.pagador.num_documento" placeholder="Ingrese N° de Documento" required tabindex="30" maxlength="15" minlength="8" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Denominación: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.pagador.denominacion" placeholder="Ingrese nombres" required tabindex="40" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Dirección: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.pagador.direccionPersona" placeholder="Ingrese dirección" tabindex="80" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> E-mail: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.pagador.email" placeholder="Ingrese email" tabindex="100" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptarPagador(); $event.preventDefault();" ng-disabled="formPagador.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancelPagador()">Cerrar</button>
</div>
