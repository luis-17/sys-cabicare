app.service("DistritoServices",function($http, $q, handleBehavior) {
    return({
      sListarCbo: sListarCbo,
    });
    function sListarCbo(datos) {
      var request = $http({
        method : "post",
        url: angular.patchURLCI +"Distrito/listar_distritos",
        data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
  });
  