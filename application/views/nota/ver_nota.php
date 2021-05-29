<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
	<form class="row" name="formDocumento">
		<div class="form-group col-sm-6 mb-md">
			<label class="control-label mb-n block"> TIPO DE NOTA</label>
			<label> {{fDataNota.tipoNota.descripcion}} </label>
		</div>
		<div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> NUM. SERIE</label>
            <label> {{fDataNota.numSerie}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> NUM DOCUMENTO</label>
            <label> {{fDataNota.numDoc}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> NUM DOC. ASOCIADO</label>
            <label> {{fDataNota.numDocAsoc}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> FECHA DE EMISIÓN</label>
            <label> {{fDataNota.fechaNota}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> USUARIO</label>
            <label> {{fDataNota.usuarioRegistro}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> SUBTOTAL</label>
            <label> {{fDataNota.subtotal}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> IGV</label>
            <label> {{fDataNota.igv}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> TOTAL</label>
            <label> {{fDataNota.total}} </label>
        </div>
        <div class="form-group col-sm-6 mb-md">
            <label class="control-label mb-n block"> Anotaciones</label>
            <label> {{fDataNota.anotaciones}} </label>
		</div>
		<div class="form-group mb-md col-sm-6">
            <label class="control-label minotaur-label mb-xs"> ARCHIVO </label>
            <div class="block">
                <a class="btn btn-link" target="_blank" href="{{fDataNota.link_pdf}}"> VER DOCUMENTO </a>
            </div>
			<!-- <div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
				<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; text-align: center;">
					<img ng-if="fDataNota.nombreArchivo" ng-src="{{ app.name + 'assets/dinamic/documentos/' + fDataNota.nombreArchivo }}" />
				</div>
			</div> -->
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div>