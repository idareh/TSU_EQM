<?php
	//---- INCLUDE session ***
	include("../eqmlib/eqmsession.php");
	require('../eqmlib/phpclass.php');
?>
<!DOCTYPE html>
<html>
<head>

    <!-- INCLUDE CSS CORE-SCRIPT -->
    <?php include("../include/meta.php"); ?>
    <!-- ------------------ -->

    <!-- INCLUDE CSS CORE-SCRIPT -->
    <?php include("../include/top_script.php"); ?>
    <!-- ------------------ -->
	

</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">

    <header class="main-header">
        <!-- INCLUDE HEADER -->
        <?php include("../include/header.php"); ?>
        <!-- -------------- -->
		
    </header>


    <!-- INCLUDE MENU -->
    <?php include("../include/menu.php"); ?>
    <!-- -------------- -->


    <div class="content-wrapper">
     

        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">สถานะการใช้งานครุภัณฑ์</h3>
							<div class="pull-right">
								<button type="button" class="btn btn-warning btn-sm text-black" >ค้นหา</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >เพิ่ม</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >แก้ไข</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >ปริ้น</button>
							</div>
                        </div>
                        <!-- /.box-header -->
                      <br>
					
					<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-10">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center">ชื่อสถานะการใช้งานครุภัณฑ์</th>
								<th class="text-center">สถานะการใช้งาน</th>
							</tr>
						</thead>
				<tbody>
				<?php	
				
						$cmd  = "SELECT  es.no,  st.no as stno ";
						$cmd .= "FROM  ".getdbname("eqm_status","es")." ";
						$cmd .= "left join ".getdbname("status","st")." on st.id = es.status ";
						$cmd .= "where es.DelBy is null";
						$catg = odbc_Exec($conn,$cmd);
						while($Result = odbc_fetch_array($catg))
                        {
				?>
					<tr>
						<td><?php echo $Result["no"]; ?></td>
						<td class="text-center"><?php echo $Result["stno"]; ?></td>
					</tr>
						<?php } ?>
					
				</tbody>
			</table>
					  </div>
					  </div>
					  <br>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- INCLUDE FOOTER -->
    <?php include("../include/footer.php"); ?>
    <!-- ----------------- ->

    <!-- Control Sidebar -->
    <?php include("../include/controlsidebar.php"); ?>
    <!-- /.control-sidebar -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- INCLUDE JS CORE-SCRIPT -->
<?php include("../include/buttom_script.php"); ?>
<!-- ------------------ -->

</body>
</html>
