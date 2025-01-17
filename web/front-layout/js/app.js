var typeOptions = [];

$("#top").click(function(e) {
    e.preventDefault();
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
});
$(function(){
    $('#rating').rating(function(vote, event){

        $.ajax({
            url: voteUrl,
            type: "POST",
            data: {rate: vote},
        }).done(function(){
            Cookies.set('vote', 'yes', { expires: 1 });
            $('#rating .star').hide();
            $('#rating .stars').append('<p>Dziękujemy za Twoją opinię.</p>');
        });
    });
});
$(document).on('click','#cookies-accept',function(){
    Cookies.set('cookiesAccept', 'yes', { expires: 365 });
    $('#cookies-info').fadeOut();
});
$(document).on('click','#search-right-column a',function(e){
    e.preventDefault();
    var data = $('#search-box-form').serialize();
    window.location.href = $(this).attr('href')+'?' + data;
});
$('#advanced-search').on('click',function(e){
    e.preventDefault();
    var self = this;
    if($(self).hasClass('more')){
        $('#search-advanced-options').slideDown('slow',function(){
            $(self).removeClass('more');
            $(self).addClass('less');
            $(self).html('mniej opcji');
        });

    }else {
        $('#search-advanced-options').slideUp('slow',function(){
            $(self).addClass('more');
            $(self).removeClass('less');
            $(self).html('więcej opcji');
        });
    }
});
$('#advanced-search').on('click',function(e){
    e.preventDefault();
    var self = this;
    if($(self).hasClass('more')){
        loadAdvancedForm();
        $('#search-advanced-options').slideDown('slow',function(){
            $(self).removeClass('more');
            $(self).addClass('less');
        });

    }else {
        $('#search-advanced-options').slideUp('slow',function(){
            $(self).addClass('more');
            $(self).removeClass('less');
        });
    }
});
$('#search_category').on('change',function(){
    loadAdvancedForm();
});

$('#search_transactionType, #search_category').on('change',function(){
    loadCountries('#search_country',$('#search_category').val(),$('#search_transactionType').val());
});
$('#search_mobile_transactionType, #search_mobile_category').on('change',function(){
    loadCountries('#search_mobile_country',$('#search_mobile_category').val(),$('#search_mobile_transactionType').val());
});
function loadCountries(placeholder, idCategory,idTransactionType){
    $.ajax({
        method: "GET",
        url: availableCountriesUrl,
        data:{idCategory:idCategory,idTransactionType: idTransactionType},
        beforeSend:function(){
            $(placeholder).closest('div').append('<i class="fa fa-spinner fa-spin"></i>');
            $(placeholder).hide();
        },
    }).done(function(response){
        var options = '';
        for(var i=0;i<response.data.length;i++){
            options += '<option value="'+response.data[i].id+'">'+response.data[i].name+'</option>';
        }
        $(placeholder).closest('div').find('i.fa-spinner').remove();
        $(placeholder).html(options);
        $(placeholder).show();
    });
}
function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
	vars[key] = value;
	});
return vars;
}
function loadAdvancedForm(catId){
     var catId = $('#search_category').val();
     var data = $('#search-box-form').serialize();

     $.ajax({
        method: "GET",
        url: searchAdvancedFormUrl.replace(/&amp;/g, '&'),
        data:data,
        beforeSend:function(){
            $('#search-box .progress').css('display','block');
            $('#search-box .progress-bar').css('width','80%');
        }
      })
      .done(function(data){
        $('#search-box .progress-bar').css('width','100%');
        $('#search-advanced-options-container').html(data);
        if(catId>2){
            $('.type-fields').html('');
            $('#search-advanced-options-container').find('div[data-form-part="type"]').prependTo('.type-fields');
            $('.rooms-fields').addClass('hidden');
            $('.type-fields').removeClass('hidden');
        }else {
            $('.rooms-fields').removeClass('hidden');
            $('.type-fields').addClass('hidden');
        }
         setTimeout(function(){
             $('#search-box .progress').css('display','none');
             $('#search-box .progress-bar').css('width','0%');
         },500);
      });
}
$('.mobile-corp-finder h5').on('click',function(e){
    e.preventDefault();
     $.ajax({
        method: "GET",
        url: officeSearchUrl,
        data:{q:''}
      }).done(function(data){
          var content = '';
          if(data.results.length>0){
              content = '';
          }
          for(var i=0;i<data.results.length;i++){
              var url = officeUrl.replace('rid',data.results[i].id).replace('rname',data.results[i].name);
              content += '<a href="'+url+'" class="search-office-result-row" data-name="'+data.results[i].name+'">'+data.results[i].name+' ' +data.results[i].addresses[0].street+'</a>';
          }
         $('#office-search-results').html(content);

      });

});
$('.dr-wgn-btn').on('click',function(e){
    $('.dropmenu-wgn').find('.navbar-nav').toggleClass('active');
});

