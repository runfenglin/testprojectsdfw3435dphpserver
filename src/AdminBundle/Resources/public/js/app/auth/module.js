/**
 * @ngdoc overview
 * @name auth module
 * @description: Route definition
 *
 * @author: Haiping Lu
 */
define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
    'angular-resource',
    'angular-ui-select',
    'angular-file-upload', 
    'angular-file-upload-shim',
    'angular-ui-datepicker'
], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.auth', [
        'ui.router', 
        'ngResource',
        'ui.select', 
        'angularFileUpload', 
        'ui.date'
    ]);
    
    couchPotato.configureApp(module);
    
    module.config(function ($stateProvider, $couchPotatoProvider, $urlRouterProvider) {

    
        $stateProvider

            .state('auth', {
                absolute: true,
                url: '/',
                views: {
                    base: {
                        templateUrl: 'tmpl/base',
                        controller: function($rootScope, $scope){
                        },
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
                            ])
                        }
                    }
                },
                data: {
                    title: 'Security'
                }
            })
            .state('auth.login', {
				url: 'login',
				views: {
					content: {
						templateUrl: 'tmpl/login',
						controller: 'LoginCtrl',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
                                'app/auth/controllers/LoginCtrl'
                            ])
                        }
					}
				},
				data: {
					title: 'Login'
				}
			});

    });

    module.run(function($couchPotato){
        module.lazy = $couchPotato;
    }).filter('propsFilter', function () {
        return function(items, props) {
            var out = [];

            if (angular.isArray(items)) {
                items.forEach(function(item) {
                    var itemMatches = false;

                    var keys = Object.keys(props);
                    for (var i = 0; i < keys.length; i++) {
                        var prop = keys[i];
                        var text = props[prop].toLowerCase();
                        if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                            itemMatches = true;
                            break;
                        }
                    }

                    if (itemMatches) {
                        out.push(item);
                    }
                });
            } else {
                // Let the output be the input untouched
                out = items;
            }

            return out;
        };
    }).filter('greaterThan', function(){
        return function(items, min) {

            var greater = [];
            angular.forEach(items, function(item){
                if(item.id > min) {
                    greater.push(item);
                }
            });
            
            return greater;
        };
    }).filter('money', function(){
		return function(input) {
			
			if (!input) {
				return '';
			}
			else {
				var filtered = input.toString().replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				return '$' + filtered;
			}
		}
	});
    
    return module;

});
