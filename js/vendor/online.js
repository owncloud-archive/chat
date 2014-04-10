(function ($) {
    $.fn.online = function(isonline) {
        var $div = this;
       
        console.log(isonline);
        if(isonline === 'true' || isonline === true){
            $div.css('border', '5px solid red');
        } else {
            $div.css('border', 'none');
        }
    };
}(jQuery));
