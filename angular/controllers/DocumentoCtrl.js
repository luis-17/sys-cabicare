app.controller('DocumentoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI',
  'DocumentoFactory',
  'DocumentoServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI,
	DocumentoFactory,
  DocumentoServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones
		$scope.fBusqueda = {};
    $scope.mySelectionGrid = [];
    $scope.btnBuscar = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    // $scope.metodos.listaDistritos = function(myCallback) {
    //   var myCallback = myCallback || function() { };
    //   DistritoServices.sListarCbo().then(function(rpta) {
    //     $scope.fArr.listaDistrito = rpta.datos;
    //     myCallback();
    //   });
    // };
    $scope.fArr.listaMes = [
			{ id: '', descripcion: '--Seleccione mes--' },
			{ id: 'ENERO', descripcion: 'ENERO' },
			{ id: 'FEBRERO', descripcion: 'FEBRERO' },
			{ id: 'MARZO', descripcion: 'MARZO' },
			{ id: 'ABRIL', descripcion: 'ABRIL' },
			{ id: 'MAYO', descripcion: 'MAYO' },
			{ id: 'JUNIO', descripcion: 'JUNIO' },
			{ id: 'JULIO', descripcion: 'JULIO' },
			{ id: 'AGOSTO', descripcion: 'AGOSTO' },
			{ id: 'SEPTIEMBRE', descripcion: 'SEPTIEMBRE' },
			{ id: 'OCTUBRE', descripcion: 'OCTUBRE' },
			{ id: 'NOVIEMBRE', descripcion: 'NOVIEMBRE' },
			{ id: 'DICIEMBRE', descripcion: 'DICIEMBRE' }
    ];
    $scope.fArr.listaDia = [
			{ id: '', descripcion: '--Seleccione dia--' },
			{ id: '01', descripcion: '01' },
			{ id: '02', descripcion: '02' },
			{ id: '03', descripcion: '03' },
			{ id: '04', descripcion: '04' },
			{ id: '05', descripcion: '05' },
			{ id: '06', descripcion: '06' },
			{ id: '07', descripcion: '07' },
			{ id: '08', descripcion: '08' },
			{ id: '09', descripcion: '09' },
			{ id: '10', descripcion: '10' },
			{ id: '11', descripcion: '11' },
			{ id: '12', descripcion: '12' },
			{ id: '13', descripcion: '13' },
			{ id: '14', descripcion: '14' },
			{ id: '15', descripcion: '15' },
			{ id: '16', descripcion: '16' },
			{ id: '17', descripcion: '17' },
			{ id: '18', descripcion: '18' },
			{ id: '19', descripcion: '19' },
			{ id: '20', descripcion: '20' },
			{ id: '21', descripcion: '21' },
			{ id: '22', descripcion: '22' },
			{ id: '23', descripcion: '23' },
			{ id: '24', descripcion: '24' },
			{ id: '25', descripcion: '25' },
			{ id: '26', descripcion: '26' },
			{ id: '27', descripcion: '27' },
			{ id: '28', descripcion: '28' },
			{ id: '29', descripcion: '29' },
			{ id: '30', descripcion: '30' },
			{ id: '31', descripcion: '31' }
    ];
    $scope.fArr.listaAnio = [
      {id : '', descripcion:'--Seleccione año--'},
      {id: '2020', descripcion: '2020'},
      {id: '2021', descripcion: '2021' },
      {id: '2022', descripcion: '2022' },
      {id: '2023', descripcion: '2023' },
      {id: '2024', descripcion: '2024' },
      {id: '2025', descripcion: '2025' }
    ];
    $scope.fArr.listaCategoria = [
      {id : '', descripcion:'--Seleccione categoria--'},
      {id: 'FACTURAS', descripcion: 'FACTURAS'},
			{id: 'RECIBOS', descripcion: 'RECIBOS' },
			{id: 'RXH', descripcion: 'RXH' },
			{id: 'BALANCES', descripcion: 'BALANCES' },
			{id: 'EECC', descripcion: 'EECC' },
			{id: 'OTROS', descripcion: 'OTROS' }
    ];

    $scope.fArr.listaMoneda = [
      {id : '', descripcion:'--Seleccione moneda--'},
      {id: 'SOLES', descripcion: 'SOLES'},
			{id: 'DOLARES', descripcion: 'DOLARES' }
		];

		$scope.fArr.listaMesFiltro = [
			{ id: 'ALL', descripcion: 'TODOS' },
			{ id: 'ENERO', descripcion: 'ENERO' },
			{ id: 'FEBRERO', descripcion: 'FEBRERO' },
			{ id: 'MARZO', descripcion: 'MARZO' },
			{ id: 'ABRIL', descripcion: 'ABRIL' },
			{ id: 'MAYO', descripcion: 'MAYO' },
			{ id: 'JUNIO', descripcion: 'JUNIO' },
			{ id: 'JULIO', descripcion: 'JULIO' },
			{ id: 'AGOSTO', descripcion: 'AGOSTO' },
			{ id: 'SEPTIEMBRE', descripcion: 'SEPTIEMBRE' },
			{ id: 'OCTUBRE', descripcion: 'OCTUBRE' },
			{ id: 'NOVIEMBRE', descripcion: 'NOVIEMBRE' },
			{ id: 'DICIEMBRE', descripcion: 'DICIEMBRE' }
    ];
    $scope.fArr.listaAnioFiltro = [
      {id : 'ALL', descripcion:'TODOS'},
      {id: '2020', descripcion: '2020'},
      {id: '2021', descripcion: '2021' },
      {id: '2022', descripcion: '2022' },
      {id: '2023', descripcion: '2023' },
      {id: '2024', descripcion: '2024' },
      {id: '2025', descripcion: '2025' }
    ];
    $scope.fArr.listaCategoriaFiltro = [
      {id : 'ALL', descripcion:'TODOS'},
      {id: 'FACTURAS', descripcion: 'FACTURAS'},
      {id: 'BOLETA', descripcion: 'BOLETA'},
			{id: 'RECIBOS', descripcion: 'RECIBOS' },
			{id: 'RXH', descripcion: 'RXH' },
			{id: 'BALANCES', descripcion: 'BALANCES' },
			{id: 'EECC', descripcion: 'EECC' },
			{id: 'OTROS', descripcion: 'OTROS' }
		];

		$scope.fBusqueda.mes = $scope.fArr.listaMesFiltro[0];
		$scope.fBusqueda.anio = $scope.fArr.listaAnioFiltro[0];
		$scope.fBusqueda.categoria = $scope.fArr.listaCategoriaFiltro[0];
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
        { field: 'documentoId', name: 'do.id', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'numSerie', name: 'do.numSerie', displayName: 'N° Serie', width: 120, enableFiltering: false, visible: true },
        { field: 'numDoc', name: 'do.numDoc', displayName: 'N° Doc', width: 120, enableFiltering: false, visible: true },
        { field: 'razonSocial', name: 'do.razonSocial', displayName: 'Razón Social', width: 140, enableFiltering: false, visible: true },
        { field: 'fechaDocumento', name: 'fechaDocumento', displayName: 'Fecha documento', width: 140, enableFiltering: false, visible: true },
        { field: 'anio', name: 'do.anio', width: 120, visible: false,
					cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Año' },
				{ field: 'mes', name: 'do.mes', width: 120, visible: false,
					cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Mes' },
				{ field: 'categoria', name: 'do.categoria', width: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'Categoria' },
        { field: 'codigoExterno', name: 'do.codigoExterno', displayName: 'Cod. Interno', width: 100 },
				{ field: 'monto', name: 'do.monto', displayName: 'Monto', width: 120 },
				{ field: 'usuarioRegistro', name: 'us.nombres', displayName: 'Usuario Creación', width: 140 },
        { field: 'fechaCreacion', name: 'do.fechaCreacion', displayName: 'Fecha Creación', width: 140 },
				{ field: 'nombreArchivo', name: 'nombreArchivo', minWidth: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left "><a class="btn btn-link" target="_blank" href="{{COL_FIELD.link}}">'+ '{{ COL_FIELD.texto }}</a></div>',  displayName: 'LINK' },
              
        // { field: 'apellido_paterno', name: 'pa.apellidoPaterno', displayName: 'Documento', minWidth: 100 }
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
            'do.id' : grid.columns[1].filters[0].term,
            'do.anio' : grid.columns[3].filters[0].term,
            'do.mes' : grid.columns[4].filters[0].term,
            'do.categoria' : grid.columns[5].filters[0].term,
            'do.codigoExterno' : grid.columns[6].filters[0].term,
            'do.monto' : grid.columns[7].filters[0].term,
            'us.nombres' : grid.columns[7].filters[0].term
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
      DocumentoServices.sListar(arrParams).then(function (rpta) {
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
      DocumentoFactory.regDocumentoModal(arrParams);
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
      DocumentoFactory.verDocumentoModal(arrParams);
    }
    // $scope.btnEditar = function() {
    //   var arrParams = {
    //     'metodos': $scope.metodos,
    //     'mySelectionGrid': $scope.mySelectionGrid,
    //     'fArr': $scope.fArr,
    //     callback: function() {
    //     }
    //   }
    //   DocumentoFactory.editDocumentoModal(arrParams);
    // }
    // $scope.btnLaboratorio = function() {
    //   var arrParams = {
    //     'metodos': $scope.metodos,
    //     'mySelectionGrid': $scope.mySelectionGrid,
    //     'fArr': $scope.fArr,
    //     callback: function() {
    //     }
    //   }
    //   DocumentoFactory.verLaboratorio(arrParams);
    // }
    $scope.btnAnular = function() {
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if (result) {
          var arrParams = {
            documentoId: $scope.mySelectionGrid[0].documentoId
          };
          blockUI.start('Procesando información...');
          DocumentoServices.sAnular(arrParams).then(function (rpta) {
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

app.service("DocumentoServices",function($http, $q, handleBehavior) {
	return({
		sListar: sListar,
		sRegistrar: sRegistrar,
		sAnular: sAnular,
	});
	function sListar(datos) {
		var request = $http({
					method : "post",
					url : angular.patchURLCI+"Documento/listar_documento",
					data : datos
		});
		return (request.then(handleBehavior.success,handleBehavior.error));
	}
	function sAnular (datos) {
		var request = $http({
					method : "post",
					url : angular.patchURLCI+"Documento/anular",
					data : datos
		});
		return (request.then(handleBehavior.success,handleBehavior.error));
	}
	function sRegistrar(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Documento/registrar",
			data: datos,
			transformRequest: angular.identity,
			headers: { 'Content-Type': undefined }
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
});

app.factory("DocumentoFactory", function($uibModal, pinesNotifications, blockUI, DocumentoServices) {
	var interfaz = {
		regDocumentoModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Documento/ver_popup_formulario',
				size: 'md',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.metodos = arrParams.metodos;
					$scope.fArr = arrParams.fArr;
					$scope.fDataDoc = {};
					$scope.fDataDoc.mes = $scope.fArr.listaMes[0];
					$scope.fDataDoc.anio = $scope.fArr.listaAnio[0];
          $scope.fDataDoc.dia = $scope.fArr.listaDia[0];
					$scope.fDataDoc.categoria = $scope.fArr.listaCategoria[0];
					
					$scope.titleForm = 'Registra documento';
					$scope.aceptar = function(){
						blockUI.start("Registrando documento...");
						var formData = new FormData();
						angular.forEach($scope.fDataDoc, function(index,val) {
							if(index == 'mes' || index == 'anio' || index == 'categoria'){

							}else{
								formData.append(val, index);
							}
							
						});
						// formData.append($scope.fDataDoc.mes.descripcion, 'mes');
						// formData.append($scope.fDataDoc.anio.descripcion, 'anio');
						// formData.append($scope.fDataDoc.categoria.descripcion, 'categoria');

            formData.append('moneda', $scope.fDataDoc.moneda.id);
            formData.append('mes', $scope.fDataDoc.mes.id);
						formData.append('anio', $scope.fDataDoc.anio.id);
            formData.append('dia', $scope.fDataDoc.dia.id);
						formData.append('categoria', $scope.fDataDoc.categoria.id);
						
						// console.log('formData ==>', formData);
						DocumentoServices.sRegistrar(formData).then(function (rpta) {
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
							var linkBtn = document.getElementById('quitarImg');
							// console.log('linkBtn ==>', linkBtn);
							linkBtn.click();
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
		verDocumentoModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Documento/ver_documento',
				size: 'md',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.fDataDoc = arrParams.documento;
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
