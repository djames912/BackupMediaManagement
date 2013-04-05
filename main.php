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
  <body bgcolor="#000000" text="#FFFFFF" >
    <?php
    session_start();
    require_once "includes/functions.php";
    
    if(!$_SESSION['Authenticated'])
    {
      if(isset($_SESSION['UserName']))
      {
        $userName = $_SESSION['UserName'];
        $userInfo = checkLocalAuth($userName);
        if($userInfo['RSLT'] == "1")
        {
          $_SESSION['Authenticated'] = false;
        }
        else
        {
          $userDetails = $userInfo['DATA'];
          $_SESSION['AccessLevel'] = $userDetails->access;
          $_SESSION['Authenticated'] = true;
        }
      }
      elseif(isset($_POST['UserName']))
      {
        $userName = $_POST['UserName'];
        $password = $_POST['Password'];
        $hashedPW = crypt($password, 69);
        $userInfo = checkLocalAuth($userName);
        if($userInfo['RSLT'] == "1")
        {
          $_SESSION['Authenticated'] = false;
        }
        else
        {
          $userDetails = $userInfo['DATA'];
          if($userDetails->password == $hashedPW)
          {
            $_SESSION['UserName'] = $userName;
            $_SESSION['AccessLevel'] = $userDetails->access;
            $_SESSION['Authenticated'] = true;
          }
          else
          {
            $_SESSION['Authenticated'] = false;
          }
        }
      }
      else
      {
        echo '<table width="100%" border="0" cellspacing="0">';
        echo '<tr>';
        echo '<td bgcolor="#000000">';
        echo '<div align="center"><font size="4" color="#FFFFFF"><b><font face="Arial, Helvetica, sans-serif">';
        echo "$applicationName";
        if($applicationInstance != '' || $applicationInstance = "Production")
            echo " - $applicationInstance";
        echo '<br><br>';
        echo "Uh oh!  Couldn't find any valid credentials.";
        echo "<br>";
        echo "Check with your system administrator.";
        echo '<br>';
        echo "***** End of Line *****";
        echo '</font></b></font></div>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        exit();
      }
    }
    
    if(!$_SESSION['Authenticated'])
    {
      echo '<table width="100%" border="0" cellspacing="0">';
      echo '<tr>';
      echo '<td bgcolor="#000000">';
      echo '<div align="center"><font size="4" color="#FFFFFF"><b><font face="Arial, Helvetica, sans-serif">';
      echo "$applicationName";
      if($applicationInstance != '' || $applicationInstance = "Production")
          echo " - $applicationInstance";
      echo '<br><br>';
      echo "User name or password not found.";
      echo '<br>';
      echo "Access denied.";
      echo '<br>';
      echo "***** End of Line *****";
      echo '</font></b></font></div>';
      echo '</td>';
      echo '</tr>';
      echo '</table>';
      exit();
    }
    
    echo '<br>';
    echo "Cleared!  Running main program.";
    ?>
  </body>
</html>
