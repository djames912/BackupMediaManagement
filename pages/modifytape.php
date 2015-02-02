<div id="modtape" class="page_content" style="display: block;">
  <div class="form1">
    <div class="div_tab">
      <label class="spn">Assign To</label>
      <select id="assignloc" class="iput">
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
      <input id="indmedid" class="iput modIndTapeOnCr" type="text">
    </div>
    <div class="rtext">
      <?php
          echo '<BR>';
          echo '<font color="white">NOTE: If you select "' . $GLOBALS['newTapeLocation'] . '" you will be recycling the media.  Be sure that is what you intend to do.</font>';
          echo '<BR>';
          echo '<font color="white">If that is NOT what you intend to do, select a different location.</font>';
      ?>
    </div>
  </div>
  <div id="indmedassign" class="results"></div>
</div>
