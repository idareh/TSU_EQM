<?php
/*******************************************************************************
* Software: FPDF Thai Positioning Improve                                      *
* Version:  1.0                                                                *
* Date:     2005-04-30                                                         *
* Advisor:  Mr. Wittawas Puntumchinda                                          *
* Coding:   Mr. Sirichai Fuangfoo                                              *
* License:  Freeware                                                           *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/

require('fpdf.php');

class FPDF_Thai extends FPDF
{
var $txt_error;	
var $s_error;
var $string_th;
var $s_th;
var $pointX;
var $pointY;
var $curPointX;
var $checkFill;
var $array_th;
// for js
var $javascript;
var $n_js;
// barcode128
var $T128;                                             // tableau des codes 128
var $ABCset="";                                        // jeu des caract?res ?ligibles au C128
var $Aset="";                                          // Set A du jeu des caract?res ?ligibles
var $Bset="";                                          // Set B du jeu des caract?res ?ligibles
var $Cset="";                                          // Set C du jeu des caract?res ?ligibles
var $SetFrom;                                          // Convertisseur source des jeux vers le tableau
var $SetTo;                                            // Convertisseur destination des jeux vers le tableau
var $JStart = array("A"=>103, "B"=>104, "C"=>105);     // Caract?res de s?lection de jeu au d?but du C128
var $JSwap = array("A"=>101, "B"=>100, "C"=>99);       // Caract?res de changement de jeu

var $NewPageGroup;   // variable indicating whether a new group was requested
var $PageGroups;     // variable containing the number of pages of the groups
var $CurrPageGroup;  // variable containing the alias of the current page group

// javascript
    function IncludeJS($script) {
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }

	function AutoPrint($dialog=false)
{
    //Open the print dialog or start printing immediately on the standard printer
    $param=($dialog ? 'true' : 'false');
    $script="print($param);";
    $this->IncludeJS($script);
}

/*
javascript.append("var params = this.getPrintParams();");
 
			javascript.append("params.interactive =	params.constants.interactionLevel.silent;");
			javascript.append("params.printerName=\"MY_PRINTER_NAME\";");
			javascript.append("params.pageHandling = params.constants.handling.shrink;");
 
			javascript.append("this.print(params);");
 */
 
function AutoPrintToPrinter($server,$printer,$dialog=false)
{
    //Print on a shared printer (requires at least Acrobat 6)
    $script = "var pp = getPrintParams();";
    if($dialog){
		$script .= "pp.interactive = pp.constants.interactionLevel.full;";
		$script .= "var fv = pp.constants.flagValues;";
		$script .= "pp.flags = fv.setPageSize;";
		$script .= "pp.pageHandling = pp.constants.handling.none;";
   }else{
//		$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
		$script .= "pp.interactive = pp.constants.interactionLevel.silent;";
		$script .= "var fv = pp.constants.flagValues;";
		$script .= "pp.flags = fv.setPageSize;";
		$script .= "pp.pageHandling = pp.constants.handling.none;";
	}
	if(($server != "")&&($printer != "")){
		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";	
	}else if($printer != ""){
		$script .= "pp.printerName = '".$printer."';";	
	}
	//echo $script;
	$script .= "print(pp);";
    $this->IncludeJS($script);
}
// end of javascript print routine

