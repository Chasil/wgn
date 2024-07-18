$('select[data-action="changeOrder"]').on('change',function(){
    $(this).closest('form').submit();
});
 $('a[data-action="edit"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#table-form').attr('action',editUrl);
     $('#table-form').submit();
});
 $('a[data-action="delete"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#modal-confirm-delete').modal();
});
 $('#modal-confirm-delete a.btn-danger').on('click',function(e){
     e.preventDefault();
     $('#table-form').attr('action',deleteUrl);
     $('#table-form').submit();
});
 $('a[data-action="promo"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#table-form').attr('action',promoUrl);
     $('#table-form').submit();
});
 $('a[data-action="renew"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#table-form').attr('action',renewUrl);
     $('#table-form').submit();
});
 $('a[data-action="activate"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#table-form').attr('action',activateUrl);
     $('#table-form').submit();
});
 $('a[data-action="removeFromObserved"]').on('click',function(e){
     e.preventDefault();
     if($('#table-form input[name="offer[]"]:checked').length===0){
         $('#modal-no-select').modal();
         return false;
     }
     $('#modal-confirm-delete').modal();

});
$('a[data-action="getSubscriptionInfo"]').on('click',function(e){
    e.preventDefault();
    $('#modal-subscription').find('.modal-body').html('');
    $.ajax({
        method:'POST',
        url:subscriptionDetailsUrl,
        beforeSend:function(){
            $('#modal-subscription').find('.modal-body').html('<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        },
        data:{id:$(this).attr('data-id')}
    }).done(function(data){
        $('#modal-subscription').find('.modal-body').html(data);
    });
    $('#modal-subscription').modal();
});
$('a[data-action="adminContact"]').on('click',function(e){
    e.preventDefault();
    $('#modal-admin-contact').modal();
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
$('#start-contact-form form').validate({sendValid:false});
$('#start-contact-form form').on('form:valid',function(e){
var self = this;
var formdata = $(self).serialize();
$.ajax({
    type: "POST",
    url: messageUrl,
    data: formdata,
    cache: false,
    beforeSend:function(){
        $('#start-contact-form').find('.btn-success').hide();
        $('#start-contact-form').find('.btn-success').closest('div.col-xs-12').append('<i class="fa fa-spinner fa-spin fa-2x"></i>');
    }
}).done(function(response){
        if(response.success){
            $('#start-contact-form').html('<h5 class="bg-green"><i class="fa fa-check-circle fa-2x"></i> Formularz został wysłany. Dziękujemy.</h5>');
        }else {
            alert('Nie udało się wysłać formularza');
            $('#start-contact-form').find('.btn-success').show();
        }
    });
});
$('select[data-action="changeOrder"]').on('change',function(){
    $(this).closest('form').submit();
});
$('.messages tr').on('click',function(e){
    e.preventDefault();
    $('#modal-message-details').find('.modal-body').html('');
    $.ajax({
        method:'POST',
        url:messageDetailsUrl,
        beforeSend:function(){
            $('#modal-message-details').find('.modal-body').html('<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        },
        data:{id:$(this).attr('data-id')}
    }).done(function(data){
        $('#modal-message-details').find('.modal-body').html(data);
    });
    $('#modal-message-details').modal();
});
$('.payments a[data-payment-id]').on('click',function(e){
    e.preventDefault();
    $('#modal-payment-details').find('.modal-body').html('');
    $.ajax({
        method:'POST',
        url:paymentDetailsUrl,
        beforeSend:function(){
            $('#modal-payment-details').find('.modal-body').html('<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        },
        data:{id:$(this).attr('data-payment-id')}
    }).done(function(data){
        $('#modal-payment-details').find('.modal-body').html(data);
    });
    $('#modal-payment-details').modal();
});

$('body').on('click','.avatar-form .upload-img',function(e){
    e.preventDefault();
    $('#avatar_form_file').trigger('click');
});
$('.avatar-form a[data-action="deleteAvatar"]').on('click',function(e){
    e.preventDefault();
  $.ajax({
        url: avatarDeleteUrl,
        type: "POST",
        processData: false,
        enctype: 'multipart/form-data',
        beforeSend:function(xhr, opts){
            $('.avatar-img-container .loader').removeClass('hidden');
        },
        contentType: false,
        success: function (res) {
            $('.avatar-img-container img.upload-img').remove();
            $('.avatar-img-container').prepend('<div class="no-img upload-img"></div>');
            $('.avatar-img-container .loader').addClass('hidden');


        }
    });
});
$('#avatar_form_file').on('change',function(){
    var i = 0, len = $(this)[0].files.length, file;
    var currentLength = $('.img.loaded,.img.loading').length;

    for ( ; i < len; i++ ) {
        file = $(this)[0].files[i];

        var formdata = new FormData();
        if (!!file.type.match(/image.*/)) {
            if (formdata) {
                formdata.append("file", file);

                $.ajax({
                    url: avatarAddUrl,
                    type: "POST",
                    data: formdata,
                    processData: false,
                    enctype: 'multipart/form-data',
                    beforeSend:function(xhr, opts){
                        $('.avatar-img-container .loader').removeClass('hidden');

                    },
                    contentType: false,
                    success: function (res) {
                        var img = new Image();
                        img.src = res.imageUrl;
                        img.onload = function(){
                            $('.avatar-img-container div.no-img').remove();
                            $('.avatar-img-container img.upload-img').remove();
                            $('.avatar-img-container').prepend(img);
                            $('.avatar-img-container img').addClass('img-responsive upload-img');

                              $('.avatar-img-container .loader').addClass('hidden');
                        };


                    }
                });
            }
        }
    }
    $(this)[0].files = null;
});