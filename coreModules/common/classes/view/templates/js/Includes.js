var cogumelo = cogumelo || {};


cogumelo.includes = function( includes ) {
  var scriptsLoadNow = [];

  $.each(includes, function(i,e) {
    if(e.type=='text/css' && e.rel=='stylesheet/less') {

      less.sheets.push(
        $('<link />', {
          href: e.src,
          rel: 'stylesheet/less',
          type: 'text/css'
        }).appendTo('head')[0]
      );
    }
    else if(e.type=='text/css') {
      $("<link/>", {
        rel: e.rel,
        type: "text/css",
        href: e.src
      }).appendTo("head");
    }
    else if(e.type=='text/javascript') {
      //scriptsLoadNow.push(  e.src  );

      var jsLink = $("<script type='text/javascript' src='"+e.src+"'>");
      $("head").append(jsLink);
    }

  });

  $.holdReady( false );

  less.refresh();
}