// code128 
function Init128(){
    $this->T128[] = array(2, 1, 2, 2, 2, 2);           //0 : [ ]               // composition des caract?res
    $this->T128[] = array(2, 2, 2, 1, 2, 2);           //1 : [!]
    $this->T128[] = array(2, 2, 2, 2, 2, 1);           //2 : ["]
    $this->T128[] = array(1, 2, 1, 2, 2, 3);           //3 : [#]
    $this->T128[] = array(1, 2, 1, 3, 2, 2);           //4 : [$]
    $this->T128[] = array(1, 3, 1, 2, 2, 2);           //5 : [%]
    $this->T128[] = array(1, 2, 2, 2, 1, 3);           //6 : [&]
    $this->T128[] = array(1, 2, 2, 3, 1, 2);           //7 : [']
    $this->T128[] = array(1, 3, 2, 2, 1, 2);           //8 : [(]
    $this->T128[] = array(2, 2, 1, 2, 1, 3);           //9 : [)]
    $this->T128[] = array(2, 2, 1, 3, 1, 2);           //10 : [*]
    $this->T128[] = array(2, 3, 1, 2, 1, 2);           //11 : [+]
    $this->T128[] = array(1, 1, 2, 2, 3, 2);           //12 : [,]
    $this->T128[] = array(1, 2, 2, 1, 3, 2);           //13 : [-]
    $this->T128[] = array(1, 2, 2, 2, 3, 1);           //14 : [.]
    $this->T128[] = array(1, 1, 3, 2, 2, 2);           //15 : [/]
    $this->T128[] = array(1, 2, 3, 1, 2, 2);           //16 : [0]
    $this->T128[] = array(1, 2, 3, 2, 2, 1);           //17 : [1]
    $this->T128[] = array(2, 2, 3, 2, 1, 1);           //18 : [2]
    $this->T128[] = array(2, 2, 1, 1, 3, 2);           //19 : [3]
    $this->T128[] = array(2, 2, 1, 2, 3, 1);           //20 : [4]
    $this->T128[] = array(2, 1, 3, 2, 1, 2);           //21 : [5]
    $this->T128[] = array(2, 2, 3, 1, 1, 2);           //22 : [6]
    $this->T128[] = array(3, 1, 2, 1, 3, 1);           //23 : [7]
    $this->T128[] = array(3, 1, 1, 2, 2, 2);           //24 : [8]
    $this->T128[] = array(3, 2, 1, 1, 2, 2);           //25 : [9]
    $this->T128[] = array(3, 2, 1, 2, 2, 1);           //26 : [:]
    $this->T128[] = array(3, 1, 2, 2, 1, 2);           //27 : [;]
    $this->T128[] = array(3, 2, 2, 1, 1, 2);           //28 : [<]
    $this->T128[] = array(3, 2, 2, 2, 1, 1);           //29 : [=]
    $this->T128[] = array(2, 1, 2, 1, 2, 3);           //30 : [>]
    $this->T128[] = array(2, 1, 2, 3, 2, 1);           //31 : [?]
    $this->T128[] = array(2, 3, 2, 1, 2, 1);           //32 : [@]
    $this->T128[] = array(1, 1, 1, 3, 2, 3);           //33 : [A]
    $this->T128[] = array(1, 3, 1, 1, 2, 3);           //34 : [B]
    $this->T128[] = array(1, 3, 1, 3, 2, 1);           //35 : [C]
    $this->T128[] = array(1, 1, 2, 3, 1, 3);           //36 : [D]
    $this->T128[] = array(1, 3, 2, 1, 1, 3);           //37 : [E]
    $this->T128[] = array(1, 3, 2, 3, 1, 1);           //38 : [F]
    $this->T128[] = array(2, 1, 1, 3, 1, 3);           //39 : [G]
    $this->T128[] = array(2, 3, 1, 1, 1, 3);           //40 : [H]
    $this->T128[] = array(2, 3, 1, 3, 1, 1);           //41 : [I]
    $this->T128[] = array(1, 1, 2, 1, 3, 3);           //42 : [J]
    $this->T128[] = array(1, 1, 2, 3, 3, 1);           //43 : [K]
    $this->T128[] = array(1, 3, 2, 1, 3, 1);           //44 : [L]
    $this->T128[] = array(1, 1, 3, 1, 2, 3);           //45 : [M]
    $this->T128[] = array(1, 1, 3, 3, 2, 1);           //46 : [N]
    $this->T128[] = array(1, 3, 3, 1, 2, 1);           //47 : [O]
    $this->T128[] = array(3, 1, 3, 1, 2, 1);           //48 : [P]
    $this->T128[] = array(2, 1, 1, 3, 3, 1);           //49 : [Q]
    $this->T128[] = array(2, 3, 1, 1, 3, 1);           //50 : [R]
    $this->T128[] = array(2, 1, 3, 1, 1, 3);           //51 : [S]
    $this->T128[] = array(2, 1, 3, 3, 1, 1);           //52 : [T]
    $this->T128[] = array(2, 1, 3, 1, 3, 1);           //53 : [U]
    $this->T128[] = array(3, 1, 1, 1, 2, 3);           //54 : [V]
    $this->T128[] = array(3, 1, 1, 3, 2, 1);           //55 : [W]
    $this->T128[] = array(3, 3, 1, 1, 2, 1);           //56 : [X]
    $this->T128[] = array(3, 1, 2, 1, 1, 3);           //57 : [Y]
    $this->T128[] = array(3, 1, 2, 3, 1, 1);           //58 : [Z]
    $this->T128[] = array(3, 3, 2, 1, 1, 1);           //59 : [[]
    $this->T128[] = array(3, 1, 4, 1, 1, 1);           //60 : [\]
    $this->T128[] = array(2, 2, 1, 4, 1, 1);           //61 : []]
    $this->T128[] = array(4, 3, 1, 1, 1, 1);           //62 : [^]
    $this->T128[] = array(1, 1, 1, 2, 2, 4);           //63 : [_]
    $this->T128[] = array(1, 1, 1, 4, 2, 2);           //64 : [`]
    $this->T128[] = array(1, 2, 1, 1, 2, 4);           //65 : [a]
    $this->T128[] = array(1, 2, 1, 4, 2, 1);           //66 : [b]
    $this->T128[] = array(1, 4, 1, 1, 2, 2);           //67 : [c]
    $this->T128[] = array(1, 4, 1, 2, 2, 1);           //68 : [d]
    $this->T128[] = array(1, 1, 2, 2, 1, 4);           //69 : [e]
    $this->T128[] = array(1, 1, 2, 4, 1, 2);           //70 : [f]
    $this->T128[] = array(1, 2, 2, 1, 1, 4);           //71 : [g]
    $this->T128[] = array(1, 2, 2, 4, 1, 1);           //72 : [h]
    $this->T128[] = array(1, 4, 2, 1, 1, 2);           //73 : [i]
    $this->T128[] = array(1, 4, 2, 2, 1, 1);           //74 : [j]
    $this->T128[] = array(2, 4, 1, 2, 1, 1);           //75 : [k]
    $this->T128[] = array(2, 2, 1, 1, 1, 4);           //76 : [l]
    $this->T128[] = array(4, 1, 3, 1, 1, 1);           //77 : [m]
    $this->T128[] = array(2, 4, 1, 1, 1, 2);           //78 : [n]
    $this->T128[] = array(1, 3, 4, 1, 1, 1);           //79 : [o]
    $this->T128[] = array(1, 1, 1, 2, 4, 2);           //80 : [p]
    $this->T128[] = array(1, 2, 1, 1, 4, 2);           //81 : [q]
    $this->T128[] = array(1, 2, 1, 2, 4, 1);           //82 : [r]
    $this->T128[] = array(1, 1, 4, 2, 1, 2);           //83 : [s]
    $this->T128[] = array(1, 2, 4, 1, 1, 2);           //84 : [t]
    $this->T128[] = array(1, 2, 4, 2, 1, 1);           //85 : [u]
    $this->T128[] = array(4, 1, 1, 2, 1, 2);           //86 : [v]
    $this->T128[] = array(4, 2, 1, 1, 1, 2);           //87 : [w]
    $this->T128[] = array(4, 2, 1, 2, 1, 1);           //88 : [x]
    $this->T128[] = array(2, 1, 2, 1, 4, 1);           //89 : [y]
    $this->T128[] = array(2, 1, 4, 1, 2, 1);           //90 : [z]
    $this->T128[] = array(4, 1, 2, 1, 2, 1);           //91 : [{]
    $this->T128[] = array(1, 1, 1, 1, 4, 3);           //92 : [|]
    $this->T128[] = array(1, 1, 1, 3, 4, 1);           //93 : [}]
    $this->T128[] = array(1, 3, 1, 1, 4, 1);           //94 : [~]
    $this->T128[] = array(1, 1, 4, 1, 1, 3);           //95 : [DEL]
    $this->T128[] = array(1, 1, 4, 3, 1, 1);           //96 : [FNC3]
    $this->T128[] = array(4, 1, 1, 1, 1, 3);           //97 : [FNC2]
    $this->T128[] = array(4, 1, 1, 3, 1, 1);           //98 : [SHIFT]
    $this->T128[] = array(1, 1, 3, 1, 4, 1);           //99 : [Cswap]
    $this->T128[] = array(1, 1, 4, 1, 3, 1);           //100 : [Bswap]                
    $this->T128[] = array(3, 1, 1, 1, 4, 1);           //101 : [Aswap]
    $this->T128[] = array(4, 1, 1, 1, 3, 1);           //102 : [FNC1]
    $this->T128[] = array(2, 1, 1, 4, 1, 2);           //103 : [Astart]
    $this->T128[] = array(2, 1, 1, 2, 1, 4);           //104 : [Bstart]
    $this->T128[] = array(2, 1, 1, 2, 3, 2);           //105 : [Cstart]
    $this->T128[] = array(2, 3, 3, 1, 1, 1);           //106 : [STOP]
    $this->T128[] = array(2, 1);                       //107 : [END BAR]

    for ($i = 32; $i <= 95; $i++) {                                            // jeux de caract?res
        $this->ABCset .= chr($i);
    }
    $this->Aset = $this->ABCset;
    $this->Bset = $this->ABCset;
    for ($i = 0; $i <= 31; $i++) {
        $this->ABCset .= chr($i);
        $this->Aset .= chr($i);
    }
    for ($i = 96; $i <= 126; $i++) {
        $this->ABCset .= chr($i);
        $this->Bset .= chr($i);
    }
    $this->Cset="0123456789";

    for ($i=0; $i<96; $i++) {                                                  // convertisseurs des jeux A & B  
        @$this->SetFrom["A"] .= chr($i);
        @$this->SetFrom["B"] .= chr($i + 32);
        @$this->SetTo["A"] .= chr(($i < 32) ? $i+64 : $i-32);
        @$this->SetTo["B"] .= chr($i);
    }
}


