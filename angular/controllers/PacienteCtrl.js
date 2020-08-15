app.controller('PacienteCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI',
  'PacienteFactory',
  'ModalReporteFactory',
  'PacienteServices',
  'DistritoServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI,
  PacienteFactory,
  ModalReporteFactory,
  PacienteServices,
  DistritoServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.metodos.listaDistritos = function(myCallback) {
      var myCallback = myCallback || function() { };
      DistritoServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaDistrito = rpta.datos;
        myCallback();
      });
    };
    $scope.fArr.listaMedioContacto = [
			{ id: '', descripcion: '--Seleccione medio de contacto--' },
			{ id: 'POR RECOMENDACION', descripcion: 'POR RECOMENDACION' },
			{ id: 'POR GOOGLE', descripcion: 'POR GOOGLE' },
			{ id: 'POR FACEBOOK', descripcion: 'POR FACEBOOK' },
			{ id: 'POR INSTAGRAM', descripcion: 'POR INSTAGRAM' },
			{ id: 'POR OTRAS REDES SOCIALES', descripcion: 'POR OTRAS REDES SOCIALES' }
    ];
    $scope.fArr.listaTipoDocumento = [
      {id : '0', descripcion:'--Seleccione tipo--'},
      {id: 'DNI', descripcion: 'DOCUMENTO NACIONAL DE IDENTIDAD'},
      {id: 'CEX', descripcion: 'CARNET DE EXTRANJERIA' },
      {id: 'PAS', descripcion: 'PASAPORTE' },
      {id: 'PTP', descripcion: 'PERMISO TEMPORAL DE PERMANENCIA' },
      {id: 'CED', descripcion: 'CEDULA' },
      {id: 'CR', descripcion: 'CARNET DE REFUGIO' }
    ];
    $scope.fArr.listaSexo = [
      {id : '0', descripcion:'--Seleccione sexo--'},
      {id: 'M', descripcion: 'MASCULINO'},
      {id: 'F', descripcion: 'FEMENINO' }
    ];
    $scope.fArr.listaOperadores = [
      {id : '0', descripcion:'--Seleccione operador--'},
      {id: 'CLARO', descripcion: 'CLARO'},
      {id: 'MOVISTAR', descripcion: 'MOVISTAR' },
      {id: 'ENTEL', descripcion: 'ENTEL' },
      {id: 'BITEL', descripcion: 'BITEL' }
    ];
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
      useExternalFiltering : true,
      enableGridMenu: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableRowSelection: true,
      enableFullRowSelection: true,
      multiSelect: false,
      columnDefs: [
        { field: 'idpaciente', name: 'pa.id', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'tipo_documento', name: 'pa.tipoDocumento', width: 160,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Tipo Documento' },
        { field: 'num_documento', name: 'pa.numeroDocumento', displayName: 'Documento', minWidth: 90 },
        { field: 'nombres', name: 'pa.nombres', displayName: 'Nombres', minWidth: 100 },
        { field: 'apellido_paterno', name: 'pa.apellidoPaterno', displayName: 'Ap. Paterno', minWidth: 100 },
        { field: 'apellido_materno', name: 'pa.apellidoMaterno', displayName: 'Ap. Materno', minWidth: 100 },
        { field: 'fecha_nacimiento', name: 'pa.fechaNacimiento', displayName: 'Fecha Nac.', minWidth: 120 },
        { field: 'email', name: 'pa.email', displayName: 'E-mail', minWidth: 100 },
        { field: 'celular', name: 'pa.celular', displayName: 'Celular', minWidth: 100 }
      ],
      onRegisterApi: function(gridApi) {
        $scope.gridApi = gridApi;
        gridApi.selection.on.rowSelectionChanged($scope,function(row){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
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
        $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
          var grid = this.grid;
          paginationOptions.search = true;
          paginationOptions.searchColumn = {
            'pa.id' : grid.columns[1].filters[0].term,
            'pa.tipoDocumento' : grid.columns[2].filters[0].term,
            'pa.numeroDocumento' : grid.columns[3].filters[0].term,
            'pa-nombres' : grid.columns[4].filters[0].term,
            'pa.apellidoPaterno' : grid.columns[5].filters[0].term,
            'pa.apellidoMaterno' : grid.columns[6].filters[0].term,
            'pa.fechaNacimiento' : grid.columns[7].filters[0].term,
            'pa.email' : grid.columns[8].filters[0].term,
            'pa.celular' : grid.columns[9].filters[0].term,
          };
          $scope.metodos.getPaginationServerSide();
        });
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
    $scope.metodos.getPaginationServerSide = function(loader) {
      if( loader ){
        blockUI.start('Procesando información...');
      }
      var arrParams = {
        paginate : paginationOptions
      };
      PacienteServices.sListar(arrParams).then(function (rpta) {
        if( rpta.datos.length == 0 ){
          rpta.paginate = { totalRows: 0 };
        }
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        $scope.gridOptions.data = rpta.datos;
        if( loader ){
          blockUI.stop();
        }
      });
      $scope.mySelectionGrid = [];
    };
    $scope.metodos.getPaginationServerSide(true);
    // MAS ACCIONES
    $scope.btnNuevo = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr ,
        callback: function() {
        }
      }
      PacienteFactory.regPacienteModal(arrParams);
    }
    $scope.btnEditar = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr,
        callback: function() {
        }
      }
      PacienteFactory.editPacienteModal(arrParams);
    }
    $scope.btnAnular = function() {
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if (result) {
          var arrParams = {
            idpaciente: $scope.mySelectionGrid[0].idpaciente
          };
          blockUI.start('Procesando información...');
          PacienteServices.sAnular(arrParams).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              $scope.metodos.getPaginationServerSide();
            }else if(rpta.flag == 0){
              var pTitle = 'Error!';
              var pType = 'danger';
            }else{
              alert('Error inesperado');
            }
            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            blockUI.stop();
          });
        }
      });
    }
    $scope.btnImprimirFicha = function() {
      var arrParams = {
        // titulo: 'FICHA DE ATENCION',
        url: angular.patchURLCI+'CentralReportes/reporte_ficha_paciente',
        datos: {
          id: $scope.mySelectionGrid[0].idpaciente,
          titulo: 'HISTORIA MÉDICA N°'+$scope.mySelectionGrid[0].idpaciente,
          tituloAbv: 'PAC-FIC'
        },
        metodo: 'php'
      };
      ModalReporteFactory.getPopupReporte(arrParams); 
    }
}]);