function searchLocation() {
     $.ajax({
        method: "GET",
        url: suggestionsUrl +"?q="+$('#search_locationIndexLike').val()
      }).done(function(data){
          var content = '';
          if(data.results.length>0){
              content = '';
          }

        for(var i=0;i<data.results.length;i++){
            content += '<div class="search-result-row" data-name="'+data.results[i].name+'">'+data.results[i].name+'</div>';
        }

         $('#l-suggestions').css('display','block');
         $('#l-suggestions').html(content);

      });
};
function searchLocationMobile() {
     $.ajax({
        method: "GET",
        url: suggestionsUrl +"?q="+$('#search_mobile_locationIndexLike').val()
      }).done(function(data){
            var content = '';
            if(data.results.length>0){
                content = '';
            }

            for(var i=0;i<data.results.length;i++){
                content += '<div class="search-result-row" data-name="'+data.results[i].name+'">'+data.results[i].name+'</div>';
            }
            $('#lm-suggestions').css('display','block');
            $('#lm-suggestions').html(content);

      });
};
$( "#search_locationIndexLike" ).on( "keyup", function() {
   if($(this).val().length > 1){
     searchLocation();
   }
});
$( "#search_locationIndexLike" ).on( "focus", function() {
          var content = '';

            var myStorage = localStorage;
            var locations = localStorage.getItem('lastLocations');

            if(locations){
                locations = JSON.parse(locations);

                content += '<div class="srg">ostatnio wybrane</div>';
                for(var i=0;i<locations.length;i++){
                    content += '<div class="search-result-row" data-name="'+locations[i].query+'">'+locations[i].query+'</div>';
                }
            }
            content += '<div class="srg">wpisz lub wybierz</div>';
            content += '<div class="search-result-row" data-name="Cała Polska">Cała Polska</div>';
            content += '<div class="search-result-row" data-name="dolnośląskie">dolnośląskie</div>';
            content += '<div class="search-result-row" data-name="kujawsko-pomorskie">kujawsko-pomorskie</div>';
            content += '<div class="search-result-row" data-name="lubelskie">lubelskie</div>';
            content += '<div class="search-result-row" data-name="lubuskie">lubuskie</div>';
            content += '<div class="search-result-row" data-name="łódzkie">łódzkie</div>';
            content += '<div class="search-result-row" data-name="małopolskie">małopolskie</div>';
            content += '<div class="search-result-row" data-name="mazowieckie">mazowieckie</div>';
            content += '<div class="search-result-row" data-name="opolskie">opolskie</div>';
            content += '<div class="search-result-row" data-name="podkarpackie">podkarpackie</div>';
            content += '<div class="search-result-row" data-name="podlaskie">podlaskie</div>';
            content += '<div class="search-result-row" data-name="pomorskie">pomorskie</div>';
            content += '<div class="search-result-row" data-name="śląskie">śląskie</div>';
            content += '<div class="search-result-row" data-name="warmińsko-mazurskie">warmińsko-mazurskie</div>';
            content += '<div class="search-result-row" data-name="wielkopolskie">wielkopolskie</div>';
            content += '<div class="search-result-row" data-name="zachodniopomorskie">zachodniopomorskie</div>';

            $('#l-suggestions').css('display','block');
            $('#l-suggestions').html(content);

});
$('.wgn-main-find-btn').on('click',function(){
    var myStorage = localStorage;
    var locations = localStorage.getItem('lastLocations');

    if(!locations){
        locations = [];
    }else {
        locations = JSON.parse(locations);
    }

    var query = $('#search_locationIndexLike').val();

    if(query==''){
        query ='Cała Polska';
    }

    for(var i=0;i<locations.length;i++){
        if(locations[i].query ===query){
            locations.splice(i,1);
            break;
        }
    }
    if(query=='Cała Polska' || query==''){
        locations.unshift({query:'Cała Polska'});
    }else {
        locations.unshift({query:query});
    }

    if(locations.length>4){
        locations.pop();
    }
    myStorage.setItem('lastLocations',JSON.stringify(locations));
    if(query==='Cała Polska'){
        $('#search_locationIndexLike').val('');
    }
});
$( "#search_locationIndexLike" ).on( "focusout", function() {

    if(!$('#l-suggestions .search-result-row:hover').length){
        $('#l-suggestions').css('display','none');
    }

});

