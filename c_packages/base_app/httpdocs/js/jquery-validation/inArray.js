$.validator.addMethod(
  "inArray",
  function( value, element, param ) {
    valueIsArray = Object.prototype.toString.call( value ) === '[object Array]';
    if( value===undefined || value===false || value===null || valueIsArray ) {
      valueText = false;
    }
    else {
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
