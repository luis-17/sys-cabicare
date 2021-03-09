app.controller('ProductoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI',
  'ProductoFactory',
  'ProductoServices',
  'TipoProductoServices',
  'SedeServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI,
  ProductoFactory,
  ProductoServices,
  TipoProductoServices,
  SedeServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones
    $scope.fBusqueda = {};
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.fArr.listaProcedencia = [{id: 'INT', descripcion: 'INTERNO'}, {id: 'EXT', descripcion: 'EXTERNO' }];
    $scope.metodos.listaTipoProducto = function(myCallback) {
      var myCallback = myCallback || function() { };
      TipoProductoServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaTipoProducto = rpta.datos;
        myCallback();
      });
    };
    $scope.metodos.listaSedeFiltro = function() {
      SedeServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaSedeFiltro = rpta.datos;
        $scope.fBusqueda.sede = $scope.fArr.listaSedeFiltro[0];
        // myCallback();

        $scope.metodos.getPaginationServerSide(true);
        
      });
    };
    $scope.metodos.listaSedeFiltro();
    
    $scope.metodos.listaSede = function(myCallback) {
      var myCallback = myCallback || function() { };
      SedeServices.sListarCbo().then(function(rpta) {
        $scope.fArr.listaSede = rpta.datos;
        myCallback();
      });
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
      useExternalFiltering : true,
      enableGridMenu: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableRowSelection: true,
      enableFullRowSelection: true,
      multiSelect: false,
      columnDefs: [
        { field: 'idproducto', name: 'pr.id', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'sede', name: 'se.nombre', width: 120, enableFiltering: false,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Sede' },
        { field: 'tipo_producto', name: 'tp.nombre', width: 160,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Tipo Producto' },
        // { field: 'tipo_producto', name: 'tp.nombre', displayName: 'Tipo Producto', minWidth: 100 },
        { field: 'nombre', name: 'pr.nombre', displayName: 'Producto', minWidth: 100 },
        { field: 'precio', name: 'pr.precio', displayName: 'Precio', minWidth: 100 },
        { field: 'procedenciaStr', name: 'pr.procedencia', displayName: 'Procedencia', minWidth: 100 }
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
            'pr.id' : grid.columns[1].filters[0].term,
            'tp.nombre' : grid.columns[3].filters[0].term,
            'pr.nombre' : grid.columns[4].filters[0].term,
            'pr.precio' : grid.columns[5].filters[0].term,
            'pr.procedencia' : grid.columns[6].filters[0].term
          }
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
        paginate : paginationOptions,
        datos: $scope.fBusqueda
      };
      ProductoServices.sListar(arrParams).then(function (rpta) {
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
    
    // MAS ACCIONES
    $scope.btnNuevo = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr ,
        callback: function() {
        }
      }
      ProductoFactory.regProductoModal(arrParams);
    }
    $scope.btnEditar = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr,
        callback: function() {
        }
      }
      ProductoFactory.editProductoModal(arrParams);
    }
    $scope.btnAnular = function() {
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if (result) {
          var arrParams = {
            idproducto: $scope.mySelectionGrid[0].idproducto
          };
          blockUI.start('Procesando información...');
          ProductoServices.sAnular(arrParams).then(function (rpta) {
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

app.factory("ProductoFactory", function($uibModal, pinesNotifications, blockUI, ProductoServices) {
  var interfaz = {
    regProductoModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({
        templateUrl: angular.patchURLCI+'Producto/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) {
          blockUI.stop();
          $scope.fData = {};
          console.log($scope.fData,'$scope.fData');
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Producto';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.fData.procedencia = $scope.fArr.listaProcedencia[0];
          var myCallBackCC = function() {
            $scope.fArr.listaTipoProducto.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo producto--'});
            $scope.fData.tipo_producto = $scope.fArr.listaTipoProducto[0];
          }
          $scope.metodos.listaTipoProducto(myCallBackCC);

          var myCallBackSede = function() {
            $scope.fArr.listaSede.splice(0,0,{ id : '0', descripcion:'--Seleccione sede--'});
            $scope.fData.sede = $scope.fArr.listaSede[0];
          }
          $scope.metodos.listaSede(myCallBackSede);

          $scope.modoEdit = true;
          $scope.aceptar = function () {
            blockUI.start('Procesando información...');
            console.log('aqui');
            ProductoServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editProductoModal: function (arrParams) {
      console.log(arrParams,'arrParams');
      blockUI.start('Abriendo formulario...');
      $uibModal.open({
        templateUrl: angular.patchURLCI+'Producto/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Producto';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          //BINDEO DE PROCEDENCIA
          var objIndexProc = $scope.fArr.listaProcedencia.filter(function(obj) {
            return obj.id == $scope.fData.procedencia;
          }).shift();
          console.log(objIndexProc, 'objIndexProc');
          $scope.fData.procedencia = objIndexProc;

          //BINDEO TIPO PRODUCTO
          var myCallBackCC = function() {
            var objIndex = $scope.fArr.listaTipoProducto.filter(function(obj) {
              return obj.id == $scope.fData.tipo_producto.id;
            }).shift();
            $scope.fData.tipo_producto = objIndex;
          }
          $scope.metodos.listaTipoProducto(myCallBackCC);

          //BINDEO SEDE
          var myCallBackSede = function() {
            var objIndex = $scope.fArr.listaSede.filter(function(obj) {
              return obj.id == $scope.fData.sede.id;
            }).shift();
            $scope.fData.sede = objIndex;
          }
          $scope.metodos.listaSede(myCallBackSede);

          $scope.modoEdit = false;
          $scope.aceptar = function () {
            blockUI.start('Procesando información...');
            ProductoServices.sEditar($scope.fData).then(function (rpta) {
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
