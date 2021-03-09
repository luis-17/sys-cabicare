<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="formProducto"> 
		<div class="form-group col-md-4 mb-md ">
			<label class="control-label mb-n"> Tipo de Producto <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.tipo_producto" ng-options="item as item.descripcion for item in fArr.listaTipoProducto" 
            	required tabindex="10" ></select>
		</div>
		<div class="form-group col-md-4 mb-md ">
			<label class="control-label mb-n"> Sede <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.sede" ng-options="item as item.descripcion for item in fArr.listaSede" 
            	required tabindex="15" ></select>
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n"> Nombre <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre" placeholder="Nombre del producto" required tabindex="20" />
		</div>
    	<div class="form-group col-md-4 mb-md ">
			<label class="control-label mb-n"> Procedencia <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.procedencia" ng-options="item as item.descripcion for item in fArr.listaProcedencia" 
            	required tabindex="30" ></select>
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Precio Referencial: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.precio" placeholder="Precio referencial" tabindex="50" />
		</div>
		
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formProducto.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 