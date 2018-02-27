
$.validator.addMethod(
  "numberEU",
  function( value, element ) {
    return ( value==='' && this.optional( element ) ) || /^-?\d+(,\d+)?$/.test( value );
  },
  __("A positive or negative decimal number please (Ej. 123,25)")
);

$.validator.addMethod(
  "numberEUDec",
  function( value, element, param ) {
    regexPatt=new RegExp('^-?\\d+(,\\d{0,'+param+'})?$');
    return ( value==='' && this.optional( element ) ) || regexPatt.test( value );
  },

  $.validator.format(__("A positive or negative number with {0} decimal, separated with comma"))
);

$.validator.addMethod(
  "minEU",
  function( value, element, param ) {
    var val = parseFloat( value.replace(',','.') );
    var par = parseFloat( ( typeof param === 'string' ) ? param.replace(',','.') : param );
    return ( ( value==='' && this.optional( element ) ) || ( val >= par ) );
  },
  __("Please enter a value greater than or equal to {0}.")
);

$.validator.addMethod(
  "maxEU",
  function( value, element, param ) {
    var val = parseFloat( value.replace(',','.') );
    var par = parseFloat( ( typeof param === 'string' ) ? param.replace(',','.') : param );
    return ( ( value==='' && this.optional( element ) ) || ( val <= par ) );
  },
  __("Please enter a value less than or equal to {0}.")
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
  $.validator.format( __('The DNI format is not NNNNNNNNC') )
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
  $.validator.format( __('The NIE format is not CNNNNNNNC' ))
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
  $.validator.format( __('The NIF format is not CNNNNNNNC' ))
);



$.validator.addMethod(
  "regex",
  function( value, element, param ) {
    return ( value==='' && this.optional( element ) ) || value.search( param ) !== -1;
  },
  $.validator.format(__("Please enter a valid value"))
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
  $.validator.format(__("Please enter a valid value"))
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
  $.validator.format(__("Please enter a valid value"))
);


$.validator.addMethod(
  "dateUE",
  function( value, element ) {
    valid = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-](\d{4}$)/.exec(value);
    return valid;
  },
  $.validator.format(__("The date format is not DD-MM-YYYY"))
);


$.validator.addMethod(
  "dateTime",
  function( value, element ) {
    // console.log( 'dateTime', value, element );
    valid = /^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{2}):(\d{2})$/.exec(value);
    return ( value==='' && this.optional( element ) ) || valid;
  },
  $.validator.format(__("The date format is not YYYY-MM-DD hh:mm:ss"))
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
  $.validator.format(__("The date entered is too old (> {0})"))
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
  $.validator.format(__("The date entered must be oldest (> {0})"))
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
  $.validator.format(__("The date entered is too old (> {0})"))
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
  $.validator.format(__("The date entered must be oldest (> {0})"))
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
  $.validator.format(__("The time entered is too old (> {0})"))
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
  $.validator.format(__("The time entered must be oldest (> {0})"))
);


$.validator.addMethod(
  "urlYoutube",
  function( value, element ) {

    var valueUrlYoutube = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/.exec(value);

    return ( value==='' && this.optional( element ) ) || valueUrlYoutube;
  },
  $.validator.format(__("The url is not a Youtube url."))
);

$.validator.addMethod(
  "notUrl",
  function( value, element ) {

    var valueUrl = /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
    return !valueUrl;

  },
  $.validator.format(__("Can not be a url"))
);

$.validator.addMethod(
  "passwordStrength",
  function( value, element ) {
    var valid = /[A-Z]/.exec(value) && /[a-z]/.exec(value) && /[0-9]/.exec(value) && /\W/.exec(value) && /^.{8,16}$/.exec(value);
    return valid;
  },
  $.validator.format( __('Password must contain 8 ~ 16 characters and must contain at least one capital letter, one singular letter, one numeric character and one special character (!@-_,./?).'))
);









/**
***  FICHEROS desde Cogumelo Form  ***
**/


