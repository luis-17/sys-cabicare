app.controller('CitaCtrl',
	['$scope',
	'$filter',
	'$state',
	'$stateParams',
	'$bootbox',
	'$log',
	'$timeout',
	'pinesNotifications',
	'uiGridConstants',
	'blockUI',
	'ReservaCitasFactory',
	'CitaServices',

	function (
		$scope,
		$filter,
		$state,
		$stateParams,
		$bootbox,
		$log,
		$timeout,
		pinesNotifications,
		uiGridConstants,
		blockUI,
		ReservaCitasFactory,
		CitaServices

	) {
		$scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones
		$scope.fBusqueda = {};

		$scope.fArr.listaTipoCita = [
			{ id: '1', descripcion: 'POR CONFIRMAR' },
			{ id: '2', descripcion: 'CONFIRMADA' }
		];

		$scope.fArr.listaSedes = [
			{ id: '1', descripcion: 'HIGUERETA' }
		];



		/* EVENTOS */
		$scope.menu = angular.element('.menu-dropdown');
		$scope.alertOnClick = function (event, jsEvent, view) {
			console.log('event', event);
			$scope.btnEditarCita(event);

		}
		$scope.alertOnResize = function (event, delta) {
			angular.element('.calendar').fullCalendar('refetchEvents');
		};

		$scope.selectCell = function (date, end, jsEvent, view) {
			var typeView = angular.element('.calendar').fullCalendar('getView');
			if (typeView.type == 'month') {
				angular.element('.calendar').fullCalendar('gotoDate', date);
				angular.element('.calendar').fullCalendar('changeView', 'agendaDay');
			} else {
				$scope.btnAgregarCita(date, end);
			}
		}
		$scope.alertOnDrop = function (event, delta) {
			blockUI.start('Actualizando calendario...');
			var datos = {
				event: event,
				delta: delta,
			};
			CitaServices.sMoverCita(datos).then(function (rpta) {
				if (rpta.flag == 1) {
					var pTitle = 'OK!';
					var pType = 'success';
				} else if (rpta.flag == 0) {
					var pTitle = 'Advertencia!';
					var pType = 'warning';
				}
				angular.element('.calendar').fullCalendar('refetchEvents');
				pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
				blockUI.stop();
			});
		};
		$scope.overlay = $('.fc-overlay');
		$scope.alertOnMouseOver = function (event, jsEvent, view) {
			$scope.event = event;
			$scope.overlay.removeClass('left right top').find('.arrow').removeClass('left right top pull-up');
			var wrap = $(jsEvent.target).closest('.fc-event');
			var cal = wrap.closest('.calendar');
			var left = wrap.offset().left - cal.offset().left;
			var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
			var top = cal.height() - (wrap.offset().top - cal.offset().top + wrap.height());
			if (right > $scope.overlay.width()) {
				$scope.overlay.addClass('left').find('.arrow').addClass('left pull-up')
			} else if (left > $scope.overlay.width()) {
				$scope.overlay.addClass('right').find('.arrow').addClass('right pull-up');
			} else {
				$scope.overlay.find('.arrow').addClass('top');
			}
			if (top < $scope.overlay.height()) {
				$scope.overlay.addClass('top').find('.arrow').removeClass('pull-up').addClass('pull-down')
			}
			(wrap.find('.fc-overlay').length == 0) && wrap.append($scope.overlay);
		}
		$scope.actualizarCalendario = function (block) {
			blockUI.start('Actualizando calendario...');
			angular.element('.calendar').fullCalendar('refetchEvents');
			blockUI.stop();
		}
		$scope.closeMenu = function () {
			$scope.menu.removeClass('open');
		}

		$scope.btnAgregarCita = function (start, end) {
			console.log('Agrega cita');
			var arrParams = {
				'start': start || null,
				'end': end || null,
				'fArr': $scope.fArr
			};
			ReservaCitasFactory.agregarCitaModal(arrParams);
		}
		$scope.btnEditarCita = function (cita) {
			console.log('Edita cita');
			var arrParams = {
				// 'start': start,
				'cita': cita,
				'fArr': $scope.fArr
			};
			ReservaCitasFactory.editarCitaModal(arrParams);
		}


		/* CARGA DE DATOS */
		$scope.eventsF = function (start, end, timezone, callback) {
			var events = [];
			blockUI.start('Actualizando calendario...');
			//console.log(start, end,'start, end');
			//console.log(start.toLocaleTimeString(), end.toLocaleTimeString(),'start.toLocaleTimeString(), end.toLocaleTimeString()');
			// $scope.fBusqueda.desde = moment(start).tz('America/Lima').format('YYYY-MM-DD');
			// $scope.fBusqueda.hasta = moment(end).tz('America/Lima').format('YYYY-MM-DD');
			$scope.fBusqueda.desde = moment(start).format('YYYY-MM-DD');
			$scope.fBusqueda.hasta = moment(end).format('YYYY-MM-DD');
			console.log(start,end);
			console.log('desde', $scope.fBusqueda.desde);
			console.log('hasta', $scope.fBusqueda.hasta);
			CitaServices.sListarCitaCalendario($scope.fBusqueda).then(function (rpta) {
				if (rpta.flag == 1) {
					angular.forEach(rpta.datos, function (row, key) {
						row.start = moment(row.start);
						row.end = moment(row.end);
					});
					events = rpta.datos;
					callback(events);
				}
				blockUI.stop();
			});
		}
		$scope.eventSources = [$scope.eventsF];
		/* Change View */
		$scope.changeView = function (view, calendar) {
			$('.calendar').fullCalendar('changeView', view);
		};
		$scope.today = function (calendar) {
			$('.calendar').fullCalendar('today');
		};

		$scope.uiConfig = {
			calendar: {
				allDaySlot: false,
				height: 450,
				contentHeight: 510,
				editable: true,
				selectable: true,
				defaultView: 'agendaWeek',
				dayNames: ["Domingo", "Lunes ", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
				dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
				monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				monthNamesShort: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				header: {
					left: 'prev',
					center: 'title',
					right: 'next'
				},
				select: $scope.selectCell,
				eventDrop: $scope.alertOnDrop,
				eventResize: $scope.alertOnResize,
				eventClick: $scope.alertOnClick,

				eventMouseover: function (data, event, view) {
					var tooltip = '<div class="tooltip-event"' +
						'style="">'
						+ 'Paciente: ' + data.paciente + '<br />'
						+ '</div>';
					$("body").append(tooltip);
					$(this).mouseover(function (e) {
						$(this).css('z-index', 10000);
						$('.tooltip-event').fadeIn('500');
						$('.tooltip-event').fadeTo('10', 1.9);
					}).mousemove(function (e) {
						$('.tooltip-event').css('top', e.pageY + 10);
						$('.tooltip-event').css('left', e.pageX + 20);
					});
				},
				eventMouseout: function (data, event, view) {
					$(this).css('z-index', 8);
					$('.tooltip-event').remove();
				},
				minTime: '07:00:00',
				maxTime: '20:00:00',
				displayEventTime: false,
				views: {
					week: {
						titleFormat: 'D MMMM YYYY',
						titleRangeSeparator: ' - '
					},
					day: {
						titleFormat: 'ddd DD-MM'
					}
				}
			}
		};



	}
]);

app.service("CitaServices", function ($http, $q, handleBehavior) {
	return({
		sListarCitaCalendario: sListarCitaCalendario,
		sListarDetalleCita: sListarDetalleCita,
		sRegistrar: sRegistrar,
		sEditar: sEditar,
	});

	function sListarCitaCalendario(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_citas",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sListarDetalleCita(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_detalle_cita",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sRegistrar(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/registrar",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sEditar(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/editar",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
});

app.factory("ReservaCitasFactory",
	function (
		$uibModal,
		pinesNotifications,
		blockUI,
		ProductoServices,
		PacienteServices,
		CitaServices
	) {
	var interfaz = {
		agregarCitaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Cita/ver_popup_form_cita',
				size: 'lg',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.fData = {};
					$scope.Form = {}
					$scope.fData.temporal = {};
					$scope.fArr = arrParams.fArr;
					$scope.fData.accion = 'reg';
					$scope.titleForm = 'Registro de Cita';
					// $scope.fArr.listaTipoCita.splice(0, 0, { id: "", descripcion: '--Seleccione tipo cita--' });
					// $scope.fData.tipoCita = $scope.fArr.listaTipoCita[0];

					$scope.fData.sede = $scope.fArr.listaSedes[0];

					/* PACIENTE */
					$scope.obtenerDatosPaciente = function () {
						if ($scope.fData.numeroDocumento) {
							PacienteServices.sListarPacientePorNumDoc($scope.fData).then(function (rpta) {
								if (rpta.flag === 1) {
									$scope.fData.paciente = rpta.datos.paciente;
									$scope.fData.pacienteId = rpta.datos.pacienteId;

									pinesNotifications.notify({
										title: 'OK.',
										text: rpta.message,
										type: 'success',
										delay: 5000
									});
								}else{
									pinesNotifications.notify({
										title: 'Advertencia.',
										text: rpta.message,
										type: 'warning',
										delay: 5000
									});

								}
							});
						}
					}

					/* DATEPICKERS */
					$scope.configDP = {};
					$scope.configDP.today = function () {
						if (arrParams.start) {
							$scope.fData.fecha = arrParams.start.toDate();
						} else {
							$scope.fData.fecha = new Date();
						}
					};
					$scope.configDP.today();

					$scope.configDP.clear = function () {
						$scope.fData.fecha = null;
					};

					$scope.configDP.dateOptions = {
						formatYear: 'yy',
						// maxDate: new Date(2031, 5, 22),
						// minDate: new Date(),
						startingDay: 1
					};

					$scope.configDP.open = function () {
						$scope.configDP.popup.opened = true;
					};

					$scope.configDP.formats = ['dd-MM-yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
					$scope.configDP.format = $scope.configDP.formats[0];
					$scope.configDP.altInputFormats = ['M!/d!/yyyy'];

					$scope.configDP.popup = {
						opened: false
					};
         			/* END DATEPICKERS */

					/* TIMEPICKERS */
					$scope.configTP = {};
					$scope.configTP.tpHoraInicio = {};
					$scope.configTP.tpHoraInicio.hstep = 1;
					$scope.configTP.tpHoraInicio.mstep = 30;
					$scope.configTP.tpHoraInicio.ismeridian = true;
					$scope.configTP.tpHoraInicio.toggleMode = function () {
						$scope.configTP.tpHoraInicio.ismeridian = !$scope.configTP.tpHoraInicio.ismeridian;
					};
					$scope.configTP.tpHoraFin = angular.copy($scope.configTP.tpHoraInicio);
					if (arrParams.start) {
						// console.log('arrParams.start.a',arrParams.start.format('a'));
						var partes_hora1 = arrParams.start.format('hh:mm').split(':');
						// console.log('partes_hora1',partes_hora1);
						var d = new Date();
						if (arrParams.start.format('a') == 'pm' && parseInt(partes_hora1[0]) != 12) {
							d.setHours(parseInt(partes_hora1[0]) + 12);
						} else {
							d.setHours(parseInt(partes_hora1[0]));
						}

						d.setMinutes(parseInt(partes_hora1[1]));
						$scope.fData.hora_desde = d;

						if (arrParams.end) {
							var partes_hora2 = arrParams.end.format('hh:mm').split(':');
						} else {
							var partes_hora2 = arrParams.start.add(30, 'minutes').format('hh:mm').split(':');
						}
						var c = new Date();
						if (arrParams.start.format('a') == 'pm' && parseInt(partes_hora2[0]) != 12) {
							c.setHours(parseInt(partes_hora2[0]) + 12);
						} else {
							c.setHours(parseInt(partes_hora2[0]));
						}
						c.setMinutes(parseInt(partes_hora2[1]));
						$scope.fData.hora_hasta = c;
					} else {
						$scope.fData.hora_desde = new Date();
						$scope.fData.hora_hasta = new Date();
					}
					$scope.actualizarHoraFin = function () {
						$scope.fData.hora_hasta = moment($scope.fData.hora_desde).add(30, 'm').toDate();
					}
          			/* END TIMEPICKERS */

					/* AUTOCOMPLETADO */

					$scope.getProductoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return ProductoServices.sListarProductoAutocomplete(params).then(function (rpta) {
							$scope.noResultsCT = false;
							if (rpta.flag === 0) {
								$scope.noResultsCT = true;
							}
							console.log('datos producto', rpta.datos);
							return rpta.datos;
						});
					}

					$scope.getSelectedProducto = function (item, model) {
						$scope.fData.temporal.idproducto = model.idproducto;
						$scope.fData.temporal.producto = model.producto;
						$scope.fData.temporal.tipoProducto = model.tipo_producto.descripcion;
						$scope.fData.temporal.precio = model.precio;
					}

					/* GRILLA DE PRODUCTOS */
					$scope.gridOptions = {
						rowHeight: 30,
						enableGridMenu: false,
						enableColumnMenus: false,
						enableRowSelection: false,
						enableSelectAll: false,
						enableFiltering: false,
						enableSorting: false,
						enableFullRowSelection: false,
						multiSelect: false,
						data: [],
						columnDefs: [
							{ field: 'idproducto', name: 'id', displayName: 'ID', minWidth: 80, width: 80 },
							{ field: 'producto', name: 'nombre', displayName: 'PRODUCTO', minWidth: 120 },
							{ field: 'tipoProducto', name: 'tipoProducto', displayName: 'Tipo Producto', minWidth: 120 },

							{ field: 'precio', name: 'precio', displayName: 'PRECIO', width: 120 },

							{
								field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
								cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
							}
						],
						onRegisterApi: function (gridApi) {
							$scope.gridApi = gridApi;
						}
					};

					$scope.getTableHeight = function () {
						var rowHeight = 30; // your row height
						var headerHeight = 30; // your header height
						var cant_filas = 4; // min 4
						if ($scope.gridOptions.data.length > cant_filas) {
							var cant_filas = $scope.gridOptions.data.length;
						}
						return {
							height: (cant_filas * rowHeight + headerHeight) + "px"
						};
					}

					$scope.agregarItemProducto = function(){
						if ($scope.fData.temporal.idproducto == null ){
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe seleccionar un producto.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						if ($scope.fData.temporal.precio == null ||
							$scope.fData.temporal.precio == "" ||
							$scope.fData.temporal.precio < 0)
						{
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'El precio no es válido.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						var producto_repetido = false;
						angular.forEach($scope.gridOptions.data, function (value, key) {
							if (value.idproducto == $scope.fData.temporal.idproducto) {
								producto_repetido = true;
								pinesNotifications.notify({
									title: 'Advertencia.',
									text: 'Ya está cargado este producto en la cesta.',
									type: 'warning',
									delay: 5000
								});
								return;
							}
						});

						if (producto_repetido === false) {
							$scope.gridOptions.data.push({
								idproducto: $scope.fData.temporal.idproducto,
								producto: $scope.fData.temporal.producto,
								tipoProducto: $scope.fData.temporal.tipoProducto,
								precio: $scope.fData.temporal.precio,
							});

							$scope.fData.temporal = {}
							$scope.calcularTotales();
						}
					}

					$scope.calcularTotales = function () {
						var totales = 0;
						angular.forEach($scope.gridOptions.data, function (value, key) {
							totales += parseFloat($scope.gridOptions.data[key].precio);
						});
						$scope.fData.total_a_pagar = totales.toFixed(2);
					}

					$scope.btnQuitarDeLaCesta = function (row) {
						var index = $scope.gridOptions.data.indexOf(row.entity);
						$scope.gridOptions.data.splice(index, 1);
						$scope.calcularTotales();
					}

					/* CALCULO DE IMC */
					$scope.calcularIMC = function(){
						$scope.fData.imc = null;
						if ($scope.fData.peso == null || $scope.fData.talla == null){
							return;
						}
						console.log('calculo imc');

						if($scope.fData.peso <= 0 || $scope.fData.talla <= 0){
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Peso y Talla deben ser numericos mayores de cero',
								type: 'warning',
								delay: 5000
							});
							return;
						}
						var talla = parseInt($scope.fData.talla) / 100;
						$scope.fData.imc = (parseFloat($scope.fData.peso) / (parseFloat(talla*talla))).toFixed(2);
					}

					/* BOTONES FINALES */
					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
					}

					$scope.registrarCita = function(){

						if ($scope.fData.tipoCita == null || $scope.fData.tipoCita == ""){
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe seleccionar un Tipo de Cita.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						if ($scope.gridOptions.data.length <= 0){
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe agregar al menos un producto.',
								type: 'warning',
								delay: 5000
							});
							return;
						}
						if ($scope.fData.hora_desde) {
							$scope.fData.hora_desde_str = $scope.fData.hora_desde.toLocaleTimeString();
						}

						if ($scope.fData.hora_hasta) {
							$scope.fData.hora_hasta_str = $scope.fData.hora_hasta.toLocaleTimeString();
						}
						$scope.fData.detalle = $scope.gridOptions.data;

						blockUI.start("Registrando cita");
						CitaServices.sRegistrar($scope.fData).then(function(rpta){
							if (rpta.flag === 1) {
								var pTitle = 'OK!';
								var pType = 'success';
								$uibModalInstance.dismiss($scope.fData);
							} else {
								var pTitle = 'Advertencia!';
								var pType = 'warning';

							}
							blockUI.stop();
							pinesNotifications.notify({
								title: pTitle,
								text: rpta.message,
								type: pType,
								delay: 5000
							});
						});
					}


				},
				resolve: {
					arrParams: function () {
						return arrParams;
					}
				}
			});
		},
		editarCitaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Cita/ver_popup_form_cita',
				size: 'lg',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.fData = arrParams.cita;
					$scope.Form = {}
					$scope.fData.temporal = {};
					$scope.fArr = arrParams.fArr;
					$scope.fData.eliminados = [];
					$scope.fData.accion = 'edit';
					$scope.titleForm = 'Edición de Cita';

					$scope.fData.sede = $scope.fArr.listaSedes[0];


					/* DATEPICKERS */
					$scope.configDP = {};
					// $scope.configDP.today = function () {
						// if (arrParams.start) {
						// 	$scope.fData.fecha = arrParams.start.toDate();
						// } else {
						// 	$scope.fData.fecha = new Date();
						// }
					// };
					// $scope.configDP.today();

					$scope.configDP.clear = function () {
						$scope.fData.fecha = null;
					};

					$scope.configDP.dateOptions = {
						formatYear: 'yy',
						maxDate: new Date(2021, 5, 22),
						minDate: new Date(),
						startingDay: 1
					};

					$scope.configDP.open = function () {
						$scope.configDP.popup.opened = true;
					};

					$scope.configDP.formats = ['dd-MM-yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
					$scope.configDP.format = $scope.configDP.formats[0];
					$scope.configDP.altInputFormats = ['M!/d!/yyyy'];

					$scope.configDP.popup = {
						opened: false
					};
					arrParams.start = moment($scope.fData.fecha);
					$scope.fData.fecha = arrParams.start.toDate();
					/* END DATEPICKERS */

					/* TIMEPICKERS */
					$scope.configTP = {};
					$scope.configTP.tpHoraInicio = {};
					$scope.configTP.tpHoraInicio.hstep = 1;
					$scope.configTP.tpHoraInicio.mstep = 30;
					$scope.configTP.tpHoraInicio.ismeridian = true;
					$scope.configTP.tpHoraInicio.toggleMode = function () {
						$scope.configTP.tpHoraInicio.ismeridian = !$scope.configTP.tpHoraInicio.ismeridian;
					};
					$scope.configTP.tpHoraFin = angular.copy($scope.configTP.tpHoraInicio);

					var partes_hora1 = $scope.fData.horaDesde.split(':');
					var d = new Date();
					d.setHours(parseInt(partes_hora1[0]));
					d.setMinutes(parseInt(partes_hora1[1]));
					$scope.fData.hora_desde = d;

					var partes_hora2 = $scope.fData.horaHasta.split(':');
					//console.log(partes_hora2);
					var c = new Date();
					c.setHours(parseInt(partes_hora2[0]));
					c.setMinutes(parseInt(partes_hora2[1]));
					$scope.fData.hora_hasta = c;

					$scope.actualizarHoraFin = function () {
						$scope.fData.hora_hasta = moment($scope.fData.hora_desde).add(30, 'm').toDate();
					}
					/* END TIMEPICKERS */

					/* AUTOCOMPLETADO */

					$scope.getProductoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return ProductoServices.sListarProductoAutocomplete(params).then(function (rpta) {
							$scope.noResultsCT = false;
							if (rpta.flag === 0) {
								$scope.noResultsCT = true;
							}
							console.log('datos producto', rpta.datos);
							return rpta.datos;
						});
					}

					$scope.getSelectedProducto = function (item, model) {
						$scope.fData.temporal.idproducto = model.idproducto;
						$scope.fData.temporal.producto = model.producto;
						$scope.fData.temporal.tipoProducto = model.tipo_producto.descripcion;
						$scope.fData.temporal.precio = model.precio;
					}

					/* GRILLA DE PRODUCTOS */
					$scope.gridOptions = {
						rowHeight: 30,
						enableGridMenu: false,
						enableColumnMenus: false,
						enableRowSelection: false,
						enableSelectAll: false,
						enableFiltering: false,
						enableSorting: false,
						enableFullRowSelection: false,
						multiSelect: false,
						data: [],
						columnDefs: [
							{ field: 'idproducto', name: 'id', displayName: 'ID', minWidth: 80, width: 80 },
							{ field: 'producto', name: 'nombre', displayName: 'PRODUCTO', minWidth: 120 },
							{ field: 'tipoProducto', name: 'tipoProducto', displayName: 'Tipo Producto', minWidth: 120 },

							{ field: 'precio', name: 'precio', displayName: 'PRECIO', width: 120 },

							{
								field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
								cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
							}
						],
						onRegisterApi: function (gridApi) {
							$scope.gridApi = gridApi;
						}
					};

					$scope.getTableHeight = function () {
						var rowHeight = 30; // your row height
						var headerHeight = 30; // your header height
						var cant_filas = 4; // min 4
						if ($scope.gridOptions.data.length > cant_filas) {
							var cant_filas = $scope.gridOptions.data.length;
						}
						return {
							height: (cant_filas * rowHeight + headerHeight) + "px"
						};
					}

					$scope.getPaginationServerSideDet = function (loader) {
						if (loader) {
							blockUI.start('Procesando información...');
						}
						var arrParams = {
							datos: $scope.fData
						};
						CitaServices.sListarDetalleCita(arrParams).then(function (rpta) {
							if (rpta.datos.length == 0) {
								rpta.paginate = { totalRows: 0 };
							}
							// $scope.gridOptions.totalItems = rpta.paginate.totalRows;
							$scope.gridOptions.data = rpta.datos;
							$scope.calcularTotales();
							if (loader) {
								blockUI.stop();
							}
						});
					};
					$scope.getPaginationServerSideDet(true);

					$scope.agregarItemProducto = function () {
						if ($scope.fData.temporal.idproducto == null) {
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe seleccionar un producto.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						if ($scope.fData.temporal.precio == null ||
							$scope.fData.temporal.precio == "" ||
							$scope.fData.temporal.precio < 0) {
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'El precio no es válido.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						var producto_repetido = false;
						angular.forEach($scope.gridOptions.data, function (value, key) {
							if (value.idproducto == $scope.fData.temporal.idproducto) {
								producto_repetido = true;
								pinesNotifications.notify({
									title: 'Advertencia.',
									text: 'Ya está cargado este producto en la cesta.',
									type: 'warning',
									delay: 5000
								});
								return;
							}
						});

						if (producto_repetido === false) {
							$scope.gridOptions.data.push({
								id:null,
								citaId: $scope.fData.id,
								idproducto: $scope.fData.temporal.idproducto,
								producto: $scope.fData.temporal.producto,
								tipoProducto: $scope.fData.temporal.tipoProducto,
								precio: $scope.fData.temporal.precio,
								estado: 1
							});

							$scope.fData.temporal = {}
							$scope.calcularTotales();
						}
					}

					$scope.calcularTotales = function () {
						var totales = 0;
						angular.forEach($scope.gridOptions.data, function (value, key) {
							totales += parseFloat($scope.gridOptions.data[key].precio);
						});
						$scope.fData.total_a_pagar = totales.toFixed(2);
					}

					$scope.btnQuitarDeLaCesta = function (row) {
						if( row.entity.id > 0 ){
							row.entity.estado = 0;
							$scope.fData.eliminados.push(row.entity)
						}
						console.log('eliminados', $scope.fData.eliminados);
						var index = $scope.gridOptions.data.indexOf(row.entity);
						console.log('elimina', index);
						$scope.gridOptions.data.splice(index, 1);
						$scope.calcularTotales();
					}

					/* CALCULO DE IMC */
					$scope.calcularIMC = function () {
						$scope.fData.imc = null;
						if ($scope.fData.peso == null || $scope.fData.talla == null) {
							return;
						}
						console.log('calculo imc');

						if ($scope.fData.peso <= 0 || $scope.fData.talla <= 0) {
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Peso y Talla deben ser numericos mayores de cero',
								type: 'warning',
								delay: 5000
							});
							return;
						}
						var talla = parseInt($scope.fData.talla) / 100;
						$scope.fData.imc = (parseFloat($scope.fData.peso) / (parseFloat(talla * talla))).toFixed(2);
					}

					/* BOTONES FINALES */
					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
					}

					$scope.registrarCita = function () {

						if ($scope.fData.tipoCita == null || $scope.fData.tipoCita == "") {
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe seleccionar un Tipo de Cita.',
								type: 'warning',
								delay: 5000
							});
							return;
						}

						if ($scope.gridOptions.data.length <= 0) {
							pinesNotifications.notify({
								title: 'Advertencia.',
								text: 'Debe agregar al menos un producto.',
								type: 'warning',
								delay: 5000
							});
							return;
						}
						if ($scope.fData.hora_desde) {
							$scope.fData.hora_desde_str = $scope.fData.hora_desde.toLocaleTimeString();
						}

						if ($scope.fData.hora_hasta) {
							$scope.fData.hora_hasta_str = $scope.fData.hora_hasta.toLocaleTimeString();
						}
						$scope.fData.detalle = $scope.gridOptions.data;

						blockUI.start("Actualizando cita");
						CitaServices.sEditar($scope.fData).then(function (rpta) {
							if (rpta.flag === 1) {
								var pTitle = 'OK!';
								var pType = 'success';
								$uibModalInstance.dismiss($scope.fData);
							} else {
								var pTitle = 'Advertencia!';
								var pType = 'warning';

							}
							blockUI.stop();
							pinesNotifications.notify({
								title: pTitle,
								text: rpta.message,
								type: pType,
								delay: 5000
							});
						});
					}


				},
				resolve: {
					arrParams: function () {
						return arrParams;
					}
				}
			});
		}
	}

	return interfaz;
});