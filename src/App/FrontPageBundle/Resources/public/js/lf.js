$('#search-box-form').validate({sendValid:false});
$('#search-box-form').on('form:valid',function(e){
var self = this;
var email = $('#search-box-form input[name="email"]').val();
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
            $('#search-box').html('<h5 class="message bg-green"><i class="fa fa-check-circle fa-2x"></i> Zapisano. Dziękujemy.</h5>');
        }else {
            alert('Nie udało się wysłać formularza');
            $('#search-box').find('.btn-success').show();
        }
    });
});


