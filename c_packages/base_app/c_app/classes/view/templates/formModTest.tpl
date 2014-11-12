<!DOCTYPE html>
<html>
  <head>
    <title>FORMs con Cogumelo</title>

    {$css_includes}

    {$js_includes}

    <style>
      div { border:1px dashed; margin:5px; padding:5px; }
      label { display:block; }
      label.error, .formError { color:red; border:2px solid red; }
      .ffn-inputFicheiro { background-color:#FFD; }
    </style>
  </head>
  <body>

    {$formOpen}

      {foreach from=$formFields key=key item=field}
        {$field}
      {/foreach}

      <div id="subidas" style="background-color:#EEE;">
      <div id="list">Info: </div>
      <!--
      <span id="drop_zone" style="background-color:blue;">Drop files here</span>
      <input type="button" name="botonUploadFile" value="subir ficheiro" onclick="uploadFile()"><br>
      -->
      <progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
      <h3 id="status">status</h3>
      <p id="loaded_n_total">carga</p>
      </div>

      <div class="JQVMC-formError">errores formError... </div>
      <div id="JQVMC-meu2-error">errores meu2... </div>
      <div id="JQVMC-ungrupo-error">errores ungrupo... </div>
      <div id="JQVMC-manual">errores manuales... </div>

    {$formClose}

    {$formValidations}

  </body>
</html>