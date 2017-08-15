<?php
	//---- INCLUDE session ***
	include("../eqmlib/eqmsession.php");
	
	$dsn			= $_SESSION['dsn'];
	$user			= $_SESSION['dbuser'];
	$pwd			= $_SESSION['pwd'];
	$getdbname 		= $_SESSION['systemdb'];
	$conn			=  odbc_connect($dsn,$user,$pwd);
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
	
	<!-- INCLUDE DataTable -->
        <?php include("../include/datatable.php"); ?>
    <!-- -------------- -->
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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <b>FINANCE</b>DPT
                <small>Version 1.0.0</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">ประเภทครุภัณฑ์</h3>
                        </div>
                        <!-- /.box-header -->
                      <br>
					  <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Name</th>
						<th>Position</th>
						<th>Office</th>
						<th>Age</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Name</th>
						<th>Position</th>
						<th>Office</th>
						<th>Age</th>
					</tr>
				</tfoot>
				<tbody>
				<?php
						$cmd = "SELECT  * FROM  ".$getdbname."eqm_category where DelBy is null";
						$catg = odbc_Exec($conn,$cmd);
						//$dbcat = dbarray($catg);
						$i= odbc_num_fields($catg);
						echo $i;
						echo count($dbcat["id"]);
						for($i=0;$i<count($dbcat["id"]);$i++){
				?>
					<tr>
						<td><?php echo $dbcat["status"]; ?></td>
						<td><?php echo $dbcat["no"]; ?></td>
						<td><?php echo $dbcat["name"]; ?></td>
						<td><?php echo $dbcat["id"]; ?></td>
					</tr>
						<?php } ?>
					
				</tbody>
			</table>
					  
					  
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
<?php include("../include/buttom_script_DataTB.php"); ?>
<!-- ------------------ -->

</body>
</html>
