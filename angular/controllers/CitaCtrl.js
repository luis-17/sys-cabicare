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
	'ModalReporteFactory',
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
		ModalReporteFactory,
		ReservaCitasFactory,
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
			{ id: 'PTP', descripcion: 'PERMISO TEMPORAL DE PERMANENCIA' },
			{id: 'CED', descripcion: 'CEDULA' },
      		{id: 'CR', descripcion: 'CARNET DE REFUGIO' }
		];
		$scope.fArr.listaSexo = [
			{ id: '0', descripcion: '--Seleccione sexo--' },
			{ id: 'M', descripcion: 'MASCULINO' },
			{ id: 'F', descripcion: 'FEMENINO' }
    ];
    $scope.fArr.listaMedioContacto = [
		{ id: '', descripcion: '--Seleccione medio de contacto--' },
		{ id: 'POR RECOMENDACION', descripcion: 'POR RECOMENDACION' },
		{ id: 'POR GOOGLE', descripcion: 'POR GOOGLE' },
		{ id: 'POR FACEBOOK', descripcion: 'POR FACEBOOK' },
		{ id: 'POR INSTAGRAM', descripcion: 'POR INSTAGRAM' },
		{ id: 'POR OTRAS REDES SOCIALES', descripcion: 'POR OTRAS REDES SOCIALES' }
    ];
    $scope.fArr.listaMetodoPago = [
			{ id: '', descripcion: '--Seleccione método de pago--' },
			{ id: 'EFECTIVO', descripcion: 'EFECTIVO' },
			{ id: 'TARJETA DE DÉBITO', descripcion: 'TARJETA DE DÉBITO' },
			{ id: 'TARJETA DE CRÉDITO', descripcion: 'TARJETA DE CRÉDITO' },
			{ id: 'TRANSFERENCIA', descripcion: 'TRANSFERENCIA' },
			{ id: 'SEGURO', descripcion: 'SEGURO' }
		];
		$scope.fArr.listaOperadores = [
			{ id: '0', descripcion: '--Seleccione operador--' },
			{ id: 'CLARO', descripcion: 'CLARO' },
			{ id: 'MOVISTAR', descripcion: 'MOVISTAR' },
			{ id: 'ENTEL', descripcion: 'ENTEL' },
			{ id: 'BITEL', descripcion: 'BITEL' }
		];


		/* EVENTOS */
		$scope.alertOnClick = function (event, jsEvent, view) {
			console.log('event', event);
			$scope.btnEditarCita(event, true);

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
		$scope.metodos.actualizarCalendario = function (block) {
			blockUI.start('Actualizando calendario...');
			angular.element('.calendar').fullCalendar('refetchEvents');
			blockUI.stop();
		}

		$scope.btnAgregarCita = function (start, end) {
			console.log('Agrega cita');
			var arrParams = {
				'start': start || null,
				'end': end || null,
				'fArr': $scope.fArr,
				'metodos': $scope.metodos
			};
			ReservaCitasFactory.agregarCitaModal(arrParams);
		}
		$scope.btnEditarCita = function (cita, bool) {
			console.log('Edita cita');
			var arrParams = {
				// 'start': start,
				'cita': cita,
				'fArr': $scope.fArr,
				'metodos': $scope.metodos,
				'bool': bool
			};
			ReservaCitasFactory.editarCitaModal(arrParams);
    }
    $scope.btnMetodoPago = function (cita) {
			console.log('ver metodo de pago');
			var arrParams = {
				// 'start': start,
				'cita': cita,
				'fArr': $scope.fArr,
				'metodos': $scope.metodos
			};
			ReservaCitasFactory.verMetodoPago(arrParams);
    }

		$scope.btnAnular = function () {
			var pMensaje = '¿Realmente desea anular el registro?';
			$bootbox.confirm(pMensaje, function (result) {
				if (result) {
					var arrParams = {
						idCita: $scope.mySelectionGrid[0].id
					};
					blockUI.start('Procesando información...');
					CitaServices.sAnular(arrParams).then(function (rpta) {
						if (rpta.flag == 1) {
							var pTitle = 'OK!';
							var pType = 'success';
							$scope.metodos.getPaginationServerSide();
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
			$scope.fBusqueda.origen = 'cit';
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
				{ field: 'tipoDocumento', name: 'tipoDocumento', displayName: 'Tipo Doc.', minWidth: 80, width: 80, visible: false },
				{ field: 'numeroDocumento', name: 'numeroDocumento', displayName: 'Nº Documento', minWidth: 90, width: 115 },
				{ field: 'paciente', name: 'paciente', displayName: 'Paciente', minWidth: 100 },
        // { field: 'medico', name: 'medico', displayName: 'Médico', minWidth: 120 },
        { field: 'medico', name: 'medico', width: 130,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.medico }}</div>',  displayName: 'Médico' },
        { field: 'total', name: 'total', displayName: 'Total', minWidth: 100, width: 100 },
        { field: 'medioContacto', name: 'ci.medioContacto', width: 160, visible: false,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Medio Contacto' },
        { field: 'metodoPago', name: 'ci.metodoPago', width: 160, visible: false,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Método de Pago' },
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

		$scope.btnExportarListaExcel = function () {
			var arrParams = {
				titulo: 'LISTADO DE ANALISIS',
				datos: {
					filtro: $scope.fBusqueda,
					paginate: paginationOptions,
					tituloAbv: 'LIST-CITA',
					titulo: 'LISTADO DE CITAS',
				},
				salida: 'excel',
				metodo: 'js'
			}
			arrParams.url = angular.patchURLCI + 'Reportes/listado_citas_excel',
				ModalReporteFactory.getPopupReporte(arrParams);
		}
	}
]);

app.service("CitaServices", function ($http, $q, handleBehavior) {
	return({
		sListarCitasGrilla: sListarCitasGrilla,
		sListarCitaCalendario: sListarCitaCalendario,
		sListarDetalleCita: sListarDetalleCita,
		sRegistrar: sRegistrar,
		sEditar: sEditar,
    sMoverCita: sMoverCita,
    sAgregarMetodoPago: sAgregarMetodoPago,
		sAnular: sAnular,
	});

	function sListarCitasGrilla(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_citas_en_grilla",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
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
	function sMoverCita(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/mover_cita",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
  }
  function sAgregarMetodoPago(datos) {
    var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/agregar_metodo_pago",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
  }
	function sAnular(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/anular",
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
		UsuarioServices,
		CitaServices,
		PacienteFactory
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
					$scope.metodos = arrParams.metodos;
					$scope.fData.accion = 'reg';

					$scope.btnNuevo = function(){
						var arrP = {
							'metodos': $scope.metodos,
							'fArr': $scope.fArr,
							callback: function (e,rpta) {
								console.log('rpta', rpta);
								$scope.fData.paciente = e.nombres + ' ' + e.apellido_paterno + ' ' + e.apellido_materno;
								$scope.fData.numeroDocumento = e.num_documento;
								$scope.fData.pacienteId = rpta.datos;
							}
						}
						PacienteFactory.regPacienteModal(arrP);
					}

					$scope.btnBuscarPaciente = function(){

						$uibModal.open({
							templateUrl: angular.patchURLCI + 'Paciente/ver_popup_busqueda_paciente',
							size: 'lg',
							backdrop: 'static',
							keyboard: false,
							controller: function ($scope, $uibModalInstance, uiGridConstants, arrToModal) {
								$scope.titleForm = 'Búsqueda de Paciente';

								$scope.fData = arrToModal.fData;
								$scope.cancel = function () {
									$uibModalInstance.dismiss('cancel');
								}

								var paginationOptions = {
									pageNumber: 1,
									firstRow: 0,
									pageSize: 100,
									sort: uiGridConstants.DESC,
									sortName: null,
									search: null
								};
								$scope.gridOptionsBC = {
									rowHeight: 30,
									paginationPageSizes: [100, 500, 1000],
									paginationPageSize: 100,
									useExternalPagination: true,
									useExternalSorting: true,
									useExternalFiltering: true,
									enableGridMenu: true,
									enableSelectAll: true,
									enableFiltering: true,
									enableRowSelection: true,
									enableFullRowSelection: true,
									multiSelect: false,
									columnDefs: [
										{ field: 'idpaciente', name: 'pa.id', displayName: 'ID', width: '75', sort: { direction: uiGridConstants.DESC } },
										{
											field: 'tipo_documento', name: 'pa.tipoDocumento', width: 160,
											cellTemplate: '<div class="ui-grid-cell-contents text-left ">' + '{{ COL_FIELD.descripcion }}</div>', displayName: 'Tipo Documento'
										},
										{ field: 'num_documento', name: 'pa.numeroDocumento', displayName: 'Documento', minWidth: 90 },
										{ field: 'nombres', name: 'pa.nombres', displayName: 'Nombres', minWidth: 100 },
										{ field: 'apellido_paterno', name: 'pa.apellidoPaterno', displayName: 'Ap. Paterno', minWidth: 100 },
										{ field: 'apellido_materno', name: 'pa.apellidoMaterno', displayName: 'Ap. Materno', minWidth: 100 },
										{ field: 'fecha_nacimiento', name: 'pa.fechaNacimiento', displayName: 'Fecha Nac.', minWidth: 120 },
										{ field: 'email', name: 'pa.email', displayName: 'E-mail', minWidth: 100 },
										{ field: 'celular', name: 'pa.celular', displayName: 'Celular', minWidth: 100 }
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
												'pa.id': grid.columns[1].filters[0].term,
												'pa.tipoDocumento': grid.columns[2].filters[0].term,
												'pa.numeroDocumento': grid.columns[3].filters[0].term,
												'pa.nombres': grid.columns[4].filters[0].term,
												'pa.apellidoPaterno': grid.columns[5].filters[0].term,
												'pa.apellidoMaterno': grid.columns[6].filters[0].term,
												'pa.fechaNacimiento': grid.columns[7].filters[0].term,
												'pa.email': grid.columns[8].filters[0].term,
												'pa.celular': grid.columns[9].filters[0].term,
											};
											$scope.getPaginationServerSide();
										});
									}
								};
								paginationOptions.sortName = $scope.gridOptionsBC.columnDefs[0].name;
								$scope.getPaginationServerSide = function (loader) {
									if (loader) {
										blockUI.start('Procesando información...');
									}
									var arrParams = {
										paginate: paginationOptions
									};
									PacienteServices.sListar(arrParams).then(function (rpta) {
										if (rpta.datos.length == 0) {
											rpta.paginate = { totalRows: 0 };
										}
										$scope.gridOptionsBC.totalItems = rpta.paginate.totalRows;
										$scope.gridOptionsBC.data = rpta.datos;
										if (loader) {
											blockUI.stop();
										}
									});
									$scope.mySelectionGrid = [];
								};
								$scope.getPaginationServerSide(true);

								$scope.aceptar = function(){
									$scope.fData.paciente = $scope.mySelectionGrid[0].nombres;
									$scope.fData.pacienteId = $scope.mySelectionGrid[0].idpaciente;
									$scope.fData.numeroDocumento = $scope.mySelectionGrid[0].num_documento;
									$uibModalInstance.dismiss('cancel');
								}
							},
							resolve: {
								arrToModal: function () {
									return {
										fData: $scope.fData
									}
								}
							}
						});
					}

					$scope.titleForm = 'Registro de Cita';
					// $scope.fArr.listaTipoCita.splice(0, 0, { id: "", descripcion: '--Seleccione tipo cita--' });
					$scope.fData.medioContacto = $scope.fArr.listaMedioContacto[0];

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
									$scope.btnNuevo();
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
					/* MEDICOS */
					$scope.getMedicoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return UsuarioServices.sListarMedicoAutocomplete(params).then(function (rpta) {
							$scope.noResultsMe = false;
							if (rpta.flag === 0) {
								$scope.noResultsMe = true;
							}
							return rpta.datos;
						});
					}

					$scope.getSelectedMedico = function (item, model) {
						$scope.fData.idmedico = model.id;

					}

					/* PRODUCTO */
					$scope.getProductoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return ProductoServices.sListarProductoAutocomplete(params).then(function (rpta) {
							$scope.noResultsPr = false;
							if (rpta.flag === 0) {
								$scope.noResultsPr = true;
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
						enableCellEdit: false,
						multiSelect: false,
						data: [],
						columnDefs: [
							{ field: 'idproducto', name: 'id', displayName: 'ID', minWidth: 80, width: 80 },
							{ field: 'producto', name: 'nombre', displayName: 'PRODUCTO', minWidth: 120 },
							{ field: 'tipoProducto', name: 'tipoProducto', displayName: 'Tipo Producto', minWidth: 120 },

							{ field: 'precio', name: 'precio', displayName: 'PRECIO (S/)', width: 120, enableCellEdit: true, cellClass: 'ui-editCell' },

							{
								field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
								cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
							}
						],
						onRegisterApi: function (gridApi) {
							$scope.gridApi = gridApi;
							gridApi.edit.on.afterCellEdit($scope, function (rowEntity, colDef, newValue, oldValue) {
								if (newValue != oldValue) {
									$scope.calcularTotales();
								}
							});
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

					$scope.aceptar = function(){

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
								$scope.metodos.actualizarCalendario(true);
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
				controller: function ($scope, $uibModalInstance, arrParams, $bootbox) {
					blockUI.stop();
					$scope.fData = arrParams.cita;
					$scope.Form = {}
					$scope.fData.temporal = {};
					$scope.fArr = arrParams.fArr;
					$scope.metodos = arrParams.metodos;
					$scope.fData.eliminados = [];
					$scope.fData.accion = 'edit';
					$scope.bool = arrParams.bool;
					$scope.titleForm = 'Edición de Cita';

					$scope.fData.sede = $scope.fArr.listaSedes[0];
          //BINDEO MEDIO CONTACTO
          var objIndexCp = $scope.fArr.listaMedioContacto.filter(function(obj) {
            return obj.id == $scope.fData.medioContacto.id;
          }).shift();
          $scope.fData.medioContacto = objIndexCp;

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
					/* MEDICOS */
					$scope.getMedicoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return UsuarioServices.sListarMedicoAutocomplete(params).then(function (rpta) {
							$scope.noResultsMe = false;
							if (rpta.flag === 0) {
								$scope.noResultsMe = true;
							}
							return rpta.datos;
						});
					}

					$scope.getSelectedMedico = function (item, model) {
						$scope.fData.idmedico = model.id;

					}

					$scope.getProductoAutocomplete = function (value) {
						var params = {
							searchText: value,
						}
						return ProductoServices.sListarProductoAutocomplete(params).then(function (rpta) {
							$scope.noResultsPr = false;
							if (rpta.flag === 0) {
								$scope.noResultsPr = true;
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
						enableCellEdit: false,
						multiSelect: false,
						data: [],
						columnDefs: [
							{ field: 'idproducto', name: 'id', displayName: 'ID', minWidth: 80, width: 80 },
							{ field: 'producto', name: 'nombre', displayName: 'PRODUCTO', minWidth: 120 },
							{ field: 'tipoProducto', name: 'tipoProducto', displayName: 'Tipo Producto', minWidth: 120 },

							{ field: 'precio', name: 'precio', displayName: 'PRECIO (S/)', width: 120, enableCellEdit: true, cellClass: 'ui-editCell' },

							{
								field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
								cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
							}
						],
						onRegisterApi: function (gridApi) {
							$scope.gridApi = gridApi;
							gridApi.edit.on.afterCellEdit($scope, function (rowEntity, colDef, newValue, oldValue) {
								if (newValue != oldValue) {
									$scope.calcularTotales();
								}
							});
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

					$scope.aceptar = function () {

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
								$scope.metodos.actualizarCalendario(true);
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

					$scope.btnAnular = function () {
						console.log('fdata', $scope.fData);
						var pMensaje = '¿Realmente desea anular el registro?';
						$bootbox.confirm(pMensaje, function (result) {
							if (result) {
								var arrParams = {
									idCita: $scope.fData.id
								};
								blockUI.start('Procesando información...');
								CitaServices.sAnular(arrParams).then(function (rpta) {
									if (rpta.flag == 1) {
										var pTitle = 'OK!';
										var pType = 'success';
										$uibModalInstance.dismiss($scope.fData);
										$scope.metodos.actualizarCalendario(true);
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

				},
				resolve: {
					arrParams: function () {
						return arrParams;
					}
				}
			});
    },
    verMetodoPago: function (arrParams) {
      blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Cita/ver_popup_form_metodo_pago',
				size: 'md',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
          // $scope.fData = {};
          console.log('arrParams ===>', arrParams)
          $scope.fDataMP = arrParams.cita;
          console.log('$scope.fDataMP ==>', $scope.fDataMP);
					// $scope.Form = {}
					// $scope.fData.temporal = {};
					$scope.fArr = arrParams.fArr;
					$scope.metodos = arrParams.metodos;
					$scope.fDataMP.accion = 'edit';

					$scope.titleForm = 'Método de Pago';
					// $scope.fArr.listaTipoCita.splice(0, 0, { id: "", descripcion: '--Seleccione tipo cita--' });
					// $scope.fData.medioContacto = $scope.fArr.listaMedioContacto[0];

					// $scope.fData.sede = $scope.fArr.listaSedes[0];

          //BINDEO METODO PAGO
          var objIndexMp = $scope.fArr.listaMetodoPago.filter(function(obj) {
            return obj.id == $scope.fDataMP.metodoPago.id;
          }).shift();
          $scope.fDataMP.metodoPago = objIndexMp;
          if ( !$scope.fDataMP.metodoPago ) {
            $scope.fDataMP.metodoPago = $scope.fArr.listaMetodoPago[0];
          }

					/* BOTONES FINALES */
					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
					}

					$scope.aceptar = function(){
						blockUI.start("Agregando método de pago");
						CitaServices.sAgregarMetodoPago($scope.fDataMP).then(function(rpta){
							if (rpta.flag === 1) {
								var pTitle = 'OK!';
								var pType = 'success';
								$uibModalInstance.dismiss($scope.fDataMP);
                // $scope.metodos.actualizarCalendario(true);
                $scope.metodos.getPaginationServerSide();
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