function Code128($x, $y, $code, $w, $h) {
    $Aguid = "";                                                                      // Cr?ation des guides de choix ABC
    $Bguid = "";
    $Cguid = "";
    for ($i=0; $i < strlen($code); $i++) {
        $needle = substr($code,$i,1);
        $Aguid .= ((strpos($this->Aset,$needle)===false) ? "N" : "O"); 
        $Bguid .= ((strpos($this->Bset,$needle)===false) ? "N" : "O"); 
        $Cguid .= ((strpos($this->Cset,$needle)===false) ? "N" : "O");
    }

    $SminiC = "OOOO";
    $IminiC = 4;

    $crypt = "";
    while ($code > "") {
                                                                                    // BOUCLE PRINCIPALE DE CODAGE
        $i = strpos($Cguid,$SminiC);                                                // for?age du jeu C, si possible
        if ($i!==false) {
            $Aguid [$i] = "N";
            $Bguid [$i] = "N";
        }

        if (substr($Cguid,0,$IminiC) == $SminiC) {                                  // jeu C
            $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);  // d?but Cstart, sinon Cswap
            $made = strpos($Cguid,"N");                                             // ?tendu du set C
            if ($made === false) {
                $made = strlen($Cguid);
            }
            if (fmod($made,2)==1) {
                $made--;                                                            // seulement un nombre pair
            }
            for ($i=0; $i < $made; $i += 2) {
                $crypt .= chr(strval(substr($code,$i,2)));                          // conversion 2 par 2
            }
            $jeu = "C";
        } else {
            $madeA = strpos($Aguid,"N");                                            // ?tendu du set A
            if ($madeA === false) {
                $madeA = strlen($Aguid);
            }
            $madeB = strpos($Bguid,"N");                                            // ?tendu du set B
            if ($madeB === false) {
                $madeB = strlen($Bguid);
            }
            $made = (($madeA < $madeB) ? $madeB : $madeA );                         // ?tendu trait?e
            $jeu = (($madeA < $madeB) ? "B" : "A" );                                // Jeu en cours

            $crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]); // d?but start, sinon swap

            $crypt .= strtr(substr($code, 0,$made), $this->SetFrom[$jeu], $this->SetTo[$jeu]); // conversion selon jeu

        }
        $code = substr($code,$made);                                           // raccourcir l?gende et guides de la zone trait?e
        $Aguid = substr($Aguid,$made);
        $Bguid = substr($Bguid,$made);
        $Cguid = substr($Cguid,$made);
    }                                                                          // FIN BOUCLE PRINCIPALE

    $check = ord($crypt[0]);                                                   // calcul de la somme de contr?le
    for ($i=0; $i<strlen($crypt); $i++) {
        $check += (ord($crypt[$i]) * $i);
    }
    $check %= 103;

    $crypt .= chr($check) . chr(106) . chr(107);                               // Chaine Crypt?e compl?te

    $i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
    $modul = $w/$i;

    for ($i=0; $i<strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
        $c = $this->T128[ord($crypt[$i])];
        for ($j=0; $j<count($c); $j++) {
            $this->Rect($x,$y,$c[$j]*$modul,$h,"F");
            $x += ($c[$j++]+$c[$j])*$modul;
        }
    }
}
// end of code128

/****************************************************************************************
* ประเภท: Function ของ Class FPDF_TH													
* อ้างอิง: Function MultiCell ของ Class FPDF											
* การทำงาน: ใช้ในการพิมพ์ข้อความหลายบรรทัดของเอกสาร PDF 										
* รูบแบบ: MultiCell (	$w = ความกว้างของCell,												
*						$h = ความสูงของCell,												
*						$txt = ข้อความที่จะพิมพ์,													
*						$border = กำหนดการแสดงเส้นกรอบ(0 = ไม่แสดง, 1= แสดง)	,				
*						$align = ตำแหน่งข้อความ(L = ซ้าย, R = ขวา, C = กึ่งกลาง, J = กระจาย),
*						$fill = กำหนดการแสดงสีของCell(0 = ไม่แสดง, 1 = แสดง)					
*					)			
*****************************************************************************************/
function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0,$uword='')
{
	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(strpos($border,'L')!==false)
				$b2.='L';
			if(strpos($border,'R')!==false)
				$b2.='R';
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			if($this->ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,'',$uword);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
			continue;
		}
		if($c==' ')
		{
			$sep=$i;
			$ls=$l;
			$ns++;
		}
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,'',$uword);
			}
			else
			{
				if($align=='J')
				{
					$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
				}
				$this->MCell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill,'',$uword);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
		}
		else
			$i++;
	}
	//Last chunk
	if($this->ws>0)
	{
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b.='B';
	$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill,'',$uword);
	$this->x=$this->lMargin;
}

