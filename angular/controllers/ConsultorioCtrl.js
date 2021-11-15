app.service("ConsultorioServices",function($http, $q, handleBehavior) {
    return({
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Consultorio/listar_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});
