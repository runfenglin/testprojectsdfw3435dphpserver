define(['app', 'lodash'], function(module, _){
	
	"use strict";
    
    return module.registerFactory('UserModel', function($resource){
        
        var baseURL = angular.element('body').data('base-url') + '/admin/api/user';
        
        var UserModel = $resource(
            baseURL + '/:Id.:Format',
            {Id: '@Id', Format: 'json'},
            {
                self: {
                    url: baseURL + '/self',
                    method: 'GET',
                    isArray: false
                }
            }
        );

        return UserModel; 
    });
		
});
