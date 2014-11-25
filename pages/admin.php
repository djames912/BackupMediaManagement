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
    <button id="change_password" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Change Password</span>
    </button>
    <button id="edit_user" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Edit User</span>
    </button>
    <BR>
    <button id="del_user" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" aria-disabled="false">
      <span class="ui-button-text">Delete User</span>
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
  <div class="results">
  <div id="show_users" class="admdetail">
    <label class="title">Current Users</label>
    <BR><BR>
    <label class="none">Some test text</label>
    </div>
    <div id="adduser" class="admdetail">
      <label class="title">Add User Form</label>
    </div>
    <div id="chng_passwd" class="admdetail">
      <label class="title">Change Password</label>
    </div>
    <div id="edituser" class="admdetail">
      <label class="title">Edit User Form</label>
  </div>
    <div id="delete_user" class="admdetail">
      <label class="title">Delete User</label>
    </div>
    <div id="addmedia" class="admdetail">
      <label class="title">Add Media</label>
    </div>
    <div id="addloc" class="admdetail">
      <label class="title">Add Location</label>
    </div>
    <div id="addvendor" class="admdetail">
      <label class="title">Add Vendor</label>
    </div>
  </div>
</div>
