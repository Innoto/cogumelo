$.validator.addMethod(
  "timeMin",
  function( value, element, param) {
    console.log(value);
    console.log(param);    
  },
    "The date entered is too old"
);


$.validator.addMethod(
  "timeMax",
  function( value, element, param) {
    console.log(value);
    console.log(param);
  },
  "The date entered must be oldest"
);
