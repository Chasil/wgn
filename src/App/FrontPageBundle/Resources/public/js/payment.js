function calculateSmsPrice(price){
    if(price===3.69){
        return 3.69;
    }else if(price===19.68){
        return 23.37;
    }

    return 23.37;
}
function calculatePrices(){
    var total = String(price).replace('.',',');
    var totalSms = String(calculateSmsPrice(price)).replace('.',',');
    var totalSmsNetto = String(calculateSmsPrice(price)/1.23).replace('.',',');
    var smsNumber = '73624';

    if($('#payment-form input[name="payment[promo]"]:checked').length>0){
        total = String(price+promoPrice).replace('.',',');
        totalSms = String(calculateSmsPrice(price+promoPrice)).replace('.',',');
        totalSmsNetto = String(calculateSmsPrice(price+promoPrice)/1.23).replace('.',',');
        smsNumber = '91974';
    }
    $('.payment-methods .payment.card .total span').html(total);
    $('.payment-methods .payment.sms .total span').html(totalSms);
    $('#sms-payment-info .total').html(totalSms);
    $('#sms-payment-info .total-netto').html(totalSmsNetto);
    $('#sms-number').html(smsNumber);
}
function showPaymentInfo(){
    if($('#payment-form .payment.selected').hasClass('sms')){

        $('#card-payment-info').slideUp();
        $('#sms-payment-info input').attr('data-validator','required');
        $('#card-payment-info input, #card-payment-info select').removeAttr('data-validator');
        $('#sms-payment-info').slideDown();
    }else {
        $('#sms-payment-info').slideUp();
        $('#sms-payment-info input').removeAttr('data-validator');
        $('#card-payment-info input[name!="payment[phone]"], #card-payment-info select').attr('data-validator','required');
        $('#card-payment-info input[name="payment[email]"]').attr('data-validator','required,email');
        $('#card-payment-info').slideDown();
    }
}
$('#payment-form').validate();
$('#payment-form :input').on('change',function(){
    calculatePrices();
});
$('#payment-form .payment :input').on('change',function(){
    $('#payment-form .payment.selected').removeClass('selected');
    $(this).closest('.payment').addClass('selected');
    showPaymentInfo();
});
$(function(){
    calculatePrices();
    showPaymentInfo();
});

/* Add attributes to company fields */
$(document).ready(function(){
    $('#payment_legalPersonality_0').removeAttr('data-validator');
    $('#payment_legalPersonality_1').removeAttr('data-validator');
    $('#payment_VAT_0').removeAttr('data-validator');
    $('#payment_VAT_1').removeAttr('data-validator');
    $('#payment_name').removeAttr('data-validator');
    $('#payment_NIP').removeAttr('data-validator');
});

$('#payment_legalPersonality_0').click(function(){
    $('#payment_name').removeAttr('data-validator');
    $('#payment_NIP').removeAttr('data-validator');
    $('#payment_legalPersonality_1').removeAttr('checked');
    $('#payment_legalPersonality_0').attr('checked', 'checked');
    $('#card-payment-info-company').css('display', 'none');
});
$('#payment_legalPersonality_1').click(function(){
    $('#payment_name').attr('data-validator','required');
    $('#payment_NIP').attr('data-validator','required');
    $('#payment_legalPersonality_0').removeAttr('checked');
    $('#payment_legalPersonality_1').attr('checked', 'checked');
    $('#card-payment-info-company').css('display', 'block');
});
$('#payment_VAT_0').click(function() {
    $('#payment_VAT_0').attr('checked', 'checked');
    $('#payment_VAT_1').removeAttr('checked');
});
$('#payment_VAT_1').click(function() {
    $('#payment_VAT_1').attr('checked', 'checked');
    $('#payment_VAT_0').removeAttr('checked');
});
