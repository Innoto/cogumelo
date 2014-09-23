<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Cogumelo</title>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/jquery-cogumelo-forms.js"></script>
    <script src="/js/jquery.serializeFormToObject.js"></script>
    <script src="/js/jquery-validation/jquery.validate.min.js"></script>
    <script src="/js/jquery-validation/additional-methods.min.js"></script>
    <script src="/js/jquery-validation/inArray.js"></script>
    <script src="/js/jquery-validation/regex.js"></script>
    <script src="/js/jquery-validation/numberEU.js"></script>
    <script src="/js/jquery-validation/timeMaxMin.js"></script>
    <script src="/js/jquery-validation/dateMaxMin.js"></script>
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
        <div>{$item->getter('lostName')} ---- {$item->getter('lostMail')} ---- {$item->getter('lostProvince')} ---- {$item->getter('lostPhone')} </div>
      {/foreach}
    {else}
      <div>Vacio</div>
    {/if}    
  </body>
</html>