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
                            <h3 class="box-title">�����Ť���ѳ��</h3>
							<div class="pull-right">
								<button type="button" class="btn btn-warning btn-sm text-black" >����</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >����</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >���</button>
								<button type="button" class="btn btn-warning btn-sm text-black" >����</button>
								<a href="sql_eqm.php?mysession=<?php echo $mysession?>" type="button" class="btn btn-danger btn-sm text-black" >��Ѻ��ا������</a>
							</div>
                        </div>
                        <!-- /.box-header -->
                      <br>
					
				
					  <table id="example1" class="table table-bordered table-striped text-sm">
						<thead>
							<tr>
								<th class="text-center">���ʺ�����</th>
								<th class="text-center">���ʤ���ѳ��</th>
								<th class="text-center">���ͤ���ѳ��</th>
								<th class="text-center">ʶҹ�����ҹ</th>
								<th class="text-center">ʶҹС����ҹ</th>
							</tr>
						</thead>
				<tbody>
				<?php	

				
						$cmd  = "SELECT  e.* ";
						$cmd .= "FROM  ".getdbname("equipm","e")." ";
						$cmd .= "where e.DelBy is null";
						$eqm = odbc_Exec($conn,$cmd);
						while($Result = odbc_fetch_array($eqm))
                        {
				?>
					<tr>
						<td ><?php echo $Result["barcode"]; ?></td>
						<td ><?php echo $Result["no"]; ?></td>
						<td ><?php echo $Result["name"]; ?></td>
						<td ><?php echo $Result["location"]; ?></td>
						<td><?php echo $Result["st_name"]; ?></td>
					
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
<?php include("../include/buttom_script.php"); ?>
<!-- ------------------ -->

</body>
</html>
