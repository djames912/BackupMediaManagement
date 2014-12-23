
  <div id="addtape" class="page_content" style="display: block;">
    <form id="addtapeform" method="POST" action="#">
    <div class="form1">
      <div class="div_tab">
        <label class="spn">Vendor</label>
        <select name="vendor" class="iput">
          <?php
            $tempVenData = getTableContents('vendors');
            foreach($tempVenData['DATA'] as $indVendor)
            {
              if($indVendor->v_name == $GLOBALS['newTapeLocation'])
                echo'<option value=' . "\"$indVendor->ID\" selected>$indVendor->v_name" . '</option>';
              else
                echo '<option value=' . "\"$indVendor->ID\">$indVendor->v_name" . '</option>';
            }
            ?>
        </select>
      </div>
      <div class="div_tab">
        <label class="spn">Location</label>
        <select name="location" class="iput">
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
        <label class="spn">PO#</label>
        <input name="ponumber" class="iput" type="text" value="11111" onfocus="if(this.value === '11111') this.value = '';">
      </div>
      <div class="div_tab">
        <label class="spn">Tape Id</label>
        <input name="tapeid" class="iput submitOnCr" type="text">
      </div>
    </div>
    </form>
    <div id="tprslt" class="results"></div>
  </div>

