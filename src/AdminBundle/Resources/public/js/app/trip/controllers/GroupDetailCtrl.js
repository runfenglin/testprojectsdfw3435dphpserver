/**
 * @ngdoc overview
 * @name GroupDetailCtrl
 * @description: Controller
 *
 * @author: Haiping Lu
 */
define(['app/trip/module'], function (module) {

    "use strict";
    
    module.registerController('GroupDetailCtrl', ['$rootScope', '$scope', '$q', 'TripModel', '$state', '$log', '$timeout', function($rootScope, $scope, $q, Trip, $state, $log, $timeout){

		Trip.get({Type: 'group', Id: $scope.$stateParams.id}).$promise.then(function(data) {
			$scope.trip = data;
		}); 
		
    }]);
});