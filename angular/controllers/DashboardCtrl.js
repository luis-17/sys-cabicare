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
    $scope.fArr = {};
    $scope.fArr.listaUltimosDist = [
      { id: 5, descripcion: 'TOP 5' },
      { id: 10, descripcion: 'TOP 10' },
      { id: 15, descripcion: 'TOP 15' },
    ];
    $scope.fArr.listaMC = [
      { id: 'PC', descripcion: 'POR CANTIDAD' },
      { id: 'PM', descripcion: 'POR MONTO' }
    ];
    $scope.fArr.listaAnio = [
      { id: 2020, descripcion: '2020' },
      { id: 2021, descripcion: '2021' },
      { id: 2022, descripcion: '2022' },
      { id: 2023, descripcion: '2023' },
      { id: 2024, descripcion: '2024' },
    ];

    $scope.fBusquedaPR = {};
    $scope.fBusquedaPR.inicio = moment().format('01-MM-YYYY');
    $scope.fBusquedaPR.fin = moment().format('DD-MM-YYYY');

    $scope.fBusquedaPPM = {};
    $scope.fBusquedaPPM.inicio = moment().format('01-01-YYYY');
    $scope.fBusquedaPPM.fin = moment().format('DD-MM-YYYY');

    $scope.fBusquedaPMM = {};
    $scope.fBusquedaPMM.anio = $scope.fArr.listaAnio[0];
    $scope.fBusquedaPMM.mc = $scope.fArr.listaMC[0];

    $scope.fBusquedaPPD = {};
    $scope.fBusquedaPPD.inicio = moment().format('01-01-YYYY');
    $scope.fBusquedaPPD.fin = moment().format('DD-MM-YYYY');
    $scope.fBusquedaPPD.ultimo = $scope.fArr.listaUltimosDist[0];

    $scope.fBusquedaMMC = {};
    $scope.fBusquedaMMC.anio = $scope.fArr.listaAnio[0];
    $scope.fBusquedaMMC.mc = $scope.fArr.listaMC[0];

    $scope.fData = {};
    $scope.fData.chartMedioContacto = { 
      chart: { 
        type: 'pie',
        height: 350,
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
        height: 350,
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

    $scope.fData.chartPacPorDist = { 
      chart: { 
        type: 'pie',
        height: 350,
      },
      title: {
        text: 'PACIENTES POR DISTRITO'
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
    $scope.consultarPacientePorDistrito = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPPD
			};
			GraficoServices.sListarPacPorDist(arrParams).then(function (rpta) {
				if (rpta.datos.length > 0) {
					$scope.fData.chartPacPorDist.series[0].data = angular.copy(rpta.datos);
				}
				blockUI.stop();
			});
		};
    $scope.consultarPacientePorDistrito();

    $scope.fData.chartProdMedicoMes = {
      chart: {
        type: 'column',
        height: 350
      },
      title: {
        text: 'Producción médico por mes'
      },
      xAxis: {
        categories: []
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Total producción por médico'
          }
      },
      tooltip: {
          pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
          shared: true
      },
      plotOptions: {
        column: {
            stacking: 'percent'
        }
      },
      series: []
    };
    $scope.consultarProdMedicoMes = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPMM
			};
			GraficoServices.sListarProdMedico(arrParams).then(function (rpta) {
        $scope.fData.chartProdMedicoMes.xAxis.categories = angular.copy(rpta.datos.categories);
        $scope.fData.chartProdMedicoMes.series = angular.copy(rpta.datos.series);
				blockUI.stop();
			});
    };
    $scope.consultarProdMedicoMes();

    $scope.fData.chartProdMedicoTiempo = {
      chart: { 
        type: 'line',
        height: 350,
      },
      title: {
        text: 'Producción del Médico - Línea de tiempo'
      },
      subtitle: {
        text: 'Fuente: Cabicare'
      },
      yAxis: {
        title: {
          text: 'Cant. / Monto S/.'
        }
      },
      xAxis: {
        categories: []
      },
      legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
      },
      plotOptions: {
        series: {
          label: {
            connectorAllowed: false
          }
        }
      },
      series: []
    };
    $scope.consultarProdMedicoTiempo = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaMMC
			};
			GraficoServices.sListarProdMedicoTiempo(arrParams).then(function (rpta) {
        $scope.fData.chartProdMedicoTiempo.xAxis.categories = angular.copy(rpta.datos.categories);
        $scope.fData.chartProdMedicoTiempo.series = angular.copy(rpta.datos.series);
				blockUI.stop();
      });
    };
    $scope.consultarProdMedicoTiempo();
}]);

app.service("GraficoServices",function($http, $q, handleBehavior) {
  return({
    sListarPacienteRecom: sListarPacienteRecom,
    sListarPacPorMes: sListarPacPorMes,
    sListarPacPorDist: sListarPacPorDist,
    sListarProdMedico: sListarProdMedico,
    sListarProdMedicoTiempo: sListarProdMedicoTiempo,
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
  function sListarPacPorDist(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_paciente_distrito",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarProdMedico(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_prod_medico_mes",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarProdMedicoTiempo(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_prod_medico_tiempo",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
});
