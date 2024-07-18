(function ( $ ) {
    var settings;
    var self;
    $.fn.lettersCounter = function(options) {
        self = this;
        settings = $.extend({
            max:1000,
            placeholder:"#message-letters-counter"
        }, options );

        $(settings.placeholder).html(calculateLeft());
        $(this).on('input paste cut',function(){
            if(calculateLeft()>=0){
                $(settings.placeholder).html(calculateLeft());
            }else {
                var currVal = $(self).val();
                $(self).val(currVal.substring(0,settings.max));
            }

        });
    }
    function calculateLeft(){
        var length = $(self).val().length;
        return settings.max - length;
    }
}( jQuery ));


