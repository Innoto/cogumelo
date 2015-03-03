<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Grupos en Cogumelo</title>

    {$css_includes}

    {$js_includes}

    <style>
      body { font-size: 12px; }
      .cgmMForm-wrap { border:1px dashed violet; margin:1px; padding:1px 5px; }
      label { display:block; margin: 0;}
      input[type="text"] { margin: 2px; padding: 2px; font-size: 12px; }
      .error, .formError { color:red; border:2px solid red; }
      .cgmMForm-inputFicheiro { background-color:#FFD; }
    </style>
  </head>
  <body>

    {$formOpen}

      {$formFields}

<!--
      <div id="subidas" style="background-color:#EEE;">
      <div id="list">Info: </div>

      <progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
      <h3 id="status">status</h3>
      <p id="loaded_n_total">carga</p>
      </div>
-->

      <div class="JQVMC-formError">errores formError... </div>
      <div id="JQVMC-meu2-error">errores meu2... </div>
      <div id="JQVMC-ungrupo-error">errores ungrupo de JQV... </div>
      <div id="JQVMC-manual">errores manuales... </div>

    {$formClose}

    {$formValidations}

    <br><hr><br>

    {$formBasura}

  </body>
</html>