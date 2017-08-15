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
                    <div class="col-md-12" style="padding-right: 0;margin-bottom: 5px;">
                        <div class="pull-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus"></i> เพิ่มข้อมูล</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">การสำรองข้อมูล</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                        
                    </div>
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

<!-- INCLUDE Modal -->
<?php include("../modal/department_add.php"); ?>
<!-- ------------------ -->

<!-- INCLUDE JS CORE-SCRIPT -->
<?php include("../include/buttom_script.php"); ?>
<!-- ------------------ -->

</body>
</html>
