/**
 * @ngdoc overview
 * @name UserCtrl
 * @description: Controller
 *
 * @author: Haiping Lu
 */
define(['app/trip/module'], function (module) {

    "use strict";
    
    module.registerController('UserCtrl', ['$scope', 'users', 'UserModel', function($scope, users, User){

		$scope.users = [];
		
        users.$promise.then(function(data){
            $scope.users = data;
        })
		
    }]);
});