$( "#search_mobile_locationIndexLike" ).on("keyup", function() {
   if($(this).val().length > 1){
     searchLocationMobile();
   }
});
    $('#search-mobile-box-form, #search-box-form').on('click','.search-result-row',function(){
        $('#search_mobile_locationIndexLike').val($(this).data('name'));
        $('#search_locationIndexLike').val($(this).data('name'));

        $('#lm-suggestions').css('display','none');
        $('#l-suggestions').css('display','none');
    });
    function autocompleteRange(element,element2, source){
    $(element).closest('.form-group').find('.suggestions').remove();
     var term = $(element).val().replace(/\s/g, '');
     var startVal = parseInt($(element2).val().replace(/\s/g, ''));
     if(isNaN(startVal)){
         startVal = 0;
     }
     var matcher = new RegExp("^"+term, "i" );
     var view = (term.length>0)? false : true;
     var suggestions = [];
     for(var i=0;i < source.length;i++){
         if(!matcher.test(source[i])){
             continue;
         }
         if((startVal==0 || source[i] > startVal)){
             suggestions.push($.number(source[i]));
         }

     }
     var box = '<div class="suggestions" style="display:block">';
     for(var i=0;i < suggestions.length;i++){
         box +='<div class="sr" data-value="'+suggestions[i]+'">'+suggestions[i]+'</div>';
     }
     box +='</div>';
     $(element).closest('.form-group').append(box);
    $('.suggestions .sr').on('click',function(){
        $(this).closest('.form-group').find(':input').val($(this).data('value'));
        $(this).parent().remove();
    });
     $(element).on('focusout',function(){
         if(!$('.suggestions .sr:hover').length){
            $(element).closest('.form-group').find('.suggestions').remove();
         }
    });
}
    $('body').on('focus input','#search_priceDefFrom, #search_priceDefTo', function(event) {
        var range = getPricesRange();
        var prices = calculateRange($(this).val(),range.start,range.end);
        var element2 = ($(this).attr('id')==='search_priceDefFrom') ? '#search_priceDefTo' : '#search_priceDefFrom';
        if($(this).val()===''){
            prices = getDefaultPrices();
        }

        autocompleteRange(this,element2,prices);
    });

    $('body').on('focus input','#search_squereFrom, #search_squereTo', function(event) {
        var range = getSqueresRange();
        var squeres = calculateRange($(this).val(),range.start,range.end);
        var element2 = ($(this).attr('id')==='search_squereFrom') ? '#search_squereTo' : '#search_squereFrom';
        if($(this).val()===''){
            squeres = getDefaultSqueres();
        }
        autocompleteRange(this,element2,squeres);
   });

    $('body').on('focus input','#search_roomsFrom,#search_roomsTo', function(event) {
        var element2 = ($(this).attr('id')==='search_roomsFrom') ? '#search_roomsTo' : '#search_roomsFrom';
        autocompleteRange(this,element2,getRoomsRange());
   });

    $('body').on('focus input','#search_priceDefm2From,#search_priceDefm2To', function(event) {
        var range = getPricesM2Range();
        var prices = calculateRange($(this).val(),range.start,range.end);
        var element2 = ($(this).attr('id')==='search_priceDefm2From') ? '#search_priceDefm2To' : '#search_priceDefm2From';
        if($(this).val()===''){
            prices = getDefaultPricesM2();
        }
        autocompleteRange(this,element2,prices);
   });
    $('body').on('focus input','#search_squerePlotFrom,#search_squerePlotTo', function(event) {

        var range = getPlotSqueresRange();
        var squeres = calculateRange($(this).val(),range.start,range.end);
        var element2 = ($(this).attr('id')==='search_squerePlotFrom') ? '#search_squerePlotTo' : '#search_squerePlotFrom';

        if($(this).val()===''){
            squeres = getDefaultPlotSqueres();
        }

        autocompleteRange(this,element2,squeres);
   });
