<html>
<body>


  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" id="inputFile" multiple style="background-color:green;">
    <span id="drop_zone" style="background-color:blue;">Drop files here</span>

    <div id="subidas" style="background-color:grey;">
      <input type="button" value="Upload File" onclick="uploadFile()"> <input type="submit" name="submit" value="Submit"><br>
      <progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
      <h3 id="status">status</h3>
      <p id="loaded_n_total">carga</p>
    </div>
  </form>

  <div id="list">Info: </div>
  <!--
  <button onclick="doIt()">Render Image</button><br>
  <canvas id="canvas" style="border: 1px solid black; height: 400px; width: 400px;">
  -->

<script type="text/javascript">

  // Check for the various File API support.
  if( !(window.File && window.FileReader && window.FileList && window.Blob) ) {
    alert('The File APIs are not fully supported in this browser.');
  }



  function handleFileSelect(evt) {
    console.log( 'handleFileSelect' );
    var files = evt.target.files; // FileList object
    procesarFiles( files );
  }

  document.getElementById('inputFile').addEventListener('change', handleFileSelect, false);



  function handleFileDrop(evt) {
    console.log( 'handleFileDrop' );
    evt.stopPropagation();
    evt.preventDefault();

    var files = evt.dataTransfer.files; // FileList object.
    procesarFiles( files );
  }

  function handleDragOver(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
  }

  // Setup the dnd listeners.
  var dropZone = document.getElementById('drop_zone');
  dropZone.addEventListener('dragover', handleDragOver, false);
  dropZone.addEventListener('drop', handleFileDrop, false);




  function procesarFiles( files ) {
    // Loop through the FileList and render image files as thumbnails.
    for (var i = 0, f; f = files[i]; i++) {
    console.log( f );

      // Only process image files.
      if (f.type.match('image.*')) {
        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function cargado(theFile) {
          return function procesando(e) {
            console.log( e );
            // Render thumbnail.
            var span = document.createElement('div');
            span.innerHTML = ['<strong>', escape(theFile.name), '</strong> (',
              theFile.type || 'n/a', ') - ',
              theFile.size, ' bytes, last modified: ',
              theFile.lastModifiedDate ? theFile.lastModifiedDate.toLocaleDateString() : 'n/a',
              '<br><img border="1" style="max-width:50px; max-height:50px;" src="', e.target.result,
              '" title="', escape(theFile.name), '"/>'].join('');
            document.getElementById('list').insertBefore(span, null);
          };
        })(f);

        // Read in the image file as a data URL.
        reader.readAsDataURL(f);
      }
      else {
        var span = document.createElement('div');
        span.innerHTML = ['<strong>', escape(f.name), '</strong> (',
          f.type || 'n/a', ') - ',
          f.size, ' bytes, last modified: ',
          f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a'].join('');
        document.getElementById('list').insertBefore(span, null);
      }

    } // for

  } // function procesarFiles






/* Video Tutorial: http://www.youtube.com/watch?v=EraNFJiY0Eg */

function uploadFile() {
  console.log( 'uploadFile' );
  var file = document.getElementById("inputFile").files[0];

  var formdata = new FormData();
  formdata.append("ajaxFileUpload", file);
  var ajax = new XMLHttpRequest();

  ajax.upload.addEventListener("progress", progressHandler, false);
  ajax.addEventListener("load", completeHandler, false);
  ajax.addEventListener("error", errorHandler, false);
  ajax.addEventListener("abort", abortHandler, false);
  ajax.open("POST", "/ajax_file_upload_parser");
  ajax.send(formdata);
}

function progressHandler(event) {
  console.log( 'progressHandler' );
  document.getElementById("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
  var percent = (event.loaded / event.total) * 100;
  document.getElementById("progressBar").value = Math.round(percent);
  document.getElementById("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}

function completeHandler(event) {
  console.log( 'completeHandler' );
  document.getElementById("status").innerHTML = event.target.responseText;
  document.getElementById("progressBar").value = 0;
}

function errorHandler(event) {
  document.getElementById("status").innerHTML = "Upload Failed";
}

function abortHandler(event) {
  document.getElementById("status").innerHTML = "Upload Aborted";
}


</script>

<script src="http://modernizr.com/downloads/modernizr-latest.js"></script>

<?php //phpinfo(); ?>

</body>
</html>
