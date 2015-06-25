/**
 * @ngdoc overview
 * @name AuthModel
 * @description: Model Layer
 *
 * @author: Haiping Lu
 */
define(['app'], function(module){
    
    "use strict";
    
    return module.registerFactory('AuthModel', function($resource){
        
        var baseURL = angular.element('body').data('base-url') + '/api/category';
        
        var AuthModel = $resource(
            baseURL,
            {},
            {
            /*    query: {
                    url: baseURL + '/all',
                    method: 'GET',
                    isArray: true
                },
				sub: {
                    url: baseURL + '/:Id/sub',
                    params: {Id: '@Id'},
                    method: 'GET',
                    isArray: true
                }*/
            }
        );

        return AuthModel; 
    });

});