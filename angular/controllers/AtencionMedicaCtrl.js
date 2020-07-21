app.controller('AtencionMedicaCtrl',
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
	'ModalReporteFactory',
	'AtencionServices',
	// 'AtencionMedicaFactory',
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
		ModalReporteFactory,
		AtencionServices,
		// AtencionMedicaFactory,
		CitaServices

	) {
		$scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones
		$scope.fBusqueda = {};

		moment.tz.add('America/Lima|LMT -05 -04|58.A 50 40|0121212121212121|-2tyGP.o 1bDzP.o zX0 1aN0 1cL0 1cN0 1cL0 1PrB0 zX0 1O10 zX0 6Gp0 zX0 98p0 zX0|11e6');

		$scope.fArr.listaTipoCita = [
			{ id: '1', descripcion: 'POR CONFIRMAR' },
			{ id: '2', descripcion: 'CONFIRMADA' }
		];

		$scope.fArr.listaSedes = [
			{ id: '1', descripcion: 'HIGUERETA' }
		];

		$scope.fArr.listaTipoDocumento = [
			{ id: '0', descripcion: '--Seleccione tipo--' },
			{ id: 'DNI', descripcion: 'DOCUMENTO NACIONAL DE IDENTIDAD' },
			{ id: 'CEX', descripcion: 'CARNET DE EXTRANJERIA' },
			{ id: 'PAS', descripcion: 'PASAPORTE' },
			{ id: 'PTP', descripcion: 'PERMISO TEMPORAL DE PERMANENCIA' }
		];
		$scope.fArr.listaSexo = [
			{ id: '0', descripcion: '--Seleccione sexo--' },
			{ id: 'M', descripcion: 'MASCULINO' },
			{ id: 'F', descripcion: 'FEMENINO' }
		];
		$scope.fArr.listaOperadores = [
			{ id: '0', descripcion: '--Seleccione operador--' },
			{ id: 'CLARO', descripcion: 'CLARO' },
			{ id: 'MOVISTAR', descripcion: 'MOVISTAR' },
			{ id: 'ENTEL', descripcion: 'ENTEL' },
			{ id: 'BITEL', descripcion: 'BITEL' }
		];


		/* EVENTOS */
		$scope.menu = angular.element('.menu-dropdown');
		$scope.alertOnClick = function (event, jsEvent, view) {

			// $scope.btnEditarCita(event, true);
			$scope.event = event;
			//console.log(jsEvent);
			$scope.menu.addClass('open');
			$scope.menu.removeClass('left right');
			var wrap = angular.element(jsEvent.target).closest('.fc-event');
			var cal = wrap.closest('.calendar');
			var left = wrap.offset().left - cal.offset().left;
			var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
			if (right > $scope.menu.width()) {
				console.log('right');
				$scope.menu.addClass('left');
			} else if (left > $scope.menu.width()) {
				$scope.menu.addClass('right');
				console.log('left');
			}else{
				console.log('ninguno');

			}

			/* console.log('cal.offset().bottom',cal.offset().bottom);
			 console.log('cal.offset().top',cal.offset().top);
			 console.log('$scope.menu.height()',$scope.menu.height());*/

			$scope.event.posX = jsEvent.pageX - cal.offset().left;
			if ($scope.event.posX < 140) {
				$scope.event.posX = 140;
			}

			$scope.event.posY = jsEvent.pageY - cal.offset().top;
			if ($scope.event.posY > 620) {
				$scope.event.posY = 620;
			}
		}
		$scope.closeMenu = function () {
			$scope.menu.removeClass('open');
		}
		$scope.alertOnResize = function (event, delta) {
			angular.element('.calendar').fullCalendar('refetchEvents');
		};

		$scope.selectCell = function (date, end, jsEvent, view) {
			$scope.closeMenu();
			var typeView = angular.element('.calendar').fullCalendar('getView');
			if (typeView.type == 'month') {
				angular.element('.calendar').fullCalendar('gotoDate', date);
				angular.element('.calendar').fullCalendar('changeView', 'agendaDay');
			} else {
				console.log('Click');
			}
		}
		// $scope.alertOnDrop = function (event, delta) {
		// 	blockUI.start('Actualizando calendario...');
		// 	var datos = {
		// 		event: event,
		// 		delta: delta,
		// 	};
		// 	CitaServices.sMoverCita(datos).then(function (rpta) {
		// 		if (rpta.flag == 1) {
		// 			var pTitle = 'OK!';
		// 			var pType = 'success';
		// 		} else if (rpta.flag == 0) {
		// 			var pTitle = 'Advertencia!';
		// 			var pType = 'warning';
		// 		}
		// 		angular.element('.calendar').fullCalendar('refetchEvents');
		// 		pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
		// 		blockUI.stop();
		// 	});
		// };
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
		$scope.metodos.actualizarCalendario = function () {
			blockUI.start('Actualizando calendario...');
			angular.element('.calendar').fullCalendar('refetchEvents');
			blockUI.stop();
		}



		// $scope.btnVerCita = function (cita) {
		// 	console.log('Ver cita');
		// 	var arrParams = {
		// 		// 'start': start,
		// 		'cita': cita,
		// 		'fArr': $scope.fArr,
		// 		'metodos': $scope.metodos

		// 	};
		// 	AtencionMedicaFactory.editarCitaModal(arrParams);
		// }

		$scope.btnAnularAtencion = function (event) {
			// console.log('event ==>', event);
			var pMensaje = '¿Realmente desea anular la atención?';
			$bootbox.confirm(pMensaje, function (result) {
				if (result) {
					var arrParams = {
						idCita: event.id
					};
					blockUI.start('Procesando información...');
					CitaServices.sAnular(arrParams).then(function (rpta) {
						if (rpta.flag == 1) {
							var pTitle = 'OK!';
							var pType = 'success';
							// $scope.metodos.getPaginationServerSide();
							$scope.metodos.actualizarCalendario();
						} else if (rpta.flag == 0) {
							var pTitle = 'Error!';
							var pType = 'danger';
						} else {
							alert('Error inesperado');
						}
						pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
						blockUI.stop();
					});
				}
			});
		}

		/* CARGA DE DATOS DEL CALENDARIO*/
		$scope.eventsF = function (start, end, timezone, callback) {

			var events = [];
			blockUI.start('Actualizando calendario...');

			$scope.fBusqueda.desde = moment(start).tz('America/Lima').format('YYYY-MM-DD');
			$scope.fBusqueda.hasta = moment(end).tz('America/Lima').format('YYYY-MM-DD');
			$scope.fBusqueda.origen = 'ate';
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

		// VISTA LISTA

		$scope.fBusqueda.fechaDesde = moment().tz('America/Lima').startOf('month').format('DD-MM-YYYY');
		$scope.fBusqueda.fechaHasta = moment().tz('America/Lima').endOf('month').format('DD-MM-YYYY');
		$scope.mySelectionGrid = [];
		$scope.btnBuscar = function () {
			$scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
			$scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
		};
		var paginationOptions = {
			pageNumber: 1,
			firstRow: 0,
			pageSize: 100,
			sort: uiGridConstants.DESC,
			sortName: null,
			search: null
		};
		$scope.gridOptions = {
			rowHeight: 30,
			paginationPageSizes: [100, 500, 1000],
			paginationPageSize: 100,
			useExternalPagination: true,
			useExternalSorting: true,
			useExternalFiltering: true,
			enableGridMenu: true,
			enableSelectAll: true,
			enableFiltering: false,
			enableRowSelection: true,
			enableFullRowSelection: true,
			multiSelect: false,
			columnDefs: [
				{ field: 'id', name: 'ci.id', displayName: 'ID', width: '75', sort: { direction: uiGridConstants.DESC } },
				{ field: 'fechaCita', name: 'fechaCita', displayName: 'Fecha Cita', minWidth: 100, width: 100, enableFiltering: false },
				{ field: 'horaDesde', name: 'horaDesde', displayName: 'Hora Cita', minWidth: 100, width: 100, enableFiltering: false },
				{ field: 'tipoDocumento', name: 'tipoDocumento', displayName: 'Tipo Doc.', minWidth: 90, width: 115 },
				{ field: 'numeroDocumento', name: 'numeroDocumento', displayName: 'Nº Documento', minWidth: 90, width: 115 },
				{ field: 'paciente', name: 'paciente', displayName: 'Paciente', minWidth: 100 },
				// { field: 'medico', name: 'medico', displayName: 'Médico', minWidth: 120 },
				{ field: 'medico', name: 'medico', width: 130, cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.medico }}</div>',  displayName: 'Médico' },
				{ field: 'total', name: 'total', displayName: 'Total', minWidth: 100, width: 100 },
				{ field: 'estado', type: 'object', name: 'estado', displayName: 'Estado', maxWidth: 200, enableFiltering: false,
					cellTemplate: '<label style="box-shadow: 1px 1px 0 black; margin: 6px auto; display: block; width: 120px;" class="label {{ COL_FIELD.clase }} ">{{ COL_FIELD.string }}</label>'
				}
			],
			onRegisterApi: function (gridApi) {
				$scope.gridApi = gridApi;
				gridApi.selection.on.rowSelectionChanged($scope, function (row) {
					$scope.mySelectionGrid = gridApi.selection.getSelectedRows();
				});
				gridApi.selection.on.rowSelectionChangedBatch($scope, function (rows) {
					$scope.mySelectionGrid = gridApi.selection.getSelectedRows();
				});
				$scope.gridApi.core.on.sortChanged($scope, function (grid, sortColumns) {
					if (sortColumns.length == 0) {
						paginationOptions.sort = null;
						paginationOptions.sortName = null;
					} else {
						paginationOptions.sort = sortColumns[0].sort.direction;
						paginationOptions.sortName = sortColumns[0].name;
					}
					$scope.metodos.getPaginationServerSide(true);
				});
				gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
					paginationOptions.pageNumber = newPage;
					paginationOptions.pageSize = pageSize;
					paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
					$scope.metodos.getPaginationServerSide(true);
				});
				$scope.gridApi.core.on.filterChanged($scope, function (grid, searchColumns) {
					var grid = this.grid;
					paginationOptions.search = true;
					paginationOptions.searchColumn = {
						'ci.id': grid.columns[1].filters[0].term,
						'pa.tipoDocumento': grid.columns[4].filters[0].term,
						'pa.numeroDocumento': grid.columns[5].filters[0].term,
						"concat_ws(' ', pa.nombres, pa.apellidoPaterno, pa.apellidoMaterno)": grid.columns[6].filters[0].term,
						"concat_ws(' ', us.nombres, us.apellidos)": grid.columns[7].filters[0].term,
						'ci.total': grid.columns[87].filters[0].term,

					};
					$scope.metodos.getPaginationServerSide();
				});
			}
		};
		paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
		$scope.metodos.getPaginationServerSide = function (loader) {
			if (loader) {
				blockUI.start('Procesando información...');
			}
			$scope.fBusqueda.origen = 'ate';
			var arrParams = {
				paginate: paginationOptions,
				datos: $scope.fBusqueda
			};
			CitaServices.sListarCitasGrilla(arrParams).then(function (rpta) {
				if (rpta.datos.length == 0) {
					rpta.paginate = { totalRows: 0 };
				}
				$scope.gridOptions.totalItems = rpta.paginate.totalRows;
				$scope.gridOptions.data = rpta.datos;
				if (loader) {
					blockUI.stop();
				}
			});
			$scope.mySelectionGrid = [];
		};

		// $scope.btnExportarListaExcel = function () {
		// 	var arrParams = {
		// 		titulo: 'LISTADO DE ANALISIS',
		// 		datos: {
		// 			filtro: $scope.fBusqueda,
		// 			paginate: paginationOptions,
		// 			tituloAbv: 'LIST-CITA',
		// 			titulo: 'LISTADO DE CITAS',
		// 		},
		// 		salida: 'excel',
		// 		metodo: 'js'
		// 	}
		// 	arrParams.url = angular.patchURLCI + 'Reportes/listado_citas_excel',
		// 		ModalReporteFactory.getPopupReporte(arrParams);
		// }
	}
]);

app.service("AtencionServices", function ($http, $q, handleBehavior) {
	return({
		// sListarCitasGrilla: sListarCitasGrilla,
		// sListarCitaCalendario: sListarCitaCalendario,
		// sListarDetalleCita: sListarDetalleCita,
		// sRegistrar: sRegistrar,
		// sEditar: sEditar,
		// sMoverCita: sMoverCita,
		// sLiberarAtencion: sLiberarAtencion,
	});

	// function sLiberarAtencion(datos) {
	// 	var request = $http({
	// 		method: "post",
	// 		url: angular.patchURLCI + "Cita/liberar_atencion",
	// 		data: datos
	// 	});
	// 	return (request.then(handleBehavior.success, handleBehavior.error));
	// }
});
