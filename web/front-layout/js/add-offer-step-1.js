$('.panel a[data-type]').on('click',function(e){
    e.preventDefault();
    $('.panel li.selected').removeClass('selected');
    $(this).closest('li').addClass('selected');
    var transaction = $('input[name="transactionType"]:checked').val();
    var url = $(this).attr('href');
    window.location.href = url + '&transaction=' + transaction;
});
$('input[name="transactionType"]').on('change',function(e){

});
$('button.next').on('click',function(e){
    e.preventDefault();
    var type = $('.panel li.selected a').attr('data-type');
    var transaction = $('input[name="transactionType"]:checked').val();

    if(!type){
        alert('wybierz typ nieruchomości');
        return;
    }
    var url = $(this).attr('data-href');
    window.location.href = url + '?transaction=' + transaction + '&type=' + type;
});
$('.row.accordion').on('show.bs.collapse', function(e) {
    $(e.target).closest('.panel').find('a[role="button"]').addClass('active');
}).on('hide.bs.collapse', function(e) {
    $(e.target).closest('.panel').find('a[role="button"]').removeClass('active');
});

