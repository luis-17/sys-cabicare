<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">
	<form class="row" name="usuarioForm">
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
 			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.nombres" placeholder="Ingrese nombres" required tabindex="100" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellidos: <small class="text-danger">(*)</small> </label>
 			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.apellidos" placeholder="Ingrese apellidos" required tabindex="100" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° de Colegiatura: </label>
 			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.cmp" placeholder="Ingrese colegiatura" tabindex="100" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> RNE: </label>
 			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.rne" placeholder="Ingrese RNE" tabindex="100" /> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Correo Electrónico: <small class="text-danger">(*)</small> </label>
 			<input type="email" class="form-control input-sm" autocomplete="off" ng-model="fData.correo" placeholder="Ingrese correo" tabindex="100" required /> 
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> SEDE HIGUERETA: </label>
			<input type="checkbox" ng-model="fData.checkHiguereta" ng-checked="fData.checkHiguereta == 1" ng-true-value="1">
		</div>
		<div class="form-group col-md-3 mb-md">
			<label class="control-label mb-n"> SEDE SAN MIGUEL: </label>
			<input type="checkbox" ng-model="fData.checkSanMiguel" ng-checked="fData.checkSanMiguel == 2" ng-true-value="2">
		</div>
		<div class="hr-line"> Datos de Acceso </div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Perfil: <small class="text-danger">(*)</small> </label>
 			<select class="form-control input-sm" ng-model="fData.perfil" ng-options="item as item.descripcion for item in fArr.listaPerfil" required tabindex="100" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Usuario: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.username" placeholder="Ingrese usuario" required tabindex="100" />
		</div>
		<div ng-show="modoEdit">
    	<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Ingrese Contraseña: <small class="text-danger">(*)</small> </label>
				<input autocomplete="off" type="password" class="form-control input-sm" ng-model="fData.passwordView" placeholder="Registre contraseña" tabindex="120" />
			</div>
	    <div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Repita la contraseña: <small class="text-danger">(*)</small> </label>
				<input autocomplete="off" type="password" class="form-control input-sm" ng-model="fData.password" placeholder="Repita contraseña" tabindex="130" />
			</div>
		</div>
		<div class="form-group col-md-6 mb-md" ng-show="!modoEdit">
			<label class="control-label mb-n"> ¿Desea cambiar clave?: </label>
			<input type="checkbox" ng-model="fData.checkCambioClave" ng-checked="" >
		</div>
		<div ng-show="fData.checkCambioClave">
			<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Ingrese nueva contraseña: <small class="text-danger">(*)</small> </label>
				<input autocomplete="off" type="password" class="form-control input-sm" ng-model="fData.password" placeholder="Registre contraseña" tabindex="150" />
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="usuarioForm.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>