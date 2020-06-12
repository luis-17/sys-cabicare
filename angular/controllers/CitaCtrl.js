app.controller('CitaCtrl',
	['$scope',
	'$filter',
	'$state',
	'$stateParams',
	'$uibModal',
	'$bootbox',
	'$log',
	'$timeout',
	'pinesNotifications',
	'uiGridConstants',
	'blockUI',
	'ReservaCitasFactory',

	function (
		$scope,
		$filter,
		$state,
		$stateParams,
		$uibModal,
		$bootbox,
		$log,
		$timeout,
		pinesNotifications,
		uiGridConstants,
		blockUI,
		ReservaCitasFactory

	) {
		// var vm = this;
		console.log('En citas');

		/* EVENTOS */
		$scope.menu = angular.element('.menu-dropdown');
		$scope.alertOnClick = function (event, jsEvent, view) {
			$scope.event = event;
			//console.log(event,jsEvent,'event,jsEvent');
			$scope.menu.addClass('open');
			$scope.menu.removeClass('left right');
			var wrap = angular.element(jsEvent.target).closest('.fc-event');
			var cal = wrap.closest('.calendar');
			var left = wrap.offset().left - cal.offset().left;
			var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
			if (right > $scope.menu.width()) {
				$scope.menu.addClass('left');
			} else if (left > $scope.menu.width()) {
				$scope.menu.addClass('right');
			}
			$scope.event.posX = jsEvent.pageX - cal.offset().left;
			if ($scope.event.posX < 140) {
				$scope.event.posX = 140;
			}

			$scope.event.posY = jsEvent.pageY - cal.offset().top;
			if ($scope.event.posY > 620) {
				$scope.event.posY = 620;
			}
		}
		$scope.alertOnResize = function (event, delta) {
			angular.element('.calendar').fullCalendar('refetchEvents');
		};

		$scope.selectCell = function (date, end, jsEvent, view) {
			console.log(angular.element('.calendar').fullCalendar('getView'), 'angular.element(.calendar).fullCalendar(getView)');
			var typeView = angular.element('.calendar').fullCalendar('getView');
			if (typeView.type == 'month') {
				angular.element('.calendar').fullCalendar('gotoDate', date);
				angular.element('.calendar').fullCalendar('changeView', 'agendaDay');
			} else {
				$scope.btnAgregarCita(date, end);
			}
		}
		$scope.alertOnDrop = function (event, delta) {
			blockUI.start('Actualizando calendario...');
			var datos = {
				event: event,
				delta: delta,
			};
			ReservaCitasServices.sMoverCita(datos).then(function (rpta) {
				if (rpta.flag == 1) {
					var pTitle = 'OK!';
					var pType = 'success';
				} else if (rpta.flag == 0) {
					var pTitle = 'Advertencia!';
					var pType = 'warning';
				}
				angular.element('.calendar').fullCalendar('refetchEvents');
				pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
				blockUI.stop();
			});
		};
		$scope.overlay = $('.fc-overlay');
		$scope.alertOnMouseOver = function (event, jsEvent, view) {
			$scope.event = event;
			$scope.overlay.removeClass('left right top').find('.arrow').removeClass('left right top pull-up');
			var wrap = $(jsEvent.target).closest('.fc-event');
			var cal = wrap.closest('.calendar');
			var left = wrap.offset().left - cal.offset().left;
			var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
			var top = cal.height() - (wrap.offset().top - cal.offset().top + wrap.height());
			if (right > $scope.overlay.width()) {
				$scope.overlay.addClass('left').find('.arrow').addClass('left pull-up')
			} else if (left > $scope.overlay.width()) {
				$scope.overlay.addClass('right').find('.arrow').addClass('right pull-up');
			} else {
				$scope.overlay.find('.arrow').addClass('top');
			}
			if (top < $scope.overlay.height()) {
				$scope.overlay.addClass('top').find('.arrow').removeClass('pull-up').addClass('pull-down')
			}
			(wrap.find('.fc-overlay').length == 0) && wrap.append($scope.overlay);
		}
		$scope.actualizarCalendario = function (block) {
			blockUI.start('Actualizando calendario...');
			angular.element('.calendar').fullCalendar('refetchEvents');
			blockUI.stop();
		}
		$scope.closeMenu = function () {
			$scope.menu.removeClass('open');
		}

		$scope.btnAgregarCita = function (start, end) {
			console.log('Agrega cita');
			var arrParams = {
				'start': start || null,
				'end': end || null
			};
			ReservaCitasFactory.agregarCitaModal(arrParams);
		}


		/* CARGA DE DATOS */
		$scope.eventsF = function (start, end, timezone, callback) {
			var events = [];
			// blockUI.start('Actualizando calendario...');
			//console.log(start, end,'start, end');
			//console.log(start.toLocaleTimeString(), end.toLocaleTimeString(),'start.toLocaleTimeString(), end.toLocaleTimeString()');
			//console.log(start,end,'moment(start).tz("America/Lima").format(YYYY-MM-DD)');
			/* $scope.fBusqueda.desde = moment(start).tz('America/Lima').format('YYYY-MM-DD');
			$scope.fBusqueda.hasta = moment(end).tz('America/Lima').format('YYYY-MM-DD');
			ReservaCitasServices.sListarCitaCalendario($scope.fBusqueda).then(function (rpta) {
				if (rpta.flag == 1) {
					angular.forEach(rpta.datos, function (row, key) {
						row.start = moment(row.start);
						row.end = moment(row.end);
					});
					events = rpta.datos;
					callback(events);
				}
				blockUI.stop();
			}); */
		}
		$scope.eventSources = [$scope.eventsF];
		/* Change View */
		$scope.changeView = function (view, calendar) {
			$('.calendar').fullCalendar('changeView', view);
		};
		$scope.today = function (calendar) {
			$('.calendar').fullCalendar('today');
		};

		$scope.uiConfig = {
			calendar: {
				allDaySlot: false,
				height: 450,
				contentHeight: 510,
				editable: true,
				selectable: true,
				defaultView: 'agendaWeek',
				dayNames: ["Domingo", "Lunes ", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
				dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
				monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				monthNamesShort: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				header: {
					left: 'prev',
					center: 'title',
					right: 'next'
				},
				select: $scope.selectCell,
				eventDrop: $scope.alertOnDrop,
				eventResize: $scope.alertOnResize,
				eventClick: $scope.alertOnClick,
				eventMouseover: function (data, event, view) {
					// console.log( event, $('.tooltip-event') );
					var tooltip = '<div class="tooltip-event"' +
						'style="">'
						+ 'Paciente: ' + data.paciente + '<br />'
						+ '</div>';
					$("body").append(tooltip);
					$(this).mouseover(function (e) {
						$(this).css('z-index', 10000);
						$('.tooltip-event').fadeIn('500');
						$('.tooltip-event').fadeTo('10', 1.9);
					}).mousemove(function (e) {
						$('.tooltip-event').css('top', e.pageY + 10);
						$('.tooltip-event').css('left', e.pageX + 20);
					});
				},
				eventMouseout: function (data, event, view) {
					$(this).css('z-index', 8);
					$('.tooltip-event').remove();
				},
				minTime: '07:00:00',
				maxTime: '20:00:00',
				displayEventTime: false,
				views: {
					week: {
						titleFormat: 'D MMMM YYYY',
						titleRangeSeparator: ' - '
					},
					day: {
						titleFormat: 'ddd DD-MM'
					}
				}
			}
		};



	}
]);

app.factory("ReservaCitasFactory", function ($uibModal, pinesNotifications, blockUI, $timeout) {
	var interfaz = {
		agregarCitaModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({
				templateUrl: angular.patchURLCI + 'Cita/ver_popup_form_cita',
				size: 'lg',
				backdrop: 'static',
				keyboard: false,
				controller: function ($scope, $uibModalInstance, arrParams) {
					blockUI.stop();
					$scope.fData = {};
					$scope.fData.accion = 'reg';
					$scope.titleForm = 'Registro de Cita';

					$scope.cancel = function () {
						$uibModalInstance.dismiss('cancel');
					}


				},
				resolve: {
					arrParams: function () {
						return arrParams;
					}
				}
			});
		}
	}

	return interfaz;
});