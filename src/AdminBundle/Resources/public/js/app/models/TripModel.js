define(['app', 'lodash'], function(module, _){
	
	"use strict";
    
    return module.registerFactory('TripModel', function($resource){
        
        var baseURL = angular.element('body').data('base-url') + '/admin/api/trip';
        
        var TripModel = $resource(
            baseURL + '/:Id.:Format',
            {Id: '@Id', Format: 'json'},
            {
                query: {
                    url: baseURL + '/:Type.:Format',
                    method: 'GET',
					params: {Type: '@Type'},
                    isArray: true
                },
				
				get: {
					url: baseURL + '/:Type/:Id.:Format',
					method: 'GET',
					params: {Type: '@Type', Id: '@Id'},
					isArray: false
				}
            }
        );

        return TripModel; 
    });
		
});
