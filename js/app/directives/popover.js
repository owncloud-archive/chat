angular.module('popover', []).directive('popover', function($templateCache, $http, $q, $compile) {
    return function(scope, element, attrs) {
        console.log('called');
        var body = angular.element('body');
        
        $http.get(attrs.templateUrl).success(function(data, status){
            console.log(attrs.templateUrl + data);
            var contentTemplate = data;
            
            var template = angular.element(
                '<div id="' + element.attr('id') + '-popover" class="popover right animation-fade" style="top: ' + attrs.top  +'px; left: 380px;">' +
                    '<div class="arrow"></div>' +
                    '<h3 class="popover-title">' + attrs.title + '<span ng-click="hide()" style="float:right"> Close</span></h3>' +
                    '<div class="popover-content">' +
                        contentTemplate + 
                    '</div>' +
                '</div>');
        
            var compiled = $compile(template);
            
            
            body.after(template);
    
            compiled(scope);
        });
        
        console.log(attrs);
            
        scope.submit = function(value) {
            scope[attrs.onSubmit](value)
            this.value = '';
            scope.hide();
        };
            
        scope.hide = function(){
            var popoverEl = angular.element('#' + element.attr('id') + '-popover');
            console.log(popoverEl);
            popoverEl.fadeOut();
        };
            
        scope.show = function(){
            var popoverEl = angular.element('#' + element.attr('id') + '-popover');
            popoverEl.fadeIn();
        }

    };
});
	


