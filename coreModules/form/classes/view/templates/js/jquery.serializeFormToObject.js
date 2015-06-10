$.fn.serializeFormToObject = function () {
  var ser = {};

  var fa = this.serializeArray();


  $.each( fa, function () {
    if( ser[ this.name ] !== undefined ) {
      if( !ser[ this.name ].push ) {
        ser[ this.name ] = [ ser[ this.name ] ];
      }
      ser[ this.name ].push( this.value || '' );
    }
    else {
      ser[ this.name ] = this.value || '';
    }
  });


  this.find(':input').each(
    function( i, elem ) {
      if( elem.name !== undefined && elem.name !== '' ) {
        if( ser[ elem.name ] === undefined ) {
          ser[ elem.name ] = false;
        }

        // order select multiple
        if( elem.multiple === true  && ser[ elem.name ].push ) {

          ser[ elem.name ] = $( elem ).find( 'option' ).filter( ':selected').toArray()
            .sort( function( a, b ) { return( parseInt( $( a ).data( 'order' ) ) - parseInt( $( b ).data( 'order' ) ) ); } )
            .map( function( e ) { return( e.value ); } );

          /*
          var opValOrd = [];
          $( elem ).find( 'option' ).filter( ':selected').each(
            function( i, opElem ) {
              $selElem = $( opElem );
              opValOrd.push( { val: $selElem.val(), ord: $selElem.data( 'order' ) } );
            }
          );
          ser[ elem.name ] = opValOrd
            .sort( function( a, b ) { return( parseInt( a.ord ) - parseInt( b.ord ) ); } )
            .map( function( e ) { return( e.val ); } );
          */
        }
      }
    }
  );


  console.log( 'resultado: ', ser );
  return ser;
};


