app.controller('RegistroAtencionCtrl', [
	'$scope',
	'$filter',
	'$state',
	'$stateParams',
	'$uibModal',
	'$bootbox',
	'pinesNotifications',
	'uiGridConstants',
	'blockUI',
	'ModalReporteFactory',
	'RegistroAtencionService',
	'DiagnosticoServices',
	'UsuarioServices',
	function ($scope, $filter, $state, $stateParams, $uibModal, $bootbox, pinesNotifications, uiGridConstants,
		blockUI,
		ModalReporteFactory,
		RegistroAtencionService,
		DiagnosticoServices,
		UsuarioServices
	){
		$scope.metodos = {}; // contiene todas las funciones
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones
		$scope.fArr.listaTipoDiagnostico = [
			{ id: '', descripcion: '--Seleccione tipo de diagnóstico--' },
			{ id: 'PRESUNTIVO', descripcion: 'PRESUNTIVO' },
			{ id: 'DEFINITIVO', descripcion: 'DEFINITIVO' }
		];
		$scope.fArr.listaTipoImg = [
			{ id: '', descripcion: '--Seleccione tipo de imagen--' },
			{ id: 'COLPOSCOPIO', descripcion: 'COLPOSCOPIO' },
			{ id: 'ECOGRAFO', descripcion: 'ECOGRAFO' }
    ];
    $scope.fArr.listaGestando = [
			{ id: '2', descripcion: 'NO' },
			{ id: '1', descripcion: 'SI' }
    ];
		$scope.fData = {}
		$scope.fData.temporal = {};
		$scope.fData.temporalImg = {};
		$scope.metodos.listaMedico = function(myCallback) {
			var myCallback = myCallback || function() { };
			UsuarioServices.sListarMedicoCbo().then(function(rpta) {
				$scope.fArr.listaMedico = rpta.datos;
				myCallback();
			});
		};

		var datos = {
			id: $stateParams.id
		}
		RegistroAtencionService.sGetCitaById(datos).then(function (rpta) {
			console.log('datos', rpta.datos);
			$scope.fData = rpta.datos;
			$scope.fData.temporal = {};
			$scope.fData.temporal.tipoDiagnostico = $scope.fArr.listaTipoDiagnostico[0];
			$scope.fData.temporalImg = {};
			$scope.fData.temporalImg.tipoImagen = $scope.fArr.listaTipoImg[0];
			$scope.getPaginationServerSideDet(true);
			$scope.getPaginationServerSideRec();
			$scope.getPaginationServerSideImg();
			//BINDEO MEDICO
			var myCallBackCC = function() {
				var objIndex = $scope.fArr.listaMedico.filter(function(obj) {
					return obj.id == $scope.fData.medico.id;
				}).shift();
				$scope.fData.medico = objIndex;
			}
      $scope.metodos.listaMedico(myCallBackCC);

      //BINDEO GESTANDO
      var objIndexCp = $scope.fArr.listaGestando.filter(function(obj) {
        return obj.id == $scope.fData.gestando.id;
      }).shift();
      $scope.fData.gestando = $scope.fArr.listaGestando[0];
      if(objIndexCp){
        $scope.fData.gestando = objIndexCp;
      }
      

		});
		/* CALCULO DE IMC */
		$scope.calcularIMC = function(){
			$scope.fData.imc = null;
			if ($scope.fData.peso == null || $scope.fData.talla == null){
				return;
			}
			console.log('calculo imc');

			if($scope.fData.peso <= 0 || $scope.fData.talla <= 0){
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Peso y Talla deben ser numericos mayores de cero.',
					type: 'warning',
					delay: 5000
				});
				return;
			}
			var talla = parseInt($scope.fData.talla) / 100;
			$scope.fData.imc = (parseFloat($scope.fData.peso) / (parseFloat(talla*talla))).toFixed(2);
		}
		$scope.grabarAtencionMedica = function(){
      $scope.fData.diagnostico = $scope.gridOptions.data;
      var tieneConsulta = false;
      angular.forEach($scope.fData.detalle, function(row, key) {
        if (row.tipoProductoId == 1) { // consulta
          tieneConsulta = true;
        }
      });
			if(!($scope.fData.diagnostico.length > 0) && tieneConsulta){
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Debe seleccionar al menos un diagnóstico.',
					type: 'warning',
					delay: 5000
				});
				return;
			}
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
		// IMAGENES
		$scope.gridOptionsImg = {
			rowHeight: 30,
			enableGridMenu: false,
			enableColumnMenus: false,
			enableRowSelection: false,
			enableSelectAll: false,
			enableFiltering: false,
			enableSorting: false,
			enableFullRowSelection: false,
			enableCellEdit: false,
			multiSelect: false,
			data: [],
			columnDefs: [
				{ field: 'idimagen', name: 'id', displayName: 'ID', minWidth: 80, width: 80, visible: false },
				{ field: 'tipoImagen', name: 'tipoImagen', minWidth: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD }}</div>',  displayName: 'TIPO IMAGEN' },
				{ field: 'descripcion', name: 'descripcion', displayName: 'DESCRIPCIÓN', minWidth: 120 },
				{ field: 'srcImagen', name: 'srcImagen', minWidth: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left "><a class="btn btn-link" target="_blank" href="{{COL_FIELD.link}}">'+ '{{ COL_FIELD.texto }}</a></div>',  displayName: 'LINK' },
				// { field: 'srcImagen', name: 'srcImagen', displayName: 'LINK IMAGEN', minWidth: 120 },
				{
					field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
					cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCestaImg(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
				}
			],
			onRegisterApi: function (gridApi) {
				$scope.gridApi = gridApi;
			}
		};
		$scope.getPaginationServerSideImg = function (loader) {
			if (loader) {
				blockUI.start('Procesando información...');
			}
			var arrParams = {
				datos: {
					idcita: $scope.fData.id
				}
			};
			RegistroAtencionService.sGetDetalleImagenes(arrParams).then(function (rpta) {
				if (rpta.datos.length == 0) {
					rpta.paginate = { totalRows: 0 };
				}
				$scope.gridOptionsImg.data = rpta.datos;
				if (loader) {
					blockUI.stop();
				}
			});
		};
		$scope.agregarItemImagen = function(){
			if ($scope.fData.temporalImg.tipoImagen.id == null ||
				$scope.fData.temporalImg.tipoImagen.id == "" ||
				$scope.fData.temporalImg.tipoImagen.id < 0)
			{
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'El campo Tipo Imagen no es válido.',
					type: 'warning',
					delay: 5000
				});
				return;
			}
			blockUI.start("Registrando imagen...");
			// $scope.fData.detalle = $scope.gridOptionsRec.data;
			var formData = new FormData();
			var arrDataImg = {
				citaId: $stateParams.id,
				tipoImagen: $scope.fData.temporalImg.tipoImagen.id,
				descripcion: $scope.fData.temporalImg.observaciones,
				srcImagen_blob: $scope.fData.temporalImg.srcImagen_blob
			};
			angular.forEach(arrDataImg, function(index,val) {
				formData.append(val,index);
			});
			// console.log('formData ==>', formData);
			RegistroAtencionService.sRegistrarImagen(formData).then(function (rpta) {
				if (rpta.flag === 1) {
					var pTitle = 'OK!';
					var pType = 'success';
					// $scope.fData.idreceta = rpta.idreceta;
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
				$scope.getPaginationServerSideImg();
				$scope.fData.temporalImg.srcImagen_blob = null;
				$scope.fData.temporalImg.srcImagen = null;
				$scope.fData.temporalImg.observaciones = null;
				$scope.fData.temporalImg.tipoImagen = $scope.fArr.listaTipoImg[0];
				var linkBtn = document.getElementById('quitarImg');
				// console.log('linkBtn ==>', linkBtn);
				linkBtn.click();
			});
		}
		$scope.btnQuitarDeLaCestaImg = function (row) {
			blockUI.start("Eliminando imagen...");
			RegistroAtencionService.sQuitarImagen(row.entity.id).then(function (rpta) {
				if (rpta.flag === 1) {
					var pTitle = 'OK!';
					var pType = 'success';
					// $scope.fData.idreceta = rpta.idreceta;
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
				$scope.getPaginationServerSideImg();
			});
			var index = $scope.gridOptions.data.indexOf(row.entity);
			$scope.gridOptions.data.splice(index, 1);
			// $scope.calcularTotales();
		}

		// DIAGNOSTICO
		$scope.getDiagnosticoAutocomplete = function (value) {
			var params = {
				searchText: value,
			};
			return DiagnosticoServices.sListarDiagnosticoAutocomplete(params).then(function (rpta) {
				$scope.noResultsPr = false;
				if (rpta.flag === 0) {
					$scope.noResultsPr = true;
				}
				console.log('datos', rpta.datos);
				return rpta.datos;
			});
		}

		$scope.getSelectedDiagnostico = function (item, model) {
			$scope.fData.temporal.iddiagnostico = model.iddiagnostico;
			$scope.fData.temporal.diagnostico = model.descripcion;
			$scope.fData.temporal.codigo = model.codigo;
			// $scope.fData.temporal.precio = model.precio;
		}
		$scope.gridOptions = {
			rowHeight: 30,
			enableGridMenu: false,
			enableColumnMenus: false,
			enableRowSelection: false,
			enableSelectAll: false,
			enableFiltering: false,
			enableSorting: false,
			enableFullRowSelection: false,
			enableCellEdit: false,
			multiSelect: false,
			data: [],
			columnDefs: [
				{ field: 'iddiagnostico', name: 'id', displayName: 'ID', minWidth: 80, width: 80, visible: false },
				{ field: 'codigo', name: 'codigo', displayName: 'CODIGO CIE10', minWidth: 120, width: 120 },
				{ field: 'diagnostico', name: 'nombre', displayName: 'DIAGNOSTICO CIE10', minWidth: 120 },
				{ field: 'tipoDiagnostico', name: 'tipoDiagnostico', minWidth: 120,
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">'+ '{{ COL_FIELD.descripcion }}</div>',  displayName: 'TIPO' },
				// { field: 'tipoDiagnostico', name: 'tipoDiagnostico', displayName: 'TIPO', minWidth: 120 },
				{
					field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
					cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCesta(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
				}
			],
			onRegisterApi: function (gridApi) {
				$scope.gridApi = gridApi;
				// gridApi.edit.on.afterCellEdit($scope, function (rowEntity, colDef, newValue, oldValue) {
				// 	if (newValue != oldValue) {
				// 		$scope.calcularTotales();
				// 	}
				// });
			}
		};
		$scope.getPaginationServerSideDet = function (loader) {
			if (loader) {
				blockUI.start('Procesando información...');
			}
			var arrParams = {
				datos: {
					citaId: $scope.fData.id
				}
			};
			DiagnosticoServices.sListarDetalleDx(arrParams).then(function (rpta) {
				if (rpta.datos.length == 0) {
					rpta.paginate = { totalRows: 0 };
				}
				// $scope.gridOptions.totalItems = rpta.paginate.totalRows;
				$scope.gridOptions.data = rpta.datos;
				// $scope.calcularTotales();
				if (loader) {
					blockUI.stop();
				}
			});
		};

		$scope.getTableHeight = function () {
			var rowHeight = 30; // your row height
			var headerHeight = 30; // your header height
			var cant_filas = 4; // min 4
			if ($scope.gridOptions.data.length > cant_filas) {
				var cant_filas = $scope.gridOptions.data.length;
			}
			return {
				height: (cant_filas * rowHeight + headerHeight) + "px"
			};
		}
		$scope.agregarItemDiagnostico = function(){
			if ($scope.fData.temporal.iddiagnostico == null ){
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Debe seleccionar un diagnostico.',
					type: 'warning',
					delay: 5000
				});
				return;
			}

			if ($scope.fData.temporal.tipoDiagnostico.id == null ||
				$scope.fData.temporal.tipoDiagnostico.id == "" ||
				$scope.fData.temporal.tipoDiagnostico.id < 0)
			{
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'El tipo de diagnóstico no es válido.',
					type: 'warning',
					delay: 5000
				});
				return;
			}

			var producto_repetido = false;
			angular.forEach($scope.gridOptions.data, function (value, key) {
				if (value.iddiagnostico == $scope.fData.temporal.iddiagnostico) {
					producto_repetido = true;
					pinesNotifications.notify({
						title: 'Advertencia.',
						text: 'Ya está cargado este diagnostico.',
						type: 'warning',
						delay: 5000
					});
					return;
				}
			});

			if (producto_repetido === false) {
				$scope.gridOptions.data.push({
					iddiagnostico: $scope.fData.temporal.iddiagnostico,
					codigo: $scope.fData.temporal.codigo,
					diagnostico: $scope.fData.temporal.diagnostico,
					tipoDiagnostico: $scope.fData.temporal.tipoDiagnostico,
					// precio: $scope.fData.temporal.precio,
				});

				$scope.fData.temporal.iddiagnostico = null;
				$scope.fData.temporal.codigo = null;
				$scope.fData.temporal.diagnostico = null;
				$scope.fData.temporal.tipoDiagnostico = $scope.fArr.listaTipoDiagnostico[0];
				// $scope.calcularTotales();
			}
		}
		$scope.btnQuitarDeLaCesta = function (row) {
			var index = $scope.gridOptions.data.indexOf(row.entity);
			$scope.gridOptions.data.splice(index, 1);
			// $scope.calcularTotales();
		}

		// RECETA
		$scope.gridOptionsRec = {
			rowHeight: 30,
			enableGridMenu: false,
			enableColumnMenus: false,
			enableRowSelection: false,
			enableSelectAll: false,
			enableFiltering: false,
			enableSorting: false,
			enableFullRowSelection: false,
			enableCellEdit: false,
			multiSelect: false,
			data: [],
			columnDefs: [
				{ field: 'nombreMedicamento', name: 'nombreMedicamento', displayName: 'MEDICAMENTO', minWidth: 120 },
				{ field: 'cantidad', name: 'cantidad', displayName: 'CANTIDAD', minWidth: 120, width:90 },
				{ field: 'indicaciones', name: 'indicaciones', displayName: 'INDICACIONES', minWidth: 120 },
				{
					field: 'eliminar', name: 'eliminar', displayName: '', width: 50,
					cellTemplate: '<button class="btn btn-default btn-sm text-danger btn-action" ng-click="grid.appScope.btnQuitarDeLaCestaRec(row);$event.stopPropagation();"> <i class="fa fa-trash" tooltip-placement="left" uib-tooltip="ELIMINAR!"></i> </button>'
				}
			],
			onRegisterApi: function (gridApi) {
				$scope.gridApi = gridApi;
			}
		};
		$scope.getPaginationServerSideRec = function (loader) {
			if (loader) {
				blockUI.start('Procesando información...');
			}
			var arrParams = {
				datos: {
					idreceta: $scope.fData.idreceta
				}
			};
			RegistroAtencionService.sGetDetalleReceta(arrParams).then(function (rpta) {
				if (rpta.datos.length == 0) {
					rpta.paginate = { totalRows: 0 };
				}
				// $scope.gridOptionsRec.totalItems = rpta.paginate.totalRows;
				$scope.gridOptionsRec.data = rpta.datos;
				// $scope.calcularTotales();
				if (loader) {
					blockUI.stop();
				}
			});
		};
		
		$scope.grabarReceta = function () {
			blockUI.start("Registrando receta...");
			$scope.fData.detalle = $scope.gridOptionsRec.data;
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
		$scope.agregarItemMedicamento = function () {
			if ($scope.fData.temporal.nombreMedicamento == null) {
				pinesNotifications.notify({
					title: 'Advertencia.',
					text: 'Debe seleccionar un diagnostico.',
					type: 'warning',
					delay: 5000
				});
				return;
			}


			var producto_repetido = false;
			angular.forEach($scope.gridOptionsRec.data, function (value, key) {
				if (value.nombreMedicamento == $scope.fData.temporal.nombreMedicamento) {
					producto_repetido = true;
					pinesNotifications.notify({
						title: 'Advertencia.',
						text: 'Ya está cargado este medicamento.',
						type: 'warning',
						delay: 5000
					});
					return;
				}
			});

			if (producto_repetido === false) {
				$scope.gridOptionsRec.data.push({
					nombreMedicamento: $scope.fData.temporal.nombreMedicamento,
					cantidad: $scope.fData.temporal.cantidad,
					indicaciones: $scope.fData.temporal.indicaciones,
				});

				$scope.fData.temporal.nombreMedicamento = null;
				$scope.fData.temporal.cantidad = null;
				$scope.fData.temporal.indicaciones = null;
			}
		}
		$scope.btnQuitarDeLaCestaRec = function (row) {
			var index = $scope.gridOptionsRec.data.indexOf(row.entity);
			$scope.gridOptionsRec.data.splice(index, 1);
			// $scope.calcularTotales();
		}
		// IMPRIMIR RECETA
		$scope.btnImprimirReceta = function () {
			var arrParams = {
				titulo: 'RECETA',
				datos: {
					cita: $scope.fData,
					salida: 'pdf',
					tituloAbv: 'REC',
					titulo: 'RECETA'
				},
				envio_correo: 'si',
				metodo: 'php',
				url: angular.patchURLCI + "Reportes/generar_pdf_receta"
			}
			ModalReporteFactory.getPopupReporte(arrParams);
		}
	}
]);
app.service("RegistroAtencionService", function ($http, $q, handleBehavior){
	return({
		sGetCitaById: sGetCitaById,
		sGetDetalleReceta: sGetDetalleReceta,
		sRegistrarAtencion: sRegistrarAtencion,
		sRegistrarReceta: sRegistrarReceta,
		sGetDetalleImagenes: sGetDetalleImagenes,
		sRegistrarImagen: sRegistrarImagen,
		sQuitarImagen: sQuitarImagen,
	});
	function sGetCitaById(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_cita_por_id",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sGetDetalleReceta(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_detalle_receta",
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
	function sGetDetalleImagenes(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/listar_detalle_imagenes",
			data: datos
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sRegistrarImagen(datos) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/registrar_imagen",
			data: datos,
			transformRequest: angular.identity,
      headers: { 'Content-Type': undefined }
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
	function sQuitarImagen(imagenId) {
		var request = $http({
			method: "post",
			url: angular.patchURLCI + "Cita/quitar_imagen",
			data: {
				id: imagenId
			}
		});
		return (request.then(handleBehavior.success, handleBehavior.error));
	}
});
