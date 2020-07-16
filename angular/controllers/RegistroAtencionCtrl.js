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

		$scope.fData = {}

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