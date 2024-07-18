 if(typeof(jQuery)=='undefined'){
     var jq = document.createElement("script");
     jq.addEventListener("load", proceed);
     jq.src = "//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js";
     document.querySelector("head").appendChild(jq);
 }else {
     proceed();
 }
 function proceed () {
    $.ajax({
        method: "GET",
        url: '//wgn.pl/embed?s=xl',
      }).done(function(html){
          $('#wgn-search').html(html);
      });
}


