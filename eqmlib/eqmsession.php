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
?>