angular.module('popover', []).directive('popover', function($templateCache, $http, $q, $compile) {
    return function(scope, element, attrs) {
        $http.get(attrs.templateUrl).success(function(data, status){
            var contentTemplate = data;
            
            var template = angular.element(
                '<div id="popover" class="popover right animation-fade" style="top: ' + attrs.top  +'px; left: 300px;">' +
                    '<div class="arrow"></div>' +
                    '<h3 class="popover-title">' + attrs.title + '<span ng-click="hide()" style="float:right"> Close</span></h3>' +
                    '<div class="popover-content">' +
                        contentTemplate + 
                    '</div>' +
                '</div>');
        
            var compiled = $compile(template);
            
            element.after(template);
    
            compiled(scope);
        });
            
        scope.submit = function() {
            scope.newConv(this.value);
            this.value = '';
            scope.hide();
        };
            
        scope.hide = function(){
            element.next().fadeOut();
        };
            
        scope.show = function(){
            element.next().fadeIn();
        }

    };
});
	


