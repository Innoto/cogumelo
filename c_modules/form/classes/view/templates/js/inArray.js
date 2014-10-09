$.validator.addMethod(
  "inArray",
  function( value, element, param ) {
    var valueText = false;
    var valueIsArray = Object.prototype.toString.call( value ) === '[object Array]';
    if( value!==undefined && value!==false && value!==null && !valueIsArray ) {
      valueText = value.toString();
    }
    // No usamos .indexOf ni inArray porque cosideran 1 != "1" y no me interesa
    for(var i=0; i<param.length; i++) {
      if( ( valueText!==false && param[i].toString() === valueText ) ||
          ( valueIsArray && value.indexOf( param[i].toString() ) !== -1 ) ) {
        return true;
      }
    }
    return ( (value===undefined || value===false || value===null) && this.optional( element ) );
  },
  $.validator.format("Please enter a valid value")
);


$.validator.addMethod(
  "notInArray",
  function( value, element, param ) {
    var valueText = false;
    var valueIsArray = Object.prototype.toString.call( value ) === '[object Array]';
    if( value!==undefined && value!==false && value!==null && !valueIsArray ) {
      valueText = value.toString();
    }
    // No usamos .indexOf ni inArray porque cosideran 1 != "1" y no me interesa
    var valueResponse = true;    
    for(var i=0; i<param.length; i++) {
      if( ( valueText!==false && param[i].toString() === valueText ) ||
          ( valueIsArray && value.indexOf( param[i].toString() ) !== -1 ) ) {
        valueResponse = false;
        break;
      }
    }
    
    return ( valueResponse || ((value===undefined || value===false || value===null) && this.optional( element )) );
  },
  $.validator.format("Please enter a valid value")
);
