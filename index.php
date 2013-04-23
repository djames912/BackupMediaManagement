<?php

session_start();
require_once "includes/functions.php";
$_SESSION['Authenticated'] = false;
$suppliedUserName = $_SERVER['REMOTE_USER'];
$userName = cleanUserName($suppliedUserName);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
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
    <title>Backup Media Management</title>
    <link rel="stylesheet" type="text/css" media="all" href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script type="text/javascript" src="js/jqueryui/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.18.custom.min.js"></script>
  </head>
  <body>
    <div style="width:960px; margin-left:auto; margin-right:auto;">
        <div id="header"> Tape Inventory System </div>
        <div id="menu">
            <div id="Modify BatchmMark" class="mbut">Modify Batch</div>
            <div id="Create BatchmMark" class="mbut">Create Batch</div>
            <div id="ReportsmMark" class="mbut">Reports</div>
            <div id="Add TapemMark" class="msel">Add Tape</div>
            <div id="Modify TapemMark" class="mbut">Modify Tape</div>
            <div id="AdminmMark" class="mbut">Admin</div>
        </div>
        <div id="main" class="content">
            <div id="add" style="display: block;">
            <div id="batch" style="display: none;">
            <div id="mod_batch" style="display: none;">
            <div id="stats" style="display: none;">
            <div id="mod" style="display: none;">
            <div id="admin" style="display: none;"></div>
        </div>
    </div>
  </body>
</html>
