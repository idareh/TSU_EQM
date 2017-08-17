<?php
	include('/webroot/tsdlib/own/tsdsession.php');
//	require('/webroot/tsdlib/pdf/code128.php');
	require('/webroot/tsdlib/pdf/fpdf_thai.php');
	require('/webroot/tsdlib/own/phpclass.php');
	require('/webroot/tsdlib/own/printutil.php');
	require('/webroot/tsdlib/own/ziplib.php');
	require('/webroot/tsdlib/own/bahttext.php');
	
	class PDF extends FPDF_Thai {
		// FPDF($orientation='P', $unit='mm', $format='A4')
		//Current column
		var $col=0;
		//Ordinate of column start
		var $y0;

// width = 190;
// height
		function Header() {
			global $font_family;
			global $printwhat;
			global $bookno,$bookname,$trnno,$trndate;
			global $afinh,$afint;
			global $terms_x,$terms_y;
			global $pageno,$saying;
			global $taxhead;
			global $agltrnh,$afinp;
			global $lcname,$lcaddress,$lctaxid,$lcno,$lcmaindoc,$lcduedate;
			global $lclabmaindoc;
		/*	$text = "บริษัท ยูนิคอร์ด จำกัด (มหาชน)";
			$this->tsdCell( array(text=>$text, width=>$this->GetStringWidth($text), height=>6,fontsize=>16, fontstyle=>"B" ) );
			$text = $agltrnh[bookname][0];
			$this->tsdCell( array(text=>$text, width=>200, height=>6 ,fontsize=>16, fontstyle=>"B" , align=>"R" , xpos=>0 ) );
			$this->Ln();
			$org_y = $this->getY();
			$ny = $this->GetY();
			if ($lcname != ""){
				$this->Ln();
				$this->SetDash(0.2,0.4); //5mm on, 5mm off

				$text = "รหัส";
				$this->tsdCell( array(text=>$text, width=>20, height=>5 ) );
				$text = $lcno;
				$this->tsdCell( array(text=>$text, width=>20, height=>5 , fontstyle=>"B" , border=>"B" , dotted=>true , xpos=>28 ) );
				$text = "เลขประจำตัวผู้เสียภาษีอากร";
				$this->tsdCell( array(text=>$text, width=>30, height=>5 ) );
				$text = $lctaxid;
				$this->tsdCell( array(text=>$text, width=>30, height=>5 , fontstyle=>"B" , border=>"B" , dotted=>true , xpos=>$this->getX()+10 ) );
				$this->Ln();

				$text = "ชื่อ";
				$this->tsdCell( array(text=>$text, width=>20, height=>5 ) );
				$text = $lcname;
				$this->tsdCell( array(text=>$text, width=>100, height=>5 , fontstyle=>"B" , border=>"B" , dotted=>true , xpos=>28 ) );
				$this->Ln();

				$y = $this->GetY();

				$text = "ที่อยู่";
				$this->tsdCell( array(text=>$text, width=>20, height=>5 ) );
				$this->tsdCell( array(text=>"", width=>100, height=>5 , border=>"B" , xpos=>28 , dotted=>true  ) );
				$this->Ln(5);

				$this->tsdCell( array(text=>"", width=>100, height=>5 , border=>"B" , xpos=>28 , dotted=>true  ) );
				$this->Ln(5);
				$this->tsdCell( array(text=>"", width=>100, height=>5 , border=>"B" , xpos=>28 , dotted=>true  ) );
				$this->Ln(5);
				$ny = $this->GetY();
				$this->SetY($y);
				$this->SetX(28);
				$x = 10;
				$height = 4;
				$text = $lcaddress;
				$this->MultiCell(100,5,$text,0,0,'L');
				$this->SetDash();
				
				
				
		}else{
				$this->Ln();
				$this->Ln();
				$this->Ln();
				$this->Ln();
				$ny = $this->GetY();
			}

			$this->SetY($org_y);

			$text = "รหัสสมุดบัญชี";
			$this->tsdCell( array(text=>$text, width=>25, height=>6, align=>"R" , border=>"LTR" , xpos=>130 ) );
			$text = $agltrnh[bookno][0];
			$this->tsdCell( array(text=>$text, width=>45, height=>6, fontstyle=>"B" , border=>"LTR" , xpos=>155 ) );
			$this->Ln();

			$text = "เลขที่ใบสำคัญ";
			$this->tsdCell( array(text=>$text, width=>25, height=>6, align=>"R" , border=>"LTR" , xpos=>130 ) );
			$text = $agltrnh[trnno][0];
			$this->tsdCell( array(text=>$text, width=>45, height=>6, fontstyle=>"B" , border=>"LTR" , xpos=>155 ) );
			$this->Ln();

			$text = "ลงวันที่";
			$this->tsdCell( array(text=>$text, width=>25, height=>6, align=>"R" , border=>"LTRB" , xpos=>130 ) );
			$text = thaildate($agltrnh[trndate][0]);
			$this->tsdCell( array(text=>$text, width=>45, height=>6, border=>"LTRB" , xpos=>155 ) );
			
			if ($lcmaindoc != ""){
				$this->Ln();
				//$text = "เลขที่ใบกำกับฯ";
				if($lclabmaindoc != ""){
					$text = $lclabmaindoc;
				}else{
					$text = "เลขที่ใบกำกับฯ";
				}
				$this->tsdCell( array(text=>$text, width=>25, height=>6, align=>"R" , border=>"LTRB" , xpos=>130 ) );
				$this->tsdCell( array(text=>$lcmaindoc, width=>45, height=>6, border=>"LTRB" , xpos=>155 ) );
				$this->Ln();
		
				$text = "วันครบกำหนด";
				$this->tsdCell( array(text=>$text, width=>25, height=>6, align=>"R" , border=>"LTRB" , xpos=>130 ) );
				$this->tsdCell( array(text=>thaildate($lcduedate), width=>45, height=>6, border=>"LTRB" , xpos=>155 ) );
			}

			$this->Ln(8);
			$text = "รหัสบัญชี";
			$this->tsdCell( array(text=>$text, width=>17, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"C" , border=>"LTRB" , ypos=>$ny ) );
			$text = "บัญชีย่อย";
			$this->tsdCell( array(text=>$text, width=>15, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"C" , border=>"LTRB"  ) );
			$text = "แผนก";
			$this->tsdCell( array(text=>$text, width=>40, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"C" , border=>"LTRB"  ) );
			$text = "ชื่อบัญชี";
			$this->tsdCell( array(text=>$text, width=>74, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"C" , border=>"LTRB"  ) );
			$text = "เดบิท";
			$this->tsdCell( array(text=>$text, width=>22, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"R" , border=>"LTRB" ) );
			$text = "เครดิต";
			$this->tsdCell( array(text=>$text, width=>22, height=>8 , fontsize=>12 , fontstyle=>"B" , align=>"R" , border=>"LTRB"  ) );
		
			$this->Ln();
		*/
		}

		function Footer() {
			global $font_family;
			global $printwhat;
			global $name;
			global $position;
			global $done;
			global $printwhen,$printby;
//			$this->Cell(10,5,$printwhat,0,0,'L');
	/*
			if ($done){
				// เร่ิมที่ 5 ซ.ม. จากขอบล่าง
			$text = "ผู้ลงรายการ";
				$width = $this->GetStringWidth($text)+2;
				$w1 = $width;
				$this->tsdCell( array(text=>$text, width=>$width, height=>5 , ypos=>-40  ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 , border=>"B" , dotted=>true ) );

				$text = "";
				$width = $this->GetStringWidth($text)+2;
				$w2 = $width;
				$this->tsdCell( array(text=>$text, width=>$width, height=>5 , xpos=>$this->getX()+10  ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 ) );

				$text = "ผู้ตรวจสอบ";
				$width = $this->GetStringWidth($text)+2;
				$w3 = $width;
				$this->tsdCell( array(text=>$text, width=>$width, height=>5 , xpos=>$this->getX()+10  ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 , border=>"B" , dotted=>true ) );

				$this->Ln(10);
				$text = "วันที่";
				$this->tsdCell( array(text=>$text, width=>$w1, height=>5 , align=>"R" ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 , border=>"B" , dotted=>true ) );

				$text = "";
				$this->tsdCell( array(text=>$text, width=>$w2, height=>5 , align=>"R" , xpos=>$this->getX()+10 ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 ) );

				$text = "วันที่";
				$this->tsdCell( array(text=>$text, width=>$w3, height=>5 , align=>"R" , xpos=>$this->getX()+10 ) );
				$this->tsdCell( array(text=>"", width=>25, height=>5 , border=>"B" , dotted=>true ) );
				$this->Ln(8);

				$text = "พิมพ์โดย ".$printby. " / " ."เมื่อ ".$printwhen;
				$this->tsdCell( array(text=>$text, width=>191, fontsize=>14 , fontstyle=>"I" , align=>"R" ,height=>6 ) );
//				$this->RoundedRect(10, $sy-10, 148, 27, 1, '1234' , 'D');
//				$this->RoundedRect($sx-1, $sy-10, 40, 27, 1, '1234' , 'D');
//				$this->Line(
			}
			*/
		}
		function SetCol($col) {
			//Set position at a given column
			$this->col=$col;
			$x=10+$col*65;
			$this->SetLeftMargin($x);
			$this->SetX($x);
		}

		function AcceptPageBreak() {
			return true;
		}

	} // end of class


	// PDF Section
	$font_family	= "AngsaNew";
	$font_normal	= 'angsa.php';
	$font_bold		= 'angsab.php';
	$font_italic		= 'angsai.php';
	$font_bi	 		= 'angsaz.php';
	$pdf = new PDF('P','mm','letter'); // FPDF($orientation='P', $unit='mm', $format='A4')
	$pdf->AddFont($font_family,'',$font_normal);
	$pdf->AddFont($font_family,'B',$font_bold);
	$pdf->AddFont($font_family,'I',$font_italic);
	$pdf->AddFont($font_family,'BI',$font_bi);
	$pdf->SetAutoPageBreak(false);
	$pdf->SetLineWidth(0.05);
	$pdf->SetFont($font_family,"",14);

$x= 20;

$pdf->AddPage();
//$pdf->SetFont('Arial','',10);

$pdf->EAN13($x,$y+20, '8854641001429');

$pdf->EAN13($x+40,$y+20, '8850692100678');


$pdf->EAN13($x+80,$y+20, '8858741303810');

$pdf->EAN13($x+120,$y+20, '8850309010208');

$pdf->Output();
?>
