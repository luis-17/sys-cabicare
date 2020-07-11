<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formMetodoPago">
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Método de Pago <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fDataMP.metodoPago" ng-options="item as item.descripcion for item in fArr.listaMetodoPago" required tabindex="60" ></select> 
        </div>
        <div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° de Operación </label>
			<input type="text" class="form-control input-sm" ng-model="fDataMP.numOperacion" placeholder="Ingrese número de operación" tabindex="50" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formMetodoPago.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>
