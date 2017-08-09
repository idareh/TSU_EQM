<?php
    $path = '../page/';
    $url = basename($_SERVER['SCRIPT_NAME']);
?>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>คุณอนุวัฒน์ พัฒนเชียร</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="ค้นหา...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                                  <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li <?php if($url == 'index.php'){echo 'class="active" '; }else { echo ''; } ?> >
                <a href="<?php $path ?>index.php">
                    <i class="fa fa-home"></i> <span>หน้าหลัก</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
			<li <?php if($url == 'check_equipment.php'){echo 'class="active" '; }else { echo ''; } ?> >
                <a href="<?php $path ?>check_equipment.php">
                    <i class="fa fa-edit"></i> <span>ตรวจสอบครุภัณฑ์</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
			
			 <li class="treeview <?php if($url == '#'){} else { echo ''; } ?>">
                <a href="#">
                    <i class="fa fa fa-tasks"></i> <span>ข้อมูลการตรวจสอบครุภัณฑ์   </span>
                    <span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#"><i class="fa fa-file-text-o"></i>การตรวจสอบครุภัณฑ์ ประจำปี</a></li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#"><i class="fa fa-file-text-o"></i> การสอบหาข้อเท็จจริง</a></li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#"><i class="fa fa-file-text-o"></i> การจำหน่ายครุภัณฑ์</a></li>
                </ul>
            </li>
			
			<li class="treeview <?php if($url == '#'){} else { echo ''; } ?>">
                <a href="#">
                    <i class="fa fa-file-text-o"></i> <span>รายงาน ครุภัณฑ์   </span>
                    <span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-file-text-o text-aqua"></i> รายงานผลการตรวจสอบครุภัณฑ์  </a>
					</li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-file-text-o text-red"></i> รายงานผลการจำหน่ายครุภัณฑ์</a>
					</li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-file-text-o text-red"></i> รายงานผลการโอนครุภัณฑ์</a>
					</li>
					<li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-file-text-o text-red"></i> รายงานผลการจัดซื้อจัดจ้าง</a>
					</li>
                </ul>
            </li>
			
           
			
				<li class="treeview <?php if($url == '#' || $url == '#' || $url == '#' || $url == '#'){echo 'active'; }else { echo ''; } ?>">
                <a href="#">
                    <i class="fa fa-book"></i> <span>ข้อมูลครุภัณฑ์</span>
                    <span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-aqua"></i>ครุภัณฑ์</a>
					</li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-aqua"></i> ประเภทครุภัณฑ์</a>
					</li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-aqua"></i> สถานที่ใช้งานครุภัณฑ์</a>
					</li>
                    <li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-aqua"></i>สถานะการใช้งานครุภัณฑ์</a>
					</li>
					<li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-yellow"></i>นำเข้าข้อมูลครุภัณฑ์</a>
					</li>
					<li <?php if($url == '#'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>#">
						<i class="fa fa-circle-o text-yellow"></i>นำเข้าข้อมูลการจำหน่ายครุภัณฑ์</a>
					</li>
                </ul>
            </li>
			
			<li class="treeview <?php if($url == 'personal_info.php' || $url == 'board_info.php' || $url == 'department_info.php' || $url == 'permission_info.php'){echo 'active'; }else { echo ''; } ?>">
                <a href="#">
                    <i class="fa fa-users"></i> <span>ข้อมูลบุคลากร</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if($url == 'personal_info.php'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>personal_info.php"><i class="fa fa-user"></i> ข้อมูลบุคลากร</a></li>
                    <li <?php if($url == 'board_info.php'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>board_info.php"><i class="fa fa-user"></i> ข้อมูลคณะกรรมการ</a></li>
                    <li <?php if($url == 'department_info.php'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>department_info.php"><i class="fa fa-building"></i> ข้อมูลหน่วยงาน</a></li>
                    <li <?php if($url == 'permission_info.php'){echo 'class="active" '; }else { echo ''; } ?>><a href="<?php $path ?>permission_info.php"><i class="fa fa-lock"></i> ข้อมูลสิทธิ์การเข้าใช้งานระบบ</a></li>
                </ul>
            </li>
			
			<li <?php if($url == 'backup.php'){echo 'class="active" '; }else { echo ''; } ?> >
                <a href="<?php $path ?>backup.php">
                    <i class="fa fa-database text-yellow"></i> <span>สำรองข้อมูลระบบ</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
			


        </ul>
    </section>
    <!-- /.sidebar -->
</aside>