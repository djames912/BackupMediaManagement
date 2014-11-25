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
    <script>$(function() { 
      $('.mbut').click(function() { 
        $('.mbut').removeClass("msel"); 
        $(this).addClass("msel");
        $(".page_content").hide();
        if(this.id == "Add_TapemMark")
          $("#addtape").show();
        if(this.id == "ReportsmMark")
          $("#reports").show();
        if(this.id == "Create_BatchmMark")
          $("#batch").show();
        if(this.id == "Modify_BatchmMark")
          $("#mod_batch").show();
        if(this.id == "Modify_TapemMark")
          $("#modtape").show();
        if(this.id == "AdminmMark")
          $("#admin").show();
      });
      $(".page_content").hide();
        
      $('.admbtn').click(function() { 
        $(".admdetail").hide();
        console.log(this);
        if(this.id == "list_users")
          $("#show_users").show();
        if(this.id == "add_user")
          $("#adduser").show();
        if(this.id == "change_password")
          $("#chng_passwd").show();
        if(this.id == "edit_user")
          $("#edituser").show();
        if(this.id == "del_user")
          $("#delete_user").show();
        if(this.id == "add_media")
          $("#addmedia").show();
        if(this.id == "add_location")
          $("#addloc").show();
        if(this.id == "add_vendor")
          $("#addvendor").show();
      });
      $(".admdetail").hide();
    });</script>
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
        if($applicationInstance != '' || $applicationInstance == 'Production')
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
    
    echo '<div style="width:960px; margin-left:auto; margin-right:auto;">';
    echo '<div id="header"> Tape Inventory System </div>';
    echo '<div id="menu">';
    if($_SESSION['AccessLevel'] >= $modBatchLevel)
      echo '<div id="Modify_BatchmMark" class="mbut">Modify Batch</div>';
    if($_SESSION['AccessLevel'] >= $createBatchLevel)
      echo '<div id="Create_BatchmMark" class="mbut">Create Batch</div>';
    if($_SESSION['AccessLevel'] >= $runReportLevel)
      echo '<div id="ReportsmMark" class="mbut">Reports</div>';
    if($_SESSION['AccessLevel'] >= $addTapeLevel)
      echo '<div id="Add_TapemMark" class="mbut">Add Tape</div>';
    if($_SESSION['AccessLevel'] >= $modTapeLevel)
      echo '<div id="Modify_TapemMark" class="mbut">Modify Tape</div>';
    if($_SESSION['AccessLevel'] >= $adminLevel)
      echo '<div id="AdminmMark" class="mbut">Admin</div>';
    echo '</div>';
    echo '<div id="main" class="content">';
    if($_SESSION['AccessLevel'] >= $addTapeLevel)
      include 'pages/addtape.php';
    if($_SESSION['AccessLevel'] >= $createBatchLevel)
      include 'pages/createbatch.php';
    if($_SESSION['AccessLevel'] >= $modBatchLevel)
      include 'pages/modifybatch.php';
    if($_SESSION['AccessLevel'] >= $runReportLevel)
      include 'pages/reports.php';
    if($_SESSION['AccessLevel'] >= $modTapeLevel)
      include 'pages/modifytape.php';
    if($_SESSION['AccessLevel'] >= $adminLevel)
      include 'pages/admin.php';
    echo '</div>';
    echo '</div>';
    ?>
    </div>
  </body>
</html>
