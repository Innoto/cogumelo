<?php
if( isset($_SERVER['HTTP_USER_AGENT']) && ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ) ) {
  header('X-UA-Compatible: IE=edge');
}
?><!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {* <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=Edge"><![endif]--> *}
  <title>Test page</title>
  <style>

    body { background: #3C3F41; }

    .test {
      margin-top: 20%;
      text-align: center;
      color: #F2F1F0;
      font-size: 54px;
      text-shadow: 1px -1px 4px rgba(240, 245, 242, 0.6);
    }

  </style>
</head>

<body>
  <div class="test">[ Cogumelo Base App ]</div>
</body>

</html>
