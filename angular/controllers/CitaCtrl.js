app.controller('CitaCtrl',
	['$scope',
	'$filter',
	'$state',
	'$stateParams',
	'$uibModal',
	'$bootbox',
	'$log',
	'$timeout',
	'pinesNotifications',
	'uiGridConstants',
	'blockUI',
	'ReservaCitasFactory',

	function (
		$scope,
		$filter,
		$state,
		$stateParams,
		$uibModal,
		$bootbox,
		$log,
		$timeout,
		pinesNotifications,
		uiGridConstants,
		blockUI,
		ReservaCitasFactory

	) {
		$scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones

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
			$scope.event = event;
			//console.log(event,jsEvent,'event,jsEvent');
			$scope.menu.addClass('open');
			$scope.menu.removeClass('left right');
			var wrap = angular.element(jsEvent.target).closest('.fc-event');
			var cal = wrap.closest('.calendar');
			var left = wrap.offset().left - cal.offset().left;
			var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
			if (right > $scope.menu.width()) {
				$scope.menu.addClass('left');
			} else if (left > $scope.menu.width()) {
				$scope.menu.addClass('right');
			}
			$scope.event.posX = jsEvent.pageX - cal.offset().left;
			if ($scope.event.posX < 140) {
				$scope.event.posX = 140;
			}

			$scope.event.posY = jsEvent.pageY - cal.offset().top;
			if ($scope.event.posY > 620) {
				$scope.event.posY = 620;
			}
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
			ReservaCitasServices.sMoverCita(datos).then(function (rpta) {
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


		/* CARGA DE DATOS */
		$scope.eventsF = function (start, end, timezone, callback) {
			var events = [];
			// blockUI.start('Actualizando calendario...');
			//console.log(start, end,'start, end');
			//console.log(start.toLocaleTimeString(), end.toLocaleTimeString(),'start.toLocaleTimeString(), end.toLocaleTimeString()');
			//console.log(start,end,'moment(start).tz("America/Lima").format(YYYY-MM-DD)');
			/* $scope.fBusqueda.desde = moment(start).tz('America/Lima').format('YYYY-MM-DD');
			$scope.fBusqueda.hasta = moment(end).tz('America/Lima').format('YYYY-MM-DD');
			ReservaCitasServices.sListarCitaCalendario($scope.fBusqueda).then(function (rpta) {
				if (rpta.flag == 1) {
					angular.forEach(rpta.datos, function (row, key) {
						row.start = moment(row.start);
						row.end = moment(row.end);
					});
					events = rpta.datos;
					callback(events);
				}
				blockUI.stop();
			}); */
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
					// console.log( event, $('.tooltip-event') );
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

app.factory("ReservaCitasFactory", function ($uibModal, pinesNotifications, blockUI, $timeout) {
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
					$scope.fData.temporal = {};
					$scope.fArr = arrParams.fArr;
					$scope.fData.accion = 'reg';
					$scope.titleForm = 'Registro de Cita';
					$scope.fArr.listaTipoCita.splice(0, 0, { id: '0', descripcion: '--Seleccione tipo cita--' });
					$scope.fData.tipo_cita = $scope.fArr.listaTipoCita[0];

					$scope.fData.sede = $scope.fArr.listaSedes[0];

					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
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

							{ field: 'precio', name: 'precio', displayName: 'PRECIO', width: 120 },

							{
								field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
								cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnAnular(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
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
						console.log('$scope.fData.temporal => ', $scope.fData.temporal);
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
								precio: $scope.fData.temporal.precio,
							});

							// limpiamos
							$scope.fData.temporal.idproducto = null;
							$scope.fData.temporal.producto = null;
							$scope.fData.temporal.precio = null;
							// $scope.calcularTotales();
						}
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