
  <div id="addtape" class="page_content" style="display: block;">
    <form method="POST" action="#">
    <div class="form1">
      <div class="div_tab">
        <label class="spn">Vendors</label>
        <select class="iput">
          <?php
            $tempVenData = getTableContents('vendors');
            foreach($tempVenData['DATA'] as $indVendor)
            {
              echo '<option value=' . "\"$indVendor->ID\">$indVendor->v_name" . '</option>';
            }
            ?>
        </select>
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
        <label class="spn">Media Type</label>
        <select class="iput">
          <?php
            $tempMedType = getTableContents('mtype');
            foreach($tempMedType['DATA'] as $indMedType)
            {
              echo '<option value=' . "\"$indMedType->ID\">$indMedType->label" . '</option>';
            }
          ?>
        </select>
      </div>
      <div class="div_tab">
        <label class="spn">Date</label>
        <input id="dp" class="iput setDate" type="text">
      </div>
      <div class="div_tab">
        <label class="spn">PO#</label>
        <input class="iput" type="text" value="11111" onfocus="if(this.value === '11111') this.value = '';">
      </div>
      <div class="div_tab">
        <label class="spn">Tape Id</label>
        <input class="iput" type="text">
      </div>
    </form>
    </div>
    <div class="results">Results</div>
  </div>

