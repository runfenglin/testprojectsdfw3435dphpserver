define(['app', 'lodash'], function(module, _){
	
	"use strict";
    
    return module.registerFactory('UserModel', function($resource){
        
        var baseURL = angular.element('body').data('base-url') + '/admin/api/user';
        
        var UserModel = $resource(
            baseURL + '/:Id.:Format',
            {Id: '@Id', Format: 'json'},
            {
                self: {
                    url: baseURL + '/self.:Format',
                    method: 'GET',
                    isArray: false
                },
				
				query: {
                    url: baseURL + '/:Type.:Format',
                    method: 'GET',
					params: {Type: '@Type'},
                    isArray: true					
				}
            }
        );

        return UserModel; 
    });
		
});