/**
*** IMPORTANTE: Reemplazamos el metodo "oficial" declarado en "additional-methods.js"
**/
// Accept a value from a file input based on a required mimetype
$.validator.addMethod(
  "accept",
  function(value, element, param) {
    console.log( ' * * * formValidators::accept ', value, $( element ), param );

    var valueResponse = true;

    var validateFiles = $( element ).data('validateFiles');
    if( validateFiles && validateFiles.length > 0 ) {
      console.log( ' * * * formValidators::accept validateFiles=', validateFiles );

      // Split mime on commas in case we have multiple types we can accept
      var typeParam = ( typeof param === 'string' ) ? param.replace( /\s/g, '' ).replace( /,/g, '|' ) : 'image/*';
      // If we are using a wildcard, make it regex friendly
      typeParam = typeParam.replace(/\*/g, '.*');

      console.log( ' * * * formValidators::accept typeParam : '+typeParam );

      var i;
      for( i = 0; i < validateFiles.length; i++ ) {
        console.log( ' * * * formValidators::accept test elem.'+i+' : '+validateFiles[i].type);
        if( !validateFiles[i].type.match( new RegExp( '\\.?(' + typeParam + ')$', 'i' ) ) ) {
          valueResponse = false;
          console.log( ' * * * formValidators::accept FAIL elem.'+i+' : '+validateFiles[i].type+' !== '+typeParam);
          break;
        }
      }
    }

    console.log( ' * * * formValidators::accept RESULTADO: ', valueResponse );

    return valueResponse;
  },
  $.validator.format("Please enter a value with a valid mimetype.")
);


/**
*** IMPORTANTE: Reemplazamos el metodo "oficial" declarado en "additional-methods.js"
**/
// Older "accept" file extension method. Old docs: http://docs.jquery.com/Plugins/Validation/Methods/accept
$.validator.addMethod(
  "extension",
  function(value, element, param) {
    console.log( ' * * * formValidators::extension ', $( element ), param );

    var valueResponse = true;

    var validateFiles = $( element ).data('validateFiles');
    if( validateFiles && validateFiles.length > 0 ) {
      console.log( ' * * * formValidators::extension validateFiles=', validateFiles );
      param = ( typeof param === 'string' ) ? param.replace( /,/g, '|' ) : 'png|jpe?g|gif';
      var i;
      for( i = 0; i < validateFiles.length; i++ ) {
        if( !validateFiles[i].name.match( new RegExp( '\\.(' + param + ')$', 'i' ) ) ) {
          valueResponse = false;
          break;
        }
      }
    }

    return valueResponse;
  },
  $.validator.format("Please enter a value with a valid extension.")
);


// Accept a value from a file input based on size
$.validator.addMethod(
  "maxfilesize",
  function(value, element, param) {
    console.log( ' * * * formValidators::maxfilesize ', $( element ), param );

    var valueResponse = true;

    // Element is optional
    // if( this.optional(element) ) { return true; }

    var validateFiles = $( element ).data('validateFiles');
    if( validateFiles && validateFiles.length > 0 ) {
      console.log( ' * * * formValidators::maxfilesize validateFiles=', validateFiles, validateFiles.length );
      for( i = 0; i < validateFiles.length; i++ ) {
        if( validateFiles[i].size > param ) {
          valueResponse = false;
          break;
        }
      }
    }


    return valueResponse;
  },
  $.validator.format("Please enter a file with a valid size (<{0} Bytes).")
);


// Accept a value from a file input based on size
$.validator.addMethod(
  "minfilesize",
  function(value, element, param) {
    console.log( ' * * * formValidators::maxfilesize ', $( element ), param );

    var valueResponse = true;

    // Element is optional
    // if( this.optional(element) ) { return true; }

    var validateFiles = $( element ).data('validateFiles');

    // if( $(element).attr("type") === "file" && element.files && element.files.length > 0 ) {
    if( validateFiles && validateFiles.length > 0 ) {
      console.log( ' * * * formValidators::maxfilesize validateFiles=', validateFiles, validateFiles.length );
      for( i = 0; i < validateFiles.length; i++ ) {
        if( validateFiles[i].size < param ) {
          valueResponse = false;
          break;
        }
      }
    }


    return valueResponse;
  },
  $.validator.format("Please enter a file with a valid size (>{0} Bytes).")
);


