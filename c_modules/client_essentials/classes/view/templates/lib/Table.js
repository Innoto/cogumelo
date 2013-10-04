
/*
Cogumelo v1.0a - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@innoto.es>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.

*/


/**
* Table Class 
*
* Client side Table class in .js
*
* @author: pablinhob
*/
var Table = Class.create({

  server_data:false, 


  /*
  * @param Object options is the initial options object
  */
  init : function(options) {
    this.options = $.extend( {
      id: false,
      table_div: $('body'),
      form_div: false,
      table_url: false,
      form_url:false,
      finish_load: function(data){} // it will be ttrigger when load is finished
    }, options || {})



    // address events

    $.address.change(function(event) { 
      
    });


    this.server_data();

  },





  /*
  *  Load data from server
  */
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
      that.filters();

      // add paginator
      that.add_paginator();

      // insert table data
      that.load_data();

      // triger finish load
      that.options.finish_load(data);

    }).fail( function(){
        that.options.table_div.html('AJAX connection failed.');
    });
  }



  /*
  * table html
  */
  create_structure: function() {

    // struct
    if(this.options.table_div.html() == "") {
        this.options.table_div.html(
          "<div class='table'>" +
            "<ul class='tabs'></ul>" +
            "<div class='filters'></ul>" +
            "<table>" +
                "<thead></thead>" +
                "<tbody></tbody>" +
            "</table>" + 
            "<div class='paginator'></div>" +
          "</div>"
        );
    }
  },


  /*
  *  Load data into table
  */
  load_data: function() {
    // table head
    var thead = "<tr>";

    $.each( this.server_data.table, function(i,e){
      thead = thead + "<th ></th>";
    });

    thead = thead + "</tr>";

    // table body

  },


  /*
  *  Tabs
  */
  add_tabs: function(tabs_box, id, name) {
    var tabs_box = this.options.table_div.search('ul.tabs');
    if(this.server_data.tabs && tabs_box.html() != "") {
      $.each(this.server_data.tabs, function(i, e){
        tabs_box.append('<li key="'+i+'">"'+e+'"</li>');
      });
    }
  },


  /*
  *  Paginator
  */
  add_paginator: function() {

  }


  /*
  *  filters
  */
  add_filters: function() {
    that = this;
    var filters_box = this.options.table_div.search('div.filters');
    if(this.server_data.filters && filters_box.html() != "") {
      $.each(this.server_data.filters, function(i, e){
        filters_box.append(that.add_filter(e));
      });
  },


  /*
  *  add filter
  */
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


  /*
  *   input search box, filter html
  */
  filter_search: function( filter ) {
    return '<input class="filter" id="'+this.options.id+'_search_'+filter.id+'" value="'+filter.default+'" >';
  },


  /*
  *   type <select> list
  */
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

