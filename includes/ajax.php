<?php>
include('../functions.php');
require_once "../connectdb.php";


#---------------------------------------------------------------
# Great/Generic peice of code for handling ajax request/returns 
# echos a Json string
#---------------------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == 'POST') {	
	$jstring = stripslashes($_POST['func']);
	if($jstring != null && ($fobj = json_decode($jstring))) {
			$func = $fobj->func;
			$res = $func($fobj->args);	
			echo json_encode($res);
	
	}	
	else
		echo "{'error':'json string empty or incorrectly formatted'}";
}
else 
	echo "{'error':'Incorrect Request type!'}";



function getAll() {
    $res = array();
    $res['vendors'] = getVendors();
    $res['loc'] = getLoc();
    $res['mtype'] = getMtype();
    return $res;
}

function getVendors() {
    return getQ("select ID as id, v_name as label from vendors");
}
function getLoc() {
    return getQ("select ID as id, label from locations");
}
function getMtype() {
    return getQ("select ID as id, label from mtype");
}

function getTapes($label) {
    return getQ("select label from tapes where label LIKE '%".$label."%'");
}

function report($args) {
    if($args->tape_id != null && $args->tape_id != '') {
        $q="select * from history where tape_id='".$args->tape_id."' order by date desc";
        return getQ($q);
    }
    else {
        return false;
    } 
}

function get_init_bacth($args) {
    $q ="select * from history where tape_id='".$args->tape_id."'";
    getQ($q);
    
}

function get_rtn_batch() {
    $batchIDs = getReturningBatchIDs();
    $tmp = validReturningBatches($batchIDs);
    $narr = array();
    $label = '';
    foreach($tmp as $key => &$val) {
        foreach($batchIDs as &$val2) {
            if($val2['ID'] == $key) {
                $narr[$key] = array('label' => $val2['label'] ,'tapes'=>$val);
                continue;
            }
        }
        
    }
    return $narr;
}

function serial_test($args) {
    return true;
}

//BAR code Loc ID, date, uname, 
function updateTape($args) {
   $link = dbconnect();
   $tape_info = getQ("select batch_id, batch_num from history where tape_id='".$args->tape_id."' order by date desc limit 1");
   //lookUpTape($tapeBarCode);
  
  // assignTape($tapeBarCode, $locID, $userName, $batchID, $batchCount, $link);
}



function get_user() {
    
}

function getQ($query, $state) {	
	$link = dbconnect();
	if ($query != null) {
    	if($result = mysql_query($query)) {
			if($state) {
				return true;
			}

        	$row_count = mysql_num_rows($result);
        	for ($i = 0; $i < $row_count; $i++) {
            	$list[$i] = mysql_fetch_array($result, MYSQL_ASSOC);
			}
			mysql_close();
			return $list;
		}
		else {
			return $result;
		}
	}
	mysql_close(); return false ;
}

#-------------------------------------------------------------------#
# Takes a KERBEROS or NTLM user name and returns a clean user name  #
# If the username is already clean it won't be manipulated          #
# Returns Fixed Username(no domain no specail characters            #
#-------------------------------------------------------------------#
function uFix($uname) {
        $slash = '/';
        $bslash = '\\';
        if(strpos($uname, $slash)) {
                $tmp = explode($slash, $uname);
                if(($c= count($tmp)) > 0) {
                        $uname = $tmp[$c-1];
                }
        }
        if(strpos($uname, $bslash)) {
                $tmp = explode($bslash, $uname);
                $uname = $tmp[count($tmp)-1];
        }
        $tmp = explode('@', $uname);
        $tmp = explode('/', $tmp[0]);
        $uname = $tmp[0];
}
?>
