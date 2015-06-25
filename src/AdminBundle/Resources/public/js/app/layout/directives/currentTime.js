define(['app/layout/module'], function (module) {

    'use strict';

    module.registerDirective('currentTime', ['$interval', 'dateFilter', function ($interval, dateFilter) {

		return {
			link: function(scope, element, attributes) {
				
				var format = "MM/dd/yyyy 'at' h:mm:ssa";
				var handler;
				
				function updateTime() {
					element.text(dateFilter(new Date(), format));
				}
				
				handler = $interval(updateTime, 1000);
			}
		}
	}]);

});