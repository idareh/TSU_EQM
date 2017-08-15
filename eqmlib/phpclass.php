<?php
function getdbname($table,$alias){
	// dbname ถูก declare ใน $_SESSION['dbname'];
	if(!isset($alias)) $alias="";
	
	$dbname = $_SESSION['systemdb'];
	/*
	$alist = $_SESSION['dbname'];
	for($i=0;$i<count($alist);$i++){
		if(strtolower($alist[$i][1])==strtolower($table)){
			$dbname = $alist[$i][2];
			break;
		}
	}*/
	if($alias==""){
		return $dbname.".dbo.".$table;
	}else{
		return $dbname.".dbo.".$table." ".$alias;
	}
}
/*
if ( !function_exists('sys_get_temp_dir') )
{
    // Based on http://www.phpit.net/
    // article/creating-zip-tar-archives-dynamically-php/2/
    function sys_get_temp_dir()
    {
        // Try to get from environment variable
        if ( !empty($_ENV['TMP']) )
        {
            return realpath( $_ENV['TMP'] );
        }
        else if ( !empty($_ENV['TMPDIR']) )
        {
            return realpath( $_ENV['TMPDIR'] );
        }
        else if ( !empty($_ENV['TEMP']) )
        {
            return realpath( $_ENV['TEMP'] );
        }

        // Detect by creating a temporary file
        else
        {
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
            if ( $temp_file )
            {
                $temp_dir = realpath( dirname($temp_file) );
                unlink( $temp_file );
                return $temp_dir;
            }
            else
            {
                return FALSE;
            }
        }
    }
}
function my_tempnam($prefix = null, $suffix = null, $dir = null)
{
    func_num_args() > 3 and exit(__FUNCTION__.'(): passed '.func_num_args().' args, should pass 0, 1, 2, or 3 args.  Usage: '.__FUNCTION__.'(optional filename prefix, optional filename suffix, optional directory)');

    $prefix = trim($prefix);
    $suffix = trim($suffix);
    $dir = trim($dir);

    empty($dir) and $dir = trim(sys_get_temp_dir());
    empty($dir) and exit(__FUNCTION__.'(): could not get system temp dir');
    is_dir($dir) or exit(__FUNCTION__."(): \"$dir\" is not a directory");
    
    //    posix valid filename characters. exclude "similar" characters 0, O, 1, l, I to enhance readability. add - _
    $fn_chars = array_flip(array_diff(array_merge(range(50,57), range(65,90), range(97,122), array(95,45)), array(73,79,108)));

    //  create random filename 20 chars long for security
    for($fn = rtrim($dir, '/') . '/' . $prefix, $loop = 0, $x = 0; $x++ < 20; $fn .= chr(array_rand($fn_chars)));
    while (file_exists($fn.$suffix))
    {
        $fn .= chr(array_rand($fn_chars));
        $loop++ > 10 and exit(__FUNCTION__."(): looped too many times trying to create a unique file name in directory \"$dir\"");
        clearstatcache();
    }

    $fn = $fn.$suffix;
    touch($fn) or exit(__FUNCTION__."(): could not create tmp file \"$fn\"");
    return $fn;
}
function tsd_tempnam($name,$printwhen,$suffix){
	return $name."-".substr($printwhen,0,10)."-".substr($printwhen,11,2)."-".substr($printwhen,14,2)."-".substr($printwhen,17,2)."-".substr($printwhen,20,3).$suffix;
}
function isnumber($str){
	$retval = 1;
	for ($i=0;$i<strlen($str);$i++){
		if(is_numeric($str[$i])==""){
			$retval = 0;
			break;
		}
	}
	return $retval;
}

function globalvars(){
	echo "var userid=".$_SESSION['userid'].";\n";
	echo "var userlogid=".$_SESSION['userlogid'].";\n";
	echo "var dsn='".$_SESSION['dsn']."';\n";
	echo "var systemdb='".$_SESSION['systemdb']."';\n";
return true;
}

function sqlexec($conn,$cmd){
	return odbc_Exec($conn,$cmd);
}

function sqlresult($result,$colname){
	return odbc_result($result,$colname);
}

function sqlconnect($dsn){
	if(!isset($dsn)){
		$dsn = $_SESSION['dsn'];
	}
	if (!isset($_SESSION['dbuser'])) {
		$user	= 'tsd';
		$pwd		= 'windows';
	} else {
		$user	= $_SESSION['dbuser'];
		$pwd		= $_SESSION['pwd'];		
	}
	$conn	=  odbc_connect($dsn,$user,$pwd);
	return $conn;
}

function sqlinsert($query,$dsn) {
	$conn	= sqlconnect($dsn);
	$query = str_replace("~",";",$query);
	// searchfor,replacewith,instr
	$result  =  odbc_Exec($conn,  $query);		
	$cmd		= "SELECT @@IDENTITY AS lastid";
	$result  =  odbc_Exec($conn,  $cmd);		
	$retval	= -1;
	while(odbc_fetch_row($result)) {
		$retval = odbc_result($result, "lastid");
	}
	odbc_close($conn);
	return $retval; 
} 

function newsqlpt($cmd,$dsn) {
	$conn	= sqlconnect($dsn);
	$query = $cmd;
	//$query = str_replace("~",";",$query);
	$result  =  odbc_Exec($conn,  $query);		
	$cols		= odbc_num_fields($result);
	$i			= 0;
	$mycols = "[";
	for ($j=1;$j<=$cols;$j++){
		$field_name	= odbc_field_name($result,$j);
		$field_type		= odbc_field_type($result,$j);
		if (($field_type=="varchar") || ($field_type=="char") ||($field_type=="decimal" )) {
			if ($field_type=="decimal"){
				$field_len = odbc_field_len($result,$j).",".odbc_field_scale($result,$j); 
			} else {
				$field_len	= odbc_field_len($result,$j); 
			}
		} else {
			$field_len	= "-";
		}
		$mycols			= $mycols."['".strtolower($field_name)."','".strtolower($field_type)."','".$field_len."'],";
	}
	$mycols = substr($mycols,0,strlen($mycols)-1)."]";

	$myrows	= "[";
	while(odbc_fetch_row($result))
	{
		$j = 1;     
		$myrows = $myrows."[";
		while($j <= $cols)
		{        
			$field_name	= odbc_field_name($result,$j);
			$field_value	= odbc_result($result, $field_name);
			if(strtolower(odbc_field_type($result,$j))=='text'){
				$value = odbc_memo($field_value);
			}else{
				$value =  rtrim(odbc_result($result, $field_name));
			}
//					$value = str_ireplace("'", "\\",$value);
			$value = str_ireplace("[","(",$value);
			$value = str_ireplace("]",")",$value);
			$myrows = $myrows."'".$value."',";
			$j++;
		}
		$myrows = substr($myrows,0,strlen($myrows)-1)."],";
		$i++;
	}      
	if ($myrows == "[" ) { 
		$myrows = "[[]]";
	} else {
		$myrows = substr($myrows,0,strlen($myrows)-1)."]";
	}
	odbc_close($conn);
	return strlen($mycols).$mycols.strlen($myrows).$myrows;
}


function sqlpt($cmd,$dsn) {
	$conn	= sqlconnect($dsn);
	// ไม่มีการต่อคำสั่ง 
	if (strpos($cmd,";") == false) {
		$cmd_array = array($cmd);
	} else {
		$cmd_array = explode(";",$cmd);
	}
	for ($cmdcount=0;$cmdcount<count($cmd_array);$cmdcount++){
		$query = $cmd_array[$cmdcount];
		if ($cmdcount < count($cmd_array)-1) {
			if (substr($query,0,1) == "=") {
				$query = substr($query,1,strlen($query)-1);
			}
			$query = str_replace("~",";",$query);
			odbc_Exec($conn,  $query);
		} else {
			if (substr($query,0,1) == "=") {
				$query = substr($query,1,strlen($query)-1);
				$query = str_replace("~",";",$query);
				odbc_Exec($conn,  $query);
				odbc_close($conn);
				return "";
			}
			$query = str_replace("~",";",$query);
			$result  =  odbc_Exec($conn,  $query);		
			if(!$result){
				$text = odbc_errormsg();
				$retval = str_replace("[Microsoft][ODBC SQL Server Driver][SQL Server]","",$text)."\n";
				$retval .= $query."\n";			
				odbc_close($conn);
				return $retval;
			}
			$cols		= odbc_num_fields($result);
			$i			= 0;
			$mycols = "[";
			for ($j=1;$j<=$cols;$j++){
				$field_name	= odbc_field_name($result,$j);
				$field_type		= odbc_field_type($result,$j);
				if (($field_type=="varchar") || ($field_type=="char") ||($field_type=="decimal" )) {
					if ($field_type=="decimal"){
						$field_len = odbc_field_len($result,$j).",".odbc_field_scale($result,$j); 
					} else {
						$field_len	= odbc_field_len($result,$j); 
					}
				} else {
					$field_len	= "-";
				}
				$mycols			= $mycols."['".strtolower($field_name)."','".strtolower($field_type)."','".$field_len."'],";
			}
			$mycols = substr($mycols,0,strlen($mycols)-1)."]";

			$myrows	= "[";
			while(odbc_fetch_row($result))
			{
				$j = 1;     
				$myrows = $myrows."[";
				while($j <= $cols)
				{        
					$field_name	= odbc_field_name($result,$j);
					$field_value	= odbc_result($result, $field_name);
					if(strtolower(odbc_field_type($result,$j))=='text'){
						$value = odbc_memo($field_value);
					}else{
						$value =  rtrim(odbc_result($result, $field_name));
					}
//					$value = str_ireplace("'", "\\",$value);
					$value = str_ireplace("[","(",$value);
					$value = str_ireplace("]",")",$value);
					$myrows = $myrows."'".$value."',";
					$j++;
				}
				$myrows = substr($myrows,0,strlen($myrows)-1)."],";
				$i++;
			}      
			if ($myrows == "[" ) { 
				$myrows = "[[]]";
			} else {
				$myrows = substr($myrows,0,strlen($myrows)-1)."]";
			}
			odbc_close($conn);
			return strlen($mycols).$mycols.strlen($myrows).$myrows;
		}
	}
}

function sqldirect($cmd,$dsn) {
	$conn	= sqlconnect($dsn);
	// ไม่มีการต่อคำสั่ง 
	if (strpos($cmd,";") == false) {
		$cmd_array = array($cmd);
	} else {
		$cmd_array = explode(";",$cmd);
	}
	for ($cmdcount=0;$cmdcount<count($cmd_array);$cmdcount++){
		$query = $cmd_array[$cmdcount];
		if ($cmdcount < count($cmd_array)-1) {
			if (substr($query,0,1) == "=") {
				$query = substr($query,1,strlen($query)-1);
			}
			$query = str_replace("~",";",$query);
			odbc_Exec($conn,  $query);
		} else {
			if (substr($query,0,1) == "=") {
				$query = substr($query,1,strlen($query)-1);
				$query = str_replace("~",";",$query);
				odbc_Exec($conn,  $query);
				odbc_close($conn);
				return "";
			}
			$query = str_replace("~",";",$query);
			$result  =  odbc_Exec($conn,  $query);		
			$cols		= odbc_num_fields($result);
			$i			= 0;
			$mycols = "[";
			for ($j=1;$j<=$cols;$j++){
				$field_name	= odbc_field_name($result,$j);
				$field_type		= odbc_field_type($result,$j);
				if (($field_type=="varchar") || ($field_type=="char") ||($field_type=="decimal" )) {
					if ($field_type=="decimal"){
						$field_len = odbc_field_len($result,$j).",".odbc_field_scale($result,$j); 
					} else {
						$field_len	= odbc_field_len($result,$j); 
					}
				} else {
					$field_len	= "-";
				}
				$mycols			= $mycols."['".strtolower($field_name)."','".strtolower($field_type)."','".$field_len."'],";
			}
			$mycols = substr($mycols,0,strlen($mycols)-1)."]";

			$myrows	= "[";
			while(odbc_fetch_row($result))
			{
				$j = 1;     
				$myrows = $myrows."[";
				while($j <= $cols)
				{        
					$field_name	= odbc_field_name($result,$j);
					$field_value	= odbc_result($result, $field_name);
					if(strtolower(odbc_field_type($result,$j))=='text'){
						$value = odbc_memo($field_value);
					}else{
						$value =  rtrim(odbc_result($result, $field_name));
					}
//					$value = str_ireplace("'", "\\",$value);
					$value = str_ireplace("[","(",$value);
					$value = str_ireplace("]",")",$value);
					$myrows = $myrows."'".$value."',";
					$j++;
				}
				$myrows = substr($myrows,0,strlen($myrows)-1)."],";
				$i++;
			}      
			if ($myrows == "[" ) { 
				$myrows = "[[]]";
			} else {
				$myrows = substr($myrows,0,strlen($myrows)-1)."]";
			}
			odbc_close($conn);
			return $myrows;
		}
	}
}


function odbc_memo($str) {
 $out="";
 for($a=0; $a<strlen($str); $a++) 
	 {
		if ($str[$a]=="'") {
			$out.="'";
		} else
		if ($str[$a]!=chr(13)) {
			if ($str[$a]!=chr(10)) {
			$out.=$str[$a];
			}
		}else{
			$out.="\n";
		}
 }
 return $out; 
} 


function tsddecode($query){
$query		= stripslashes($query);
$query		= unescape($query);
return $query;
}

function code2utf($num){
  if($num<128) 
   return chr($num);
  if($num<1024) 
   return chr(($num>>6)+192).chr(($num&63)+128);
  if($num<32768) 
   return chr(($num>>12)+224).chr((($num>>6)&63)+128)
         .chr(($num&63)+128);
  if($num<2097152) 
   return chr(($num>>18)+240).chr((($num>>12)&63)+128)
         .chr((($num>>6)&63)+128).chr(($num&63)+128);
  return '';
}

function code2thai($num){
	$num = $num-3424;
	if ($num == 161) return 'ก';
	if ($num == 162) return 'ข';
	if ($num == 163) return 'ฃ';
	if ($num == 164) return 'ค';
	if ($num == 165) return 'ฅ';
	if ($num == 166) return 'ฆ';
	if ($num == 167) return 'ง';
	if ($num == 168) return 'จ';
	if ($num == 169) return 'ฉ';
	if ($num == 170) return 'ช';
	if ($num == 171) return 'ซ';
	if ($num == 172) return 'ฌ';
	if ($num == 173) return 'ญ';
	if ($num == 174) return 'ฎ';
	if ($num == 175) return 'ฏ';
	if ($num == 176) return 'ฐ';
	if ($num == 177) return 'ฑ';
	if ($num == 178) return 'ฒ';
	if ($num == 179) return 'ณ';
	if ($num == 180) return 'ด';
	if ($num == 181) return 'ต';
	if ($num == 182) return 'ถ';
	if ($num == 183) return 'ท';
	if ($num == 184) return 'ธ';
	if ($num == 185) return 'น';
	if ($num == 186) return 'บ';
	if ($num == 187) return 'ป';
	if ($num == 188) return 'ผ';
	if ($num == 189) return 'ฝ';
	if ($num == 190) return 'พ';
	if ($num == 191) return 'ฟ';
	if ($num == 192) return 'ภ';
	if ($num == 193) return 'ม';
	if ($num == 194) return 'ย';
	if ($num == 195) return 'ร';
	if ($num == 196) return 'ฤ';
	if ($num == 197) return 'ล';
	if ($num == 198) return 'ฦ';
	if ($num == 199) return 'ว';
	if ($num == 200) return 'ศ';
	if ($num == 201) return 'ษ';
	if ($num == 202) return 'ส';
	if ($num == 203) return 'ห';
	if ($num == 204) return 'ฬ';
	if ($num == 205) return 'อ';
	if ($num == 206) return 'ฮ';
	if ($num == 207) return 'ฯ';
	if ($num == 208) return 'ะ';
	if ($num == 209) return 'ั';
	if ($num == 210) return 'า';
	if ($num == 211) return 'ำ';
	if ($num == 212) return 'ิ';
	if ($num == 213) return 'ี';
	if ($num == 214) return 'ึ';
	if ($num == 215) return 'ื';
	if ($num == 216) return 'ุ';
	if ($num == 217) return 'ู';
	if ($num == 218) return 'ฺ';
	if ($num == 223) return '฿';
	if ($num == 224) return 'เ';
	if ($num == 225) return 'แ';
	if ($num == 226) return 'โ';
	if ($num == 227) return 'ใ';
	if ($num == 228) return 'ไ';
	if ($num == 229) return 'า';
	if ($num == 230) return 'ๆ';
	if ($num == 231) return '็';
	if ($num == 232) return '่';
	if ($num == 233) return '้';
	if ($num == 234) return '๊';
	if ($num == 235) return '๋';
	if ($num == 236) return '์';
	if ($num == 237) return 'ํ';
	if ($num == 238) return 'า';
	if ($num == 240) return '๐';
	if ($num == 241) return '๑';
	if ($num == 242) return '๒';
	if ($num == 243) return '๓';
	if ($num == 244) return '๔';
	if ($num == 245) return '๕';
	if ($num == 246) return '๖';
	if ($num == 247) return '๗';
	if ($num == 248) return '๘';
	if ($num == 249) return '๙';
	if ($num == 4796) return '"';
	if ($num == 4797) return '"';
	if ($num == 4806) return "...";
	if ($num == 4787) return "-";
//	return code2utf($num);
	return '['.$num.']';
}

//	if ($num == 4792) return "''";
//	if ($num == 4793) return "''";
//	if ($num == 4792) return "\'";
//	if ($num == 4793) return "\'";


function unescape($strIn, $iconv_to = 'UTF-8') {
  $strOut = '';
  $iPos = 0;
  $len = strlen ($strIn);
  while ($iPos < $len) {
   $charAt = substr ($strIn, $iPos, 1);
   if ($charAt == '%') {
     $iPos++;
     $charAt = substr ($strIn, $iPos, 1);
     if ($charAt == 'u') {
       // Unicode character
       $iPos++;
       $unicodeHexVal = substr ($strIn, $iPos, 4);
       $unicode = hexdec ($unicodeHexVal);
//	   if ($unicode > 1024 or $unicode < 2097152)
		if ($unicode > 1024 or $unicode < 3674)
		 $strOut .= code2thai($unicode);
	   else
		 $strOut .= code2utf($unicode);
       $iPos += 4;
     }
     else {
       // Escaped ascii character
       $hexVal = substr ($strIn, $iPos, 2);
       if (hexdec($hexVal) > 127) {
         // Convert to Unicode 
         $strOut .= code2utf(hexdec ($hexVal));
       }
       else {
         $strOut .= chr (hexdec ($hexVal));
       }
       $iPos += 2;
     }
   }
   else {
     $strOut .= $charAt;
     $iPos++;
   }
  }
//  if ($iconv_to != "UTF-8") {
//  $strOut = iconv("UTF-8", $iconv_to, $strOut);
//  }  
  return $strOut;
} 

function tsdnumber_format($num,$dec,$dec_point=".",$thousands=","){
	if($num=="") return "";
	$num = (float) $num;
	return number_format($num,$dec,$dec_point,$thousands);
}

function numtrim($str,$decimals=10,$minimum=0,$dec_point=".",$thousands=","){
	$fstr = tsdnumber_format($str,$decimals,$dec_point,$thousands);
	if ($decimals==0){ return $fstr; }
	$astr = explode( $dec_point,$fstr );
	$decpart = $astr[1];	// 7870000000
	while (true){
		if ((strlen($decpart)==0) || (substr($decpart,-1) != "0")) { 
			break; 
		}
		$decpart = substr($decpart,0,strlen($decpart)-1);
	}
	while (strlen($decpart)<$minimum){
		$decpart .= "0";
	}
	if ($decpart==""){
		return $astr[0];
	}else{
		return $astr[0].".".$decpart;
	}
}



function addcomma($str){
	$val = floatval($str);
	$newstr = strval($val);
	$part = explode(".",$newstr);
	$whole = number_format($part[0]);
	if ($part[1]==""){
		return $whole;
	}else{
		return $whole.".".$part[1];
	}
}

function days_in_month($month, $year) { 
	// calculate number of days in a month 
	return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
} 

function engdate($cdate){
	$dt		= explode(" ",$cdate);
	$date	= explode("-",$dt[0]);
	$time	= $dt[1];
	return $date[2]."/".$date[1]."/".$date[0];
}

function engsdate($cdate){
	$dt		= explode(" ",$cdate);
	$date	= explode("-",$dt[0]);
	$time	= $dt[1];
	return $date[2]."/".$date[1]."/".substr($date[0],2,2);
}

	function thaisdate($date){
		$year = substr($date,0,4);
		$year = $year+543;
		$year = substr($year,2,2);

		$month = substr($date,5,2);

		$day = intval(substr($date,8,2));
//		$amonth = array("มกราคม","กุมภาพันธ์","มนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		$month = $month;
		return strzero($day,2)."/".strzero($month,2)."/".$year;
	}

	function thaildate($date){
		$year = substr($date,0,4);
		$year = $year+543;
		//$year = substr($year,2,2);

		$month = substr($date,5,2);

		$day = intval(substr($date,8,2));
//		$amonth = array("มกราคม","กุมภาพันธ์","มนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		$month = $month;
		return strzero($day,2)."/".strzero($month,2)."/".$year;
	}
	
	function thaidate($date){
		$day = intval(substr($date,3,2));
		$month = substr($date,0,2);
		$year = substr($date,6,4);
		$year = $year+543;
//		$year = substr($year,2,2);
		$amonth = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		$month = $month-1;
		return strzero($day,2)."/".strzero($month,2)."/".$year;
	}

	function thail_date($date){
		// 2007-10-01
		$year = substr($date,0,4);
		$year = $year+543;
//		$year = substr($year,2,2);

		$month = substr($date,5,2);

		$day = intval(substr($date,8,2));
		$amonth = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		$month = $month-1;
		return $day." ".$amonth[$month]." ".$year;
	}

	function iif($what,$if_true,$if_false)	{
		if ($what){
			return $if_true;
		} else {
			return $if_false;
		}
	}

	function monthyear($date){
		$year = substr($date,0,4);
		$year = $year+543;
//		$year = substr($year,2,2);

		$month = substr($date,5,2);

		$day = intval(substr($date,8,2));
		$amonth = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
		$month = $month-1;
		return $amonth[$month]." ".$year;

	}

	function engmonthyear($date){
		$year = substr($date,0,4);
		$year = $year;
//		$year = substr($year,2,2);

		$month = substr($date,5,2);

		$day = intval(substr($date,8,2));
		$amonth = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$month = $month-1;
		return $amonth[$month]." ".$year;

	}

	function at($what,$inwhat){
		$test = strpos($inwhat,$what);
		if(is_numeric($test)){
			$retval = $test;
		}else{
			$retval = -1;
		}
		return $retval;
	}

	function strzero($num,$len){
		return substr("00000000".$num,$len*-1);
	}

	function dateadd($date,$days){
		$year = substr($date,0,4);
		$month = substr($date,5,2);
		$day = intval(substr($date,8,2));
		$realdate = mktime(0,0,0,$month,$day,$year);
		$realdate = $realdate + (((60*60)*24)*($days+1));
		$adate = getdate($realdate);
		return $adate["year"]."-".strzero($adate["mon"],2)."-".strzero($adate["mday"],2)." ".strzero($adate["hours"],2).":".strzero($adate["minutes"],2).":".strzero($adate["seconds"],2).".000";
	}

	function cleanquote($str){
		if($str==""){
			return $str;
		}
		return str_replace("'","''",$str);
	}

	function	updatelog_add($lcdbname,$lnid){
		$username	= $_SESSION['username'];
		$dsn		= $_SESSION['dsn'];
		$user	= $_SESSION['dbuser'];
		$pwd	= $_SESSION['pwd'];
		$conn	=  odbc_connect($dsn,$user,$pwd);
		if($lnid==""){
			return true;
		}
		
		$apart = explode(".",$lcdbname);
		$cmd = "select id from ".$apart[0].".".$apart[1].".sysobjects where name='".$apart[2]."'";
		$chkobj = odbc_Exec($conn,$cmd);
		$cmd = "select id from ".$apart[0].".".$apart[1].".syscolumns where id=".odbc_result($chkobj,"id")." and name='addwhen'";
		$chkcol = odbc_Exec($conn,$cmd);

		if(odbc_result($chkcol,"id") != ""){
			$cmd = "select addwhen from ".$lcdbname." where id=".$lnid;
		}else{
			$cmd = "select id from ".$apart[0].".".$apart[1].".syscolumns where id=".odbc_result($chkobj,"id")." and name='updatewhen'";
			$chkcol = odbc_Exec($conn,$cmd);
			if(odbc_result($chkcol,"id") != ""){
				$cmd = "select updatewhen as addwhen from ".$lcdbname." where id=".$lnid;
			}else{
				$cmd = "";
			}
		}
		
		if (getenv(HTTP_X_FORWARDED_FOR)){
			$nodeaddress=getenv(HTTP_X_FORWARDED_FOR);
		}else{
			$nodeaddress=getenv(REMOTE_ADDR);
		}

		if($cmd != ""){
			$chk = odbc_Exec($conn,$cmd);
			$updatelog = "'".thaidatetime(odbc_result($chk,"addwhen"))." เพิ่มโดย ".$username." ที่เครื่อง ".$nodeaddress."'\n";
			$cmd = "update ".$lcdbname." set updatelog=".$updatelog." where id=".$lnid;

			odbc_Exec($conn,$cmd);
		}
		
		return true;
	}
	
	
	function thaidatetime( $date	) {
		//var	ndate, sdate, day, month, year;
		if ($date ==	"")
		{
			return "";
		}
		$ndate =	explode(" ",$date); 
		$sdate =	explode("-",$ndate[0]);   
		$year = $sdate[0]+543;
		//$year = String($year);
		$month =	$sdate[1];
		$day	= $sdate[2];

		$date = $day."/".$month."/".$year." ".substr($ndate[1],0,8);
		return $date;
	}


function mysqlpt($cmd) {
	$conn = new mysqli('localhost', 'root', 'bytsd', '');
	if ($conn->connect_error) {
		die('Connect Error (' . $conn->connect_errno . ') '
				. $conn->connect_error);
	}
//	$charset = "SET character_set_results=tis620";
//	$result->$conn->query($charset);
	//mysqli_query("SET NAMES TIS620");
	$result  =  $conn->query($cmd);
	$mycols = "[";
	$acol	= array();
	while ($finfo = mysqli_fetch_field($result)) {
		$ft = $finfo->type;
		if($ft==1){
			$cft = "tinyint";
		}else if($ft==2){
			$cft = "smallint";
		}else if($ft==3){
			$cft = "int";
		}else if($ft==4){
			$cft = "float";
		}else if($ft==5){
			$cft = "double";
		}else if($ft==7){
			$cft = "timestamp";
		}else if($ft==8){
			$cft = "bigint";
		}else if($ft==9){
			$cft = "mediumint";
		}else if($ft==10){
			$cft = "date";
		}else if($ft==11){
			$cft = "time";
		}else if($ft==12){
			$cft = "datetime";
		}else if($ft==13){
			$cft = "year";
		}else if($ft==252){
			$cft = "text";
		}else if($ft==253){
			$cft = "char";
		}else if($ft==254){
			$cft = "char";
		}else{
			$cft = "??";
		}	
		$mycols			= $mycols."['".strtolower($finfo->name)."','".$cft."','".$finfo->max_length."'],";
		$acol[count($acol)] = strtolower($finfo->name);
	}	
	$mycols = substr($mycols,0,strlen($mycols)-1)."]";

	$myrows	= "[";
	$result->data_seek(0);
	while ($row = $result->fetch_assoc()){
		$j = 1;     
		$myrows = $myrows."[";
		for($j=0;$j< count($acol);$j++){
			$value	= trim( $row[$acol[$j]] );
			//$value = str_ireplace("'", "\\",$value);
			$value = str_ireplace("[","(",$value);
			$value = str_ireplace("]",")",$value);
			$myrows = $myrows."'".$value."',";
		}
		$myrows = substr($myrows,0,strlen($myrows)-1)."],";
	}      
	if ($myrows == "[" ) { 
		$myrows = "[[]]";
	} else {
		$myrows = substr($myrows,0,strlen($myrows)-1)."]";
	}
	$result->close();
	$conn->close();
//	return $myrows;
	return strlen($mycols).$mycols.strlen($myrows).$myrows;
}

function sqlstr($str,$type="str"){
	if($type=="n" || $type=="num"){
		return iif($str=="","null",$str);
	}else{
		return iif($str=="","null","'".cleanquote($str)."'");		
	}
}
	*/
?>