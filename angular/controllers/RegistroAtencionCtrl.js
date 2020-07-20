app.controller('RegistroAtencionCtrl', [
	'$scope',
	'$filter',
	'$state',
	'$stateParams',
	'$uibModal',
	'$bootbox',
	'pinesNotifications',
	'uiGridConstants',
	'blockUI',
	'ModalReporteFactory',
	'RegistroAtencionService',
	'DiagnosticoServices',
	function ($scope, $filter, $state, $stateParams, $uibModal, $bootbox, pinesNotifications, uiGridConstants,
		blockUI,
		ModalReporteFactory,
		RegistroAtencionService,
		DiagnosticoServices
	){
		$scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones
		$scope.fArr.listaTipoDiagnostico = [
			{ id: '', descripcion: '--Seleccione tipo de diagnóstico--' },
			{ id: 'PRESUNTIVO', descripcion: 'PRESUNTIVO' },
			{ id: 'DEFINITIVO', descripcion: 'DEFINITIVO' }
    ];
		$scope.fData = {}
		$scope.fData.temporal = {};
		
		var datos = {
			id: $stateParams.id
		}
		RegistroAtencionService.sGetCitaById(datos).then(function (rpta) {
			console.log('datos', rpta.datos);
			$scope.fData = rpta.datos;
			$scope.fData.temporal = {};
			$scope.fData.temporal.tipoDiagnostico = $scope.fArr.listaTipoDiagnostico[0];
			$scope.getPaginationServerSideDet(true);

		});
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
					text: 'Peso y Talla deben ser numericos mayores de cero.',
					type: 'warning',
					delay: 5000
				});
				return;
			}
			var talla = parseInt($scope.fData.talla) / 100;
			$scope.fData.imc = (parseFloat($scope.fData.peso) / (parseFloat(talla*talla))).toFixed(2);
		}
		$scope.grabarAtencionMedica = function(){
			$scope.fData.diagnostico = $scope.gridOptions.data;
			if(!($scope.fData.diagnostico.length > 0)){
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Debe seleccionar al menos un diagnóstico.',
					type: 'warning',
					delay: 5000
				});
				return;
			}
			blockUI.start("Registrando atencion...");
			RegistroAtencionService.sRegistrarAtencion($scope.fData).then(function (rpta) {
				if (rpta.flag === 1) {
					var pTitle = 'OK!';
					var pType = 'success';
					// $scope.metodos.actualizarCalendario(true);
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
		// recetas
		$scope.grabarReceta = function(){
			blockUI.start("Registrando receta...");

			RegistroAtencionService.sRegistrarReceta($scope.fData).then(function (rpta) {
				if (rpta.flag === 1) {
					var pTitle = 'OK!';
					var pType = 'success';
					$scope.fData.idreceta = rpta.idreceta;
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
		$scope.getDiagnosticoAutocomplete = function (value) {
			var params = {
				searchText: value,
			};
			return DiagnosticoServices.sListarDiagnosticoAutocomplete(params).then(function (rpta) {
				$scope.noResultsPr = false;
				if (rpta.flag === 0) {
					$scope.noResultsPr = true;
				}
				console.log('datos', rpta.datos);
				return rpta.datos;
			});
		}

		$scope.getSelectedDiagnostico = function (item, model) {
			$scope.fData.temporal.iddiagnostico = model.iddiagnostico;
			$scope.fData.temporal.diagnostico = model.descripcion;
			$scope.fData.temporal.codigo = model.codigo;
			// $scope.fData.temporal.precio = model.precio;
		}
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
				{ field: 'iddiagnostico', name: 'id', displayName: 'ID', minWidth: 80, width: 80, visible: false },
				{ field: 'codigo', name: 'codigo', displayName: 'CODIGO CIE10', minWidth: 120, width: 120 },
				{ field: 'diagnostico', name: 'nombre', displayName: 'DIAGNOSTICO CIE10', minWidth: 120 },
				{ field: 'tipoDiagnostico', name: 'tipoDiagnostico', minWidth: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'TIPO' },
				// { field: 'tipoDiagnostico', name: 'tipoDiagnostico', displayName: 'TIPO', minWidth: 120 },
				{
					field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
					cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
				}
			],
			onRegisterApi: function (gridApi) {
				$scope.gridApi = gridApi;
				// gridApi.edit.on.afterCellEdit($scope, function (rowEntity, colDef, newValue, oldValue) {
				// 	if (newValue != oldValue) {
				// 		$scope.calcularTotales();
				// 	}
				// });
			}
		};
		$scope.getPaginationServerSideDet = function (loader) {
			if (loader) {
				blockUI.start('Procesando información...');
			}
			var arrParams = {
				datos: {
					citaId: $scope.fData.id
				}
			};
			DiagnosticoServices.sListarDetalleDx(arrParams).then(function (rpta) {
				if (rpta.datos.length == 0) {
					rpta.paginate = { totalRows: 0 };
				}
				// $scope.gridOptions.totalItems = rpta.paginate.totalRows;
				$scope.gridOptions.data = rpta.datos;
				// $scope.calcularTotales();
				if (loader) {
					blockUI.stop();
				}
			});
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
		$scope.agregarItemDiagnostico = function(){
			if ($scope.fData.temporal.iddiagnostico == null ){
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Debe seleccionar un diagnostico.',
					type: 'warning',
					delay: 5000
				});
				return;
			}

			if ($scope.fData.temporal.tipoDiagnostico.id == null ||
				$scope.fData.temporal.tipoDiagnostico.id == "" ||
				$scope.fData.temporal.tipoDiagnostico.id < 0)
			{
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'El tipo de diagnóstico no es válido.',
					type: 'warning',
					delay: 5000
				});
				return;
			}

			var producto_repetido = false;
			angular.forEach($scope.gridOptions.data, function (value, key) {
				if (value.iddiagnostico == $scope.fData.temporal.iddiagnostico) {
					producto_repetido = true;
					pinesNotifications.notify({
						title: 'Advertencia.',
						text: 'Ya está cargado este diagnostico.',
						type: 'warning',
						delay: 5000
					});
					return;
				}
			});

			if (producto_repetido === false) {
				$scope.gridOptions.data.push({
					iddiagnostico: $scope.fData.temporal.iddiagnostico,
					codigo: $scope.fData.temporal.codigo,
					diagnostico: $scope.fData.temporal.diagnostico,
					tipoDiagnostico: $scope.fData.temporal.tipoDiagnostico,
					// precio: $scope.fData.temporal.precio,
				});

				$scope.fData.temporal.iddiagnostico = null;
				$scope.fData.temporal.codigo = null;
				$scope.fData.temporal.diagnostico = null;
				$scope.fData.temporal.tipoDiagnostico = $scope.fArr.listaTipoDiagnostico[0];
				// $scope.calcularTotales();
			}
		}
		$scope.btnQuitarDeLaCesta = function (row) {
			var index = $scope.gridOptions.data.indexOf(row.entity);
			$scope.gridOptions.data.splice(index, 1);
			// $scope.calcularTotales();
		}
		// IMPRIMIR RECETA
		$scope.btnImprimirReceta = function () {
			var arrParams = {
				titulo: 'RECETA',
				datos: {
					cita: $scope.fData,
					salida: 'pdf',
					tituloAbv: 'REC',
					titulo: 'RECETA'
				},
				envio_correo: 'si',
				metodo: 'php',
				url: angular.patchURLCI + "Reportes/generar_pdf_receta"
			}
			ModalReporteFactory.getPopupReporte(arrParams);
		}
	}
]);
app.service("RegistroAtencionService", function ($http, $q, handleBehavior){
	return({
		sGetCitaById: sGetCitaById,
		sRegistrarAtencion: sRegistrarAtencion,
		sRegistrarReceta: sRegistrarReceta,
	});
	function sGetCitaById(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_cita_por_id",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sRegistrarAtencion(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/registrar_atencion",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sRegistrarReceta(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/registrar_receta",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
});