<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formPaciente">
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Tipo Documento: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.tipo_documento" ng-options="item as item.descripcion for item in fArr.listaTipoDocumento" required tabindex="10" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> N° de Documento: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.num_documento" placeholder="Ingrese N° de Documento" required tabindex="30" maxlength="8" minlength="8" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Nombres: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Ingrese nombres" required tabindex="40" />
		</div>
    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellido Paterno: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_paterno" placeholder="Ingrese apellido paterno" required tabindex="50" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Apellido Materno: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_materno" placeholder="Ingrese apellido materno" tabindex="50" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Sexo <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.sexo" ng-options="item as item.descripcion for item in fArr.listaSexo" required tabindex="60" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Tipo de Sangre: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.tipo_sangre" placeholder="Ingrese tipo de sangre" tabindex="66" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Fecha de Nac.:<small class="text-danger">(*)</small> </label>
			<input type="tel" class="form-control input-sm" ng-model="fData.fecha_nacimiento" required tabindex="70" input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		</div>
		<div class="form-group col-md-6 mb-md ">
			<label class="control-label mb-n"> Operador: <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.operador" ng-options="item as item.descripcion for item in fArr.listaOperadores" required tabindex="75" ></select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Celular: </label>
			<input type="text" class="form-control input-sm" ng-model="fData.celular" placeholder="Ingrese celular" tabindex="80" />
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> E-mail: </label>
			<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Ingrese email" tabindex="100" />
		</div>
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Alergias </label>
			<textarea class="form-control input-sm" ng-model="fData.alergias" placeholder="Alergias" tabindex="200" rows="5"></textarea>
		</div>
		<div class="form-group col-md-12 mb-md">
			<label class="control-label mb-n"> Antecedentes </label>
			<textarea class="form-control input-sm" ng-model="fData.antecedentes" placeholder="Antecedentes" tabindex="300" rows="5"></textarea>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formPaciente.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 