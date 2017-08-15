<?php 
		/*$mysession =	$_GET['mysession'];
	if ( empty($mysession) ) {
		$mysession =	$_POST['mysession'];
		if ( empty($mysession) ) {
			$micro  = microtime();
			$micro = str_replace(" ","",$micro);    // strip out the blanks
			$micro = str_replace(".","",$micro);    // strip out the periods
			$mysession = "tsdapp" . $micro;
		}	
	}
	session_name($mysession);
	session_start();
	*/
	
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	// always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0

	echo "<script>\n";
	echo "var mysession='".$mysession."';\n";
	if (!isset($_SESSION['userid'])){
		echo "var userid='';\n";
	}else{
		echo "var userid='".$_SESSION['userid']."';\n";
	}
	echo "var libpath='".$_SESSION['libpath']."';\n";
	echo "var commonpath='".$_SESSION['commonpath']."';\n";	
	echo "</script>\n";

	function onStartElement($parser, $tagname, $att){
		global $current_tag;
		$current_tag = $tagname;
	}

	function onCData($parser, $cdata) {
		global $current_tag;
		global $datas;
		if ($current_tag) {
			$datas[$current_tag] = $cdata;
		}
	}

	function onEndElement($parser, $tagname) {
		global $current_tag;
		$current_tag = null;
	}
	
	function setjs($dirname,$js,$cpath){
		if($cpath==""){
			$cdir = "/";
		}else{
			$cdir = "/..".$cpath;
		}
		foreach($js as $filename) {
			if($cdir=="/"){
				$filemtime = filemtime( $filename);
			}else{
				$filemtime = filemtime( "/webroot/".$cpath . $filename);
			}
			echo "<script type='text/javascript' language='javascript' src='$cpath$filename?$filemtime'></script>\n";
		}	
	}
	
	function setcss($dirname,$css,$cpath){
		if($cpath==""){
			$cdir = "/";
		}else{
			$cdir = "/..".$cpath;
		}
		foreach($css as $filename) {
			if($cdir=="/"){
				$filemtime = filemtime( $filename);
			}else{
				$filemtime = filemtime( "/webroot/".$cpath . $filename);
			}
			echo "<link href='$cpath$filename?$filemtime' rel='stylesheet'></link>\n";
		}	
	}

	function tsdgetcurdir($dirstr){
		$retval = substr(str_replace("\\","/",$dirstr),2);
		$retval = str_replace("/webroot","",$retval);
		return $retval;
	}


	if(isset($_POST["userno"])){
		$userno		= $_POST['userno'];
		$password	= $_POST['password'];
		if(strpos($password," or ")){
			$password = "badhacker";
			header("Location: http://www.royalthaipolice.go.th/");
		}
		$login_status = "";
	/* อ่าน config */
		$current_tag = null;
		$datas = Array();

		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "onStartElement", "onEndElement");
		xml_set_character_data_handler($xml_parser, "onCData");

		$file = "system/config.xml";
		if (!($fp = fopen($file, "r"))){
			$login_status = "ไม่สามารถเปิดแฟ้มเก็บค่าของระบบได้ / Cannot open system configuration file.";
		}else{
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					$login_status = "XML error : ". xml_error_string(xml_get_error_code($xml_parser))." at line ".xml_get_current_line_number($xml_parser);
					break;
				 }
			 }
			if($login_status == ""){
				xml_parser_free($xml_parser);
				$dsn 		= $datas['DSN'];
				$user 		= $datas['USER'];
				$pwd	 	= $datas['PWD'];
				$systemdb = $datas['DBSYSTEM'];
				$tempdb 	= $datas['DBTEMP'];
				$appid 		= $datas['APPID'];
				$sqlbackuppath = $datas['SQLBACKUPPATH'];
				$backupdownloadpath = $datas['BACKUPDOWNLOADPATH'];
				$libpath				= $datas["LIBPATH"];
				$commonpath	= $datas["COMMONPATH"];
				$conn		=  odbc_connect($dsn,$user,$pwd);
				$cmd		= "SELECT id,name,issupervisor,usergroupid FROM ".$systemdb.".dbo.users WHERE no='".$userno."' AND password='".$password."'";
				$result  	=  odbc_Exec($conn,$cmd);		
				$rows		= odbc_num_rows($result);
				if(odbc_result($result, "id")==""){
					$login_status = "รหัสผู้ใช้ และ/หรือ รหัสผ่านไม่ถูกต้อง<br>Incorrect User ID. and/or password.";
				}else if(odbc_result($result, "usergroupid")==16){
					$login_status = "คุณไม่มีสิทธิ์เข้าใช้งานระบบ ERP ";
				}
			}
		}
		if($login_status == ""){
			$_SESSION['userid']		= odbc_result($result, "id");
			$_SESSION['username'] = odbc_result($result, "name");
			$_SESSION['dsn']			= $dsn;
			$_SESSION['dbuser']		= $user;
			$_SESSION['pwd']			= $pwd;
			$_SESSION['systemdb']	= $systemdb;
			$_SESSION['tempdb']		= $tempdb;
			$_SESSION['sqlbackuppath'] = "f:/webroot/sif/";
			$_SESSION['backupdownloadpath'] = $backupdownloadpath;
			
			$_SESSION['approot']	= "/sif/";
			$_SESSION['uploadpath']= "/sif/upload/";
			$_SESSION['bgget']		= "#f1f1f1";
			$_SESSION["libpath"]		=  $libpath;
			$_SESSION["commonpath"] = $commonpath;
			$_SESSION['bgbrowse']	= "#f1f1f1";
			$_SESSION['bgdialog']	= "#808000";
			$_SESSION['company'] 	= "1";
			$_SESSION['companyename'] = "Siam International Food Co., Ltd.";
			$_SESSION['companyname'] = "บริษัท สยาม อินเตอร์แนชชั่นแนล ฟู๊ด จำกัด";
			$_SESSION["accpl"] 		= "2321";
			$_SESSION["yearpl"] 		= "2320";
			$_SESSION["yearbeg"] 	= "1";
			$_SESSION['datebeg']	= "2009-12-31";
			
			$_SESSION['alabstyle'] = array("Lid up","Bottom up");
			$_SESSION['actnpackingstyle'] = array("Lid up","Bottom up");
			$_SESSION['atraypressstyle'] = array("IMP");
			$_SESSION['atraypackingstyle'] = array("Lid up","Bottom up");
			$_SESSION['alabinkpos'] = array("ฝากระป๋อง","ก้นกระป๋อง");
			
			
			/*
			$_SESSION['issupervisor'] = odbc_result($result, "issupervisor");
			// read application 
			$query = "select name from ".$systemdb.".dbo.application where id=".$appid;
			$result  =  odbc_Exec($conn,$query);		
			$_SESSION['appname'] = odbc_result($result, "name");
			// read dbtables
			$query = "select name,dbname from ".$systemdb.".dbo.dbtables where applicationid=".$appid;
			$result  =  odbc_Exec($conn,$query);		
			$i = 0;
			while(odbc_fetch_row($result)) {
				$dbname[$i][1] = trim(odbc_result($result, "name"));
				$dbname[$i][2] = trim(odbc_result($result, "dbname"));
				$i++;
			}
			$_SESSION['dbname']	= $dbname;
			// create dblist for backup
			$query = "select dbname from ".$systemdb.".dbo.dbtables where applicationid=".$appid." group by dbname";
			$result  =  odbc_Exec($conn,$query);		
			$i = 0;
			while(odbc_fetch_row($result)) {
				$dblist[$i] = trim(odbc_result($result, "dbname"));
			}
			$dblist[count($dblist)] = $systemdb;
			$_SESSION['dblist'] = $dblist;
			
			$tablename = $systemdb.".dbo.userlog";
			
			$query = "select top 1 loginwhen,logoutwhen,nodeid from ".$tablename." where userid=".$_SESSION['userid']." order by loginwhen desc";
			$result = odbc_Exec($conn,$query);
			$_SESSION['lastlogin'] = odbc_result($result,"loginwhen");
			$_SESSION['lastlogout'] = odbc_result($result,"logoutwhen");
			$_SESSION['lastnodeid'] = trim(odbc_result($result,"nodeid"));
			
			$query	= "INSERT ".$tablename." (userid,nodeid,appname,loginwhen) VALUES (".$_SESSION['userid'].",";
			$query	= $query."'".$_SERVER["REMOTE_ADDR"]."','".$_SESSION['appname']."',getdate())";
			$result  =  odbc_Exec($conn,$query);		
			$query	= "SELECT @@IDENTITY AS userlogid";
			$result	=  odbc_Exec($conn,$query);
			$_SESSION['userlogid'] = odbc_result($result, "userlogid");
			$query	= "SELECT nodeid,loginwhen FROM ".$tablename." WHERE id=".$_SESSION['userlogid'];
			$result	=  odbc_Exec($conn,$query);
			$_SESSION['loginwhen'] = odbc_result($result, "loginwhen");
			$_SESSION['nodeid'] = odbc_result($result, "nodeid");
			*/
			//echo $mysession;
			//echo "<br>";
			//echo $_SESSION['userid'];
			header("Location: index.php?mysession=".$mysession);
			//header("Location: http://www.google.co.th");
		}
	} // isset(userno);	
