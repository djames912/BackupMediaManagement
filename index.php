<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Backup Media Management</title>
    <link rel="stylesheet" type="text/css" media="all" href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script type="text/javascript" src="js/jqueryui/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.18.custom.min.js"></script>
  </head>
  <body>
    <?php
    session_start();
    require_once "includes/functions.php";
    $_SESSION['Authenticated'] = false;
    $suppliedUserName = $_SERVER['REMOTE_USER'];
    $userName = cleanUserName($suppliedUserName);
    
    if($userName == null || $userName = '')
    {
      $myBaseURL = $_SERVER['HTTP_HOST'] . $applicationRoot . 'locallogin.php';
      echo '<meta http-equiv="refresh" content="0;URL=\'http://' . $myBaseURL . '\'">';     
    }
    else
    {
      $_SESSION['UserName'] = $userName;
      $myBaseURL = $_SERVER['HTTP_HOST'] . $applicationRoot . 'main.php';
      echo '<meta http-equiv="refresh" content="0;URL=\'http://' . $myBaseURL . '\'">';
    }
    ?>    
  </body>
</html>
