<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <?php
    require_once "includes/configure.php";
    echo "<br>";
    $userName = $_SERVER['REMOTE_USER'];
    if($userName == null || $userName == "")
      $userName = "unknown";
    echo "$userName";
    ?>
  </body>
</html>
