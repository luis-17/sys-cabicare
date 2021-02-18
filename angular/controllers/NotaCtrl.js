app.controller('NotaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI',
  'NotaFactory',
  'NotaServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI,
	NotaFactory,
    NotaServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones
    $scope.fBusqueda = {};
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.fArr.listaTipoNotaFiltro = [
      { id: 'ALL', descripcion: '--Seleccione tipo--' },
      { id: 'NOTA DE DEBITO', descripcion: 'NOTA DE DEBITO' },
      { id: 'NOTA DE CREDITO', descripcion: 'NOTA DE CREDITO' }
    ];
    $scope.fArr.listaTipoNota = [
        { id: 'ALL', descripcion: '--Seleccione tipo--' },
        { id: 'NOTA DE DEBITO', descripcion: 'NOTA DE DEBITO' },
        { id: 'NOTA DE CREDITO', descripcion: 'NOTA DE CREDITO' }
    ];

    $scope.fBusqueda.tipoNota = $scope.fArr.listaTipoNotaFiltro[0];
    $scope.fBusqueda.desde = $filter('date')(new Date(),'01-MM-yyyy');
    $scope.fBusqueda.desdeHora = '00';
    $scope.fBusqueda.desdeMinuto = '00';
    $scope.fBusqueda.hastaHora = 23;
    $scope.fBusqueda.hastaMinuto = 59;
    $scope.fBusqueda.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
    // $scope.fBusqueda.anio = $scope.fArr.listaAnioFiltro[0];
    // $scope.fBusqueda.categoria = $scope.fArr.listaCategoriaFiltro[0];
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
        { field: 'notaId', name: 'no.id', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'tipoNota', name: 'no.tipoNota', width: 120,
					cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Tipo Nota' },
        { field: 'fechaNota', name: 'no.fechaNota', width: 80,  displayName: 'Fecha de Doc.', enableFiltering: false },
        { field: 'numSerie', name: 'no.numSerie', width: 80,  displayName: 'N° Serie' },
        { field: 'numDoc', name: 'no.numDoc', displayName: 'N° Documento', width: 100 },
        { field: 'total', name: 'no.total', displayName: 'Total', width: 120, enableFiltering: false },
        { field: 'numDocAsoc', name: 'no.numDocAsoc', displayName: 'Doc. asociado', width: 120, enableFiltering: false },
		    { field: 'usuarioRegistro', name: 'us.nombres', displayName: 'Usuario Creación', width: 140, enableFiltering: false }
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
            'no.id' : grid.columns[1].filters[0].term,
            'no.tipoNota' : grid.columns[2].filters[0].term,
            'no.numSerie' : grid.columns[3].filters[0].term,
            'no.numDoc' : grid.columns[4].filters[0].term
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
            paginate : paginationOptions,
            datos: $scope.fBusqueda
      };
      NotaServices.sListar(arrParams).then(function (rpta) {
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
      NotaFactory.regNotaModal(arrParams);
	}
	$scope.btnVer = function(documento) {
      var arrParams = {
            'documento': documento,
            'metodos': $scope.metodos,
            'fArr': $scope.fArr ,
            'fSessionCI': $scope.fSessionCI,
            callback: function() {
        }
      }
      NotaFactory.verNotaModal(arrParams);
    }
    // $scope.btnEditar = function() {
    //   var arrParams = {
    //     'metodos': $scope.metodos,
    //     'mySelectionGrid': $scope.mySelectionGrid,
    //     'fArr': $scope.fArr,
    //     callback: function() {
    //     }
    //   }
    //   NotaFactory.editDocumentoModal(arrParams);
    // }
    // $scope.btnLaboratorio = function() {
    //   var arrParams = {
    //     'metodos': $scope.metodos,
    //     'mySelectionGrid': $scope.mySelectionGrid,
    //     'fArr': $scope.fArr,
    //     callback: function() {
    //     }
    //   }
    //   NotaFactory.verLaboratorio(arrParams);
    // }
    $scope.btnAnular = function() {
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if (result) {
          var arrParams = {
            notaId: $scope.mySelectionGrid[0].notaId
          };
          blockUI.start('Procesando información...');
          NotaServices.sAnular(arrParams).then(function (rpta) {
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
    // $scope.btnImprimirFicha = function() {
    //   var arrParams = {
    //     // titulo: 'FICHA DE ATENCION',
    //     url: angular.patchURLCI+'CentralReportes/reporte_ficha_paciente',
    //     datos: {
    //       id: $scope.mySelectionGrid[0].idpaciente,
    //       titulo: 'HISTORIA MÉDICA N°'+$scope.mySelectionGrid[0].idpaciente,
    //       tituloAbv: 'PAC-FIC'
    //     },
    //     metodo: 'php'
    //   };
    //   ModalReporteFactory.getPopupReporte(arrParams); 
    // }
}]);

app.service("NotaServices",function($http, $q, handleBehavior) {
	return({
		sListar: sListar,
		sRegistrar: sRegistrar,
		sAnular: sAnular,
	});
	function sListar(datos) {
		var request = $http({
					method : "post",
					url : angular.patchURLCI+"Nota/listar_documento",
					data : datos
		});
		return (request.then(handleBehavior.success,handleBehavior.error));
	}
	function sAnular (datos) {
		var request = $http({
					method : "post",
					url : angular.patchURLCI+"Nota/anular",
					data : datos
		});
		return (request.then(handleBehavior.success,handleBehavior.error));
	}
	function sRegistrar(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Nota/registrar",
			data: datos
			// transformRequest: angular.identity,
			// headers: { 'Content-Type': undefined }
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
});

app.factory("NotaFactory", function($uibModal, pinesNotifications, blockUI, NotaServices) {
	var interfaz = {
		regNotaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Nota/ver_popup_formulario',
				size: 'md',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.metodos = arrParams.metodos;
					$scope.fArr = arrParams.fArr;
					$scope.fDataNota = {};
					$scope.fDataNota.tipoNota = $scope.fArr.listaTipoNota[0];
					
					$scope.titleForm = 'Registra documento';
					$scope.aceptar = function(){
						blockUI.start("Registrando documento...");
						NotaServices.sRegistrar($scope.fDataNota).then(function (rpta) {
							if (rpta.flag === 1) {
								var pTitle = 'OK!';
								var pType = 'success';
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
							$scope.metodos.getPaginationServerSide(true);
						});
					}
					/* BOTONES FINALES */
					$scope.cancel = function () {
							$uibModalInstance.dismiss('cancel');
					}
				},
				resolve: {
					arrParams: function() {
						return arrParams;
					}
				}
			});
		},
		verNotaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Nota/ver_nota',
				size: 'md',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.fDataNota = arrParams.documento;
					$scope.metodos = arrParams.metodos;
					$scope.fArr = arrParams.fArr;
					$scope.titleForm = 'Ver documento';
					/* BOTONES FINALES */
					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
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