function tsdhline($ap){
	if(!isset($ap[from_x]))	$from_x = $this->GetX();	else $from_x = $ap[from_x];
	if(!isset($ap[from_y]))	$from_y = $this->GetY();	else $from_y = $ap[from_y];
	if(!isset($ap[width]))		$width = $this->w-$this->rMargin-$this->x;	else $width = $ap[width];
	if(!isset($ap[linewidth]))	$linewidth = $this->LineWidth; else $linewidth = $ap[linewidth];
	if(!isset($ap[dotted]))	$dotted = false; else $dotted = $ap[dotted];
	if(!isset($ap[lprinted]))	$lprinted = true; else $lprinted =$ap[lprinted];
	if($dotted) $this->SetDash(0.2,0.4);
	$xlinewidth = $this->LineWidth;
	$this->SetLineWidth($linewidth);
	if($lprinted) $this->Line($from_x,$from_y,$from_x+$width,$from_y); 
	if($dotted) $this->SetDash();
	$this->SetLineWidth($xlinewidth);
}

function tsdvline($ap){
	if(!isset($ap[from_x]))	$from_x = $this->GetX();	else $from_x = $ap[from_x];
	if(!isset($ap[from_y]))	$from_y = $this->GetY();	else $from_y = $ap[from_y];
	if(!isset($ap[to_y]))		$to_y = $this->GetY();	else $to_y = $ap[to_y];
	if(!isset($ap[lprinted]))	$lprinted = true; else $lprinted =$ap[lprinted];
	if(!isset($ap[linewidth]))	$linewidth = $this->LineWidth; else $linewidth = $ap[linewidth];
	$xlinewidth = $this->LineWidth;
	$this->SetLineWidth($linewidth);
	if($lprinted) $this->Line($from_x,$from_y,$from_x,$to_y); 
	$this->SetLineWidth($xlinewidth);
}


function tsdcell($ap){
//	$w=0,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='',$fontname='',$fontstyle='',$fontsize='',$linewidth=0,$dash=0,$xpos=0; $ypos=0,$linewidth=0.2
	if(!isset($ap[width]))		$width = 0;		else $width = $ap[width];
	if(!isset($ap[height]))		$height = 0;		else $height = $ap[height];
	if(!isset($ap[text]))			$text = "";			else $text = $ap[text];
	if(!isset($ap[border]))		$border = 0;		else $border = $ap[border];
	if(!isset($ap[ln]))			$ln = 0;				else $ln = $ap[ln];
	if(!isset($ap[align]))		$align = "";		else $align = $ap[align];
	if(!isset($ap[fill]))			$fill = 0;			else $fill = $ap[fill];
	if(!isset($ap[linkurl]))		$link = 0;			else $link = $ap[linkurl];
	if(!isset($ap[fontname]))	$fontname = $this->FontFamily;	else $fontname = $ap[fontname];
	if(!isset($ap[fontstyle]))	$fontstyle = $this->FontStyle;	else $fontstyle = $ap[fontstyle];
	if(!isset($ap[fontsize]))	$fontsize = $this->FontSizePt;	else $fontsize = $ap[fontsize];
	if(!isset($ap[dotted]))		$dotted = false; else $dotted = $ap[dotted];
	if(!isset($ap[lprinted]))	$lprinted = true; else $lprinted =$ap[lprinted];
	if(isset($ap[ypos])) $this->SetY($ap[ypos]);
	if(isset($ap[xpos])) $this->SetX($ap[xpos]);
	if(isset($ap[linewidth])) $this->SetLineWidth($ap[linewidth]);
	$xfontname = $this->FontFamily; $xfontstyle = $this->FontStyle; $xfontsize = $this->FontSizePt;
//	echo "fontfamily=".$this->FontFamily." : style=".$fontstyle." : size=".$fontsize;
	if($dotted) $this->SetDash(0.2,0.4);
	$this->SetFont($fontname,$fontstyle,$fontsize);
	if($width==0) $width = $this->GetStringWidth($text);
	if($lprinted) $this->Cell($width,$height,$text,$border,$ln,$align,$fill,$link);
	if(($fontname != $xfontname) || ($fontstyle != $xfontstyle) || ($fontsize != $xfontsize)) $this->SetFont($xfontname,$xfontstyle,$xfontsize);
	if($dotted) $this->SetDash();
	if(isset($ap[linewidth])) $this->SetLineWidth(0.2);
	return true;
}

function tsdMultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
{
		if(!isset($ap[width]))		$width = 0;		else $width = $ap[width];
	if(!isset($ap[height]))		$height = 0;		else $height = $ap[height];
	if(!isset($ap[text]))			$text = "";			else $text = $ap[text];
	if(!isset($ap[border]))		$border = 0;		else $border = $ap[border];
	if(!isset($ap[ln]))			$ln = 0;				else $ln = $ap[ln];
	if(!isset($ap[align]))		$align = "";		else $align = $ap[align];
	if(!isset($ap[fill]))			$fill = 0;			else $fill = $ap[fill];
	if(!isset($ap[linkurl]))		$link = 0;			else $link = $ap[linkurl];
	if(!isset($ap[fontname]))	$fontname = $this->FontFamily;	else $fontname = $ap[fontname];
	if(!isset($ap[fontstyle]))	$fontstyle = $this->FontStyle;	else $fontstyle = $ap[fontstyle];
	if(!isset($ap[fontsize]))	$fontsize = $this->FontSizePt;	else $fontsize = $ap[fontsize];
	if(!isset($ap[dotted]))		$dotted = false; else $dotted = $ap[dotted];
	if(!isset($ap[lprinted]))	$lprinted = true; else $lprinted =$ap[lprinted];
	if(isset($ap[ypos])) $this->SetY($ap[ypos]);
	if(isset($ap[xpos])) $this->SetX($ap[xpos]);
	if(isset($ap[linewidth])) $this->SetLineWidth($ap[linewidth]);
	$xfontname = $this->FontFamily; $xfontstyle = $this->FontStyle; $xfontsize = $this->FontSizePt;
//	echo "fontfamily=".$this->FontFamily." : style=".$fontstyle." : size=".$fontsize;
	if($dotted) $this->SetDash(0.2,0.4);
	$this->SetFont($fontname,$fontstyle,$fontsize);
	if($width==0) $width = $this->GetStringWidth($text);
//	if($lprinted) $this->Cell($width,$height,$text,$border,$ln,$align,$fill,$link);
	if(($fontname != $xfontname) || ($fontstyle != $xfontstyle) || ($fontsize != $xfontsize)) $this->SetFont($xfontname,$xfontstyle,$xfontsize);
	if($dotted) $this->SetDash();
	if(isset($ap[linewidth])) $this->SetLineWidth(0.2);

	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(strpos($border,'L')!==false)
				$b2.='L';
			if(strpos($border,'R')!==false)
				$b2.='R';
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			if($this->ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
			continue;
		}
		if($c==' ')
		{
			$sep=$i;
			$ls=$l;
			$ns++;
		}
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			}
			else
			{
				if($align=='J')
				{
					$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
				}
				$this->MCell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
		}
		else
			$i++;
	}
	//Last chunk
	if($this->ws>0)
	{
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b.='B';
	$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
	$this->x=$this->lMargin;
}

