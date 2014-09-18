$.validator.addMethod(
  "regex",
  function( value, element, param ) {
    return ( value==='' && this.optional( element ) ) || value.search( param ) !== -1;
  },
  $.validator.format("Please enter a valid value")
);
