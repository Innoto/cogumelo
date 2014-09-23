$.validator.addMethod(
  "dateMin",
  function( value, element, param) {
    console.log("value:"+value);
    console.log("param:"+param);
  },
    "The date entered is too old"
);


$.validator.addMethod(
  "dateMax",
  function( value, element, param ) {
    console.log("value:"+value);
    console.log("param:"+param);
  },
  "The date entered must be oldest"
);
