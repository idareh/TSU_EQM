<?php 
		$mysession =	$_GET['mysession'];
	if ( empty($mysession) ) {
		$mysession =	$_POST['mysession'];
		if ( empty($mysession) ) {
			$micro  = microtime();
			$micro = str_replace(" ","",$micro);    // strip out the blanks
			$micro = str_replace(".","",$micro);    // strip out the periods
			$mysession = "eqm" . $micro;
		}	
	}
	session_name($mysession);
	session_start();
	

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

	//---เช็คค้า XML
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
	//-- End เช็คค่า XML
	
	//--ตรวจสอบค่า Post ที่รับเข้ามารอง 2 เพื่อเช็ค User & Pass
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
				$conn		=  odbc_connect($dsn,$user,$pwd);
				$cmd		= "SELECT id,issupervisor,no,name,password From ".$systemdb.".dbo.users WHERE no='".$userno."' AND password='".$password."'";
				$result  	=  odbc_Exec($conn,$cmd);		
				$rows		= odbc_num_rows($result);
				if(odbc_result($result, "id")==""){
					$login_status = "รหัสผู้ใช้ และ/หรือ รหัสผ่านไม่ถูกต้อง<br>Incorrect User ID. and/or password.";
				}
			}
		}
		if($login_status == ""){
			$_SESSION['userid']		= odbc_result($result, "id");
			$_SESSION['username'] 	= odbc_result($result, "name");
			$_SESSION['dsn']		= $dsn;
			$_SESSION['dbuser']		= $user;
			$_SESSION['pwd']		= $pwd;
			$_SESSION['systemdb']	= $systemdb.".dbo.";
			$_SESSION['tempdb']		= $tempdb.".dbo.";
			$_SESSION['issupervisor'] = odbc_result($result, "issupervisor");
			
			header("Location: eqmcommon/index.php?mysession=".$mysession);
			
		}
	} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html >
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ลงชื่อเข้าใช้งานระบบ || ระบบสารสนเทศ เพื่อการติดตามและประเมินผลการตรวจสอบพัสดุประจำปี ตามแผนปฏิบัติการประจำปี มหาวิทยาลัยทักษิณ</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<script>
	var userid = "";
/* 	function init(){
		baseinit();
		document.getElementById("footer").style.visibility="hidden";
		var headerObj = document.getElementById("header");
		headerObj.style.backgroundImage = "url(header.jpg)";
		headerObj.style.backgroundRepeat = "no-repeat";
		document.getElementById("txtuserno").focus();
	} */

	function dosubmit(){
		var txtuserno 	= document.getElementById("txtuserno");
		var txtpassword = document.getElementById("txtpassword");
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

</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="login-logo">
            <b>FINANCE </b>DPT
        </div>
                        
			<form name="myform" method="post" action="index.php">
			
            <div class="form-group has-feedback">
				<input type="hidden" name="userno" value="">
				<input type="hidden" name="password" value="">
				<input type="text" id="txtuserno" name="txtuserno" autocomplete="off" class="form-control" placeholder="Username" >
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" id="txtpassword" name="txtpassword">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" id="chk" name="chk"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
					<a class="btn btn-primary btn-block btn-flat" id="black" href="#" onclick="dosubmit();" onkeypress="mykeypress(event);">Sign In</a>
                </div>
            </div>
			</form>
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
		<br>
        <a href="forgot.php">I forgot my password</a>

</div>
</div>

<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>