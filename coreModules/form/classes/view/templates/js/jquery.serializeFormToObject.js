$.fn.serializeFormToObject = function () {
  var ser = {};

  var fa = this.serializeArray();

  var getDataInfo = function getDataInfo( elem ) {
    // PELIGRO: Los valores recuperados por .data() no son fiables!!!
    var dataInfo = false;

    var attrTmp = elem.attributes;
    $.each( attrTmp, function( i, attr ) {
      if( attr.name.indexOf('data-') === 0 ) {
        if( dataInfo === false ) {
          dataInfo = {};
        }
        dataInfo[ attr.name ] = attr.value;
      }
    } );

    return dataInfo;
  };

  $.each( fa, function () {
    if( ser[ this.name ] === undefined ) {
      ser[ this.name ] = {};
      ser[ this.name ].value = this.value || '';
    }
    else {
      if( !ser[ this.name ].value.push ) {
        ser[ this.name ].value = [ ser[ this.name ].value ];
      }
      ser[ this.name ].value.push( this.value || '' );
    }
  });


  $( ':input[form="' + this.attr( 'id' ) + '"]' ).each(
    function( i, elem ) {
      if( elem.name !== undefined && elem.name !== '' ) {
        $elem = $( elem );

        // Set false value
        if( ser[ elem.name ] === undefined ) {
          ser[ elem.name ] = {};
          ser[ elem.name ].value = false;
        }
        // Order select values
        if( elem.multiple === true && $elem.hasClass( 'cgmMForm-order' ) && ser[ elem.name ].value.push ) { // Array de options
          cogumelo.log( 'Ordenando '+ elem.name, ser[ elem.name ] );
          ser[ elem.name ].value = $elem.find( 'option' ).filter( ':selected').toArray()
            .sort( function( a, b ) { return( parseInt( $( a ).data( 'order' ) ) - parseInt( $( b ).data( 'order' ) ) ); } )
            .map( function( e ) { return( e.value ); } );
        }

        // Cargamos la informacion del los atributos "data-*"
        var dataInfo = getDataInfo( elem );
        if( dataInfo ) {
          ser[ elem.name ].dataInfo = dataInfo;
        }

        // Cargamos la informacion del los atributos "data-*" en campos con opciones
        if( $elem.is('select') ) {
          var dataMultiInfo = {};
          $elem.find(":selected").each( function() {
            var dataInfo = getDataInfo( this );
            if( dataInfo ) {
              dataMultiInfo[ this.value ] = dataInfo;
            }
          } );
          cogumelo.log(dataMultiInfo.elements);
          if( !jQuery.isEmptyObject( dataMultiInfo ) ) {
            ser[ elem.name ].dataMultiInfo = dataMultiInfo;
          }
        }
      }
    }
  );


  // Google reCAPTCHA
  $( '.g-recaptcha[form="' + this.attr( 'id' ) + '"] [name="g-recaptcha-response"]' ).each(
    function( i, elem ) {
      if( elem.name !== undefined && elem.name !== '' ) {
        // Set value
        if( ser[ elem.name ] === undefined ) {
          ser[ elem.name ] = {};
        }
        if( ser[ elem.name ].value === undefined ) {
          ser[ elem.name ].value = elem.value;
        }
        grecaptcha.reset();
      }
    }
  );


  cogumelo.log( 'serializeFormToObject: ', ser );
  return ser;
};
