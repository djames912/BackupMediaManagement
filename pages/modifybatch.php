<div id="mod_batch" class="page_content" style="">
  <div class="form1">
    <!-- <div class="div_tab">
      <label class="spn">Date</label>
      <input  id="checkindate" class="iput setDate" type="text">
    </div> -->
    <div class="div_tab">
      <label class="spn">Locations</label>
      <select id="checkinloc" class="iput">
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
      <label class="spn">Media Id</label>
      <input id="btchmbrid" class="iput batchChkTapeOnCr" type="text">
    </div>
    <button id="checkedbatch" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only green-button admbtn" value="Submit" role="button" disabled="disabled" aria-disabled="false">
      <span class="ui-button-text">Submit</span>
    </button>
    <div id="retbatchlist" class="batch"></div>
  </div>
  <!-- </form> -->
  <div id="retbatchmbrs" class="results"></div>
</div>