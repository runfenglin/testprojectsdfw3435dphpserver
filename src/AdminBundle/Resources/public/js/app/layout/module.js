define(['angular',
    'angular-couch-potato',
    'angular-ui-router'], function (ng, couchPotato) {

    "use strict";


    var module = ng.module('app.layout', ['ui.router']);


    couchPotato.configureApp(module);

    module.config(function ($stateProvider, $couchPotatoProvider, $urlRouterProvider) {


        $stateProvider
            .state('app', {
                abstract: true,
				url: '/',
                views: {
                    root: {
                        templateUrl: 'tmpl/layout',
                        resolve: {
                            deps: $couchPotatoProvider.resolveDependencies([
                                'app/layout/directives/loginInfo',
                                'app/layout/directives/currentTime',
								'app/models/UserModel'
                                //'modules/graphs/directives/inline/sparklineContainer',
                            //    'components/chat/directives/asideChatWidget'
                            ])
                        }
                    }
                }
            });
        $urlRouterProvider.otherwise('/dashboard');

    });

    module.run(function ($couchPotato) {
        module.lazy = $couchPotato;
    });

    return module;

});
