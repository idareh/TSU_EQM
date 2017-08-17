<?php
	include('../eqmlib/eqmsession.php');
	require('../eqmlib/phpclass.php');
	require('../eqmlib/pdf/fpdf_thai.php');
	require('../eqmlib/pdf/jspdf.php');

	header('Content-type: text/html; charset=windows-874'); // for returning thai charset
	
	// pdf class ถ้า printhow=="1"
	class PDF extends FPDF_Thai {	
		function Header() {
			global $font_family,$font_size,$height,$pagewidth;
			global $pageno,$atrnh,$hc,$pdf,$ah,$aw,$start_y;		
			global $lang,$mg_left,$mg_top;
			global $coverage,$ing_header;
			global $copyno;
			global $printwhen;
			global $d1,$d2,$fristdate,$lastdate;
			$org_y = $this->getY();
			
			$this->SetLineWidth(0.2);
			$this->Rect($mg_left+5,$this->GetY(),$pagewidth-$mg_left,$height*2);
			$this->SetLineWidth(0.05);
			$text = $_SESSION['companyename']; //"Siam International Food Co., Ltd.";
			$width = ($pagewidth - $mg_left)/3;
			
			$this->tsdCell( array(text=>$text, width=>$width, align=>'C',border=>"B",height=>$height, fontsize=>$font_size+6) );		
			
			$text ="ระบบ ERP";
			$this->tsdCell( array(text=>$text, width=>$width, align=>'C',border=>"LB",height=>$height, fontsize=>$font_size+6) );		

			$text = "Ref. No. ".$atrnh['printformno'][0];
			$this->tsdCell( array(text=>$text, width=>$width, align=>'L',border=>"LB",height=>$height, fontsize=>$font_size+3) );		

			$this->Ln();
			//$date1 = thaildate($printwhen);
			
			if($d1 !='' && $d2 !=''&& $d2 !=1){$text = "จากวันที่ : ".thaildate($d1)."  ถึง : ".thaildate($d2);}
			else{$text = "จากวันที่ : ".thaildate($fristdate)."  ถึง : ".thaildate($lastdate);}
			$this->tsdCell( array(text=>$text, width=>$width, align=>'L',border=>"L",height=>$height, fontsize=>$font_size+3) );	
			
			$text = "Report Date : ".thaildate($printwhen)." ".substr($printwhen,11,8);
			$this->tsdCell( array(text=>$text, width=>$width, align=>'L',border=>"L",height=>$height, fontsize=>$font_size+3) );		
			
			$text = "  ";
			$this->tsdCell( array(text=>$text, width=>$width, align=>'L',border=>"L",height=>$height,fontsize=>$font_size+3) );		
			//$this->SetTextColor(0,0,0);
			//$this->SetDrawColor(0,0,0);
			$this->Ln($height*1.5);
			//$text = $_SESSION["companyename"];
			
			
			
			$this->Setx(5);
			$this->SetFillColor(230,230,230);
			for($i=0;$i<count($ah);$i++){
				$this->tsdCell( array(text=>$ah[$i], align=>'C', width=>$aw[$i], height=>$height+1, border=>"LTRB",fill=>1,fontsize=>$font_size+2) );
			}
			
			$this->SetFillColor(0,0,0);
			$this->Ln();
			
			/*$start_y = $this->GetY();
			$this->SetFont($font_family,"",$font_size);
			*/
			
		}
	function Footer() {
			global $font_family,$font_size;
			global $height;
			global $lang,$pagewidth;
			global $mg_left,$lang;
			global $ing_header;
			global $printwhen;

			$this->SetY(-($height*5-2));
			
			$box_x 	= $mg_left+5;
			$box_y 	= $this->GetY();
			$box_w	=	($pagewidth/3)-3;
			$box_h	= $height*3;
			
			$this->RoundedRect($box_x, $box_y, $box_w, $box_h, 1, '1234' , 'D');			
			$this->tsdCell( array(text=>"ผู้รายงาน", fontsize=>12, align=>"L", ypos=>$this->GetY()+2.5 ) );
			$text = $atrnh['requestby'][0];
			$this->tsdCell( array(text=>$text, xpos=>$this->GetX()+2, width=>44, height=>6, border=>"B", dotted=>true, linewidth=>0.1) );
			$box_x += $box_w+4.5;
	
			$this->RoundedRect($box_x, $box_y, $box_w, $box_h, 1, '1234' , 'D');			
			$this->tsdCell( array(text=>"ผู้ตรวจสอบ ",fontsize=>12, align=>"L",xpos=>$box_x ) );
			$text = $atrnh['requestby'][0];
			$this->tsdCell( array(text=>$text, xpos=>$this->GetX()+2, width=>44, height=>6, border=>"B", dotted=>true, linewidth=>0.1) );
			$box_x += $box_w+4.5;
			
			$this->RoundedRect($box_x, $box_y, $box_w, $box_h, 1, '1234' , 'D');			
			$this->tsdCell( array(text=>"  ผู้อนุมัติ  ",fontsize=>12, align=>"L",xpos=>$box_x ) );
			$text = $atrnh['requestby'][0];
			$this->tsdCell( array(text=>$text, xpos=>$this->GetX()+2, width=>44, height=>6, border=>"B", dotted=>true, linewidth=>0.1) );
			$this->Ln();
			//$text = "นายอิฐดาเร๊ะ หมัดเหยด";
			$text = trim($_SESSION['username']);
			$this->tsdCell( array(text=>$text, align=>"C",width=>65, height=>6, fontsize=>14 ) );	
			$text = "นายอับดุลอาซิด หะยีมะยิ";
			$this->tsdCell( array(text=>$text, align=>"C",width=>80, height=>6, fontsize=>14 ) );	
			$text = "นายปิยะ เหาตะวานิช";
			$this->tsdCell( array(text=>$text, align=>"C",width=>55, height=>6, fontsize=>14 ) );				
			$box_x += $box_w+1;
			
			$this->SetY(-8);
			$text = "print_report_user";
			$this->tsdCell( array(text=>$text, height=>4, align=>'L', fontsize=>10) );
			$this->SetX($leftmargin);
			$text = "สั่งพิมพ์โดย ".trim($_SESSION['username'])." วันที่ ".thaisdate($printwhen)." เวลา ".substr($printwhen,11,8);
			$this->tsdCell( array(text=>$text, width=>$pagewidth, height=>4, align=>'R', fontsize=>10) );

			
			$this->SetY(-($height*1.5));
			$this->tsdCell( array(text=>"Page ".$this->PageNo()."/{nb}",width=>$pagewidth,height=>$height,align=>"C"));
			

		}
	
		function AcceptPageBreak() {
			return true;
		}	
	}

	$prheadid		= $_POST["prheadid"];
	$dsn			= $_SESSION['dsn'];
	$user			= $_SESSION['dbuser'];
	$pwd			= $_SESSION['pwd'];
	$conn			=  odbc_connect($dsn,$user,$pwd);
	
	$papersize      = "2";
	$font_family	= "sarabunz";
	$font_normal	= 'sarabun.php';
	$font_bold		= 'sarabunb.php';
	$font_italic	= 'sarabuni.php';
	$font_bi	 	= 'sarabunz.php';
	$font_family	= "CordiaNew";
	$font_normal	= 'cordia.php';
	$font_bold		= 'cordiab.php';
	$font_italic	= 'cordiai.php';
	$font_bi	 	= 'cordiaz.php';
	
	$font_size		= 10;
	$height			= 6;
	$pagewidth	= 0;
	if($papersize=="2"){
		$pageheight	= 192;
	}else{
		$pageheight =  192;
	}
	$leftmargin	= 5;
	$topmargin	= 5;
