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
                            <h3 class="box-title">��Ǩ�ͺ����ѳ��</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>���ʺ�����</label>
                                        <input type="text" class="form-control" placeholder="Enter ..." autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>˹��§ҹ</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>�������Թ��Ѿ��</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>��Դ�Թ�÷Ѿ��</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>�ѡɳ��Թ��Ѿ��</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>�Ţ����͡���</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>���ʤ���ѳ��</label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12"></div>
                                <div class="col-md-12 text-center">
                                    <button class="btn btn-success"><i class="fa fa-save"></i> �ѹ�֡������</button>
                                    <button class="btn btn-danger"><i class="fa fa-close"></i> ¡��ԡ</button>
                                </div>
                            </form>
                        </div>
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
