angular.module('popover', []).directive('popover', function($templateCache, $http, $q, $compile) {
    function link(scope, element, attrs) {
        var body = angular.element('body');
        
        $http.get(attrs.templateUrl).success(function(data, status){
            var contentTemplate = data;
            var top = 30 + (Chat.ui.getConvListIndex(attrs.convId) - 1 ) * 70;;
            var template = angular.element(
                '<div id="' + element.attr('id') + '-popover" class="popover right animation-fade" style="top: ' + top  +'px; left: 380px;">' +
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
        
        scope.submit = function(value) {
            scope.onSubmit({arg1 :  value});
            this.value = '';
            scope.hide();
        };
            
        scope.hide = function(){
            var popoverEl = angular.element('#' + element.attr('id') + '-popover');
            popoverEl.fadeOut();
        };
            
        element.on('click', function(event) {
            var popoverEl = angular.element('#' + element.attr('id') + '-popover');
            popoverEl.fadeIn();   
        });
    }
         
    return {
        scope : {
            "onSubmit" : "&onSubmit"
        },
        link : link
    };
});
	


