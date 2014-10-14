$.validator.addMethod(
  "uppercase",
  function( value, element, param) {
    return (param === 1);
  },
    "This isn't uppercase"
);
