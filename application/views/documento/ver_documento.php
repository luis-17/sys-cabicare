<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formDocumento">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n block"> FECHA DE DOCUMENTO</label>
			<label> {{fDataDoc.fechaDocumento}}</label>
		</div>
		<div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Categoria</label>
            <label> {{fDataDoc.categoria.descripcion}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Código Interno</label>
            <label> {{fDataDoc.codigoExterno}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Monto</label>
            <label> {{fDataDoc.monto}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> RUC</label>
            <label> {{fDataDoc.ruc}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> RAZON SOCIAL</label>
            <label> {{fDataDoc.razonSocial}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> N° Serie / N° Doc</label>
            <label> {{fDataDoc.numSerie}} - {{fDataDoc.numDoc}}</label>
		</div>
		<!-- <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> N° Doc. </label>
            <label> {{fDataDoc.numDoc}} </label>
        </div> -->
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Moneda</label>
            <label> {{fDataDoc.moneda.descripcion}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Descripción </label>
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