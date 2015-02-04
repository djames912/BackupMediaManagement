<div id="admin" class="page_content" style="display: block;">
  <div class="form1">
    <label class="title">Current User:</label>
    <label class="spn"> <?php echo $_SESSION['UserName'] ?></label>
    <label class="title">Access:</label>
    <label class="spn"> <?php echo $_SESSION['AccessLevel'] ?></label>
    <br><br>
    <div align="center">
      <label class="title">Manage Users</label>
    </div>
    <BR>
    <button id="list_users" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">List Users</span>
    </button>
    <button id="add_user" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Add User</span>
    </button>
    <BR><BR>
    <div align="center">
    <label class="title">Manage Types</label>
    </div>
    <BR>
    <button id="add_media" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Add Media Type</span>
    </button>
    <button id="add_location" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Add Location</span>
    </button>
    <button id="add_vendor" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Add Vendor</span>
    </button>
  </div>
  <div  class="results">
  <div id="show_users" class="admdetail"></div>
    <div id="adduser" class="admdetail">
      <label class="title">Add User Form</label>
      <form id="addnewuserform" class="form1">
        <label class="spn">*First Name</label>
        <input class="iput" type="text" name="newuserfirst" value="User given name." onfocus="if(this.value === 'User given name.') this.value = '';">
        <label class="spn">*Last Name</label>
        <input class="iput" type="text" name="newuserlast" value="User last name." onfocus="if(this.value === 'User last name.') this.value = '';">
        <label class="spn">User Name:</label>
        <input class="iput" type="text" name="newusername" value="Enter new user name here." onfocus="if(this.value === 'Enter new user name here.') this.value = '';">
        <label class="spn">Password:</label>
        <input class="iput" type="password" name="newuserpasswd">
        <label class="spn">*Access:</label>
        <select id="accesslvl" name="useraccesslevel" class="iput">
        <?php
          echo '<option value="' . $GLOBALS['adminLevel'] . '">Administrator</option>';
          echo '<option value="' . $GLOBALS['modTapeLevel'] . '">Media Control</option>';
          echo '<option value="' . $GLOBALS['modBatchLevel'] . '" selected>Batch Control</option>';
          echo '<option value="' . $GLOBALS['runReportLevel'] . '">Media/Reports</option>';
          echo '<option value="' . $GLOBALS['createBatchLevel'] . '">Add Batch Only</option>';
          echo '<option value="' . $GLOBALS['addTapeLevel'] . '">Add Media Only</option>';
        ?>
        </select>  
        <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" role="button" aria-disabled="false" >
          <span class="ui-button-text">Submit</span>
        </button>
        <BR><BR>
          <font color="white">* = Required field.</font>
      </form>
    </div>
    <div id="addmedia" class="admdetail">
      <div id="medialist"></div>
      <BR><BR>
      <form id="addmediaform" class="form1">
        <input class="iput" type="text" name="mediatypename" value="Add new media type." onfocus="if(this.value === 'Add new media type.') this.value = '';">
        <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" role="button" aria-disabled="false" >
          <span class="ui-button-text">Submit</span>
        </button>
      </form>
    </div>
    <div id="addloc" class="admdetail">
      <div id="locationlist"></div>
      <BR><BR>
      <form id="addlocationform" class="form1">
        <input class="iput" type="text" name="locationname" value="Add new location." onfocus="if(this.value === 'Add new location.') this.value = '';">
        <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" role="button" aria-disabled="false">
          <span class="ui-button-text">Submit</span>
        </button>
      </form>
    </div>
    <div id="addvendor" class="admdetail">
      <div id="vendorlist"></div>
      <BR><BR>
      <form id="addvendorform" class="form1">
        <input class="iput" type="text" name="vendorname" value="Add new vendor." onfocus="if(this.value === 'Add new vendor.') this.value = '';">
        <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" role="button" aria-disabled="false" >
          <span class="ui-button-text">Submit</span>
        </button>
      </form>
    </div>
  </div>
</div>
