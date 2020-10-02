app.controller('DashboardCtrl', [
    '$scope',
    '$filter',
    '$state',
    '$stateParams',
    '$bootbox',
    '$log',
    '$timeout',
    'pinesNotifications',
    'uiGridConstants',
    'blockUI',
    'GraficoServices',
  function( $scope,
    $filter,
    $state,
    $stateParams,
    $bootbox,
    $log,
    $timeout,
    pinesNotifications,
    uiGridConstants,
    blockUI,
    GraficoServices
  ) {
    $scope.fBusquedaPR = {};
    $scope.fBusquedaPR.inicio = moment().format('01-MM-YYYY');
    $scope.fBusquedaPR.fin = moment().format('DD-MM-YYYY');

    $scope.fBusquedaPPM = {};
    $scope.fBusquedaPPM.inicio = moment().format('01-01-YYYY');
    $scope.fBusquedaPPM.fin = moment().format('DD-MM-YYYY');

    $scope.fData = {};
    $scope.fData.chartMedioContacto = { 
      chart: { 
        type: 'pie',
        height: 250,
      },
      title: {
        text: 'PACIENTES POR MEDIO DE CONTACTO'
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' 
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: false
          },
          showInLegend: true
        }
      },
      legend: {
        labelFormat: '{name} ( {y} )' 
      },
      series: [{ 
        name: 'Medio de contacto.',
        colorByPoint: true,
        data: [],
      }]
    };
    $scope.consultarPacientePorRecom = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPR
			};
			GraficoServices.sListarPacienteRecom(arrParams).then(function (rpta) {
				if (rpta.datos.length > 0) {
					$scope.fData.chartMedioContacto.series[0].data = angular.copy(rpta.datos);
				}
				blockUI.stop();
			});
		};
    $scope.consultarPacientePorRecom();

    $scope.fData.chartPacPorMes = { 
      chart: { 
        type: 'pie',
        height: 250,
      },
      title: {
        text: 'PACIENTES POR MES'
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' 
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: false
          },
          showInLegend: true
        }
      },
      legend: {
        labelFormat: '{name} ( {y} )' 
      },
      series: [{ 
        name: '',
        colorByPoint: true,
        data: [],
      }]
    };
    $scope.consultarPacientePorMes = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPPM
			};
			GraficoServices.sListarPacPorMes(arrParams).then(function (rpta) {
				if (rpta.datos.length > 0) {
					$scope.fData.chartPacPorMes.series[0].data = angular.copy(rpta.datos);
				}
				blockUI.stop();
			});
		};
    $scope.consultarPacientePorMes();
}]);

app.service("GraficoServices",function($http, $q, handleBehavior) {
  return({
    sListarPacienteRecom: sListarPacienteRecom,
    sListarPacPorMes: sListarPacPorMes,
  });
  function sListarPacienteRecom(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_paciente_recomendacion",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarPacPorMes(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_paciente_mes",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
});