// Accept a value from a file input
$.validator.addMethod(
  "multipleMax",
  function(value, element, param) {
    var valueResponse = false;

    console.log( ' * * * formValidators::multipleMax ', $( element ), param );
    var $fileField = $( element );
    var groupFiles = false;
    var groupId = $fileField.attr('data-fm_group_id');

    if( groupId ) {
      var idForm = $fileField.attr('form');
      var formCtlr = cogumelo.formControllerInfo.getFormInfo( idForm, 'controller' );
      // console.log( ' * * * formValidators::multipleMax Form ', idForm, formCtlr );

      if( formCtlr && typeof formCtlr.fileGroup[ groupId ] !== 'undefined' ) {
        groupFiles = formCtlr.fileGroup[ groupId ];
      }
    }
    // console.log( ' * * * formValidators::multipleMax IDs', groupId, groupFiles, groupFiles.length );

    var validateFiles = $fileField.data('validateFiles');
    // console.log( ' * * * formValidators::multipleMax validateFiles=', validateFiles, validateFiles.length );

    var countFiles = ( validateFiles ) ? validateFiles.length : 0;
    if( groupFiles ) {
      countFiles += groupFiles.length;
    }
    // console.log( ' * * * formValidators::multipleMax countFiles=', countFiles );

    if( countFiles <= param ) {
      valueResponse = true;
    }


    return valueResponse;
  },
  $.validator.format("Demasiados ficheros. (Límite: {0} ficheros).")
);


// Accept a value from a file input
$.validator.addMethod(
  "multipleMin",
  function(value, element, param) {
    var valueResponse = false;

    console.log( ' * * * formValidators::multipleMin ', $( element ), param );
    var $fileField = $( element );
    var groupFiles = false;
    var groupId = $fileField.attr('data-fm_group_id');

    if( groupId ) {
      var idForm = $fileField.attr('form');
      var formCtlr = cogumelo.formControllerInfo.getFormInfo( idForm, 'controller' );
      // console.log( ' * * * formValidators::multipleMax Form ', idForm, formCtlr );

      if( formCtlr && typeof formCtlr.fileGroup[ groupId ] !== 'undefined' ) {
        groupFiles = formCtlr.fileGroup[ groupId ];
      }
    }
    // console.log( ' * * * formValidators::multipleMin IDs', groupId, groupFiles, groupFiles.length );

    var validateFiles = $fileField.data('validateFiles');
    // console.log( ' * * * formValidators::multipleMin validateFiles=', validateFiles, validateFiles.length );

    var countFiles = ( validateFiles ) ? validateFiles.length : 0;
    if( groupFiles ) {
      countFiles += groupFiles.length;
    }
    // console.log( ' * * * formValidators::multipleMin countFiles=', countFiles );

    if( countFiles >= param ) {
      valueResponse = true;
    }


    return valueResponse;
  },
  $.validator.format("Pocos ficheros. (Límite: {0} ficheros).")
);


// Accept a value from a file input based on size
$.validator.addMethod(
  "fileRequired",
  function( value, element ) {
    var valueResponse = false;

    console.log( ' * * * formValidators::fileRequired ', $( element ) );
    var $fileField = $( element );
    var groupFiles = false;
    var groupId = $fileField.attr('data-fm_group_id');

    if( groupId ) {
      var idForm = $fileField.attr('form');
      var formCtlr = cogumelo.formControllerInfo.getFormInfo( idForm, 'controller' );
      // console.log( ' * * * formValidators::multipleMax Form ', idForm, formCtlr );

      if( formCtlr && typeof formCtlr.fileGroup[ groupId ] !== 'undefined' ) {
        groupFiles = formCtlr.fileGroup[ groupId ];
      }
    }
    // console.log( ' * * * formValidators::multipleMin IDs', groupId, groupFiles, groupFiles.length );

    var validateFiles = $fileField.data('validateFiles');
    // console.log( ' * * * formValidators::multipleMin validateFiles=', validateFiles, validateFiles.length );

    var countFiles = ( validateFiles ) ? validateFiles.length : 0;
    if( groupFiles ) {
      countFiles += groupFiles.length;
    }
    console.log( ' * * * formValidators::fileRequired countFiles=', countFiles );

    if( countFiles > 0 ) {
      valueResponse = true;
    }


    return valueResponse;
  },
  $.validator.format("Este campo es obligatorio.")
);
// // http://jqueryvalidation.org/required-method/
// required: function( value, element, param ) {
//   // check if dependency is met
//   if ( !this.depend( param, element ) ) {
//     return "dependency-mismatch";
//   }
//   if ( element.nodeName.toLowerCase() === "select" ) {
//     // could be an array for select-multiple or a string, both are fine this way
//     var val = $( element ).val();
//     return val && val.length > 0;
//   }
//   if ( this.checkable( element ) ) {
//     return this.getLength( value, element ) > 0;
//   }
//   return value.length > 0;
// },
