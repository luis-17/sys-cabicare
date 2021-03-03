app.controller('CentralReportesCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI',
  'UsuarioServices',
  'ModalReporteFactory',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI,
    UsuarioServices,
    ModalReporteFactory,
  ) {
    $scope.metodos = {}; // contiene todas las funciones
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones
    $scope.mySelectionGrid = [];
    $scope.fBusqueda = {};
    $scope.fBusqueda.fecha = $filter('date')(new Date(),'dd-MM-yyyy');
    $scope.fBusqueda.desde = $filter('date')(new Date(),'dd-MM-yyyy');
    $scope.fBusqueda.desdeHora = '00';
    $scope.fBusqueda.desdeMinuto = '00';
    $scope.fBusqueda.hastaHora = 23;
    $scope.fBusqueda.hastaMinuto = 59;
    $scope.fBusqueda.hasta = $filter('date')(new Date(),'dd-MM-yyyy'); 
    $scope.fBusqueda.anio = $filter('date')(new Date(),'yyyy');
    $scope.fBusqueda.tipoCuadro = 'reporte';
    $scope.fBusqueda.salida = 'pdf';
    $scope.fBusqueda.tiposalida = 'pdf';
    $scope.showDivEmptyData = false;
    $scope.selectedReport = {};
    $scope.fArr.listaOrigen = [
      { id : 'ALL', descripcion:'TODOS'},
      { id : 'INT', descripcion:'INTERNO'},
      { id : 'EXT', descripcion:'EXTERNO'}
    ];
    $scope.fBusqueda.origen = $scope.fArr.listaOrigen[0];
    $scope.fArr.listaTipoReporte = [
      { id : '', descripcion:'--Seleccione tipo--'},
      { id : 'DET', descripcion:'DETALLADO'},
      { id : 'RPP', descripcion:'RESUMIDO POR PRODUCTO'}
    ];
    $scope.fArr.listaTipoReporte2 = [
      { id : '', descripcion:'--Seleccione tipo--'},
      { id : 'DET', descripcion:'DETALLADO'},
      { id : 'RPP', descripcion:'RESUMIDO POR PRODUCTO'},
      { id : 'RPM', descripcion:'RESUMIDO POR MÉDICO'}
    ];
    $scope.fBusqueda.tipoReporte = $scope.fArr.listaTipoReporte[0];
    $scope.fBusqueda.tipoReporte2 = $scope.fArr.listaTipoReporte2[0];
    $scope.fArr.listaOrden = [
      { id : 'OC', descripcion:'ORDENADO POR CANTIDAD'},
      { id : 'OM', descripcion:'ORDENADO POR MONTO'}
    ];
    $scope.fBusqueda.orden = $scope.fArr.listaOrden[0];
    $scope.fArr.listaEstadisticas = [
      {
        textReporte: 'REPORTES 1',
        open: true,
        reportes: [
          {
            id: 'AM-PRODGEN',
            tipoCuadro: 'report',
            name: 'PRODUCCIÓN GENERAL'
          },
          {
            id: 'AM-PRODMED',
            tipoCuadro: 'report',
            name: 'PRODUCCIÓN POR MÉDICO'
          },
          {
            id: 'PAC-ALL',
            tipoCuadro: 'report',
            name: 'REPORTE DE PACIENTES'
          },
          {
            id: 'CIT-ALL',
            tipoCuadro: 'report',
            name: 'REPORTE DE CITAS'
          }
        ]
      }
    ];
    $scope.metodos.listaMedico = function() {
			// var myCallback = myCallback || function() { };
			UsuarioServices.sListarMedicoCbo().then(function(rpta) {
        $scope.fArr.listaMedico = rpta.datos;
        $scope.fArr.listaMedico.splice(0,0,{ id : '0', descripcion:'--Seleccione médico--'});
        $scope.fBusqueda.medico = $scope.fArr.listaMedico[0];
				// myCallback();
			});
    };
    $scope.metodos.listaMedico();
    $scope.selectReport = function (row) { 
      $scope.contRangoAnos = true;
      //console.log('selectedReport ', row);
      $scope.selectedReport = row; 
      // console
      // var desde30 = moment().subtract(30,'days'); 
      // if(row.id=="AS-APE"){
      //   $scope.fBusqueda.allEmpresas = false;
      //   $scope.fBusqueda.allEmpleados = true;
        
      //   $scope.fBusqueda.desde = $filter('date')(desde30.toDate(),'dd-MM-yyyy');
      // }
    } 

    // BOTON PROCESAR
    $scope.btnConsultarReporte = function () { 
      // var strControllerJS = 'CentralReportes';
      // var strControllerPHP = 'CentralReportes'; 
      switch ( $scope.selectedReport.id ) { 
        // ADMINISTRACION
          case 'AM-PRODMED':
            var arrParams = {
              url: angular.patchURLCI+'CentralReportes/reporte_produccion_medico',
              metodo: 'php',
              datos: {
                desde: $scope.fBusqueda.desde,
                hasta: $scope.fBusqueda.hasta,
                medico: $scope.fBusqueda.medico,
                tipoReporte: $scope.fBusqueda.tipoReporte,
                orden: $scope.fBusqueda.orden,
                origen: $scope.fBusqueda.origen,
                titulo: 'PRODUCCIÓN DE MÉDICO',
                tituloAbv: 'AM-PRODMED'
              }
            };
            ModalReporteFactory.getPopupReporte(arrParams);
            break;
          case 'AM-PRODGEN':
            var arrParams = {
              url: angular.patchURLCI+'CentralReportes/reporte_produccion_general',
              metodo: 'php',
              datos: {
                desde: $scope.fBusqueda.desde,
                hasta: $scope.fBusqueda.hasta,
                tipoReporte: $scope.fBusqueda.tipoReporte2,
                orden: $scope.fBusqueda.orden,
                origen: $scope.fBusqueda.origen,
                titulo: 'PRODUCCIÓN GENERAL',
                tituloAbv: 'AM-PRODGEN'
              }
            };
            ModalReporteFactory.getPopupReporte(arrParams);
            break;
          case 'PAC-ALL':
            var arrParams = {
              url: angular.patchURLCI+'Reportes/reporte_pacientes',
              metodo: 'php',
              salida: 'excel',
              datos: {
                // desde: $scope.fBusqueda.desde,
                // hasta: $scope.fBusqueda.hasta,
                // tipoReporte: $scope.fBusqueda.tipoReporte,
                // orden: $scope.fBusqueda.orden,
                // origen: $scope.fBusqueda.origen,
                titulo: 'REPORTE DE PACIENTES',
                tituloAbv: 'PAC-ALL'
              }
            };
            ModalReporteFactory.getPopupReporte(arrParams);
            break;
          case 'CIT-ALL':
            var arrParams = {
              url: angular.patchURLCI+'Reportes/listado_citas_excel',
              metodo: 'php',
              salida: 'excel',
              datos: {
                paginate: {},
                filtro: {
                  fechaDesde: $scope.fBusqueda.desde,
                  fechaHasta: $scope.fBusqueda.hasta
                },
                titulo: 'REPORTE DE CITAS',
                tituloAbv: 'CIT-ALL'
              }
            };
            ModalReporteFactory.getPopupReporte(arrParams); 
            break;
          // NINGUN REPORTE SELECCIONADO
          default: 
            pinesNotifications.notify({ title: 'Advertencia.', text: 'Seleccione un reporte', type: 'warning', delay: 2000 });
      }
    }
}]);
// ProductoServices
app.service("ProductoServices",function($http, $q, handleBehavior) {
  return({
    sListar: sListar,
    sListarProductoAutocomplete: sListarProductoAutocomplete,
    sRegistrar: sRegistrar,
    sEditar: sEditar,
    sAnular: sAnular
  });
  function sListar(datos) {
    var request = $http({
          method : "post",
          url : angular.patchURLCI+"Producto/listar_producto",
          data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarProductoAutocomplete(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Producto/listar_autocompletado_producto",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sRegistrar (datos) {
    var request = $http({
          method : "post",
          url : angular.patchURLCI+"Producto/registrar",
          data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sEditar (datos) {
    var request = $http({
          method : "post",
          url : angular.patchURLCI+"Producto/editar",
          data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sAnular (datos) {
    var request = $http({
          method : "post",
          url : angular.patchURLCI+"Producto/anular",
          data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
});

// app.factory("ProductoFactory", function($uibModal, pinesNotifications, blockUI, ProductoServices) {
//   var interfaz = {
    
//   }
//   return interfaz;
// });
