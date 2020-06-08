app.service("PacienteServices",function($http, $q, handleBehavior) {
    return({
        sBuscarPacientes: sBuscarPacientes,
        sListarPacientesBusqueda: sListarPacientesBusqueda 
    });
    function sBuscarPacientes(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cliente/buscar_cliente_para_formulario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarPacientesBusqueda(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cliente/buscar_cliente_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});