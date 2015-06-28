/**
 * @ngdoc overview
 * @name RequestCtrl
 * @description: Controller
 *
 * @author: Haiping Lu
 */
define(['app/trip/module'], function (module) {

    "use strict";
    
    module.registerController('RequestCtrl', ['$scope', 'trips', 'TripModel', function($scope, trips, Trip){

		$scope.trips = [];
		
        trips.$promise.then(function(data){
            $scope.trips = data;
        })
		
		$scope.filterTrip = function() {
			switch($scope.type) {
				case '2': {
					return {group:false,driver:'!'};
				}
				case '1': {
					return {group:true};
				}
				case '3': {
					return {driver:'!!'}
				}
				default: {
					return {}
				}
			}
		}
    }]);
});