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

