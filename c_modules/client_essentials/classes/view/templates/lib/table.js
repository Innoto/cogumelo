


var table = Class.create({

  server_data:false, 

  init : function(options) {
    this.options = $.extend( {
      id: false,
      table_div: $('body'),
      form_div: false,
      table_url: false,
      form_url:false,
      finish_load: function(this){}
    }, options || {})
  



  },


  server_data: function(){
    that=this;
    // Call server!!
    $.ajax({
      url: this.options.table_url,
      dataType: 'JSON',
      async:false
    }).done( function(data){
     
      // dump server data
      that.server_data = data;

      // add tabs
      that.add_tabs();

      // add filters
      that.add_filters();

      // add paginator
      that.add_paginator();

      // triger finish load
      that.finish_load();

    }).fail( function(){
        that.options.table_div.html('AJAX connection failed.');
    });
  }



  //
  //  table html
  //
  create_structure: function() {

    // struct
    this.options.table_div.html(
      "<div class='table'>" +
        "<ul class='tabs'></ul>" +
        "<div class='filters'></ul>" +
        "<table></table>" + 
        "<div class='paginator'></div>" +
      "</div>"
    );

  },


  //
  //  Tabs
  //
  add_tabs: function(tabs_box, id, name) {
    var tabs_box = this.options.table_div.search('ul.tabs');
    if(this.server_data.tabs && tabs_box.html() != "") {
      $.each(this.server_data.tabs, function(i, e){
        tabs_box.append('<li key="'+i+'">"'+e+'"</li>');
      });
    }
  },

  //
  //  Paginator
  //
  add_paginator: function() {

  }

  //
  //  filters
  //
  add_filters: function() {
    that = this;
    var filters_box = this.options.table_div.search('div.filters');
    if(this.server_data.filters && filters_box.html() != "") {
      $.each(this.server_data.filters, function(i, e){
        filters_box.append(that.add_filter(e));
      });
  },

  //
  // add filter
  //
  filter: function(e) {
    var filter_html = "";

    if(  e.type == 'search' ) {
      filter_html = that.filter_search(e);
    }
    else if(e.type == 'list') {
      filter_html = that.filter_list(e);
    } 

    return filter_html;
  },




  filter_search: function( filter ) {
    return '<input class="filter" id="'+this.options.id+'_search_'+filter.id+'" value="'+filter.default+'" >';
  },



  filter_list: function( filter ) {
    that = this;
    sub_filters = [];
    default_value = filter.default;

    ret_html = '<div class="filter"> <select id="'+this.options.id+'_search_'+filter.id+'" value="'+filter.default+'" >';

    // filter elements
    $.each(filter ,function(i,e) {

      var option_text;

      // selected element
      if(default_value == i){
        var def = "SELECTED";
      }
      else {
        var def = "";
      }

      if(typeof e === "string" || typeof e === "number") { //normal filter
        option_text = e;
      }
      else { //extended filter
        option_text = e.list_name;
        sub_filters.push(that.filter(e));
      }

      // option string
      ret_html = ret_html + '<option value="' + i + '" '+ def +' >' + option_text + '</option>';

    });

    // aditional filters
    $.each(sub_filters ,function(i,e) {
      ret_html = ret_html + e;
    });

    ret_html = ret_html + '</div>'

    return ret_html;
  }

});

