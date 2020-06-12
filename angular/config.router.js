'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [
      '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;
      }
    ]
  )
  .config(
    [
      '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 'MODULE_CONFIG',
      function ($stateProvider, $urlRouterProvider, JQ_CONFIG, MODULE_CONFIG) {
        var layout = "tpl/app.html";
        if(window.location.href.indexOf("material") > 0){
          layout = "tpl/blocks/material.layout.html";
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }else{
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }

        $stateProvider
          .state('access', {
              url: '/access',
              template: '<div ui-view class="fade-in-right-big smooth"></div>'
          })
          .state('access.login', {
              url: '/login',
              templateUrl: 'tpl/login.html',
              resolve: load( ['angular/controllers/Login.js'] )
          })
          .state('app', {
            abstract: true,
            url: '/app',
            templateUrl: layout
          })
          .state('app.dashboard', {
            url: '/dashboard',
            templateUrl: 'tpl/app_dashboard.html',
            resolve: load(['angular/controllers/chart.js'])
          })
          .state('app.usuario', {
            url: '/usuario',
            templateUrl: 'tpl/usuario.html',
            resolve: load([
              'angular/controllers/UsuarioCtrl.js',
              'angular/controllers/PerfilCtrl.js'
              // 'angular/controllers/ColaboradorCtrl.js'
            ])
          })
          .state('app.perfil', {
            url: '/perfil',
            templateUrl: 'tpl/perfil.html',
            resolve: load([
              'angular/controllers/PerfilCtrl.js'
            ])
          })
          // .state('app.paciente', {
          //   url: '/paciente',
          //   templateUrl: 'tpl/paciente.html',
          //   resolve: load([
          //     'angular/controllers/PacienteCtrl.js'
          //   ])
          // })
          .state('app.producto', {
            url: '/producto',
            templateUrl: 'tpl/producto.html',
            resolve: load([
              'angular/controllers/ProductoCtrl.js',
              'angular/controllers/TipoProductoCtrl.js'
            ])
          })
          .state('app.reserva-cita', {
            url: '/reserva-cita',
            templateUrl: 'tpl/reserva-cita.html',

            resolve: load([
              // 'angular/controllers/ReservaCitaCtrl.js'
              'angular/controllers/CitaCtrl.js'
            ])
          })
          ;

        function load(srcs, callback) {
          return {
              deps: ['$ocLazyLoad', '$q',
                function( $ocLazyLoad, $q ){
                  var deferred = $q.defer();
                  var promise  = false;
                  srcs = angular.isArray(srcs) ? srcs : srcs.split(/\s+/);
                  if(!promise){
                    promise = deferred.promise;
                  }
                  angular.forEach(srcs, function(src) {
                    promise = promise.then( function(){
                      if(JQ_CONFIG[src]){
                        return $ocLazyLoad.load(JQ_CONFIG[src]);
                      }
                      angular.forEach(MODULE_CONFIG, function(module) {
                        if( module.name == src){
                          name = module.name;
                        }else{
                          name = src;
                        }
                      });
                      return $ocLazyLoad.load(name);
                    } );
                  });
                  deferred.resolve();
                  return callback ? promise.then(function(){ return callback(); }) : promise;
              }]
          }
        }
      }
    ]
  );
