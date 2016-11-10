$.fn.serializeFormToObject = function () {
  var ser = {};

  var fa = this.serializeArray();


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
        // Set false value
        if( ser[ elem.name ] === undefined ) {
          ser[ elem.name ] = {};
          ser[ elem.name ].value = false;
        }
        // Order select values
        if( elem.multiple === true && $( elem ).hasClass( 'cgmMForm-order' ) && ser[ elem.name ].value.push ) { // Array de options
          console.log( 'Ordenando '+ elem.name, ser[ elem.name ] );
          ser[ elem.name ].value = $( elem ).find( 'option' ).filter( ':selected').toArray()
            .sort( function( a, b ) { return( parseInt( $( a ).data( 'order' ) ) - parseInt( $( b ).data( 'order' ) ) ); } )
            .map( function( e ) { return( e.value ); } );
        }

        $dataInfo = $( elem ).data();
        // PELIGRO: Los valores recuperados por data() no siven!!!
        ser[ elem.name ].dataInfo = false;

        $.each( $dataInfo, function( k, v ) {
          if( ser[ elem.name ].dataInfo === false ) {
            ser[ elem.name ].dataInfo = {};
          }
          ser[ elem.name ].dataInfo[ k ] = $( elem ).attr( 'data-'+k );
        } );
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


  console.log( 'serializeFormToObject: ', ser );
  return ser;
};


