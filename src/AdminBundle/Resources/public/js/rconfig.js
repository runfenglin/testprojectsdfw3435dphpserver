/**
 * @name: rconfig
 * @description: Define required js path
 *
 * @author: Haiping Lu
 */
var require = {

    waitSeconds: 0,
    
    paths: {
        'jquery': [
            '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min',
            '/bundles/admin/plugins/jquery/dist/jquery.min'
        ],
        'jquery-ui': '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min',
        'bootstrap': '//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min',
        'angular': '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min',
        'angular-resource': '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-resource.min',
        'angular-sanitize': '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-sanitize.min',
        'angular-animate': '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-animate.min',
        'angular-cookies': '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-cookies.min',
        'angular-ui-router': '/bundles/admin/plugins/angular-ui-router/release/angular-ui-router.min',
        'angular-couch-potato': '/bundles/admin/plugins/angular-couch-potato/dist/angular-couch-potato',
        'angular-bootstrap': '/bundles/admin/plugins/angular-bootstrap/ui-bootstrap-tpls.min',
        'ckeditor': '/bundles/admin/plugins/ckeditor/ckeditor',
        'domReady': '/bundles/admin/plugins/requirejs-domready/domReady',
        'notification': '/bundles/admin/plugins/notification/SmartNotification.min',
        'angular-file-upload': '/bundles/admin/plugins/angular-file-upload/angular-file-upload',
        'angular-file-upload-all': '/bundles/admin/plugins/angular-file-upload/angular-file-upload-all',
        'angular-file-upload-shim': '/bundles/admin/plugins/angular-file-upload/angular-file-upload-shim',
        'angular-ui-datepicker':'/bundles/admin/plugins/angular-ui-datepicker/datepicker',
        'angular-ui-tooltip':'/bundles/admin/plugins/angular-ui-tooltip/tooltip',
        'angular-ui-dimensions':'/bundles/admin/plugins/angular-ui-tooltip/dimensions',
        'angular-ui-select': '/bundles/admin/plugins/angular-ui/ui-select/select',
        'angular-ui-sortable': '/bundles/admin/plugins/angular-ui/ui-sortable/sortable',
		'lodash': '/bundles/admin/plugins/lodash/dist/lodash.min',
		'sparkline': '/bundles/admin/plugins/relayfoods-jquery.sparkline/dist/jquery.sparkline.min',
		'modules-includes': 'includes',
        'app': 'app'
    },
    // angular does not support AMD out of the box, put it in a shim
    shim: {
        'jquery-ui': { deps: ['jquery']},
        'angular': {exports: 'angular', deps: ['jquery']},
        'angular-resource': {deps: ['angular']},
        'angular-animate': {deps: ['angular']},
        'angular-cookies': {deps: ['angular']},
        'angular-sanitize': {deps: ['angular']},
        'angular-bootstrap': {deps: ['angular']},
        'angular-ui-router': {deps: ['angular']},
        'angular-couch-potato': {deps: ['angular']},
        'ckeditor': {deps: ['jquery']},
        'bootstrap': {deps: ['jquery']},
        'notification': {deps: ['jquery']},
        'modules-includes': { deps: ['angular']},
        'angular-ui-datepicker': { deps: ['jquery-ui']},
        'angular-ui-tooltip': { deps: ['angular-ui-dimensions']},
        'angular-ui-dimensions': { deps: ['angular']},
		'sparkline': { deps: ['jquery']},
    },
    priority: [
        "jquery",
        "boostrap",
        "angular"
    ]
};