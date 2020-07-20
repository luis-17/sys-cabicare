app.service("DiagnosticoServices",function($http, $q, handleBehavior) {
  return({
    sListarDiagnosticoAutocomplete: sListarDiagnosticoAutocomplete,
    sListarDetalleDx: sListarDetalleDx,
  });
  function sListarDiagnosticoAutocomplete(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Diagnostico/listar_autocompletado_diagnostico",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
  function sListarDetalleDx(datos) {
    var request = $http({
      method : "post",
      url: angular.patchURLCI +"Diagnostico/listar_detalle_dx",
      data : datos
    });
    return (request.then(handleBehavior.success,handleBehavior.error));
  }
});
