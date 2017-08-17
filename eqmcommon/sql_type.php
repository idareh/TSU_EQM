<?php
	//---- INCLUDE session ***
	include("../eqmlib/eqmsession.php");
	require('../eqmlib/phpclass.php');
	header("Content-Type: text/plain; charset=TIS-620");
					
						$cmd  = "SELECT  t.* ";
						$cmd .= "FROM  ".getdbname("eqm_type","t")." ";
						$cmd .= "where t.DelBy is null";
						$cal = odbc_Exec($conn,$cmd);
						while($Result = odbc_fetch_array($cal))
                        {
							//if($Result["unitname"]!=""){
								
							/* $cmd  = "SELECT  u.id ";
							$cmd .= "FROM  ".getdbname("eqm_unit","u")." ";
							$cmd .= "where  u.name='".$Result["unitname"]."' ";
							$uid  = odbc_Exec($conn,$cmd);
							$uidx = odbc_result($uid,"id"); */
							
							$cmd  = "SELECT  c.id ";
							$cmd .= "FROM  ".getdbname("eqm_category","c")." ";
							$cmd .= "where  c.no='".$Result["no_category"]."' ";
							$tid  = odbc_Exec($conn,$cmd);
							$tidn = odbc_result($tid,"id");
							
							$cmd  ="update ".getdbname("eqm_type"," ")." set eqm_categoryID='".$tidn."' ";
							$cmd .=" where id=".$Result["id"]." ";
							$uid  = odbc_Exec($conn,$cmd);
							//}
				 }

	header("Location: eqm_type_info.php?mysession=".$mysession);

?>
					
