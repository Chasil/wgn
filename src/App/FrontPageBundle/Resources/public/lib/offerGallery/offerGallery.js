(function ( $ ) {

    $.fn.offerGallery = function() {
        var self = this;
        var itemWidth = 0;
        var visibleItems = 5;

        var totalItems = (self).find('.thumbs ul li').length;
        setWidth();
        $(this).on('click','.thumbs a',function(e){
            e.preventDefault();
            if($(this).hasClass('prev') || $(this).hasClass('next')){
                return;
            }
            markAsMain($(this));

        });
        $(this).on('click','.thumbs a.prev',function(e){
            e.preventDefault();
            moveLeft();

        });
        $(this).on('click','.thumbs a.next',function(e){
            e.preventDefault();
            moveRight();

        });
        $(this).on('click','.main-photo a.prev',function(e){
            e.preventDefault();
            var current = $(self).find('.thumbs li.arrow-box').first();
            if(current.length > 0 && current.prev().length > 0){
               markAsMain(current.prev());
               moveLeft();
            }else {
               markAsMain($(self).find('.thumbs li').first());
            }


        });
        $(this).on('click','.main-photo a.next',function(e){
            e.preventDefault();
            var current = $(self).find('.thumbs li.arrow-box').first();
            if(current.length > 0 && current.next().length > 0){
               markAsMain(current.next());
               moveRight();
            }else if(current.length > 0 && current.next().length === 0){
                markAsMain($(self).find('.thumbs li').last());
                moveRight();
            }else {
               markAsMain($(self).find('.thumbs li').first());
            }

        });
        function setWidth(){
            var containerWidth = $(self).find('.thumbs').width();
            itemWidth = parseInt(containerWidth / visibleItems);
            var width = 0;

            $(self).find('.thumbs ul li').each(function(){
                $(this).width(itemWidth - 6);
                width += itemWidth;
            });
            $(self).find('ul').width(width);
        }
        function moveLeft(){
            var left = parseInt($(self).find('.thumbs ul').css('left'));
            left = (left) ? left : 0;
            if(left>=0){
                return;
            }
            $(self).find('.thumbs ul').css('left',left + itemWidth);
        }
        function moveRight(){
            var left = parseInt($(self).find('.thumbs ul').css('left'));
            var maxLeft = (totalItems - visibleItems) * itemWidth;
            left = (left) ? left : 0;
            if(Math.abs(left)>=maxLeft){
                return;
            }
            $(self).find('.thumbs ul').css('left',left - itemWidth);
        }
        function markAsMain(element){
            $(self).find('li.arrow-box').removeClass('arrow-box');
            element.closest('li').addClass('arrow-box');
            var src = element.find('img').attr('data-preview-src');
            $(self).find('.main-photo img').attr('src',src);
            $(self).find('.main-photo .main-img').attr('data-gallery-item',element.attr('data-gallery-item'));
        }
    }
}( jQuery ));


