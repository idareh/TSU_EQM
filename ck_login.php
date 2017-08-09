<?php

	if(isset($_POST["loginuser"])){
		$userno		= $_POST['loginuser'];
		$password	= $_POST['loginpass'];
		/*if(strpos($password," or ")){
			$password = "badhacker";
			header("Location: http://www.royalthaipolice.go.th/");
		}*/
		$login_status = "";
	/* อ่าน config */
		$current_tag = null;
		$datas = Array();

		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "onStartElement", "onEndElement");
		xml_set_character_data_handler($xml_parser, "onCData");

		$file = "eqmsystem/config.xml";
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
				//$appid 		= $datas['APPID'];
				//$sqlbackuppath = $datas['SQLBACKUPPATH'];
				//$backupdownloadpath = $datas['BACKUPDOWNLOADPATH'];
				//$libpath				= $datas["LIBPATH"];
				//$commonpath	= $datas["COMMONPATH"];
				$conn		=  odbc_connect($dsn,$user,$pwd);
				$cmd		= "SELECT id,status,no,name,Password,UserGroupID FROM ".$systemdb.".dbo.users WHERE no='".$userno."' AND password='".$password."'";
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
			//echo $mysession;
			//echo "<br>";
			//echo $_SESSION['userid'];
			header("Location: index.php?mysession=".$mysession);
			//header("Location: http://www.google.co.th");
		}
	} // isset(userno);	
?>

