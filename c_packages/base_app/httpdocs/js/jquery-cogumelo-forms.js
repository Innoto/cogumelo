


function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({
    errorClass: "formError",
    rules: rules,
    messages: messages,
    submitHandler:
      function ( form ) {
        $.ajax( {
           contentType: 'application/json', processData: false,
           data: JSON.stringify( $( form ).serializeFormToObject() ),
           type: 'POST', url: $( form ).attr( 'action' ),
           dataType : 'json'
        } )
        .done( function ( response ) {
          console.log( response );
          if( response.success == 'success' ) {
            console.log( 'OK' );
            alert( 'PREMIO!!!  Todo OK ;-)' );
          }
          else {
            console.log( 'ERROR' );
            // $validateForm_'.$this->id.'.showErrors( response.jvErrors );
            // if( response.jvErrors !== '' )  $validateForm_'.$this->id.'.showErrors( { "input2": $.validator.messages[ "minlength" ] } );
            for(var i in response.jvErrors) {
              errObj = response.jvErrors[i];
              console.log( errObj );

              if ( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === '' ) {
                errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] = $validateForm.defaultMessage( errObj['fieldName'], errObj['msgRule'] )( errObj['ruleParams'] );
              }
              console.log( errObj[ 'JVshowErrors' ] );

              $validateForm.showErrors( errObj[ 'JVshowErrors' ] );
            };
            if( response.formError !== '' ) $validateForm.showErrors( {"submit": response.formError} );
          }
        } );
        return false; // required to block normal submit since you used ajax
      }
  });
  /*
  if( count( $this->rules ) > 0 ) {
    rules: json_encode( $this->rules );
  }

  if( count( $this->messages ) > 0 ) {
    messages: json_encode( $this->messages );
  }
  */

  console.log( $validateForm );
  return $validateForm
} // function




/*



    $html .= '<!-- Validate form '.$this->name.' -->'."\n";
    $html .= '<script>'."\n";
    $html .= 'var jvErrors=false;'."\n";
    $html .= 'var jvForm=false;'."\n";
    $html .= '$().ready(function() {'."\n";
    $html .= '  $validateForm_'.$this->id.' = $("#'.$this->id.'").validate({'."\n";

    $html .= $separador.'    submitHandler: '."\n".
      'function ( form ) {'."\n".
      '  $.ajax( { '.
          ' contentType: "application/json", processData: false, '.
          ' data: JSON.stringify( $(form).serializeFormToObject() ), '.
          ' type: "POST", url: $(form).attr("action"), '.
          ' dataType : "json" } )'."\n".
      '  .done( function ( response ) {'."\n".
      '    console.log( response );'."\n".
      '    if (response.success == "success") { console.log("success"); } '."\n".
      '    else { '."\n".
      '      jvErrors = response.jvErrors;'."\n".
      '      jvForm = $validateForm_'.$this->id.';'."\n".
      '      //$validateForm_'.$this->id.'.showErrors( response.jvErrors );'."\n".
      '      $validateForm_'.$this->id.'.showErrors( { "input2": $.validator.messages[ "minlength" ] } );'."\n".
      '      if(response.formError!=="") $validateForm_'.$this->id.'.showErrors({"submit": response.formError});'."\n".
      '    }'."\n".
      '  });'."\n".
      '  return false; // required to block normal submit since you used ajax'."\n".
      '}'."\n";

    $html .= '  });'."\n";

    $html .= 'console.log( $validateForm_'.$this->id.' );'."\n";

    $html .= '});'."\n";
    $html .= '</script>'."\n";

    $html .= '<!-- Validate form '.$this->name.' - END -->'."\n";


*/