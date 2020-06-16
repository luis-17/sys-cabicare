<div class="modal-header">
  <h4 class="modal-title"> {{titleForm}} </h4>
</div>
<div class="modal-body">
	<section class="tile-body p-0">
		<form name="formCita" role="form" novalidate class="form-validation">
			<div class="row">

				<div class="form-group col-md-6">
	              	<label for="name" class="control-label minotaur-label" style="width: 100%;">Paciente
	              		<span class="text-danger">*</span>:
						<!-- <span ng-controller="PacienteController as pac" ng-click="pac.btnNuevo(true,callback);"
								ng-if="fData.accion == 'reg'"
								style="float: right;font-size: 10px;font-weight: 500;">¿Nuevo paciente?
						</span> -->
	              	</label>
					<div class="input-group">
						<span class="input-group-btn">
							<input
								type="text"
								ng-model="fData.numeroDocumento"
								class="form-control input-sm"
								style="width:80px;margin-right:4px;"
								placeholder="DNI"
								ng-disabled="fData.accion != 'reg'"
								ng-enter="obtenerDatosPaciente();"
								ng-change="fData.paciente = null; fData.pacienteId = null;"
							/>
						</span>


						<input type="text"
							ng-model="fData.paciente"
							class="form-control input-sm"
							placeholder="Paciente"
							required
							disabled
						>
	            	</div>
	            </div>

				<div class="form-group col-md-3" ng-class="{'has-error': Form.formCita.tipoCita.$invalid}">
					<label for="tipoCita" class="control-label minotaur-label">Tipo de Cita <span class="text-danger">*</span>: </label>
					<!-- <select
						name="tipoCita"
						class="form-control input-sm"
						ng-model="fData.tipoCita"
						ng-options="item as item.descripcion for item in fArr.listaTipoCita"

						required
					></select> -->

					<select name="tipoCita" ng-model="fData.tipoCita" required>
						<option value="">--Seleccione tipo cita--</option>
						<option ng-repeat="item in fArr.listaTipoCita" value="{{item.id}}">{{item.descripcion}}</option>
					</select>

	            </div>

				<div class="form-group col-md-3">
					<label for="name" class="control-label minotaur-label">Sede : </label>
					<select
						class="form-control input-sm"
						ng-model="fData.sede"
						ng-options="item as item.descripcion for item in fArr.listaSedes"
						required
					></select>
	            </div>

				<div class="form-group col-md-2">
					<label for="name" class="control-label minotaur-label">Peso(kg) : </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.peso"
						placeholder="kg"
						ng-change="calcularIMC()"
					>
	            </div>

				<div class="form-group col-md-2">
					<label for="name" class="control-label minotaur-label">Talla(cm) : </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.talla"
						placeholder="cm"
						ng-change="calcularIMC()"
					>
	            </div>

				<div class="form-group col-md-2">
					<label for="name" class="control-label minotaur-label">IMC : </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.imc"
						placeholder="IMC"
						disabled
					>
	            </div>

				<div class="form-group col-md-6">
					<label for="name" class="control-label minotaur-label">Frecuencia Cardiaca: </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.frecuenciaCardiaca"
						placeholder="Frecuencia Cardiaca"
					>
	            </div>

				<div class="form-group col-md-6">
	            	<label for="name" class="control-label minotaur-label">Médico : </label>
					<input
						type="text"
						ng-if="fData.accion == 'reg'"
						class="form-control input-sm"
						ng-model="fData.medico"
                        placeholder="Digite el Médico..."
						typeahead-loading="loadingLocations"
						uib-typeahead="item as item.medico for item in getMedicoAutocomplete($viewValue)"
						typeahead-min-length="2"
						typeahead-on-select="getSelectedMedico($item, $model, $label)"
						autocomplete="off"
					>
				</div>

				<div class="form-group col-md-6">
	              <label for="name" class="control-label minotaur-label">Fecha <span class="text-danger">*</span>: </label>
	              <div class="input-group">
	                <input type="text" required class="form-control" uib-datepicker-popup ng-model="fData.fecha" is-open="configDP.popup.opened"
	                		datepicker-options="configDP.dateOptions" close-text="Cerrar" />
	                <span class="input-group-btn">
	                	<button type="button" class="btn btn-default" ng-click="configDP.open()"><i class="fa fa-calendar"></i></button>
	              	</span>
	              </div>
	            </div>

				<div class="form-group col-md-6">
	              <label for="name" class="control-label minotaur-label">Apuntes: </label>
				  <textarea
					class="form-control input-sm"
					ng-model="fData.apuntesCita"
					rows="3"
				  ></textarea>
	            </div>

	            <div class="form-group col-md-3">
	              	<label for="name" class="control-label minotaur-label">Hora inicio
	              		<span class="text-danger">*</span>:
	              	</label>
	              	<div uib-timepicker required
		              	ng-model="fData.hora_desde"
		              	ng-change="actualizarHoraFin();"
		              	hour-step="configTP.tpHoraInicio.hstep"
		              	minute-step="configTP.tpHoraInicio.mstep"
		              	show-meridian="configTP.tpHoraInicio.ismeridian">
	             	</div>
	            </div>

	            <div class="form-group col-md-3">
	              	<label for="name" class="control-label minotaur-label">Hora Fin
	              		<span class="text-danger">*</span>:
	              	</label>
	              	<div uib-timepicker required
	              		ng-model="fData.hora_hasta"
	              		hour-step="configTP.tpHoraFin.hstep"
	              		minute-step="configTP.tpHoraFin.mstep"
	              		show-meridian="configTP.tpHoraFin.ismeridian">
	              	</div>
	            </div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-6 col-sm-6">
					<label class="control-label minotaur-label mb-xs"> Productos </label>
					<div class="input-group">
						<span class="input-group-btn">
							<input
								type="text"
								ng-model="fData.temporal.idproducto"
								class="form-control input-sm"
								style="width:40px;margin-right:4px;"
								placeholder="ID"
								ng-disabled="true"
							/>
						</span>
						<input
							id="temporalProducto"
							type="text"
							ng-model="fData.temporal.producto"
							class="form-control input-sm"
							placeholder="Busque Producto"
							typeahead-loading="loadingLocations"
							typeahead-wait-ms="500"
							uib-typeahead="item as item.descripcion for item in getProductoAutocomplete($viewValue)"
							typeahead-on-select="getSelectedProducto($item, $model, $label)"
							typeahead-min-length="2"
							autocomplete="off"
							ng-change = "fData.temporal.idproducto = null; fData.temporal.precio = null"
						/>
						<i ng-show="loadingLocations" class="fa fa-refresh"></i>
						<div ng-show="noResultsLPSC">
							<i class="fa fa-remove"></i>
							No se encontró resultados
						</div>
					</div>
				</div>

				<div class="form-group col-md-3 col-sm-6 mb-md">
					<label class="control-label mb-xs"> Precio </label>
					<input
						type="text"
						ng-model="fData.temporal.precio"
						class="form-control input-sm"
						autocomplete="off"
					/>
				</div>

				<div class="form-group col-md-3 mb-md  mt-xs">
					<button
						class="btn btn-success btn-sm mt-md"
						style="width: 100%;"
						ng-click="agregarItemProducto()"
						ng-disabled="fData.temporal.idproducto == null && (fData.temporal.precio == null || fData.temporal.precio == '')"
					>AGREGAR</button>
				</div>

				<div class="col-xs-12">
					<div ui-grid="gridOptions" ui-grid-auto-resize ui-grid-resize-columns class="grid table-responsive fs-mini-grid" ng-style="getTableHeight();"></div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-6 col-xs-12 pl-n pull-right">
					<div class="row">
						<div class="form-inline mt-xs col-xs-12 text-right">
							<label class="control-label minotaur-label mr-xs mt-sm text-success f-14"> TOTAL A PAGAR (S/): </label>
							<input type="text" class="form-control pull-right text-center" disabled ng-model="fData.total_a_pagar" placeholder="0.00" style="width: 160px; font-size: 17px; font-weight: bolder;"/>
						</div>
					</div>
				</div>
			</div>


		</form>
	</section>
</div>
<div class="modal-footer">
  	<button class="btn btn-lightred btn-ef btn-ef-4 btn-ef-4c" ng-click="cancel()">
  		<i class="fa fa-arrow-left"></i> Cancelar
  	</button>
  	<button class="btn btn-success btn-ef btn-ef-3 btn-ef-3c"
  		ng-disabled="formCita.$invalid" ng-click="registrarCita();">
  		<i class="fa fa-arrow-right"></i> Guardar
  	</button>
</div>