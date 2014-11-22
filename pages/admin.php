<div id="admin" class="page_content" style="display: block;">
  <div class="form1">
    <label class="spn">User:</label>
    <label class="spn"> <?php echo $_SESSION['UserName'] ?></label>
    <label class="spn">Access:</label>
    <label class="spn"> <?php echo $_SESSION['AccessLevel'] ?></label>
    <br><br>
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" value="Submit" role="button" aria-disabled="false" disabled="disabled">
      <span class="ui-button-text">Change Password</span>
    </button>
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" value="Submit" role="button" aria-disabled="false" disabled="disabled">
      <span class="ui-button-text">Add User</span>
    </button>
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" value="Submit" role="button" aria-disabled="false" disabled="disabled">
      <span class="ui-button-text">Edit User</span>
    </button>
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" value="Submit" role="button" aria-disabled="false" disabled="disabled">
      <span class="ui-button-text">Delete User</span>
    </button>
  </div>
  <div class="results"></div>
</div>
