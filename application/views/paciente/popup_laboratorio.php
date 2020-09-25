<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> Fecha de Examen.:<small class="text-danger">(*)</small> </label>
			<input type="tel" class="form-control input-sm" ng-model="fDataLab.temporalImg.fechaExamen" required input-mask mask-options="{alias: 'dd-mm-yyyy'}" />
		</div>
		<div class="form-group mb-md col-sm-6">
			<label class="control-label minotaur-label mb-xs"> Cargar Documento </label>
			<div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
				<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; text-align: center;">
					<img ng-if="fDataLab.temporalImg.srcDocumento" ng-src="{{ app.name + 'assets/dinamic/laboratorio/' + fDataLab.temporalImg.srcDocumento }}" />
				</div>
				<div>
					<a id="quitarImg" href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a> 
					<span class="btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span> 
						<span class="fileinput-exists">Cambiar</span> 
						<input type="file" name="file" file-model="fDataLab.temporalImg.srcDocumento_blob" /> 
					</span>
				</div>
			</div>
		</div>
		<div class="form-group mb-md col-sm-12">
			<label class="control-label minotaur-label mb-xs"> Observaciones </label>
			<textarea class="form-control" rows="3" ng-model="fDataLab.temporalImg.observaciones"></textarea>	
		</div>
		<div class="form-group col-md-2 mb-md mt-xs">
			<button class="btn btn-success btn-sm mt-md" style="width: 100%;"
				ng-click="agregarItemImagen()"
				ng-disabled="fDataLab.temporalImg.srcDocumento == ''"
			>AGREGAR</button>
		</div>
		<div class="col-xs-12">
			<div ui-grid="gridOptionsLab" ui-grid-auto-resize ui-grid-resize-columns ng-style="metodos.getTableHeight();" 
				class="grid table-responsive fs-mini-grid"></div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <!-- <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formPaciente.$invalid">Aceptar</button> -->
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 