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
    session_start();
    require_once "includes/functions.php";
    if(!$_SESSION['authenticated'])
    {
      if(isset($_SESSION['UserName']))
      {
        $userName = $_SESSION['UserName'];
      }
      elseif(isset($_POST['UserName']))
      {
        $userName = $_POST['UserName'];
        $password = $_POST['Password'];
        $hashedPW = crypt($password, 69);
        
      }
      else
      {
        echo "Uh oh!  Couldn't find any valid credentials.";
        echo "<br>";
        echo "Check with your system administrator.";
        exit();
      }
    }
     
    ?>
  </body>
</html>
