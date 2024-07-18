$('.category-offers-description form').validate();
$('.category-offers-description form').on('form:invalid',function(e){
    var element = $('.category-offers-description form .form-group.has-error').first();
    $('html, body').animate({
        scrollTop: element.offset().top
    }, 1000);
});
function initialize() {
}

$('.box.box-primary .gallery .img').on('click',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loaded')){
        $('#images').trigger('click');
    }
});

$('#images').on('change',function(){
    var i = 0, len = $(this)[0].files.length, file, maxFiles = 12;
    var currentLength = $('.img.loaded,.img.loading').length;

    if(currentLength+len > maxFiles){
        len = maxFiles - currentLength;
        alert('Masksymalnie możesz dodać 12 zdięć!');
    }
    for ( ; i < len; i++ ) {
        file = $(this)[0].files[i];
        var formdata = new FormData();
        if (!!file.type.match(/image.*/)) {
            if (formdata) {
                var url = imageAddUrl;
                var currUrl = url + '?id='+idOffer;
                formdata.append("images[]", file);
                $.ajax({
                    url: currUrl,
                    type: "POST",
                    data: formdata,
                    processData: false,
                    beforeSend:function(xhr, opts){
                        var placeholder = $('.row.gallery').find('.img').not('.loading,.loaded').first();
                        if(placeholder.length>0){
                            placeholder.addClass('loading').html('<div class="gallery-loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                        }else{
                            xhr.abort();
                        }
                    },
                    contentType: false,
                    success: function (res) {

                        var img = new Image();
                        img.src = res.imageUrl;
                        img.onload = function(){
                            var element = $('.row.gallery').find('.img.loading').first();
                            element.append('<a href="#" class="remove-image"></a>');
                            element.append(img);
                            element.removeClass('loading')
                                .addClass('ui-sortable-handle loaded')
                                .attr('data-id',res.id);
                            element.find('.gallery-loader').remove();
                        };
                    }
                });
            }
        }
    }
    $(this)[0].files = null;
});
$( ".row.gallery" ).on('click',".remove-image",function(){
    var placeholder = $(this).closest('.img');
    var id = placeholder.attr('data-id');
    $.ajax({
        url: imageRemoveUrl,
        type: "POST",
        beforeSend:function(){
            placeholder.append('<div class="gallery-loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        },
        data: {id:id},
        success: function(res) {
            placeholder.closest('.images-list').append('<div class="img"></div>');
            placeholder.remove();
        }});
    return false;
});
$(".images-list").on( "sortstop", function( event, ui ) {
    var id = idOffer;
    var items = [];
    $( ".images-list .img.loaded" ).each(function( index ) {
        items[index] = $( this ).attr('data-id');
    });

    $.ajax({
        url: imageSortUrl,
        type: "POST",
        data: {idOffer:id,ids:items},
        success: function (res) {

        }});
} );
$(function() {
    $( ".images-list" ).sortable({
        items: ".img.loaded"
    });
    $( ".images-list" ).disableSelection();
});