//	$pdf = new PDF('P','mm','letter'); // FPDF($orientation='P', $unit='mm', $format='A4')
	if($papersize=="2"){
		$pdf = new PDF('P','mm','A4');
	}else{
		$pdf = new PDF('P','mm','letter');
	}
	$pdf->AddFont($font_family,'',$font_normal);
	$pdf->AddFont($font_family,'B',$font_bold);
	$pdf->AddFont($font_family,'I',$font_italic);
	$pdf->AddFont($font_family,'BI',$font_bi);
	$pdf->SetFont($font_family,"",$font_size);
	$pdf->AliasNbPages();
	$pdf->SetAutoPageBreak(false);
	$pdf->SetLeftMargin($leftmargin);
	$pdf->SetTopMargin($topmargin);
	$pdf->SetFillColor(192,192,192);
	$pagewidth = 208;

	$cmd = "select getdate() as printwhen";
	$chk = odbc_Exec($conn,$cmd);
	$printwhen = odbc_result($chk,"printwhen");
	
	$d1= $_GET["d1"];
	$d2 = $_GET["d2"];
	$temptable = $_GET["tp"];
	
	$cmd = "select us.no as usno, us.name as username, ug.name as usergroupname, us.AddWhen as adddate, s.name as nameadd ";
	$cmd .=" ,case us.isSupervisor when 1 then 'Administrator' when 0 then 'User' else 'User' end as usadmin ";
	$cmd .=" ,case ug.id when 16 then 'เลิกใช้งาน'   else 'ปกติ'  end as ugid ";
	$cmd .= "from ".$temptable." tp ";
	$cmd .= "inner join ".getdbname("users","us")." on us.id = tp.id  ";
	$cmd .= "inner join ".getdbname("usergroup","ug")." on ug.id = us.usergroupid ";
	$cmd .= "inner join ".getdbname("users","s")." on s.id = us.addby ";
	$cmd .= "where  us.DelBy IS NULL order by us.addwhen DESC ";
	/*
	$r = odbc_Exec($conn,$cmd);
	if(!$r) echo $cmd;
	$alist = dbarray($r);
	*/
	$trnl = odbc_Exec($conn,$cmd);
	if(!$trnl) echo $cmd;
	$atrnl = dbarray($trnl);
	//---ขนาดความกว้าง
	$w = 0;
	$datafmt = array( 	array("#",					7,	 "C"),
						array("รหัสชื่อผู้ใช้",					27,	 "L"),
						array("ชื่อผู้ใช้",					30,	 "L"),
						array("กลุ่มชื่อผู้ใช้",					38,	 "L"),
						array("สถานะ",					17, "C"),
						array("สถานะการใช้งานระบบ",					30, "C"),
						array("วันที่เพิ่มผู้ใช้",					20,	 "C"),
						array("เพิ่มโดย",				30,	 "C") );
						// รวม 202
		for($i=0;$i<count($datafmt);$i++){
			$ah[$i] = $datafmt[$i][0];
			$aw[$i] = $datafmt[$i][1];
			$aal[$i] = $datafmt[$i][2];
		}
		$pagewidth = $mg_left;
		for($i=0;$i<count($aw);$i++){
			$pagewidth += $aw[$i];
		}
		// ตำแหน่งและความกว้างของช่องตัวเลข
		$num_width = $aw[ count($aw)-1 ];
		$num_x = $mg_left;
		for($i=0;$i<count($aw)-1;$i++){
			$num_x+=$aw[$i];
		}
		//	หาวันช่วงการพิม 
		$lastdate  = $atrnl[adddate][0];
		$x 		   = count($atrnl[adddate]);
		$fristdate = $atrnl[adddate][$x-1];
		//ตำแหน่งและความกว้างของช่องตัวหนังสือ
		$caption_w = 40;
		$caption_x = $num_x - $caption_w;
		for($ic=0;$ic<$x;$ic++){
		//for($ic=0;$ic<50;$ic++){
			
			if(!$sayhead){
				$pageno++;
				$pdf->AddPage();
				$sayhead = true;
			}	
			$lcname = $atrnl[usno][$ic];
			$uname = $atrnl[username][$ic];
			$righ = $atrnl[usergroupname][$ic];
			$xx2 = $atrnl[ugid][$ic];
			$xx1 = $atrnl[usadmin][$ic];
			$adddate = thaisdate($atrnl[adddate][$ic]);
			$addname = $atrnl[nameadd][$ic];
			
			$arow = array( $ic+1,$lcname,$uname,$righ,$xx1,$xx2,$adddate,$addname);
			
			$pdf->SetX($mg_left+5);	
			for($i=0;$i<count($arow);$i++){
				$text = tsdfitwidth($arow[$i],$aw[$i]);
				$pdf->tsdCell( array(text=>$text, width=>$aw[$i], align=>$aal[$i],border=>"LTRB", height=>$height,fontsize=>$font_size+2 ) );
				
				}
			$pdf->Ln();
			
			$pageheight= 265;
			if($pdf->GetY() > $pageheight){
				$pdf->tsdCell( array(text=>"",width=>$pagewidth, align=>"C", height=>$height,border=>"T",linewidth=>0.1  ) );
				$sayhead = false;
				$saybrk = false;
			}else{
			}
			if(($ic<count($ic)-1)&&($pdf->GetY()>=$maxy)){
				$sayhead = false;
				$end_y = $pdf->GetY();
				doline_vertical($pdf,$ah,$aw,$start_y,$end_y);
	

				$pdf->SetFillColor(230,230,230);
					
				if($atrnh[preflang][$hc]=="2"){
					$text = "Continued on page ".($pageno+1);
				}else{
					$text = "มีต่อหน้า ".($pageno+1);
				}
				$pdf->tsdCell( array(text=>$text, width=>$pagewidth-$mg_left, align=>'C', height=>$height ,xpos => $mg_left,border=>"LTRB",fill=>"1") );
				$pdf->Ln($height+4);
				
				
				$pdf->SetY($maxy);
				$pdf->SetX($mg_left);
				$text = $atrnh[remark][$hc];
				$pdf->MultiCell($aw[0]+$aw[1]+$aw[2],4.5,$text,0,0,'L');
				
			}
		}

							
	if($printer=="0"){
		//$pdf->AutoPrintToPrinter($server,$printer,false);		
		$pdf->AutoPrint(false);
	}
	$pdf->Output();
	odbc_close($conn);
	
	// end of program
	
	

?>