


$.validator.addMethod(
  "numberEU",
  function( value, element ) {
    return ( value==='' && this.optional( element ) ) || /^-?\d+(,\d+)?$/.test( value );
  },
  "A positive or negative decimal number please (Ej. 123,25)"
);



$.validator.addMethod(
  "dni",
  function( value, element ) {
    var result = false;

    var patt = /^([0-9]{8})([A-Z])$/i;
    var match = patt.exec( value );
    if( match ) {
      var numero    = match[1];
      var letra_dni = match[2].toUpperCase();

      if( letra_dni === 'TRWAGMYFPDXBNJZSQVHLCKE'.substr( numero%23, 1 ) ) {
        result = true;
      }
    }

    return result;
  },
  $.validator.format( 'The DNI format is not NNNNNNNNC' )
);

$.validator.addMethod(
  "nie",
  function( value, element ) {
    var result = false;

    var patt = /^([XYZ]?)([0-9]{7})([A-Z])$/i;
    var match = patt.exec( value );
    if( match ) {
      var letraNie = match[1].toUpperCase();
      var numero   = match[2];
      var letraDni = match[3].toUpperCase();

      // Ajustes NIE
      switch( letraNie ) {
        case 'X':
          numero = '0'+numero;
        break;
        case 'Y':
          numero = '1'+numero;
        break;
        case 'Z':
          numero = '2'+numero;
        break;
      }

      if( letraDni === 'TRWAGMYFPDXBNJZSQVHLCKE'.substr( numero%23, 1 ) ) {
        result = true;
      }
    }

    return result;
  },
  $.validator.format( 'The NIE format is not CNNNNNNNC' )
);

$.validator.addMethod(
  "nif",
  function( value, element ) {
    var result = false;

    var patt = /^([A-HJ-NP-SUVW])([0-9]{7})([A-J0-9])$/i;
    var match = patt.exec( value );
    if( match ) {
      var letraTipo = match[1].toUpperCase();
      var numero    = match[2];
      var letraCtrl = match[3].toUpperCase();

      var sum = 0;
      // summ all even digits
      for( i=1; i<7; i+=2 ) {
        sum += parseInt( numero.substr( i, 1 ) );
      }
      // x2 all odd position digits and sum all of them
      for( i=0; i<7; i+=2 ) {
        t = parseInt( numero.substr( i, 1 ) ) * 2;
        sum += (t>9) ? 1 + ( t%10 ) : t;
      }


      // Rest to 10 the last digit of the sum
      control = 10 - ( sum%10 );

      // control can be a numbber or letter
      if( letraCtrl == control || letraCtrl == 'JABCDEFGHI'.substr( control, 1 ) ) {
        result = true;
      }
    }

    return result;
  },
  $.validator.format( 'The NIF format is not CNNNNNNNC' )
);



$.validator.addMethod(
  "regex",
  function( value, element, param ) {
    return ( value==='' && this.optional( element ) ) || value.search( param ) !== -1;
  },
  $.validator.format("Please enter a valid value")
);



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



$.validator.addMethod(
  "dateTime",
  function( value, element ) {
    // console.log( 'dateTime', value, element );
    valid = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    return ( value==='' && this.optional( element ) ) || valid;
  },
  $.validator.format("The date format is not YYYY-MM-DD hh:mm:ss")
);


$.validator.addMethod(
  "dateTimeMin",
  function( value, element, param) {

    var valueDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueDateTime = "";
    var paramDateTime = "";

    if(valueDateTimeArray)
      valueDateTime = new Date(valueDateTimeArray[1], valueDateTimeArray[2], valueDateTimeArray[3], valueDateTimeArray[4], valueDateTimeArray[5], valueDateTimeArray[6]);
    if(paramDateTimeArray)
      paramDateTime = new Date(paramDateTimeArray[1], paramDateTimeArray[2], paramDateTimeArray[3], paramDateTimeArray[4], paramDateTimeArray[5], paramDateTimeArray[6]);

    return (valueDateTime.getTime() > paramDateTime.getTime());

  },
  $.validator.format("The date entered is too old (> {0})")
);


