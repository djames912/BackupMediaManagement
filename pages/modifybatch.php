<div id="mod_batch" class="page_content" style="">
  <form id="modifyBatch" method="POST" action="#">
  <div class="form1">
    <div class="div_tab">
      <label class="spn">Date</label>
      <input id="dp" class="iput setDate" type="text">
    </div>
    <div class="div_tab">
      <label class="spn">Locations</label>
      <select class="iput">
        <?php
            $tempLocData = getTableContents('locations');
            foreach($tempLocData['DATA'] as $indLocation)
            {
              echo '<option value=' . "\"$indLocation->ID\">$indLocation->label" . '</option>';
            }
          ?>
      </select>
    </div>
    <div class="div_tab">
      <label class="spn">Tape Id</label>
      <input class="iput" type="text">
    </div>
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button" value="Submit" role="button" aria-disabled="false" disabled="disabled">
      <span class="ui-button-text">Submit</span>
    </button>
    <div id="retbatchlist" class="batch">
    </div>
  </div>
  </form>
  <div id="retbatchmbrs" class="results"></div>
</div>