?>
<link rel="SHORTCUT ICON" href="img/sif-small.png">
<title>SIF's webERP</title>
<script>
	var userid = "";
	function init(){
		baseinit();
		document.getElementById("footer").style.visibility="hidden";
		var headerObj = document.getElementById("header");
		headerObj.style.backgroundImage = "url(header.jpg)";
		headerObj.style.backgroundRepeat = "no-repeat";
		document.getElementById("txtuserno").focus();
	}

	function dosubmit(){
		var txtuserno 	= document.getElementById("txtuserno");
		var txtpassword = document.getElementById("txtpassword");
		//var combocompany = document.getElementById("combocompany");
		var errorbox		= document.getElementById("errorbox");
		if((txtuserno.value == "") || (txtpassword.value == "")){
			errorbox.innerHTML =  "ต้องใส่ทั้งรหัสผู้ใช้และรหัสผ่าน<br>User ID. and password required.";
			errorbox.style.visibility = "visible";
			if(txtuserno.value==""){
				txtuserno.focus();
			}else{
				txtpassword.focus();
			}
		}else{
			document.myform.userno.value = txtuserno.value;
			document.myform.password.value = txtpassword.value;
			//alert(combocompany.value);
			//document.myform.company.value = combocompany.value;
			
			document.myform.submit();	
		}
	}
	function formkeypress(event){
		if(event.keyCode==13){
			dosubmit();
		}else if(event.keyCode==27){	
			var txtuserno 	= document.getElementById("txtuserno");
			var txtpassword = document.getElementById("txtpassword");
			txtuserno.value = ""; txtpassword.value = "";
			txtuserno.focus();
		}else{
			var errorbox		= document.getElementById("errorbox");
			errorbox.style.visibility = "hidden";
		}
	}
