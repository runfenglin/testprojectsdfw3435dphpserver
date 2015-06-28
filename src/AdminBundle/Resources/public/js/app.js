'use strict';

/**
 * @ngdoc overview
 * @name app
 * @description: Main module of the application
 *
 * @author: Haiping Lu
 */

define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
    'angular-animate',
    'angular-sanitize',
    'angular-bootstrap',
    'smartwidgets',
    'notification'
], function (ng, couchPotato) {

    var app = ng.module('app', [
        'ngSanitize',
        'ngAnimate',
        'scs.couch-potato',
        'ui.router',
        'ui.bootstrap',
        // App
		'app.layout',
        'app.auth',
		'app.dashboard',
		'app.trip',
		'app.user',
		'app.graphs',
		'app.widgets'
    ]);
    
    couchPotato.configureApp(app);

    app.config(['$interpolateProvider', function($interpolateProvider) {
    
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    }]);
            
    app.config(['$provide', '$httpProvider', function($provide, $httpProvider){
    
        $provide.factory('ErrorHttpInterceptor', function($q) {
        
            var errorCounter = 0;
            
            function notifyError(rejection){
                console.log(rejection);
                 
                $.smallBox({
                    title: rejection.status + ' ' + rejection.statusText,
                    content: rejection.data,
                    color: "#C46A69",
                    icon: "glyphicon glyphicon-warning-sign shake animated",
                    number: ++errorCounter,
                    timeout: 60000
                });
                
            }
            
            return {
                requestError: function(rejection) {
                
                    notifyError(rejection);
                    
                    return $q.reject(rejection);
                },
                
                responseError: function(rejection) {
                
                    notifyError(rejection);
                    
                    return $q.reject(rejection);
                }
            }
        })
        
        $httpProvider.interceptors.push('ErrorHttpInterceptor');
    }])
    
    .config(['$tooltipProvider', function($tooltipProvider){
    
        $tooltipProvider.options({animation: false});
    }]);

    app.run(function ($couchPotato, $rootScope, $state, $stateParams) {
        app.lazy = $couchPotato,
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        $rootScope.appConfig = {
            baseUrl:angular.element('body').data('root') + '/admin/api/'
        }
    });

    return app;
});
