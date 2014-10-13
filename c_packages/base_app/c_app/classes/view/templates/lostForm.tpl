<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Cogumelo</title>

    {$css_includes}

    <script>
      less = {
        env: "development",
        async: false,
        fileAsync: false,
        poll: 1000,
        functions: { },
        dumpLineNumbers: "all",
        relativeUrls: false,
        errorReporting: 'console'
      };
    </script>

    {$js_includes}



    <!-- script>$.validator.setDefaults( { submitHandler: function(){ alert("submitted!"); } } );</script -->
    <style> label.error{ color:red; } </style>
  </head>
  <body>
    {$lostFormOpen}
      {foreach from=$lostFormFields key=key item=field}
      <p>
        {$field}
      </p>
      {/foreach}
    {$lostFormClose}
    {$lostFormValidations}

    <h3>Listado de Perdidos</h3>
    {if $lostList}
      {foreach from=$lostList key=key item=item}
        <div>{$item->getter('id')} ---- {$item->getter('lostName')} ---- {$item->getter('lostMail')} ---- {$item->getter('lostProvince')} ---- {$item->getter('lostPhone')} </div>
      {/foreach}
    {else}
      <div>Vacio</div>
    {/if}
  </body>
</html>