</script>
<style>
</style>
</head>
<body onload="init();" onkeydown="formkeypress(event);">
	
		<div id="header">
		</div>  <!-- header -->
		<div id="appnav">
			<br>
		</div>
		<div id="content" >
			<div id="contenttext">
				<div id="programbox">
					<!-- <img style="margin-right:10px; float:left;" src='wwa.jpg'></img> -->
					<span class="h1">SIF's webERP</span>
					<br>
					<span class="h2">ในกรณีที่ไม่สามารถเข้าระบบได้ ให้เข้าใช้งานผ่านลิ้งค์สำรองได้ที่ <h3><b><font color="red">erp2.sif.co.th</font></b></h3></span>
				</div>
				<div id="loginbox">
					<form name="myform" method="post" action="login.php">
						<input type="hidden" name="userno" value="">
						<input type="hidden" name="password" value="">
						<label class = "labget" for=txtuserno>รหัสผู้ใช้ / User ID.</label>
						<input type="text" id="txtuserno" name="txtuserno" autocomplete="off" /><br />
						<label class = "labget" for=txtpassword>รหัสผ่าน / Password</label>
						<input type="text" class="password" id="txtpassword" name="txtpassword" style="-webkit-text-security: disc;" autocomplete="off" /><br />
						
						<a class="button" id="black" href="#" onclick="dosubmit();" onkeypress="mykeypress(event);">ตกลง / Submit</a>
						
					</form>
					<!-- <iframe id='sifframe' style="width:99.5%; height:98%; " src='frame_index.php?mysession=<?php echo $mysession; ?>' frameborder="0"></iframe> -->

				</div>	
				<div id="errorbox">
					<?php 
						if($login_status != ""){
							echo "<script>";
							echo "var errorbox		= document.getElementById('errorbox');";
							echo "errorbox.style.visibility = 'visible';";
							echo "errorbox.innerHTML = '".$login_status."';";
							echo "</script>";
						}
					?>					
				</div>
			</div> <!-- contenttext -->
		</div> <!-- content -->	
	<div id="footer">		
		<!-- <span  style="font-size:10px; color:#41627e;">&copy;2555 :: Thai System Development Co., Ltd.</span> -->
	</div> <!-- footer  -->	

</body>
</html>
