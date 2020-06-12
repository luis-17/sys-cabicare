<div class="modal-header">
  <h4 class="modal-title"> {{titleForm}} </h4>
</div>
<div class="modal-body">
	<section class="tile-body p-0">
		<form name="formCita" role="form" novalidate class="form-validation">
			<div class="row">

			</div>
		</form>
	</section>
</div>
<div class="modal-footer">
  	<button class="btn btn-lightred btn-ef btn-ef-4 btn-ef-4c" ng-click="cancel()">
  		<i class="fa fa-arrow-left"></i> Cancelar
  	</button>
  	<button class="btn btn-success btn-ef btn-ef-3 btn-ef-3c"
  		ng-disabled="formCita.$invalid" ng-click="ok();">
  		<i class="fa fa-arrow-right"></i> Guardar
  	</button>
</div>