<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Backup Media Management - Login</title>
    <link rel="stylesheet" type="text/css" media="all" href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script type="text/javascript" src="js/jqueryui/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.18.custom.min.js"></script>
  </head>
  <?php
    require_once "includes/functions.php";
  ?>
  <body onload="document.form1.UserName.focus()">
    <table width="100%" border="0" cellspacing="0">
      <tr>
        <td>
          <div align="center"><font color="#FFFFFF"><b>
              <?php
                echo "$applicationName";
                if($applicationInstance != '' || $applicationInstance = "Production")
                  echo " - $applicationInstance";
              ?>
              </font></b></div>
        </td>
      </tr>
      <tr>
        <td>
          <div align="center"><font color="#FFFFFF">Login</font></div>
        </td>
      </tr>
    </table>
    <br>
    <form name="form1" method="post" action="main.php">
      <table width="75%" border="0" align="center">
        <tr>
          <td width="49%">
            <div align="right"><font color="#FFFFFF">User Name:</font></div>
          </td>
          <td width="51%">
            <input type="text" name="UserName" size="20">
          </td>
        </tr>
        <tr>
          <td width="49%">
            <div align="right"><font color="#FFFFFF">Password:</font></div>
          </td>
          <td width="51%">
            <input type="password" name="Password" size="20">
          </td>
        </tr>
      </table>
      <p align="center">
        <input type="submit" name="Login" value="Login">
      </p>
    </form>
    <p>&nbsp;</p>
  </body>
</html>
