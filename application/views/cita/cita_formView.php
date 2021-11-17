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
						<span ng-click="btnNuevo();"
								ng-if="fData.accion == 'reg'"
								style="float: right;font-size: 10px;font-weight: 500;">¿Nuevo paciente?
						</span>
	              	</label>
					<div class="input-group">
						<span class="input-group-btn">
							<input
								type="text"
								ng-model="fData.numeroDocumento"
								class="form-control input-sm"
								style="width:140px;margin-right:4px;"
								placeholder="DNI"
								ng-disabled="fData.accion != 'reg'"
								ng-enter="obtenerDatosPaciente();"
								ng-change="fData.paciente = null; fData.pacienteId = null;"/>
						</span>


						<input type="text"
							ng-model="fData.paciente"
							class="form-control input-sm"
							placeholder="Paciente"
							required
							disabled>

						<span class="input-group-btn" ng-if="fData.accion == 'reg'">
							<button class="btn btn-default btn-sm" type="button" ng-click="btnBuscarPaciente()"><i class="fa fa-search"></i> </button>
						</span>
	            	</div>
	            </div>

				<div ng-if="!(fData.tipoCita == 3)" class="form-group col-md-3" ng-class="{'has-error': Form.formCita.tipoCita.$invalid}">
					<label for="tipoCita" class="control-label minotaur-label">Tipo de Cita <span class="text-danger">*</span>: </label>
					<select class="form-control input-sm" name="tipoCita" ng-model="fData.tipoCita" required>
						<option value="">--Seleccione tipo cita--</option>
						<option ng-repeat="item in fArr.listaTipoCita" value="{{item.id}}">{{item.descripcion}}</option>
					</select>
	      </div>
				<div ng-if="fData.tipoCita == 3" class="form-group col-md-3">
					<label for="tipoCita" class="control-label minotaur-label">Tipo de Cita: </label>
					<p style="color: green;">ATENDIDA</p>
	      </div>
				<div class="form-group col-md-3">
					<label for="name" class="control-label minotaur-label">Sede : </label>
					<label for="name" class="control-label" style="font-weight:bold;color:blue;display: block;font-size: 20px;"> {{ fSessionCI.sede }} </label>
					<!-- <select
						class="form-control input-sm"
						ng-model="fData.sede"
						ng-options="item as item.descripcion for item in fArr.listaSedes"
						required
					></select> -->
	      </div>
				<div class="form-group col-md-3 mb-md">
					<label class="control-label mb-n"> Tipo de Documento </label>
					<select class="form-control input-sm" ng-change="onChangeTipoDoc(); $event.preventDefault();" ng-model="fData.tipoDocumentoCont" 
						ng-options="item as item.descripcion for item in fArr.listaTipoDocumentoCont" tabindex="60" ></select> 
				</div>
				<div class="form-group col-md-3 mb-md">
					<label class="control-label mb-n"> N° Serie / Factura </label>
					<div class="group-bloque" style="display:flex;">
						<input type="text" class="form-control input-sm" disabled ng-model="fData.numSerie" placeholder="Ingrese N° Serie" 
							style="width: 100px; margin-right: 4px;" />
						<input type="text" class="form-control input-sm" disabled ng-model="fData.numDoc" placeholder="Ingrese N° Doc." 
							style="" />
					</div>
				</div>
				<div class="form-group col-md-4">
	            	<label for="name" class="control-label minotaur-label">Médico : </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.medico"
                        placeholder="Digite el Médico..."
						typeahead-loading="loadingLocations1"
						uib-typeahead="item as item.medico for item in getMedicoAutocomplete($viewValue)"
						typeahead-min-length="2"
						typeahead-on-select="getSelectedMedico($item, $model, $label)"
						autocomplete="off"
						ng-change="fData.idmedico = null;"
					>
					<i ng-show="loadingLocations1" class="fa fa-refresh" style="position: absolute;"></i>
					<div ng-show="noResultsMe" style="position: absolute;" class="text-danger">
						<i class="fa fa-remove"></i>
						No se encontró resultados
					</div>
				</div>

				<div class="form-group col-md-2">
					<label for="name" class="control-label minotaur-label">Fecha <span class="text-danger">*</span>: </label>
					<div class="input-group">
						<input type="text" required class="form-control input-sm" uib-datepicker-popup ng-model="fData.fecha" 
						is-open="configDP.popup.opened" datepicker-options="configDP.dateOptions" close-text="Cerrar" 
						ng-disabled="fData.tipoCita == 3"/>
						<span class="input-group-btn">
							<button type="button" class="btn btn-default btn-sm" ng-click="configDP.open()" ng-disabled="fData.tipoCita == 3"><i class="fa fa-calendar"></i></button>
						</span>
					</div>
				</div>
				
				<div class="form-group col-md-3">
	              <label for="name" class="control-label minotaur-label">Apuntes: </label>
				  <textarea
					class="form-control input-sm"
					ng-model="fData.apuntesCita"
					rows="3"
				  ></textarea>
	      </div>
				<div class="form-group col-md-3 mb-md" ng-class="{'has-error': Form.formCita.tipoCita.$invalid}">
					<label class="control-label mb-n"> Consultorio <span class="text-danger">*</span> </label>
					<select class="form-control input-sm" ng-model="fData.consultorio" required 
						ng-options="item as item.descripcion for item in fArr.listaConsultorioForm" tabindex="60" ></select> 
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

				<div class="form-group col-md-3">
					<label for="name" class="control-label minotaur-label">Temperatura: </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.temperaturaCorporal"
						placeholder="Temperatura Corporal"
					>
							</div>
				<div class="form-group col-md-3">
					<label for="name" class="control-label minotaur-label">Presión Arterial: </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.presionArterial"
						placeholder="Presión Arterial"
					>
	      </div>
				<!-- <div class="form-group col-md-3">
					<label for="name" class="control-label minotaur-label">Frecuencia Cardiaca: </label>
					<input
						type="text"
						class="form-control input-sm"
						ng-model="fData.frecuenciaCardiaca"
						placeholder="Frecuencia Cardiaca"
					>
	      </div> -->
			</div>
			<hr>
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
							typeahead-loading="loadingLocations2"
							typeahead-wait-ms="500"
							uib-typeahead="item as item.descripcion for item in getProductoAutocomplete($viewValue)"
							typeahead-on-select="getSelectedProducto($item, $model, $label)"
							typeahead-min-length="2"
							autocomplete="off"
							ng-change = "fData.temporal.idproducto = null; fData.temporal.precio = null;"
						/>
					</div>
					<i ng-show="loadingLocations2" class="fa fa-refresh" style="position: absolute;"></i>
					<div ng-show="noResultsPr" style="position: absolute;" class="text-danger">
						<i class="fa fa-remove"></i>
						No se encontró resultados
					</div>
				</div>
				<div class="form-group col-md-3 col-sm-6 mb-md">
					<label class="control-label mb-xs"> Precio </label>
					<label class="control-label mb-xs" style="display: block;font-weight: bold;font-size: 20px;"> {{fData.temporal.precio}} </label>
				</div>
				<!-- <div class="form-group col-md-3 col-sm-6 mb-md">
					<label class="control-label mb-xs"> Precio </label>
					<input
						type="text"
						ng-model="fData.temporal.precio"
						class="form-control input-sm"
						autocomplete="off"
					/>
				</div> -->

				<div class="form-group col-md-3 mb-md  mt-xs">
					<button
						class="btn btn-success btn-sm mt-md"
						style="width: 100%;"
						ng-click="agregarItemProducto()"
						ng-disabled="fData.temporal.idproducto == null && (fData.temporal.precio == null || fData.temporal.precio == '')"
					>AGREGAR</button>
				</div>

				<div class="col-xs-12">
					<div ui-grid="gridOptions" ui-grid-auto-resize ui-grid-resize-columns ui-grid-edit class="grid table-responsive fs-mini-grid" ng-style="getTableHeight();"></div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-7 col-xs-12 pl-n pull-right">
					<div class="row">
						<div class="form-inline mt-xs col-xs-4 text-right">
							<label class="control-label minotaur-label mr-xs mt-sm text-success f-14"> SUBTOTAL (S/): </label>
							<input type="text" class="form-control pull-right text-center" disabled ng-model="fData.subtotal" placeholder="0.00" style="width: 160px; font-size: 17px; font-weight: bolder;"/>
						</div>
						<div class="form-inline mt-xs col-xs-4 text-right">
							<label class="control-label minotaur-label mr-xs mt-sm text-success f-14"> IGV (S/): </label>
							<input type="text" class="form-control pull-right text-center" disabled ng-model="fData.igv" placeholder="0.00" style="width: 160px; font-size: 17px; font-weight: bolder;"/>
						</div>
						<div class="form-inline mt-xs col-xs-4 text-right">
							<label class="control-label minotaur-label mr-xs mt-sm text-success f-14"> TOTAL A PAGAR (S/): </label>
							<input type="text" class="form-control pull-right text-center" disabled ng-model="fData.total_a_pagar" placeholder="0.00" style="width: 160px; font-size: 17px; font-weight: bolder;"/>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="row" ng-if="fData.tipoCita == '3' || fData.tipoCita == '2'">
				<div class="col-sm-12">
					<h3> Datos de Pago</h3>
				</div>
				<!-- <hr class="col-lg-12"> -->
				<div class="col-lg-12">
					<div class="row">
						<div class="form-group mb-md col-md-3 col-sm-3">
							<label class="control-label minotaur-label mb-xs"> Método de pago </label>
							<select class="form-control input-sm" ng-model="fData.temporalCont.metodoPago" 
								ng-options="item as item.descripcion for item in fArr.listaMetodoPago" tabindex="60" ></select> 
						</div>

						<div class="form-group col-md-3 col-sm-6 mb-md">
							<label class="control-label mb-xs"> N° Operación </label>
							<input
								type="text"
								ng-model="fData.temporalCont.numOperacion"
								class="form-control input-sm"
								autocomplete="off"
							/>
						</div>

						<div class="form-group col-md-3 col-sm-6 mb-md">
							<label class="control-label mb-xs"> Monto </label>
							<input
								type="text"
								ng-model="fData.temporalCont.monto"
								class="form-control input-sm"
								autocomplete="off"
							/>
						</div>

						<div class="form-group col-md-3 mb-md  mt-xs">
							<button
								class="btn btn-success btn-sm mt-md"
								style="width: 100%;"
								ng-click="agregarItemPago()"
								ng-disabled="fData.temporalCont.idproducto == null && (fData.temporalCont.monto == null || fData.temporalCont.monto == '')"
							>AGREGAR</button>
						</div>
						<div class="col-xs-12">
							<div ui-grid="gridOptionsCont" ui-grid-auto-resize ui-grid-resize-columns ui-grid-edit class="grid table-responsive fs-mini-grid" ng-style="getTableHeightCont();"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-xs-12 pl-n pull-right">
							<div class="row">
								<div class="form-inline mt-xs col-xs-12 text-right">
									<label class="control-label minotaur-label mr-xs mt-sm text-success f-14"> TOTAL PAGADO (S/): </label>
									<input type="text" class="form-control pull-right text-center" disabled ng-model="fData.total_pagado" placeholder="0.00" style="width: 160px; font-size: 17px; font-weight: bolder;"/>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- <div class="form-group col-md-4 mb-md">
					<label class="control-label mb-n"> Método de Pago </label>
					<select class="form-control input-sm" ng-model="fData.metodoPago" ng-options="item as item.descripcion for item in fArr.listaMetodoPago" tabindex="60" ></select> 
				</div>
				<div class="form-group col-md-4 mb-md">
					<label class="control-label mb-n"> N° de Operación </label>
					<input type="text" class="form-control input-sm" ng-model="fData.numOperacion" placeholder="Ingrese número de operación" tabindex="50" />
				</div> -->
				<div class="form-group col-md-12 mb-md">
					<label class="control-label mb-n"> Anotaciones </label>
					<textarea class="form-control input-sm" ng-model="fData.anotacionesPago" placeholder="Anotaciones" tabindex="200" rows="5"></textarea>
				</div>
				
			</div>

			<!-- FACTRACION ELECTRONICA -->
			<div class="row" ng-if="fData.tipoCita == '3' || fData.tipoCita == '2'">
				<div class="col-sm-12">
					<h3> Facturación Electrónica</h3>
				</div>
				<div class="col-sm-12 mb">
					<button ng-click="btnGenerarFact()" class="btn btn-info pull-right" type="button">GENERAR FACTURA ELECTRÓNICA</button>
				</div>
				<div class="col-lg-12">
					<div ui-grid="gridOptionsFE" ui-grid-auto-resize ui-grid-resize-columns ui-grid-edit class="grid table-responsive fs-mini-grid" ng-style="getTableHeightFE();"></div>
				</div>
			</div>

			<hr>
			<div class="row" ng-if="fData.tipoCita == '3' || fData.tipoCita == '2'">
				<div class="col-sm-12">
					<h3> Datos de atención</h3>
				</div>
				<div class="form-group col-md-12">
	        <label for="name" class="control-label minotaur-label">Plan: </label>
				  <textarea
					class="form-control input-sm"
					ng-model="fData.plan"
					disabled
					rows="3"
				  ></textarea>
				</div>
				<div class="form-group col-md-12">
	        <label for="name" class="control-label minotaur-label">Observaciones: </label>
				  <textarea
					class="form-control input-sm"
					ng-model="fData.observaciones"
					disabled
					rows="3"
				  ></textarea>
	      </div>
			</div>

		</form>
	</section>
</div>

<div class="modal-footer">
    <button class="btn btn-danger pull-left" ng-click="btnAnular(fData)" ng-if="fData.accion == 'edit' && bool">Anular</button>
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formCita.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar </button>
</div>
