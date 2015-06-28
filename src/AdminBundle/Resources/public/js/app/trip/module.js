/**
 * @ngdoc overview
 * @name trip module
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

    var module = ng.module('app.trip', [
        'ui.router', 
        'ngResource'
    ]);
    
    couchPotato.configureApp(module);
    
	module.config(function ($stateProvider, $couchPotatoProvider) {
		$stateProvider
			
			.state('app.trip', {
				url: 'trip',
				views: {
					"content@app": {
                        templateUrl: 'tmpl/trip/base',
                        controller: function ($scope) {
							
                        },
                        controllerAs: 'BaseCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([]),
                        }
                    }
				},
				data:{
					title: 'Trip'
				}
			})
			
			.state('app.trip.request', {
				url: '/request',
				views: {
					trip: {
						controller: 'RequestCtrl',
						templateUrl: 'tmpl/trip/request',
						resolve: {
							deps: $couchPotatoProvider.resolveDependencies([
								'app/trip/controllers/RequestCtrl'
							]),
							trips: function(TripModel){
                                return TripModel.query({Type: 'request'});
                            },
						}
					}
				},
				data:{
					title: 'Trip Request'
				}
			})
			
			.state('app.trip.group', {
				url: '/group',
				views: {
                    "trip@app.trip": {
                        templateUrl: 'tmpl/trip/group',
                        controller: 'GroupCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
								'app/trip/controllers/GroupCtrl'
							]),	
							trips: function(TripModel){
                                return TripModel.query({Type: 'group'});
                            },
                        }
                    }
                }
			})
			
			.state('app.trip.group.detail', {
				url: '/:id',
				views: {
                    "trip@app.trip": {
                        templateUrl: 'tmpl/trip/group-detail',
                        controller: 'GroupDetailCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
								'app/trip/controllers/GroupDetailCtrl'
							])	
							
                        }
                    }
                }
			})
			
			.state('app.trip.single', {
				url: '/single',
				views: {
                    "trip@app.trip": {
                        templateUrl: 'tmpl/trip/single',
                        controller: 'SingleCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
								'app/trip/controllers/SingleCtrl'
							]),	
							trips: function(TripModel){
                                return TripModel.query({Type: 'single'});
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
