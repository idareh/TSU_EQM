<?php
	$mysession =	$_GET['mysession'];
	if ( empty($mysession) ) {
		$mysession =	$_POST['mysession'];
		if ( empty($mysession) ) {
			$micro  = microtime();
			$micro = str_replace(" ","",$micro);    // strip out the blanks
			$micro = str_replace(".","",$micro);    // strip out the periods
			$mysession = "eqmapp" . $micro;
		}	
	}
	session_name($mysession);
	session_start();	
	//---- show mysession
	echo "<script>\n";
	echo "var mysession='".$mysession."';\n";
	if (!isset($_SESSION['userid'])){
		echo "var userid='';\n";
	}else{
		echo "var userid='".$_SESSION['userid']."';\n";
	}
	echo "</script>\n";
	
		$userid			= $_SESSION['userid'];
		$username		= $_SESSION['username'];
		$dsn			= $_SESSION['dsn'];
		$user			= $_SESSION['dbuser'];
		$pwd			= $_SESSION['pwd'];
		$getdbname		= $_SESSION['systemdb'];
		$tempdb			= $_SESSION['tempdb'];
		$issupervisor	= $_SESSION['issupervisor'];
		
		$conn			=  odbc_connect($dsn,$user,$pwd);
		
		
?>