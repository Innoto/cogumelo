


var CogumeloTable = Class.create({
  init : function(options) {
    this.options = $.extend( {
      id: false,
      table_div: $('body'),
      form_div: false,
      table_url: false,
      form_url:false
    }, options || {})


  }
});