/****************************************************************************************
* ประเภท  : Function	ของ Class FPDF_TH													
* อ้างอิง	   : Function Cell	ของ Class FPDF												
* การทำงาน  : ใช้ในการพิมพ์ข้อความทีละบรรทัดของเอกสาร PDF 											
* รูบแบบ  : Cell (	$w = ความกว้างของCell,													
*					$h = ความสูงของCell,													
*					$txt = ข้อความที่จะพิมพ์,													
*					$border = กำหนดการแสดงเส้นกรอบ(0 = ไม่แสดง, 1= แสดง),					
*					$ln = ตำแหน่งที่อยู่ถัดไปจากเซลล์(0 = ขวา, 1 = บรรทัดถัดไป, 2 = ด้านล่าง),
*					$align = ตำแหน่งข้อความ(L = ซ้าย, R = ขวา, C = กึ่งกลาง, T = บน, B = ล่าง),	
*					$fill = กำหนดการแสดงสีของCell(0 = ไม่แสดง, 1 = แสดง),					
*					$link = URL ที่ต้องการให้ข้อความเชื่อมโยงไปถึง									
*				)	
*****************************************************************************************/
function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{
	$this->checkFill="";
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
	{
		//ขึ้นหน้าใหม่อัตโนมัต
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3f Tw',$ws*$k));
		}
	}
	//กำหนดความกว้างเซลล์เท่ากับหน้ากระดาษ
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$this->s_th='';
	//กำหนดการแสดงเส้นกรอบ 4 ด้าน และสีกรอบ
	if($fill==1 || $border==1)
	{
		if($fill==1)
			$op=($border==1) ? 'B' : 'f';
		else
			$op='S';
		$this->s_th=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		if($op=='f')
			$this->checkFill=$op;
	}
	//กำหนดการแสดงเส้นกรอบทีละเส้น
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(strpos($border,'L')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}


	if($txt!=='')
	{			
		$x=$this->x;
		$y=$this->y;
		//กำหนดการจัดข้อความในเซลล์ตามแนวระดับ
		if(strpos($align,'R')!==false)
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif(strpos($align,'C')!==false)
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		//กำหนดการจัดข้อความในเซลล์ตามแนวดิ่ง
		if(strpos($align,'T')!==false)
			$dy=$h-(.7*$this->k*$this->FontSize);
		elseif(strpos($align,'B')!==false)
			$dy=$h-(.3*$this->k*$this->FontSize);
		else
			$dy=.5*$h;
		//กำหนดการขีดเส้นใต้ข้อความ
		if($this->underline)
		{	
			//กำหนดบันทึกกราฟิก
			if($this->ColorFlag)
				$this->s_th.=' q '.$this->TextColor.' ';
			//ขีดเส้นใต้ข้อความ0
			$this->s_th.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			//กำหนดคืนค่ากราฟิก
			if($this->ColorFlag)
				$this->s_th.=' Q ';
		}
		//กำหนดข้อความเชื่อมโยงไปถึง
		if($link)
			$this->Link($this->x,$this->y,$this->GetStringWidth($txt),$this->FontSize,$link);
		/*if($s)
			$this->_out($s);
		$s='';*/
		//ตัดอักษรออกจากข้อความ ทีละตัวเก็บลงอะเรย์
		$this->array_th=substr($txt,0);
		$i=0;
		$this->pointY=($this->h-($y+$dy+.3*$this->FontSize))*$k;
		$this->curPointX=($x+$dx)*$k;
		$this->string_th='';
		$this->txt_error=0;

		while($i<=strlen($txt))
		{	
			//กำหนดตำแหน่งที่จะพิมพ์อักษรในเซลล์
			$this->pointX=($x+$dx+.02*$this->GetStringWidth($this->array_th[$i-1]))*$k;
			if(($this->array_th[$i]=='่')||($this->array_th[$i]=='้')||($this->array_th[$i]=='๊')||($this->array_th[$i]=='๋')||($this->array_th[$i]=='์')||($this->array_th[$i]=='ิ')||($this->array_th[$i]=='ี')||($this->array_th[$i]=='ึ')||($this->array_th[$i]=='ื')||($this->array_th[$i]=='็')||($this->array_th[$i]=='ั')||($this->array_th[$i]=='ำ')||($this->array_th[$i]=='ุ')||($this->array_th[$i]=='ู'))
			{
				//ตรวจสอบอักษร ปรับตำแหน่งและทำการพิมพ์
				$this->_checkT($i);

				if($this->txt_error==0)
					$this->string_th.=$this->array_th[$i];
				else
				{
					$this->txt_error=0;
				}
			}
			else
				$this->string_th.=$this->array_th[$i];

			//เลื่อนตำแหน่ง x ไปที่ตัวที่จะพิมพ์ถัดไป
			$x=$x+$this->GetStringWidth($this->array_th[$i]);
			$i++;
		}
		$this->TText($this->curPointX,$this->pointY,$this->string_th);
		/*$this->s_th.=$this->s_hidden.$this->s_error;*/
		//$this->s_th.=$this->s_error;
		if($this->s_th)
			$this->_out($this->s_th);
	}
	else
		//นำค่าไปแสดงเมื่อไม่มีข้อความ
		$this->_out($this->s_th);

	$this->lasth=$h;
	//ตรวจสอบการวางตำแหน่งของเซลล์ถัดไป
	if($ln>0)
	{
		//ขึ้นบรรทัดใหม่
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

/********************************************************************************
* ใช้งาน: Function	Cell ของ Class FPDF_TH										
* การทำงาน: ใช้ในการตรวจสอบอักษร และปรับตำแหน่งก่อนที่จะทำการพิมพ์							
* ความต้องการ: $this->array_th = อะเรย์ของอักษรที่ตัดออกจากข้อความ						
*						$i = ลำดับปัจจุบันในอะเรย์ที่จะทำการตรวจสอบ						
*						$s = สายอักขระของโคด PDF
*********************************************************************************/
function _checkT($i)
{   
	$pointY=$this->pointY;
	$pointX=$this->pointX;
	//ตวจสอบการแสดงผลของตัวอักษรเหนือสระบน
	if($this->_errorTh($this->array_th[$i])==1)
	{
		//ตรวจสอบตัวอักษรก่อนหน้านั้นไม่ใช่สระบน ปรับตำแหน่งลง	
		if(($this->_errorTh($this->array_th[$i-1])!=2)&&($this->array_th[$i+1]!="ำ"))
		{
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointY=$this->pointY-.2*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{
				$pointY=$this->pointY-.23*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				$pointY=$this->pointY-.17*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
		}
			
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
		if($this->_errorTh($this->array_th[$i-1])==3)		
		{
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{			
				$pointX=$this->pointX-.25*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				$pointX=$this->pointX-.4*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
		}

		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นไปอีกเป็นตัวอักษรหางยาวบน	
		if($this->_errorTh($this->array_th[$i-2])==3)	
		{					
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{						
				$pointX=$this->pointX-.25*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				//$pointX=$this->pointX-.4*$this->GetStringWidth($this->array_th[$i-2])*$this->k;	
				$pointX=$this->pointX-.4*$this->GetStringWidth($this->array_th[$i])*$this->k;		//edit on 03/04/55					
				$this->txt_error=1;
			}
		}
	}
	//จบการตรวจสอบตัวอักษรเหนือสระบน

	//ตวจสอบการแสดงผลของตัวอักษรสระบน
	elseif($this->_errorTh($this->array_th[$i])==2)
	{
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
		if($this->_errorTh($this->array_th[$i-1])==3)	
		{
			$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
			$this->txt_error=1;
		}
		//ถ้าตัวนั้นเป็นสระอำ
		if($this->array_th[$i]=="ำ")
			//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
			if($this->_errorTh($this->array_th[$i-2])==3)	
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
	}																						
	//จบการตรวจสอบตัวอักษรสระบน

	//ตวจสอบการแสดงผลของตัวอักษรสระล่าง
	elseif($this->_errorTh($this->array_th[$i])==6)
	{
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษร ญ. กับ ฐ.
		if($this->_errorTh($this->array_th[$i-1])==5)						
		{	//$this->string_th		$this->curPointX
			$this->TText($this->curPointX,$this->pointY,$this->string_th);
			$this->string_th='';
			$this->curPointX=$this->pointX;

			if($this->checkFill=='f')
				$this->s_th.=' q ';
			else
				$this->s_th.=' q 1 g ';
			//สร้างสี่เหลี่ยมไปปิดที่ฐานล่างของตัวอักษร ญ. กับ ฐ. $s.
			$this->s_th.=sprintf('%.2f %.2f %.2f %.2f re f ',$this->pointX-$this->GetStringWidth($this->array_th[$i-1])*$this->k,$this->pointY-.27*$this->FontSize*$this->k,.9*$this->GetStringWidth($this->array_th[$i-1])*$this->k,.25*$this->FontSize*$this->k);
			$this->s_th.=' Q ';

			$this->txt_error=1;
		}
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นอักขระ ฏ. กับ ฎ.
		elseif($this->_errorTh($this->array_th[$i-1])==4)							
		{
			$pointY=$this->pointY-.25*$this->FontSize*$this->k;
			$this->txt_error=1;
		}
		//จบการตรวจสอบตัวอักษรสระล่าง
	}																						
	//จบการตรวจสอบตัวอักษระสระล่าง
		
	if($this->txt_error==1)
		$this->TText($pointX,$pointY,$this->array_th[$i]);
}

/********************************************************************************
* ใช้งาน: Function	_checkT ของ Class FPDF_TH				
* การทำงาน: ใช้ในการตรวจสอบอักษรที่อาจจะทำให้เกิดการพิมพ์ที่ผิดพลาด			
* ความต้องการ: $char_th = ตัวอักษรที่จะใช้ในการเปรียบเทียบ			
*********************************************************************************/
function _errorTh($char_th)
{	
	$txt_error=0;
	//ตัวอักษรบน-บน
	if(($char_th=='่')||($char_th=='้')||($char_th=='๊')||($char_th=='๋')||($char_th=='์'))
		$txt_error=1;
	//ตัวอักษรบน
	elseif(($char_th=='ิ')||($char_th=='ี')||($char_th=='ึ')||($char_th=='ื')||($char_th=='็')||($char_th=='ั')||($char_th=='ำ'))
		$txt_error=2;
	//ตัวอักษรกลาง-บน
	elseif(($char_th=='ป')||($char_th=='ฟ')||($char_th=='ฝ'))
		$txt_error=3;
	//ตัวอักษรกลาง-ล่าง
	elseif(($char_th=='ฎ')||($char_th=='ฏ'))
		$txt_error=4;
	//ตัวอักษรกลาง-ล่าง
	elseif(($char_th=='ญ')||($char_th=='ฐ'))
		$txt_error=5;
	//ตัวอักษรสระล่าง
	elseif(($char_th=='ุ')||($char_th=='ู'))
		$txt_error=6;
	else
		$txt_error=0;
	return $txt_error;
}

/********************************************************************************
* ใช้งาน: Function	_checkT ของ Class FPDF_TH									*
* การทำงาน: ใช้ในพิมพ์ตัวอักษรที่ตรวจสอบแล้ว									*
* ความต้องการ: $txt_th = ตัวอักษร 1 ตัว ที่ตรวจสอบแล้ว							*
*						$s = สายอักขระของโคด PDF								*
*********************************************************************************/
function TText($pX,$pY,$txt_th)
{	
	//ตวจสอบการใส่สีเซลล์
	if($this->ColorFlag)
		$this->s_th.=' q '.$this->TextColor.' ';
	$txt_th2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt_th)));
	//ระบุตำแหน่ง และพิมพ์ตัวอักษร
	$this->s_th.=sprintf(' BT %.2f %.2f Td (%s) Tj ET ',$pX,$pY,$txt_th2);
	if($this->ColorFlag)
		$this->s_th.=' Q ';
}

