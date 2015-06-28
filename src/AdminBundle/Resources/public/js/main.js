/**
 * @ngdoc overview
 * @name main
 * @description: Entry of Angular APP
 *
 * @author: Haiping Lu
 */
// Defer AngularJS bootstrap
window.name = "NG_DEFER_BOOTSTRAP!";

define([
    'require',
    'jquery',
    'angular',
    'domReady',
    'bootstrap',
	'appConfig',
    'app',
    'modules-includes'
], function (require, $, ng, domReady) {
    'use strict';

    domReady(function (document) {
        ng.bootstrap(document, ['app']);
        ng.resumeBootstrap();
    });
});