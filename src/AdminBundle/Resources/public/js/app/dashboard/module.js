/**
 * @ngdoc overview
 * @name dashboard module
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

    var module = ng.module('app.dashboard', [
        'ui.router', 
        'ngResource'
    ]);
    
    couchPotato.configureApp(module);
    
	module.config(function ($stateProvider, $couchPotatoProvider) {
		$stateProvider
			.state('app.dashboard', {
				url: 'dashboard',
				views: {
					"content@app": {
						controller: 'DashboardCtrl',
						templateUrl: 'tmpl/dashboard',
						resolve: {
							deps: $couchPotatoProvider.resolveDependencies([
								'app/dashboard/controllers/DashboardCtrl',
								'app/modules/graphs/directives/inline/sparklineContainer'
							])
						}
					}
				},
				data:{
					title: 'Dashboard'
				}
			});
	});

    module.run(function($couchPotato){
        module.lazy = $couchPotato;
    });
    
    return module;

});
