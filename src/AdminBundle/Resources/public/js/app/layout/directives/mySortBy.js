define(['app/layout/module'], function (module) {

    'use strict';

    module.registerDirective('mySortBy', function () {
        return {
            restrict: 'A',
			require: '^mySortColumns',
            link: function (scope, element, attributes, ctrl) {

                var column = attributes.mySortBy;
				element.css('cursor', 'pointer');
				element.bind('click', function(e){
					
					ctrl.setPredicate(column);
					ctrl.setReverse(!ctrl.getReverse());
					
				});
				
            }
        }
    });
});
