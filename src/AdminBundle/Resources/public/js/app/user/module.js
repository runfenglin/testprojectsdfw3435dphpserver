/**
 * @ngdoc overview
 * @name user module
 * @description: Route definition
 *
 * @author: Haiping Lu
 */
define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
    'angular-resource',
], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.user', [
        'ui.router', 
        'ngResource'
    ]);
    
    couchPotato.configureApp(module);
    
	module.config(function ($stateProvider, $couchPotatoProvider) {
		$stateProvider
			
			.state('app.user', {
				url: 'user',
				views: {
					"content@app": {
                        templateUrl: 'tmpl/user/base',
                        controller: function ($scope) {
							
                        },
                        controllerAs: 'BaseCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([]),
                        }
                    }
				},
				data:{
					title: 'User'
				}
			})
			
			.state('app.user.mobile', {
				url: '/mobile',
				views: {
					user: {
						controller: 'MobileCtrl',
						templateUrl: 'tmpl/user/mobile',
						resolve: {
							deps: $couchPotatoProvider.resolveDependencies([
								'app/user/controllers/MobileCtrl'
							]),
							users: function(UserModel){
                                return UserModel.query({Type: 'mobile'});
                            },
						}
					}
				},
				data:{
					title: 'Mobile User'
				}
			})
			
			.state('app.user.media', {
				url: '/media',
				views: {
                    "user@app.user": {
                        templateUrl: 'tmpl/user/media',
                        controller: 'MediaCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
								'app/user/controllers/MediaCtrl'
							]),	
							users: function(UserModel){
                                return UserModel.query({Type: 'media'});
                            },
                        }
                    }
                }
			});
			
			
	});

    module.run(function($couchPotato){
        module.lazy = $couchPotato;
    });
    
    return module;

});
