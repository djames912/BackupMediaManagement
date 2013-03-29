<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <?php
    if(isset($_SERVER['REMOTE_USER']))
    {
      $suppliedUserName = $_SERVER['REMOTE_USER'];
      $userName = cleanUserName($suppliedUserName);
    }
    elseif(isset($_POST['UserName']))
    {
      $userName = $_POST['UserName'];
    }
    else
    {
      echo "Uh oh!  Couldn't find any valid credentials.";
      echo "<br>";
      echo "Check with your system administrator.";
      exit();
    }
    echo "Main Program";
    echo "<br>";
    echo "User: $userName"; 
    echo "<br>";
    //echo "Pass: " . $_POST['Password'];
    ?>
  </body>
</html>
