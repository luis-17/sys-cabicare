<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formDocumento">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n"> PERIODO</label>
			<label> {{fDataDoc.mes.descripcion}} - {{fDataDoc.anio.descripcion}} </label>
		</div>
		<div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n"> Categoria</label>
            <label> {{fDataDoc.categoria.descripcion}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n"> Código Interno</label>
            <label> {{fDataDoc.codigoExterno}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n"> Monto</label>
            <label> {{fDataDoc.monto}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n"> Anotaciones</label>
            <label> {{fDataDoc.observaciones}} </label>
		</div>
		
		<div class="form-group mb-md col-sm-6">
            <label class="control-label minotaur-label mb-xs"> ARCHIVO </label>
            <div class="block">
                <a class="btn btn-link" target="_blank" href="{{fDataDoc.nombreArchivo.link}}"> {{ fDataDoc.nombreArchivo.texto }} </a>
            </div>
			<!-- <div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
				<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; text-align: center;">
					<img ng-if="fDataDoc.nombreArchivo" ng-src="{{ app.name + 'assets/dinamic/documentos/' + fDataDoc.nombreArchivo }}" />
				</div>
			</div> -->
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>