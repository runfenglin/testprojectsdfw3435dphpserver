define(['app/layout/module'], function (module) {

    'use strict';

    module.registerDirective('mySortColumns', function () {
        return {
            restrict: 'A',
			scope: {
				predicate: '=',
				reverse: '='
			},
			controller: function($scope){
				
				$scope.predicate = null;
				$scope.reverse = false;
				
				$scope.sorting = function() {
					if (!arguments.length) {
						return false;
					}
					else if (arguments.length == 1 && arguments[0] == $scope.predicate) {
						return true;
					}
					else if (arguments.length == 2 
							&& arguments[0] == $scope.predicate 
							&& !!arguments[1] == $scope.reverse) {
						return true;
					}
					return false;
				};
				
				this.setPredicate = function(col) {
					$scope.predicate = col;
					
				};
				
				this.setReverse = function(r) {
					$scope.reverse = !!r;
				};
				
				this.getReverse = function() {
					return $scope.reverse;
				};

			},
            link: function (scope, element, attributes) {
                
				element.children().each(function(k, o){
					if(scope.predicate) {
						return;
					}
					scope.predicate = angular.element(o).attr('my-sort-by');
				});
				
				scope.$watchCollection('[predicate,reverse]', function(newValues, oldValues, scope){

					element.children().each(function(k, o){
						if(o.hasAttribute('my-sort-by')) {
							angular.element(o).removeClass('sorting sorting_asc sorting_desc');
							var col = o.getAttribute('my-sort-by');
							if (!scope.sorting(col)) {
								o.className = o.className + ' sorting';
							}
							else if(scope.sorting(col, false)){
								o.className = o.className + ' sorting_asc';
							}
							else if(scope.sorting(col, true)){
								o.className = o.className + ' sorting_desc';
							}
						}
					});
				});
            }
        }
    });
});
