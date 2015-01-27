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
    <!-- <script type="text/javascript" src="js/lgen.js"></script>  --> 
    <script type="text/javascript" src="js/main.js"></script>
    <script>$(function() { 
       // In the main.php page, jQuery on load anonymous function:
      $(".batchAddTapeOnCr").each(function(index) 
      {
        add_event(this, events.keyup, batchTapeInputCapture);
       });
      $(".submitOnCr").each(function(index) 
      { 
        add_event(this, events.keyup, tapeInputCapture);
      });
      $(".setDate").datepicker();
      $(".setDate").datepicker("setDate", time_to_text(today()));
      $('#addvendorform').on('submit', function(e)
      {
	addVendor();
	e.preventDefault();
      });
      $('#addmediaform').on('submit', function(e)
      {
	addMedia();
	e.preventDefault();
      });
      $('#addlocationform').on('submit', function(e)
      {
	addLocation();
	e.preventDefault();
      });
      $('#addtapeform').on('submit', function(e)
      {
        addTape();
        e.preventDefault();
      });
      $('#indmediahistory').on('submit', function(e)
      {
        getMediaHistory();
        e.preventDefault();
      });
      /* $('#createbatchform').on('submit', function(e)
      {
        addBatch();
        e.preventDefault();
      }); */
      /*
      $('#submitbatch').click(function(e)
      {
        console.log("Running addBatch()");
        addBatch();
        e.preventDefault();
      });
      */
      $('.mbut').click(function() { 
        $('.mbut').removeClass("msel"); 
        $(this).addClass("msel");
        $(".page_content").hide();
        if(this.id === "Add_TapemMark")
          $("#addtape").show();
        if(this.id === "ReportsmMark")
          $("#reports").show();
        if(this.id == "Create_BatchmMark")
          $("#batch").show();
        if(this.id == "Modify_BatchmMark")
        {
          getReturningBatchIDs();
          $("#mod_batch").show();
        }
        if(this.id == "Modify_TapemMark")
          $("#modtape").show();
        if(this.id == "AdminmMark")
          $("#admin").show();   
      });
      $(".page_content").hide();
        
      $('.admbtn').click(function() { 
        $(".admdetail").hide();
        if(this.id == "list_users")
        {
          //console.log(toJSON(this));
          var displayData = prepData("User List", "generateUserList");
          //console.log(displayData);
          ajaxCall(displayData, listUsersCallback);
          $("#show_users").show();
        }
        if(this.id == "add_user")
        {
          var displayData = prepData("Add User form under development.", "test");
          //console.log(displayData);
          ajaxCall(displayData, adduserCallback);
          $("#adduser").show();
        }
        if(this.id == "add_media")
        {
          //console.log(toJSON(this));
          var displayData = prepData("Media List", "generateMediaList");
          ajaxCall(displayData, listMediaCallback);
          $("#addmedia").show();
        }
        if(this.id == "add_location")
        {
           var displayData = prepData("Location List", "generateLocationList");
           ajaxCall(displayData, listLocationCallback);
          $("#addloc").show();
        }
        if(this.id == "add_vendor")
        {
          var displayData = prepData("Vendor List", "generateVendorList");
          ajaxCall(displayData, listVendorCallback);
          $("#addvendor").show();
        }
        if(this.id == "submitbatch")
        {
          addBatch();
          e.preventDefault();
        }
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
        echo $GLOBALS['applicationName'];
        if($GLOBALS['applicationInstance'] != '' || $GLOBALS['applicationInstance'] == 'Production')
            echo " - " . $GLOBALS['applicationInstance'];
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
      echo $GLOBALS['applicationName'];
      if($GLOBALS['applicationInstance'] != '' || $GLOBALS['applicationInstance'] = "Production")
          echo " -  " . $GLOBALS['applicationInstance'];
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
    if($_SESSION['AccessLevel'] >= $GLOBALS['modBatchLevel'])
      echo '<div id="Modify_BatchmMark" class="mbut">Modify Batch</div>';
    if($_SESSION['AccessLevel'] >= $GLOBALS['createBatchLevel'])
      echo '<div id="Create_BatchmMark" class="mbut">Create Batch</div>';
    if($_SESSION['AccessLevel'] >= $GLOBALS['runReportLevel'])
      echo '<div id="ReportsmMark" class="mbut">Reports</div>';
    if($_SESSION['AccessLevel'] >= $GLOBALS['addTapeLevel'])
      echo '<div id="Add_TapemMark" class="mbut">Add Tape</div>';
    if($_SESSION['AccessLevel'] >= $GLOBALS['modTapeLevel'])
      echo '<div id="Modify_TapemMark" class="mbut">Modify Tape</div>';
    if($_SESSION['AccessLevel'] >= $GLOBALS['adminLevel'])
      echo '<div id="AdminmMark" class="mbut">Admin</div>';
    echo '</div>';
    echo '<div id="main" class="content">';
    if($_SESSION['AccessLevel'] >= $GLOBALS['addTapeLevel'])
      include 'pages/addtape.php';
    if($_SESSION['AccessLevel'] >= $GLOBALS['createBatchLevel'])
      include 'pages/createbatch.php';
    if($_SESSION['AccessLevel'] >= $GLOBALS['modBatchLevel'])
      include 'pages/modifybatch.php';
    if($_SESSION['AccessLevel'] >= $GLOBALS['runReportLevel'])
      include 'pages/reports.php';
    if($_SESSION['AccessLevel'] >= $GLOBALS['modTapeLevel'])
      include 'pages/modifytape.php';
    if($_SESSION['AccessLevel'] >= $GLOBALS['adminLevel'])
      include 'pages/admin.php';
    echo '</div>';
    echo '</div>';
    ?>
    </div>
  </body>
</html>