$(function(){
    loadAdvancedForm();
    $('#fb .header').on('click',function(){
        if($('#fb').css('right')==='-220px'){
            $('#fb').animate({right: 0});
        }else {
            $('#fb').animate({right: '-220px'});
        }
    });

});
String.prototype.contains = function(it) { return this.indexOf(it) != -1; };

function getRoomsRange(){
    return [1,2,3,4,5];
}
function getPricesRange(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();
    var transaction = (mobile)? $('#search_mobile_transactionType').val() : $('#search_transactionType').val();
    var type = (transaction==='1' || transaction==='2')? 1 : 2;

    var range = {start:3,end:7};
    switch(category){
        case '1':
            range = (type===1) ? {start:5,end:8} : {start:4,end:6};
        break;
        case '2':
            range = (type===1) ? {start:6,end:8} : {start:4,end:6};
        break;
        case '3':
            range = (type===1) ? {start:6,end:8} : {start:4,end:6};
        break;
        case '4':
            range = (type===1) ? {start:5,end:8} : {start:4,end:6};
        break;
        case '5':
            range = (type===1) ? {start:5,end:8} : {start:4,end:6};
        break;
        case '6':
            range = (type===1) ? {start:5,end:8} : {start:3,end:5};
        break;
    }
    return range;
}
function getPricesM2Range(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();
    var transaction = (mobile)? $('#search_mobile_transactionType').val() : $('#search_transactionType').val();
    var type = (transaction==='1' || transaction==='2')? 1 : 2;

    var range = {start:3,end:7};
    switch(category){
        case '1':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
        case '2':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
        case '3':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
        case '4':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
        case '5':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
        case '6':
            range = (type===1) ? {start:4,end:6} : {start:2,end:5};
        break;
    }
    return range;
}
function getSqueresRange(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();

    var range = {start:3,end:7};
    switch(category){
        case '1':
        case '2':
        case '5':
        case '6':
            range = {start:2,end:5};
        break;
        case '3':
        case '4':
            range = {start:3,end:6};
        break;
    }
    return range;
}
function getPlotSqueresRange(){
    return {start:3,end:6};
}
function calculate(val,start,end,range){
    var orgVal = val;
    var length =  val.length;

    var endVal = val;
    for(var i=length;i<start;i++){val+='0';}
    for(var i=length;i<end-1;i++){endVal+='0';}

    if(parseInt(val)>parseInt(endVal) || val.length>start) {return;}
    range.push(parseInt(val));
    var fill = start-length;
    var part = '5';
    for(var i=1;i<fill;i++){part+='0';}
    if(parseInt(orgVal+part)>parseInt(endVal)) {return;}
    range.push(parseInt(orgVal+part));
}
function calculateRange(val,start,end){
   	var range = [];
        for(var i = start;i<end;i++){
                calculate(val,i,end,range);
        }
    return range;
}
function getDefaultPricesM2(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();
    var transaction = (mobile)? $('#search_mobile_transactionType').val() : $('#search_transactionType').val();
    var type = (transaction==='1' || transaction==='2')? 1 : 2;
    var prices = [];
    switch(category){
        case '1':
            if(type===1){
                prices = [1000,2000,3000,4000,5000,6000,7000,8000,9000,10000,12000,13000,14000,15000,20000];
            }else {
                prices = [20,50,100,200,500,1000,2000,2500,3000,4000,5000,6000,8000,10000];
            };
        break;
        case '2':
        case '4':
        case '5':
            if(type===1){
                prices = [1000,2000,3000,4000,5000,6000,7000,8000,9000,10000,11000,12000,15000,17000,20000];
            }else {
                prices = [500,1000,1500,2000,2500,3000,3500,4000,5000,6000,7000,8000,9000,10000];
            };
        break;
        case '3':
            if(type===1){
                prices = [10,20,50,100,150,200,250,300,400,500,1000,2000,3000,4000,5000];
            }else {
                prices = [10,20,50,100,150,200,250,300,400,500,1000];
            };
        break;
        case '6':
            if(type===1){
                prices = [500,600,700,800,900,1000,1500,1700,2000,2500,3000,3500,4000,4500,5000];
            }else {
                prices = [10,15,20,30,35,40,50,60,70,80,90,100,150,200];
            };
        break;
    }
    return prices;
}
function getDefaultPrices(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();
    var transaction = (mobile)? $('#search_mobile_transactionType').val() : $('#search_transactionType').val();
    var type = (transaction==='1' || transaction==='2')? 1 : 2;
    var prices = [];
    switch(category){
        case '1':
            if(type===1){
                prices = [50000,100000,150000,200000,250000,300000,350000,400000,450000,500000,600000,800000,1000000,2000000,5000000];
            }else {
                prices = [500,800,1000,1200,1500,1800,2000,2500,3000,4000,5000,6000,8000,10000,12000];
            };
        break;
        case '2':
            if(type===1){
                prices = [100000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,2000000,3000000,4000000,5000000,10000000];
            }else {
                prices = [1000,1500,2000,1800,2500,3000,3500,4000,5000,6000,8000,10000,12000,15000,20000,25000];
            };
        break;
        case '3':
            if(type===1){
                prices = [10000,20000,50000,70000,100000,200000,500000,700000,1000000,2000000,5000000,7000000,10000000];
            }else {
                prices = [1000,2000,3000,4000,5000,7000,10000,20000,30000,40000,50000,70000,100000,250000,500000];
            };
        break;
        case '4':
            if(type===1){
                prices = [10000,20000,50000,70000,100000,150000,200000,500000,1000000,2000000,5000000,7000000,10000000,50000000,100000000];
            }else {
                prices = [1000,1500,2000,3000,4000,5000,7000,10000,15000,20000,50000,100000,250000,500000,1000000];
            };
        break;
        case '5':
             if(type===1){
                prices = [10000,20000,50000,70000,100000,150000,200000,500000,1000000,2000000,5000000,7000000,10000000,50000000,100000000];
            }else {
                prices = [1000,1500,2000,3000,4000,5000,7000,10000,15000,20000,50000,100000,250000,500000,1000000];
            };
        break;
        case '6':
            if(type===1){
                prices = [5000,10000,15000,20000,25000,30000,35000,40000,45000,50000,60000,70000,80000,90000,100000];
            }else {
                prices = [100,150,200,300,400,500,600,700,800,900,1000,1500,2000,2500,3000];
            };
        break;
    }
    return prices;
}
function getDefaultSqueres(mobile){
    var category = (mobile)? $('#search_mobile_category').val() : $('#search_category').val();

    var squeres = [];
    switch(category){
        case '1':
        case '5':
            squeres = [25,30,35,40,45,50,55,60,70,80,90,100,120,150,200];
        break;
        case '2':
            squeres = [50,80,100,120,150,200,250,300,350,400,500,700,1000,1500,2000];
        break;
        case '3':
             squeres = [500,800,1000,1500,2000,2500,3000,4000,5000,7000,10000,20000,50000,70000,100000];
        break;
        case '4':
            squeres = [25,50,75,100,200,300,400,500,750,1000,2000,50000,7000,10000,50000];
        break;
        case '6':
            squeres = [10,15,20,25,30,35,40,45,50,60,70,80,90,100,200];
        break;
    }
    return squeres;
}
function getDefaultPlotSqueres(){
    return [25,30,35,40,45,50,55,60,70,80,90,100,120,150,200];

}
function popitup(url,height,width,name) {
	newWindow=window.open(url,name,'height='+height+',width='+width,',scrollbars=1');
	if (window.focus) {newWindow.focus()}
	return false;
}