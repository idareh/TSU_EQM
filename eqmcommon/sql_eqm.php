<?php
	//---- INCLUDE session ***
	include("../eqmlib/eqmsession.php");
	require('../eqmlib/phpclass.php');
	header("Content-Type: text/plain; charset=TIS-620");
	
	
	echo "รอดำเนินการ ..............";
		/*			
						$cmd  = "SELECT  st.* ";
						$cmd .= "FROM  ".getdbname("eqm_style","st")." ";
						$cmd .= "where st.DelBy is null";
						$catg = odbc_Exec($conn,$cmd);
						while($Result = odbc_fetch_array($catg))
                        {
							//if($Result["unitname"]!=""){
								
							$cmd  = "SELECT  u.id ";
							$cmd .= "FROM  ".getdbname("eqm_unit","u")." ";
							$cmd .= "where  u.name='".$Result["unitname"]."' ";
							$uid  = odbc_Exec($conn,$cmd);
							$uidx = odbc_result($uid,"id");
							
							$cmd  = "SELECT  t.id ";
							$cmd .= "FROM  ".getdbname("eqm_type","t")." ";
							$cmd .= "where  t.no='".$Result["no_type"]."' ";
							$tid  = odbc_Exec($conn,$cmd);
							$tidn = odbc_result($tid,"id");
							
							$cmd  ="update ".getdbname("eqm_style"," ")." set unitid='".$uidx."', eqm_typeID='".$tidn."' ";
							$cmd .=" where id=".$Result["id"]." ";
							$uid  = odbc_Exec($conn,$cmd);
							//}
				 }

	header("Location: eqm_style_info.php?mysession=".$mysession);*/

?>
					