/****************************************************************************************
* ใช้งาน: called by function MultiCell within this class								
* อ้างอิง: Function Cell	ของ Class FPDF												
* การทำงาน: ใช้ในการพิมพ์ข้อความทีละบรรทัดของเอกสาร PDF 											
* รูบแบบ: MCell (	$w = ความกว้างของCell,													
*					$h = ความสูงของCell,													
*					$txt = ข้อความที่จะพิมพ์,													
*					$border = กำหนดการแสดงเส้นกรอบ(0 = ไม่แสดง, 1= แสดง),					
*					$ln = ตำแหน่งที่อยู่ถัดไปจากเซลล์(0 = ขวา, 1 = บรรทัดถัดไป, 2 = ด้านล่าง),
*					$align = ตำแหน่งข้อความ(L = ซ้าย, R = ขวา, C = กึ่งกลาง, T = บน, B = ล่าง),	
*					$fill = กำหนดการแสดงสีของCell(0 = ไม่แสดง, 1 = แสดง)			
*					$link = URL ที่ต้องการให้ข้อความเชื่อมโยงไปถึง		
*				)
*****************************************************************************************/
function MCell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='',$uword=''){
	if($uword!='' && strpos($txt,$uword)){
		
		if(gettype($border)=='string'){
			$border1 = "L";
			$border2 = "R";
		}else{
			$border1 = 0;
			$border2 = 0;
		}
		$text = substr($txt,0,strpos($txt,$uword));
		$strwidth = $this->GetStringWidth($text);
		$this->Cell($strwidth,$h,$text,$border1,0,'L');
		$this->underline = true;
		$this->Cell($this->GetStringWidth($uword),$h,$uword,0,0,'L');
		$this->underline = false;
		$text = substr($txt,strpos($txt,$uword)+strlen($uword));
		$strwidth2 = $w-$strwidth-$this->GetStringWidth($uword);
		$this->Cell($strwidth2,$h,$text,$border2,0,'L');
		
	}else{
		$this->checkFill="";
		$k=$this->k;
		if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
		{
			//ขึ้นหน้าใหม่อัตโนมัต
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			{
				$this->ws=$ws;
				$this->_out(sprintf('%.3f Tw',$ws*$k));
			}
		}
		//กำหนดความกว้างเซลล์เท่ากับหน้ากระดาษ
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$this->s_th='';
		//กำหนดการแสดงเส้นกรอบ 4 ด้าน และสีกรอบ
		if($fill==1 || $border==1)
		{
			if($fill==1)
				$op=($border==1) ? 'B' : 'f';
			else
				$op='S';
			$this->s_th=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
			if($op=='f')
				$this->checkFill=$op;
		}
		//กำหนดการแสดงเส้นกรอบทีละเส้น
		if(is_string($border))
		{
			$x=$this->x;
			$y=$this->y;
			if(strpos($border,'L')!==false)
				$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'T')!==false)
				$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(strpos($border,'R')!==false)
				$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'B')!==false)
				$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		}
	
	
		if($txt!=='')
		{			
			$x=$this->x;
			$y=$this->y;
			//กำหนดการจัดข้อความในเซลล์ตามแนวระดับ
			if(strpos($align,'R')!==false)
				$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
			elseif(strpos($align,'C')!==false)
				$dx=($w-$this->GetStringWidth($txt))/2;
			else
				$dx=$this->cMargin;
			//กำหนดการจัดข้อความในเซลล์ตามแนวดิ่ง
			if(strpos($align,'T')!==false)
				$dy=$h-(.7*$this->k*$this->FontSize);
			elseif(strpos($align,'B')!==false)
				$dy=$h-(.3*$this->k*$this->FontSize);
			else
				$dy=.5*$h;
			//กำหนดการขีดเส้นใต้ข้อความ
			
			if($this->underline)
			{	
				//กำหนดบันทึกกราฟิก
				if($this->ColorFlag)
					$this->s_th.='q '.$this->TextColor.' ';
				//ขีดเส้นใต้ข้อความ0
				$this->s_th.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
				//กำหนดคืนค่ากราฟิก
				if($this->ColorFlag)
					$this->s_th.=' Q';
			}
			//กำหนดข้อความเชื่อมโยงไปถึง
			if($link)
				$this->Link($this->x,$this->y,$this->GetStringWidth($txt),$this->FontSize,$link);
			if($this->s_th)
				$this->_out($this->s_th);
			$this->s_th='';
			//ตัดอักษรออกจากข้อความ ทีละตัวเก็บลงอะเรย์
			$this->array_th=substr($txt,0);
			$i=0;
	
			while($i<=strlen($txt))
			{	
				//กำหนดตำแหน่งที่จะพิมพ์อักษรในเซลล์
				$this->pointX=($x+$dx+.02*$this->GetStringWidth($this->array_th[$i-1]))*$k;
				$this->pointY=($this->h-($y+$dy+.3*$this->FontSize))*$k;
				//ตรวจสอบอักษร ปรับตำแหน่งและทำการพิมพ์
				$this->_checkT($i);
				if($this->txt_error==0)
					$this->TText($this->pointX,$this->pointY,$this->array_th[$i]);
				else
				{
					$this->txt_error=0;
				}
				//ตรวจสอบการใส่เลขหน้า
				if($this->array_th[$i]=='{'&&$this->array_th[$i+1]=='n'&&$this->array_th[$i+2]=='b'&&$this->array_th[$i+3]=='}')
					$i=$i+3;
				//เลื่อนตำแหน่ง x ไปที่ตัวที่จะพิมพ์ถัดไป
				$x=$x+$this->GetStringWidth($this->array_th[$i]);
				$i++;
			}
			$this->_out($this->s_th);
		}
		else
			//นำค่าไปแสดงเมื่อไม่มีข้อความ
			$this->_out($this->s_th);
	
		$this->lasth=$h;
		//ตรวจสอบการวางตำแหน่งของเซลล์ถัดไป
		if($ln>0)
		{
			//ขึ้นบรรทัดใหม่
			$this->y+=$h;
			if($ln==1)
				$this->x=$this->lMargin;
		}
		else
			$this->x+=$w;
	}
}