app.service("PacienteServices",function($http, $q, handleBehavior) {
    return({
      sListar: sListar,
      sRegistrar: sRegistrar,
      sEditar: sEditar,
      sAnular: sAnular,
      sBuscarPacientes: sBuscarPacientes,
      sListarPacientesBusqueda: sListarPacientesBusqueda,
      sListarPacientePorNumDoc: sListarPacientePorNumDoc
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/listar_paciente",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sBuscarPacientes(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/buscar_paciente_para_formulario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarPacientesBusqueda(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/buscar_paciente_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
  function sListarPacientePorNumDoc(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Paciente/buscar_paciente_por_num_documento",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("PacienteFactory", function($uibModal, pinesNotifications, blockUI, PacienteServices) {
  var interfaz = {
    regPacienteModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({
        templateUrl: angular.patchURLCI+'Paciente/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) {
          blockUI.stop();
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Paciente';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          // bindeo distrito
          var myCallBackDT = function() {
            $scope.fArr.listaDistrito.splice(0,0,{ id : '0', descripcion:'--Seleccione distrito--'});
            $scope.fData.distrito = $scope.fArr.listaDistrito[0];
          }
          $scope.metodos.listaDistritos(myCallBackDT);
          // $scope.fArr.listaTipoDocumento.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo documento--'});
          $scope.fData.tipo_documento = $scope.fArr.listaTipoDocumento[0];
          // $scope.fArr.listaSexo.splice(0,0,{ id : '0', descripcion:'--Seleccione sexo--'});
          $scope.fData.sexo = $scope.fArr.listaSexo[0];
          // $scope.fArr.listaTipoDocumento.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo documento--'});
          $scope.fData.operador = $scope.fArr.listaOperadores[0];
          $scope.fData.medioContacto = $scope.fArr.listaMedioContacto[0];
          // var myCallBackCC = function() {
          //   $scope.fArr.listaTipoPaciente.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo paciente--'});
          //   $scope.fData.tipo_paciente = $scope.fArr.listaTipoProducto[0];
          // }
          // $scope.metodos.listaTipoProducto(myCallBackCC);
          $scope.modoEdit = true;
          $scope.aceptar = function () {
            blockUI.start('Procesando información...');
            console.log('aqui');
            PacienteServices.sRegistrar($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $uibModalInstance.dismiss('cancel');
                if(typeof $scope.metodos.getPaginationServerSide == 'function'){
                  $scope.metodos.getPaginationServerSide(true);
                }
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
              }else{
                alert('Error inesperado');
              }
              arrParams.callback($scope.fData, rpta);
              blockUI.stop();
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            });
          }
        },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
      });
    },
    editPacienteModal: function (arrParams) {
      console.log(arrParams,'arrParams');
      blockUI.start('Abriendo formulario...');
      $uibModal.open({
        templateUrl: angular.patchURLCI+'Paciente/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) {
          blockUI.stop();
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          console.log(arrParams,'arrParams.mySelectionGrid');
          if( arrParams.mySelectionGrid.length == 1 ){
            $scope.fData = arrParams.mySelectionGrid[0];
            console.log($scope.fData ,'$scope.fData ');
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de Paciente';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          //BINDEO DISTRITO
          var myCallBackDT = function() {
            var objIndex = $scope.fArr.listaDistrito.filter(function(obj) {
              return obj.id == $scope.fData.distrito.id;
            }).shift();
            $scope.fData.distrito = objIndex;
          }
          $scope.metodos.listaDistritos(myCallBackDT);
          //BINDEO DE TIPO DOCUMENTO
          var objIndexTD = $scope.fArr.listaTipoDocumento.filter(function(obj) {
            return obj.id == $scope.fData.tipo_documento.id;
          }).shift();
          console.log(objIndexTD, 'objIndexTDfff');
          $scope.fData.tipo_documento = objIndexTD;
          //BINDEO OPERADOR
          var objIndexOp = $scope.fArr.listaOperadores.filter(function(obj) {
            return obj.id == $scope.fData.operador.id;
          }).shift();
          $scope.fData.operador = objIndexOp;
          //BINDEO SEXO
          var objIndexSx = $scope.fArr.listaSexo.filter(function(obj) {
            return obj.id == $scope.fData.sexo.id;
          }).shift();
          $scope.fData.sexo = objIndexSx;
          //BINDEO MEDIO CONTACTO
          var objIndexCp = $scope.fArr.listaMedioContacto.filter(function(obj) {
            return obj.id == $scope.fData.medioContacto.id;
          }).shift();
          $scope.fData.medioContacto = objIndexCp;
          // var myCallBackCC = function() {
          //   var objIndex = $scope.fArr.listaTipoProducto.filter(function(obj) {
          //     return obj.id == $scope.fData.tipo_producto.id;
          //   }).shift();
          //   $scope.fData.tipo_producto = objIndex;
          // }
          // $scope.metodos.listaTipoProducto(myCallBackCC);
          $scope.modoEdit = false;
          $scope.aceptar = function () {
            blockUI.start('Procesando información...');
            PacienteServices.sEditar($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $uibModalInstance.dismiss('cancel');
                if(typeof $scope.metodos.getPaginationServerSide == 'function'){
                  $scope.metodos.getPaginationServerSide(true);
                }
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
              }else{
                alert('Error inesperado');
              }
              arrParams.callback($scope.fData);
              blockUI.stop();
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
            });
          }
        },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
      });
    }
  }
  return interfaz;
});
