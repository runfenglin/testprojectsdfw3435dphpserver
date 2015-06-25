define(['app/layout/module'], function(module){
    "use strict";

    return module.registerDirective('loginInfo', function(UserModel){

        return {
            restrict: 'A',
            templateUrl: 'directive/tmpl/login-info',
            link: function(scope, element){
				scope.user = {};
                UserModel.self(function(data){
                    scope.user = data;
                });
            }
        }
    })
});