$.validator.addMethod(
  "dateTimeMax",
  function( value, element, param ) {
    var valueDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramDateTimeArray = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueDateTime = "";
    var paramDateTime = "";

    if(valueDateTimeArray)
      valueDateTime = new Date(valueDateTimeArray[1], valueDateTimeArray[2], valueDateTimeArray[3], valueDateTimeArray[4], valueDateTimeArray[5], valueDateTimeArray[6]);
    if(paramDateTimeArray)
      paramDateTime = new Date(paramDateTimeArray[1], paramDateTimeArray[2], paramDateTimeArray[3], paramDateTimeArray[4], paramDateTimeArray[5], paramDateTimeArray[6]);

    return (valueDateTime.getTime() < paramDateTime.getTime());
  },
  $.validator.format("The date entered must be oldest (> {0})")
);



$.validator.addMethod(
  "dateMin",
  function( value, element, param) {

    var valueDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(value);
    var paramDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(param);
    var valueDate = "";
    var paramDate = "";

    if(valueDateArray)
      valueDate = new Date(valueDateArray[1], valueDateArray[2], valueDateArray[3]);
    if(paramDateArray)
      paramDate = new Date(paramDateArray[1], paramDateArray[2], paramDateArray[3]);

    return (valueDate.getTime() > paramDate.getTime());

  },
  $.validator.format("The date entered is too old (> {0})")
);


$.validator.addMethod(
  "dateMax",
  function( value, element, param ) {
    var valueDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(value);
    var paramDateArray = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(param);
    var valueDate = "";
    var paramDate = "";

    if(valueDateArray)
      valueDate = new Date(valueDateArray[1], valueDateArray[2], valueDateArray[3]);
    if(paramDateArray)
      paramDate = new Date(paramDateArray[1], paramDateArray[2], paramDateArray[3]);

    return (valueDate.getTime() < paramDate.getTime());
  },
  $.validator.format("The date entered must be oldest (> {0})")
);



$.validator.addMethod(
  "timeMin",
  function( value, element, param) {

    var valueTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueTimeSeconds = "";
    var paramTimeSeconds = "";
    if(valueTimeArray){
      valueTimeSeconds = parseInt(valueTimeArray[0])*3600 + parseInt(valueTimeArray[1])*60 + parseInt(valueTimeArray[2]);
    }
    if(paramTimeArray){
      paramTimeSeconds = parseInt(paramTimeArray[0])*3600 + parseInt(paramTimeArray[1])*60 + parseInt(paramTimeArray[2]);
    }

    return (valueTimeSeconds > paramTimeSeconds);
  },
  $.validator.format("The time entered is too old (> {0})")
);


$.validator.addMethod(
  "timeMax",
  function( value, element, param ) {

    var valueTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    var paramTimeArray = /^(\d{1,2}):(\d{2}):(\d{2})$/.exec(param);
    var valueTimeSeconds = "";
    var paramTimeSeconds = "";
    if(valueTimeArray){
      valueTimeSeconds = parseInt(valueTimeArray[0])*3600 + parseInt(valueTimeArray[1])*60 + parseInt(valueTimeArray[2]);
    }
    if(paramTimeArray){
      paramTimeSeconds = parseInt(paramTimeArray[0])*3600 + parseInt(paramTimeArray[1])*60 + parseInt(paramTimeArray[2]);
    }

    return (valueTimeSeconds < paramTimeSeconds);
  },
  $.validator.format("The time entered must be oldest (> {0})")
);




// Accept a value from a file input based on size
$.validator.addMethod(
  "maxfilesize",
  function(value, element, param) {

    // Element is optional
    var optionalValue = this.optional(element);
    if( optionalValue ) {
      return optionalValue;
    }

    var valueResponse = true;

    if( $(element).attr("type") === "file" && element.files && element.files.length > 0 ) {
      for( i = 0; i < element.files.length; i++ ) {
        if( element.files[i].size > param ) {
          valueResponse = false;
          break;
        }
      }
    }

    // Either return true because we've validated each file, or because the
    // browser does not support element.files and the FileList feature
    return valueResponse;
  },
  $.validator.format("Please enter a file with a valid size (<{0} Bytes).")
);


// Accept a value from a file input based on size
$.validator.addMethod(
  "minfilesize",
  function(value, element, param) {

    // Element is optional
    var optionalValue = this.optional(element);
    if( optionalValue ) {
      return optionalValue;
    }

    var valueResponse = true;

    if( $(element).attr("type") === "file" && element.files && element.files.length > 0 ) {
      for( i = 0; i < element.files.length; i++ ) {
        if( element.files[i].size < param ) {
          valueResponse = false;
          break;
        }
      }
    }

    // Either return true because we've validated each file, or because the
    // browser does not support element.files and the FileList feature
    return valueResponse;
  },
  $.validator.format("Please enter a file with a valid size (>{0} Bytes).")
);



