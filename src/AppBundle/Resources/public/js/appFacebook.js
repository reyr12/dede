
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1550697705198886',
      xfbml      : true,
      version    : 'v2.2'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));

function simpleShareFBTW(event){
  var o=$(event.target);
  if( $(o).is('.simpleShareFB') ){
    simpleShareFBurl=$(o).attr('txttrack');   
     /* simpleShareFBsub = new Function("response","url",
        $(o).attr('onshare')!="ga" ? $(o).attr('onshare'):
         // "_gaq.push(['_trackSocial','Facebook','Share',url]);"+
          "setTimeout(function(){ _gaq.push(['_trackEvent','Facebook','Share',url]); },500);"
        );
      */
    FB.ui(
      {
        method: 'feed',
        link: $(o).attr('url'),
        picture: $(o).attr('img').indexOf(',')>-1?$(o).attr('img').split(','):$(o).attr('img'),
        name: $(o).attr('tit'),
        //caption: $(o).attr('tit'),
        description: $(o).attr('txt')
      },
      function (response) {
        if(response) if(response['post_id']) //simpleShareFBsub(response,simpleShareFBurl);
        simpleShareFBurl=false; simpleShareFBsub=false;
      }
    );
    return false;
  }
}

simpleShareFBurl=false; simpleShareFBsub=false; simpleShareFBsubFollow=false; simpleShareFBsubUnFollow=false;
simpleShareTWurl=false; simpleShareTWsub=false;

if(document.addEventListener){  document.addEventListener("click", simpleShareFBTW, false); }
else{ document.attachEvent("onclick", simpleShareFBTW); }

    