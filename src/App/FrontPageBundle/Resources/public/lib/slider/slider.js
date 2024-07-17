(function ( $ ) {
    $.fn.slider = function(options) {
        var settings = $.extend({

        }, options );
        var self = this;
        $(self).each(function(){
            var slideCount = $(this).find('.b-slides .b-box').length;
            $(this).find('.b-slides .b-box').not(':first').css('display','none');
            var height = $(this).find('.b-slides .b-box').height();
            var self = this;
            if(slideCount>1){
                setInterval(function(){
                    $(self).find('.b-box:first-child').appendTo($(self).find('.b-slides')).fadeOut(2000);
                    $(self).find('.b-box:first-child').fadeIn(2000);
                }, 3000);
            }
        });
    };
}( jQuery ));