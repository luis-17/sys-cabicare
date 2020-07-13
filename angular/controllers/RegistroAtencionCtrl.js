app.controller('RegistroAtencionCtrl', [
	'$scope',
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
	'$location',
	'RegistroAtencionService',
	function ($scope, $filter, $state, $stateParams, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, $location, RegistroAtencionService){

		console.log('idCita', $stateParams.id);
		var datos = {
			id: $stateParams.id
		}
		RegistroAtencionService.sGetCitaById(datos).then(function (rpta) {
			console.log('datos', rpta.datos);
			$scope.fData = rpta.datos;
		});

		$scope.grabarAtencionMedica = function(){
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
		var paginationOptionsREC = {
			pageNumber: 1,
			firstRow: 0,
			pageSize: 10,
			sort: uiGridConstants.DESC,
			sortName: null,
			search: null
		};
		$scope.mySelectionRECGrid = [];
		$scope.btnToggleFiltering = function () {
			$scope.gridOptionsRecetaMedica.enableFiltering = !$scope.gridOptionsRecetaMedica.enableFiltering;
			$scope.gridApiREC.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
		};
		$scope.gridOptionsRecetaMedica = {
			paginationPageSizes: [10, 50, 100, 500, 1000],
			paginationPageSize: 10,
			minRowsToShow: 8,
			useExternalPagination: true,
			useExternalSorting: true,
			useExternalFiltering: true,
			enableGridMenu: true,
			enableRowSelection: true,
			enableSelectAll: true,
			enableFiltering: false,
			enableFullRowSelection: true,
			multiSelect: false,
			data: [],
			columnDefs: [

				{ field: 'fecha', name: 'fecha_receta', displayName: 'Fecha', width: 120, enableCellEdit: false },
				{ field: 'indicaciones', name: 'indicacionesGenerales', displayName: 'Indicaciones Generales', minWidth: 150, enableCellEdit: false },

				{
					field: 'accion', displayName: 'Acción', enableCellEdit: false, width: 100,
					cellTemplate: '<button type="button" class="btn btn-sm btn-danger center-block" ng-click="grid.appScope.btnAnularMedicamentoReceta(row)"> <i class="fa fa-trash"></i> </button>'
				}
			],
			onRegisterApi: function (gridApiREC) {
				$scope.gridApiREC = gridApiREC;
				gridApiREC.selection.on.rowSelectionChanged($scope, function (row) {
					$scope.mySelectionRECGrid = gridApiREC.selection.getSelectedRows();
				});
				gridApiREC.selection.on.rowSelectionChangedBatch($scope, function (rows) {
					$scope.mySelectionRECGrid = gridApiREC.selection.getSelectedRows();
				});
				$scope.gridApiREC.core.on.sortChanged($scope, function (grid, sortColumns) {
					if (sortColumns.length == 0) {
						paginationOptionsREC.sort = null;
						paginationOptionsREC.sortName = null;
					} else {
						paginationOptionsREC.sort = sortColumns[0].sort.direction;
						paginationOptionsREC.sortName = sortColumns[0].name;
					}
					$scope.getPaginationRECServerSide();
				});
				$scope.gridApiREC.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
					console.log(newPage, pageSize);
					paginationOptionsREC.pageNumber = newPage;
					paginationOptionsREC.pageSize = pageSize;
					paginationOptionsREC.firstRow = (paginationOptionsREC.pageNumber - 1) * paginationOptionsREC.pageSize;
					$scope.getPaginationRECServerSide();
				});
			}
		};
		paginationOptionsREC.sortName = $scope.gridOptionsRecetaMedica.columnDefs[0].name;
	}
]);
app.service("RegistroAtencionService", function ($http, $q, handleBehavior){
	return({
		sGetCitaById: sGetCitaById,
		sRegistrarAtencion: sRegistrarAtencion
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
});