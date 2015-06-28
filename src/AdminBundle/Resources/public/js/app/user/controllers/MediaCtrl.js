/**
 * @ngdoc overview
 * @name MediaCtrl
 * @description: Controller
 *
 * @author: Haiping Lu
 */
define(['app/user/module'], function (module) {

    "use strict";
    
    module.registerController('MediaCtrl', ['$scope', 'users', 'UserModel', function($scope, users, User){

		$scope.users = [];
		
        users.$promise.then(function(data){
            $scope.users = data;
        })
		
    }]);
});