<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <?php
    session_start();
    require_once "includes/functions.php";
    $suppliedUserName = $_SERVER['REMOTE_USER'];
    $userName = cleanUserName($suppliedUserName);
    if($userName == null || $userName = '')
    {
      $myBaseURL = str_replace('index.php', 'locallogin.php', $_SERVER['REQUEST_URI']);
      echo '<meta http-equiv="refresh" content="0; url=http://' . $_SERVER["HTTP_HOST"] . $myBaseURL .'">';
    }
    else
    {
      $_SESSION['UserName'] = $userName;
      $myBaseURL = str_replace('index.php', 'main.php', $_SERVER['REQUEST_URI']);
      echo '<meta http-equiv="refresh" content="0; url=http://' . $_SERVER["HTTP_HOST"] . $myBaseURL .'">';
    }
    ?>
  </body>
</html>
