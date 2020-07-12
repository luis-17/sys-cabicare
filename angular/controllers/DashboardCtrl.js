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
    'PacienteServices',
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
    PacienteServices
  ) {
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
        data: [{
          name:"FEMENINO",
          y:60,
          sliced:false,
          selected:false
        },
        {
          name:"MASCULINO",
          y:34,
          sliced:true,
          selected:true
        }],
      }]
    }; 
}]);