function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }


    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

	function Rotate($angle, $x=-1, $y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}
//    $this->Rotate(45,55,190);
//    $this->Text(55,190,$texte);
//    $this->Rotate(0);

	function EAN13($x, $y, $barcode, $h=16, $w=.35)
	{
	    $this->Barcode($x,$y,$barcode,$h,$w,13);
	}
	
	function UPC_A($x, $y, $barcode, $h=16, $w=.35)
	{
	    $this->Barcode($x,$y,$barcode,$h,$w,12);
	}
	
	function GetCheckDigit($barcode)
	{
	    //Compute the check digit
	    $sum=0;
	    for($i=1;$i<=11;$i+=2)
	        $sum+=3*$barcode[$i];
	    for($i=0;$i<=10;$i+=2)
	        $sum+=$barcode[$i];
	    $r=$sum%10;
	    if($r>0)
	        $r=10-$r;
	    return $r;
	}
	
	function TestCheckDigit($barcode)
	{
	    //Test validity of check digit
	    $sum=0;
	    for($i=1;$i<=11;$i+=2)
	        $sum+=3*$barcode[$i];
	    for($i=0;$i<=10;$i+=2)
	        $sum+=$barcode[$i];
	    return ($sum+$barcode[12])%10==0;
	}
	
	function Barcode($x, $y, $barcode, $h, $w, $len)
	{
	    //Padding
	    $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
	    if($len==12)
	        $barcode='0'.$barcode;
	    //Add or control the check digit
	    if(strlen($barcode)==12)
	        $barcode.=$this->GetCheckDigit($barcode);
	    elseif(!$this->TestCheckDigit($barcode))
	        $this->Error('Incorrect check digit');
	    //Convert digits to bars
	    $codes=array(
	        'A'=>array(
	            '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
	            '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
	        'B'=>array(
	            '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
	            '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
	        'C'=>array(
	            '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
	            '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
	        );
	    $parities=array(
	        '0'=>array('A','A','A','A','A','A'),
	        '1'=>array('A','A','B','A','B','B'),
	        '2'=>array('A','A','B','B','A','B'),
	        '3'=>array('A','A','B','B','B','A'),
	        '4'=>array('A','B','A','A','B','B'),
	        '5'=>array('A','B','B','A','A','B'),
	        '6'=>array('A','B','B','B','A','A'),
	        '7'=>array('A','B','A','B','A','B'),
	        '8'=>array('A','B','A','B','B','A'),
	        '9'=>array('A','B','B','A','B','A')
	        );
	    $code='101';
	    $p=$parities[$barcode[0]];
	    for($i=1;$i<=6;$i++)
	        $code.=$codes[$p[$i-1]][$barcode[$i]];
	    $code.='01010';
	    for($i=7;$i<=12;$i++)
	        $code.=$codes['C'][$barcode[$i]];
	    $code.='101';
	    //Draw bars
	    for($i=0;$i<strlen($code);$i++)
	    {
	        if($code[$i]=='1')
	            $this->Rect($x+$i*$w,$y,$w,$h,'F');
	    }
	    //Print text uder barcode
	    //$this->SetFont('Arial','',12);
	    //$this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
	}
	
	function StartPageGroup()
    {
        $this->NewPageGroup=true;
    }

    // current page in the group
    function GroupPageNo()
    {
        return $this->PageGroups[$this->CurrPageGroup];
    }

    // alias of the current page group -- will be replaced by the total number of pages in this group
    function PageGroupAlias()
    {
        return $this->CurrPageGroup;
    }

    function _beginpage($orientation,$format)
    {
        parent::_beginpage($orientation,$format);
        if($this->NewPageGroup)
        {
            // start a new group
            $n = sizeof($this->PageGroups)+1;
            $alias = "{nb$n}";
            $this->PageGroups[$alias] = 1;
            $this->CurrPageGroup = $alias;
            $this->NewPageGroup=false;
        }
        elseif($this->CurrPageGroup)
            $this->PageGroups[$this->CurrPageGroup]++;
    }

    function _putpages()
    {
        $nb = $this->page;
        if (!empty($this->PageGroups))
        {
            // do page number replacement
            foreach ($this->PageGroups as $k => $v)
            {
                for ($n = 1; $n <= $nb; $n++)
                {
                    $this->pages[$n]=str_replace($k, $v, $this->pages[$n]);
                }
            }
        }
        parent::_putpages();
    }

//End of class
}

?>
