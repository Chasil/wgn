var map;
var geocoder;
var marker;
$('.new-offer form').validate();
$('.new-offer form').on('form:invalid',function(e){
    var element = $('.new-offer form .form-group.has-error').first();
    $('html, body').animate({
        scrollTop: element.offset().top
    }, 1000);
});
function initialize() {
    var mapProp = {
        center:new google.maps.LatLng(51.1078852,17.03853760000004),
        zoom:12,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById("offer-on-map"),mapProp);

    var latLng = new google.maps.LatLng('51.1078852','17.03853760000004');

    marker = new google.maps.Marker({
        position: latLng,
        map: map,
        draggable:true,
    });

    map.setCenter(latLng);
    google.maps.event.addListener(marker, 'dragend', function(event) {
        $('#offer_lat').val(event.latLng.lat());
        $('#offer_lng').val(event.latLng.lng());
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

$('#offer_city, #offer_street').on('input',function(){
    geolocalize();
});
$('#offer_city, #offer_street').on('change',function(){
    geolocalize();
});
$( "#offer_city, #offer_street" ).on( "focusout", function() {
   $(this).closest('.sgg').html('');
});
$( "#offer_city, #offer_street" ).on( "focusin", function() {
   $('.sgg').html('');
});
function geolocalize(){
        var country = $('#offer_country').val();
        var city = $('#offer_city').val();
        var region = $('#offer_region').val();
        var street = $('#offer_street').val();

        var address = country + ' ' + region + ' ' + city + street;
        geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            var location = results[0].geometry.location;
            map.setCenter(location);
            marker.setPosition(location);
            $('#offer_lat').val(location.lat());
            $('#offer_lng').val(location.lng());
          }
        });

        return false;
}
$('#offer_properties_squere, #offer_properties_price').on('input',function(){
    var squere = parseFloat($('#offer_properties_squere').val().replace(' ',''));
    var price = parseFloat($('#offer_properties_price').val().replace(' ',''));
    if(!squere || !price){
        return;
    }

    var priceM2 = price/squere;
    priceM2 = priceM2.toFixed(2).replace('.',',');
    $('#offer_properties_pricem2').val(priceM2);
});
$('.offer-form .gallery .img').on('click',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loaded')){
        $('#images').trigger('click');
    }
});
$('.next.preview').on('click',function(e){

    if($('.img.loading').length >0){
       e.preventDefault();
       return;
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
        success: function (res) {
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
    $('#offer_properties_squere,#offer_properties_price,#offer_typeProperties_monthPayments').number( true, 0, ',', ' ' );
    $('#offer_properties_pricem2').number( true, 2, ',', ' ' );
});

function searchCity() {
     $.ajax({
        method: "GET",
        url: suggestionsCityUrl +"?q="+$('#offer_city').val()
      }).done(function(data){
          var content = '';
          if(data.results.length>0){
              content = '';
          }
          for(var i=0;i<data.results.length;i++){
              content += '<div class="search-result-row" data-name="'+data.results[i].name+'">'+data.results[i].name+'</div>';
          }
         $('#c-suggestions').css('display','block');
         $('#c-suggestions').html(content);

      });
};
function searchStreet() {
     $.ajax({
        method: "GET",
        url: suggestionsStreetUrl +"?q="+$('#offer_street').val()
      }).done(function(data){
          var content = '';
          if(data.results.length>0){
              content = '';
          }
          for(var i=0;i<data.results.length;i++){
              content += '<div class="search-result-row" data-name="'+data.results[i].name+'">'+data.results[i].name+'</div>';
          }
         $('#s-suggestions').css('display','block');
         $('#s-suggestions').html(content);

      });
};

$( "#offer_city" ).on( "keyup", function() {
   if($(this).val().length > 1){
     searchCity();
   }
});
$('#c-suggestions').on('click','.search-result-row',function(){
    $('#offer_city').val($(this).data('name'));
    $('#c-suggestions').html('');
});
$( "#offer_street" ).on( "keyup", function() {
   if($(this).val().length > 1){
     searchStreet();
   }
});
$('#s-suggestions').on('click','.search-result-row',function(){
    $('#offer_street').val($(this).data('name'));
    $('#s-suggestions').html('');
});
$('#offer_country').on('change',function(){
    if($(this).val()=='1'){
        $('#form-part-province').removeClass('hidden');
        $('#form-part-district').removeClass('hidden');
        var input = '<select id="offer_region" name="offer[region]" required="required" data-validator="required" autocomplete="off" class="form-control">'
                   +'<option value="dolnośląskie">dolnośląskie</option>            <option value="kujawsko-pomorskie">kujawsko-pomorskie</option>            <option value="lubelskie">lubelskie</option>            <option value="lubuskie">lubuskie</option>            <option value="łódzkie">łódzkie</option>            <option value="małopolskie">małopolskie</option>            <option value="mazowieckie">mazowieckie</option>            <option value="opolskie">opolskie</option>            <option value="podkarpackie">podkarpackie</option>            <option value="podlaskie">podlaskie</option>            <option value="pomorskie">pomorskie</option>            <option value="śląskie">śląskie</option>            <option value="świętokrzyskie">świętokrzyskie</option>            <option value="warmińsko-mazurskie">warmińsko-mazurskie</option>            <option value="wielkopolskie">wielkopolskie</option>            <option value="zachodniopomorskie">zachodniopomorskie</option></select>'
                   +'</select>';
        $('#form-part-province .form-group').append(input);
        $('#form-part-region').addClass('hidden');
        $('#form-part-region input').remove();
        $('#form-part-city').removeClass('col-md-6').addClass('col-md-4');
    }else {
        $('#form-part-province').addClass('hidden');
        $('#form-part-district').addClass('hidden');
        $('#form-part-region').removeClass('hidden');
        $('#form-part-province .form-group select').remove();
        $('#form-part-region input').remove();
        var input = '<input type="text" id="offer_region" name="offer[region]" required="required" autocomplete="off" class="form-control" data-validator="required">';
        $('#form-part-region .form-group').append(input);
        $('#form-part-city').removeClass('col-md-4').addClass('col-md-6');
    }
});