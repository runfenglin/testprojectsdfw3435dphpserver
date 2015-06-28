/**
 * @ngdoc overview
 * @name includes
 * @description: Included files
 *
 * @author: Haiping Lu
 */
define([
    'app/models/AuthModel',
	'app/layout/module',
	'app/layout/directives/hrefVoid',
    'app/layout/directives/bigBreadcrumbs',
    'app/layout/directives/mySortColumns',
    'app/layout/directives/mySortBy',
    'app/layout/actions/toggleMenu',
    'app/layout/actions/fullScreen',    
    'app/layout/actions/minifyMenu',
    'app/layout/actions/resetWidgets',
    'app/layout/directives/smartMenu',
    'app/layout/directives/smartRouterAnimationWrap',	
    'app/auth/module',
	'app/dashboard/module',
	'app/trip/module',
	'app/user/module',
	'app/models/TripModel',
	'app/modules/graphs/module',
	'app/modules/widgets/directives/widgetGrid',
    'app/modules/widgets/directives/jarvisWidget',
], function(){
    'use strict';
});