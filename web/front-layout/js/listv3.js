$('#mark').on('click', function (e) {
    e.preventDefault();
    $('.my-item-list input[name="mark"]').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('.my-item-list').addClass('hidden');
        }
    });
});
$('[data-action="changeOrder"]').on('change', function (e) {
    e.preventDefault();
    var currUrl = window.location.href;
    var redirectTo = '';
    if(currUrl.contains('?')){
        redirectTo = currUrl + '&order=' + $(this).val();
    }else {
        redirectTo = currUrl + '?order=' + $(this).val();
    }

    window.location.href = redirectTo;
});
$('#unmark').on('click', function (e) {
    e.preventDefault();
    $('.my-item-list').removeClass('hidden');
    $('.my-item-list input[name="mark"]').removeAttr('checked');
});
$('#print').on('click', function (e) {
    e.preventDefault();
    var counter = 0;
    $('.my-item-list input[name="mark"]').each(function () {
        if ($(this).is(':checked')) {
            var offerName = $(this).closest('.my-item-list').find('h1 a').html() + ' ' + counter;

            var url = $(this).closest('.my-item-list').find('a.offer-image').attr('href');
            popitup(url + '?print=1', 600,850,offerName);
            counter++;
        }
    });
});
$('a[data-action="adminContact"]').on('click',function(e){
    e.preventDefault();
    $('#modal-admin-contact').modal();
});
$('#load-more').on('click',function(e){
    e.preventDefault();

    if($('#load-more').is(':visible')==false){
        return;
    }

    var page = parseInt($(this).attr('data-page'));
    var position = $(this).offset().top;
    $(this).attr('data-page',page+1);
    var url = '';
    if(pageUrl.contains('?')){
        url = pageUrl + '&page='+page;
    }else {
        url = pageUrl + '?page='+page;
    }


    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        beforeSend:function(){
            $('#load-more').hide();
            $('.view-options.bottom').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
        }
    }).done(function(response){
            $('.items-box').append(response);

            $('.view-options.bottom').appendTo('.items-box');
            if(parseInt($('#load-more').attr('data-page')) <= parseInt($('#load-more').attr('data-pages'))){
                $('#load-more').show();
            }

            $('.view-options.bottom').find('.fa-spin').remove();

        });
});
$( window ).scroll(function() {
  var scroll = $(window).scrollTop();
  var offset = ($('#load-more').offset().top - ( $(window).height() * 1.2)) - scroll;

  if(offset <= 0){
      $('#load-more').trigger('click');
  }
});
$('.results-newsletter form').validate({sendValid:false});
$('.results-newsletter form').on('form:valid',function(e){
var self = this;
var email = $('.results-newsletter input[name="email"]').val();
var conditions = $('#search-box-form').serialize();
$.ajax({
    type: "POST",
    url: resultsNotificationUrl,
    data: {conditions:conditions,email:email},
    cache: false,
    beforeSend:function(){
        $('#results-notification').find('.btn-success').hide();
        $('#results-notification').find('.btn-success').closest('.form-group').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
    }
}).done(function(response){
        if(response.success){
            $('#results-notification').html('<h5 class="bg-green"><i class="fa fa-check-circle fa-2x"></i> Zapisano. Dziękujemy.</h5>');
        }else {
            alert('Nie udało się wysłać formularza');
            $('#results-notification').find('.btn-success').show();
        }
    });
});
$('#modal-admin-contact form').validate({sendValid:false});
$('#modal-admin-contact form').on('form:valid',function(e){
var self = this;
var formdata = $(self).serialize();
$.ajax({
    type: "POST",
    url: messageUrl,
    data: formdata,
    cache: false,
    beforeSend:function(){
        $('#contact-form').find('.btn-success').hide();
        $('#contact-form').find('.btn-success').closest('div.col-xs-12').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
    }
}).done(function(response){
        if(response.success){
            $('#contact-form').html('<h5 class="bg-green"><i class="fa fa-check-circle fa-2x"></i> Formularz został wysłany. Dziękujemy.</h5>');
        }else {
            alert('Nie udało się wysłać formularza');
            $('#contact-form').find('.btn-success').show();
        }
    });
});

