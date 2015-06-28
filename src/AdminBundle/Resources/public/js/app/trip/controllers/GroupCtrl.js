/**
 * @ngdoc overview
 * @name GroupCtrl
 * @description: Controller
 *
 * @author: Haiping Lu
 */
define(['app/trip/module'], function (module) {

    "use strict";
    
    module.registerController('GroupCtrl', ['$scope', 'trips', 'TripModel', function($scope, trips, Trip){

		$scope.trips = [];
		
        trips.$promise.then(function(data){
            $scope.trips = data;
        })
		
    }]);
});