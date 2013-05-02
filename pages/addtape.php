<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Backup Media Management Add Tape</title>
    <link rel="stylesheet" type="text/css" media="all" href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    <script type="text/javascript" src="js/jqueryui/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="js/lgen.js"></script>   
    <script type="text/javascript" src="js/main.js"></script>
     </head>
  <body>
  <div id="addtape" class="page_content" style="display: block;">
    <div class="form1">
      <div class="div_tab">
        <label class="spn">Vendors</label>
        <select class="iput">
          <option value="1">ADSII</option>
          <option value="4">CDW</option>
          <option value="5">Quantum</option>
          <option value="6">Unknown</option>
        </select>
      </div>
      <div class="div_tab">
        <label class="spn">Locations</label>
        <select class="iput">
          <option value="1">Tape Library</option>
          <option value="2">Offsite Storage</option>
          <option value="3">Destroyed</option>
          <option value="4">Basement Storage</option>
          <option value="5">User Possession</option>
          <option value="6">Data Center Cabinet 1</option>
          <option value="7">Data Center Cabinet 2</option>
          <option value="8">Safe 1 (3A Wiring Closet)</option>
          <option value="9">Safe 2 (3C Wiring Closet)</option>
          <option value="10">Safe 3 (3A Wiring Closet)</option>
          <option value="11">Safe 4 (2A Wiring Closet)</option>
          <option value="12">Restore </option>
          <option value="13">Data Center Cabinet 3</option>
        </select>
      </div>
      <div class="div_tab">
        <label class="spn">Media Type</label>
        <select class="iput">
          <option value="1">LTO2</option>
          <option value="2">LTO5</option>
          <option value="3">LTO3</option>
          <option value="4">LTO4</option>
        </select>
      </div>
      <div class="div_tab">
        <label class="spn">Date</label>
        <input id="dp1367008655880" class="iput hasDatepicker" type="text">
      </div>
      <div class="div_tab">
        <label class="spn">PO#</label>
        <input class="iput" type="text">
      </div>
      <div class="div_tab">
        <label class="spn">Tape Id</label>
        <input class="iput" type="text">
      </div>
    </div>
    <div class="results"></div>
  </div>
  </body>
</html>
