<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Backup Media Management</title>
    <link rel="stylesheet" type="text/css" media="all" href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script type="text/javascript" src="js/jqueryui/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="js/lgen.js"></script>   
    <script type="text/javascript" src="js/main.js"></script>
  </head>
  <body>
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
        echo '<td>';
        echo '<div align="center"><font color="#FFFFFF">';
        echo "$applicationName";
        if($applicationInstance != '' || $applicationInstance = "Production")
            echo " - $applicationInstance";
        echo '<br><br>';
        echo "Uh oh!  Couldn't find any valid credentials.";
        echo "<br>";
        echo "Check with your system administrator.";
        echo '<br>';
        echo "***** End of Line *****";
        echo '</font></div>';
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
      echo '<td>';
      echo '<div align="center"><font color="#FFFFFF">';
      echo "$applicationName";
      if($applicationInstance != '' || $applicationInstance = "Production")
          echo " - $applicationInstance";
      echo '<br><br>';
      echo "User name or password not found.";
      echo '<br>';
      echo "Access denied.";
      echo '<br>';
      echo "***** End of Line *****";
      echo '</font></div>';
      echo '</td>';
      echo '</tr>';
      echo '</table>';
      exit();
    }
    $testVar = 1;
    echo '<div style="width:960px; margin-left:auto; margin-right:auto;">';
    echo '<div id="header">';
    echo 'Backup Media Management';
    echo '</div>';
    echo '<div id="menu"></div>';
    echo '<div class="content" id="main">';
    if($testVar >= $addTapeLevel)
      echo '<div id="add" ></div>';
    if($testVar >= $createBatchLevel)
      echo '<div id="batch" ></div>';
    if($testVar >= $modBatchLevel)
      echo '<div id="mod_batch" ></div>';
    if($testVar >= $runReportLevel)
      echo '<div id="stats"></div>';
    if($testVar >= $modTapeLevel)
      echo '<div id="mod"></div>';
    if($testVar >= $adminLevel)
      echo '<div id="admin"></div>';
    echo '</div>';
    echo '</div>';
    ?>
  </body>
</html>
