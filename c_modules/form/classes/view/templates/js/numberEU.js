$.validator.addMethod(
  "numberEU",
  function( value, element ) {
    return ( value==='' && this.optional( element ) ) || /^-?\d+(,\d+)?$/.test( value );
  },
  "A positive or negative decimal number please (Ej. 123,25)"
);
