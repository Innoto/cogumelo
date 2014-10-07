


function setValidateForm( idForm, rules, messages ) {

  var $validateForm = $( '#'+idForm ).validate({
    debug: true,
    errorClass: "formError",
    rules: rules,
    messages: messages,
    submitHandler:
      function ( form ) {
        $( form ).find( '[type="submit"]' ).attr("disabled", "disabled");
        $.ajax( {
           contentType: 'application/json', processData: false,
           data: JSON.stringify( $( form ).serializeFormToObject() ),
           type: 'POST', url: $( form ).attr( 'action' ),
           dataType : 'json'
        } )
        .done( function ( response ) {
          console.log( response );
          if( response.success == 'success' ) {

          }
          else {
            console.log( 'ERROR' );
            // $validateForm_'.$this->id.'.showErrors( response.jvErrors );
            // if( response.jvErrors !== '' )  $validateForm_'.$this->id.'.showErrors( { "input2": $.validator.messages[ "minlength" ] } );
            for(var i in response.jvErrors) {
              errObj = response.jvErrors[i];
              console.log( errObj );
              if ( errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] === '' ) {
                $defMess = $validateForm.defaultMessage( errObj['fieldName'], errObj['msgRule'] );
                if( $.isFunction( $defMess ) ) {
                  $defMess = $defMess( errObj['ruleParams'] );
                }
                errObj[ 'JVshowErrors' ][ errObj[ 'fieldName' ] ] = $defMess;
              }
              $validateForm.showErrors( errObj[ 'JVshowErrors' ] );
            };
            if( response.formError !== '' ) $validateForm.showErrors( {"submit": response.formError} );
          }
          $( form ).find( '[type="submit"]' ).removeAttr("disabled");
        } );
        return false; // required to block normal submit since you used ajax
      }
  });

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