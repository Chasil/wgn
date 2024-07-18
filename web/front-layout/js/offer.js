var map;
var marker;
var url = window.location.href ;

function initialize() {

    if(typeof offerLat =='undefined' || typeof offerLat =='offerLng'){
        return;
    }
    var mapProp = {
        center: new google.maps.LatLng(offerLat,offerLng),
        zoom:12,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementsByClassName("map-container")[0],mapProp);

    var latLng = new google.maps.LatLng(offerLat,offerLng);

    marker = new google.maps.Marker({
        position: latLng,
        map: map,
        draggable:true,
    });

    map.setCenter(latLng);
}
//google.maps.event.addDomListener(window, 'load', initialize);


$(function(){
    check();
    $('.print-offer').on('click',function(e){
        e.preventDefault();
        popitup(printUrl, 600,850,offerName);
    });
    $('.gallery').offerGallery();
    $('.contact-form textarea').lettersCounter();
    $('.contact-form form').validate({sendValid:false});
    $('.gallery').magnificPopup({
      delegate: 'a.gallery-img',
      type: 'image',
      tLoading: 'Ładuję #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function(item) {
          return item.el.attr('title');
        }
      }
    });
    $('.map-container a').magnificPopup({
      type: 'iframe'
      // other options
    });
    $('.main-img').on('click',function(e){
        e.preventDefault();
        var item = $(this).attr('data-gallery-item');
        $('.gallery-links a[data-gallery-item="'+item+'"]').trigger('click');
    });
});
$('select[name="currency"]').on('change',function(){
    var val = $(this).val();
    var currency = $.map(currencies, function(curr) {
        return curr.id == val ? curr : null;
    });
    var price = $(this).attr('data-price');
    var squere = $(this).attr('data-squere');

    price = parseFloat(price);
    squere = parseFloat(squere);

    var newPrice = price / parseFloat(currency[0].exchangeRate);
    var pricem2 = newPrice / squere;

    newPrice = $.number(newPrice,0);
    pricem2 = $.number(pricem2,0);

    $('#price-exchange').html(newPrice);
    $('#pricem2-exchange').html(pricem2);
});
$('#mobile-contact a.phone').on('click',function(){
    ga('send', 'event', 'telefon', 'zadzwon', 'oferta', 1);
});
$('.contact-form form').on('form:valid',function(e){
    var self = this;
    var formdata = $(self).serialize();
    ga('send', 'event', 'formularz', 'wyslij', 'oferta', 1);
    $.ajax({
        type: "POST",
        url: sendMessageUrl,
        data: formdata,
        cache: false,
        beforeSend:function(){
            $('.contact-form').find('.btn-success').hide();
            $('.contact-form').find('.btn-success').closest('div.col-xs-12').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
        }
    }).done(function(response){
            if(response.success){
                $('.contact-form').find('[data-form-container]').html('<h5 class="bg-green"><i class="fa fa-check-circle fa-2x"></i> Formularz został wysłany. Dziękujemy.</h5>');
            }else {
                alert('Nie udało się wysłać formularza');
                $('.contact-form').find('.btn-success').show();
            }
        });
});
$('a[data-action="addToObserved"]').on('click',function(e){
    e.preventDefault();
    var self = this;
    $.ajax({
        type: "POST",
        url: addToObservedUrl,
        data: {id:idOffer},
        cache: false,
        beforeSend:function(){
            $(self).addClass('hidden');
            $(self).closest('div').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
        }
    }).done(function(response){
            $(self).closest('div').find('i.fa-spin').remove();
            $('a[data-action="removeFormObserved"]').removeClass('hidden');
            $('.observed-cart').removeClass('hidden');
            var currCount = $('.observed-cart span').html();
            var count = parseInt(currCount) + 1;

            $('.observed-cart span').html(count);
        });
});

$('a[data-action="removeFormObserved"]').on('click',function(e){
    e.preventDefault();
    var self = this;
    $.ajax({
        type: "POST",
        url: removeFormObservedUrl,
        data: {id:idOffer},
        cache: false,
        beforeSend:function(){
            $(self).addClass('hidden');
            $(self).closest('div').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
        }
    }).done(function(response){
            $(self).closest('div').find('i.fa-spin').remove();
            $('a[data-action="addToObserved"]').removeClass('hidden');
            var currCount = $('.observed-cart span').html();
            var count = parseInt(currCount) - 1;
            if(count == 0){
                $('.observed-cart').addClass('hidden');
            }
            $('.observed-cart span').html(count);
        });
});