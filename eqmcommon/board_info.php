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
                            <h3 class="box-title">ข้อมูลคณะกรรมการ</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Task</th>
                                    <th>Progress</th>
                                    <th style="width: 40px">Label</th>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>Update software</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-red">55%</span></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Clean database</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar progress-bar-yellow" style="width: 70%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-yellow">70%</span></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Cron job running</td>
                                    <td>
                                        <div class="progress progress-xs progress-striped active">
                                            <div class="progress-bar progress-bar-primary" style="width: 30%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light-blue">30%</span></td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td>Fix and squish bugs</td>
                                    <td>
                                        <div class="progress progress-xs progress-striped active">
                                            <div class="progress-bar progress-bar-success" style="width: 90%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-green">90%</span></td>
                                </tr>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <ul class="pagination pagination-sm no-margin pull-right">
                                <li><a href="#">&laquo;</a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">&raquo;</a></li>
                            </ul>
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
<?php include("../modal/board_add.php"); ?>
<!-- ------------------ -->

<!-- INCLUDE JS CORE-SCRIPT -->
<?php include("../include/buttom_script.php"); ?>
<!-- ------------------ -->

</body>
</html>
