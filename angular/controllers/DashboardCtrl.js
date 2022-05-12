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
    $scope.fArr.listaTG = [
      { id: 'CPM', descripcion: 'CITAS POR MES' },
      { id: 'PNPM', descripcion: 'PACIENTES NUEVOS POR MES' }
    ];
    $scope.fArr.listaTLE = [
      { id: 'ALL', descripcion: 'GENERAL' },
      { id: 'PM', descripcion: 'POR MÉDICO' }
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
    $scope.fBusquedaPPM.tg = $scope.fArr.listaTG[0];

    $scope.fBusquedaPMM = {};
    $scope.fBusquedaPMM.anio = $scope.fArr.listaAnio[0];
    $scope.fBusquedaPMM.mc = $scope.fArr.listaMC[0];

    $scope.fBusquedaPPD = {};
    $scope.fBusquedaPPD.inicio = moment().format('01-01-YYYY');
    $scope.fBusquedaPPD.fin = moment().format('DD-MM-YYYY');
    $scope.fBusquedaPPD.ultimo = $scope.fArr.listaUltimosDist[0];

    $scope.fBusquedaPDP = {};
    $scope.fBusquedaPDP.inicio = moment().format('01-01-2020');
    $scope.fBusquedaPDP.fin = moment().format('DD-MM-YYYY');

    $scope.fBusquedaEMB = {};
    $scope.fBusquedaEMB.inicio = moment().format('01-01-YYYY');
    $scope.fBusquedaEMB.fin = moment().format('DD-MM-YYYY');

    $scope.fBusquedaMMC = {};
    $scope.fBusquedaMMC.anio = $scope.fArr.listaAnio[0];
    $scope.fBusquedaMMC.mc = $scope.fArr.listaMC[0];

    $scope.fBusquedaTLE = {};
    $scope.fBusquedaTLE.anio = $scope.fArr.listaAnio[0];
    $scope.fBusquedaTLE.tipoTLE = $scope.fArr.listaTLE[0];

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
        type: 'column',
        // height: 350,
      },
      title: {
        text: 'COMPARATIVO DE CONSOLIDADOS POR MES'
      },
      xAxis: {
        categories: [],
        // categories: ['A','B','C','D','E'],
        crosshair: true
      },
      yAxis: {
        min: 0,
        title: {
            text: 'Cantidad'
        }
      },
      // tooltip: {
        // pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' 
      // },
      plotOptions: {
        column: {
          pointPadding: 0.2,
          borderWidth: 0
        }
        // pie: {
        //   allowPointSelect: true,
        //   cursor: 'pointer',
        //   dataLabels: {
        //     enabled: false
        //   },
        //   showInLegend: true
        // }
      },
      // legend: {

      //   labelFormat: '{name} ( {y} )'
      // },
      series: [{ 
        name: '',
        data: [],
        // data: [178, 194, 162, 198, 62],
      }]
    };
    $scope.consultarPacientePorMes = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPPM
			};
			GraficoServices.sListarPacPorMes(arrParams).then(function (rpta) {
        console.log('rpta.datos:: ', rpta.datos);
				// if (rpta.datos.length > 0) {
        $scope.fData.chartPacPorMes.series[0].data = angular.copy(rpta.datos.series);
        $scope.fData.chartPacPorMes.xAxis.categories = angular.copy(rpta.datos.categories);
        console.log('$scope.fData.chartPacPorMes::', $scope.fData.chartPacPorMes);
				// }
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

    $scope.fData.chartPacPorEmb = { 
      chart: { 
        type: 'pie',
        height: 350,
      },
      title: {
        text: 'PACIENTES VS EMBARAZADAS'
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
    $scope.consultarPacientePorEmb = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaEMB
			};
			GraficoServices.sListarPacPorEmb(arrParams).then(function (rpta) {
				if (rpta.datos.length > 0) {
					$scope.fData.chartPacPorEmb.series[0].data = angular.copy(rpta.datos);
				}
				blockUI.stop();
			});
		};
    $scope.consultarPacientePorEmb();

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
          // layout: 'horizontal',
          align: 'center'
          // verticalAlign: 'middle'
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

    $scope.fData.chartPacienteEmbTL = {
      chart: { 
        type: 'line',
        height: 350,
      },
      title: {
        text: 'Pacientes Embarazadas - General / Por médico'
      },
      subtitle: {
        text: 'Fuente: Cabicare'
      },
      yAxis: {
        title: {
          text: 'Cant.'
        }
      },
      xAxis: {
        categories: []
      },
      legend: {
          align: 'center'
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
    $scope.consultarPacientePorEmbTL = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaTLE
			};
			GraficoServices.sListarPacPorEmbTL(arrParams).then(function (rpta) {
        $scope.fData.chartPacienteEmbTL.xAxis.categories = angular.copy(rpta.datos.categories);
        $scope.fData.chartPacienteEmbTL.series = angular.copy(rpta.datos.series);
				blockUI.stop();
      });
    };
    $scope.consultarPacientePorEmbTL();

    // pacientes por distrito - panal
    $scope.fData.chartPacienteDistPanel = {
      chart: { 
        type: 'tilemap',
        inverted: true,
        height: '150%'
      },
      title: {
        text: 'Pacientes por distrito'
      },
      subtitle: {
        text: 'Fuente: Cabicare'
      },
      xAxis: {
        visible: false
      },
      yAxis: {
        visible: false
      },
      colorAxis: {
        dataClasses: [{
          from: 0,
          to: 10,
          color: '#fcfaaf',
          name: '< 10'
        }, {
          from: 10,
          to: 50,
          color: '#bee391',
          name: '10 - 50'
        }, {
          from: 50,
          to: 100,
          color: '#81cb73',
          name: '50 - 100'
        }, {
          from: 100,
          to: 250,
          color: '#43b455',
          name: '100 - 250'
        }, {
          from: 250,
          color: '#059c37',
          name: '> 250'
        }]
      },
      tooltip: {
        headerFormat: '',
        pointFormat: 'Total de pacientes de <b> {point.name}</b> es <b>{point.value}</b>'
      },
      plotOptions: {
        series: {
          dataLabels: {
            enabled: true,
            format: '{point.hc-a2}',
            color: '#000000',
            style: {
                textOutline: false
            }
          }
        }
      },
      series: [{
        name: '',
        data: []
      }]
    };

    $scope.consultarPacientePorDistPanel = function () {
			blockUI.start('Procesando información...');
			var arrParams = {
				datos: $scope.fBusquedaPDP
			};
			GraficoServices.sListarPacPorDistPanel(arrParams).then(function (rpta) {
        // $scope.fData.chartPacienteDistPanel.colorAxis.dataClasses = angular.copy(rpta.datos.colorAxis);
        $scope.fData.chartPacienteDistPanel.series[0].data = angular.copy(rpta.datos.series);
				blockUI.stop();
      });
    };
    $scope.consultarPacientePorDistPanel();
}]);

app.service("GraficoServices",function($http, $q, handleBehavior) {
  return({
    sListarPacienteRecom: sListarPacienteRecom,
    sListarPacPorMes: sListarPacPorMes,
    sListarPacPorDist: sListarPacPorDist,
    sListarProdMedico: sListarProdMedico,
    sListarProdMedicoTiempo: sListarProdMedicoTiempo,
    sListarPacPorEmb: sListarPacPorEmb,
    sListarPacPorEmbTL: sListarPacPorEmbTL,
    sListarPacPorDistPanel: sListarPacPorDistPanel,
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
  function sListarPacPorEmb(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_pacientes_embarazo",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarPacPorEmbTL(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_pacientes_embarazo_timeline",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarPacPorDistPanel(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Grafico/listar_pacientes_por_distrito_panel",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
});
