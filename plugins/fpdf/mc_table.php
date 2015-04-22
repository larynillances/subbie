<?php
require(realpath(APPPATH ."../plugins/fpdf/fpdf.php"));

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;
    var $fills;

    var $headerColumn;
    var $search;
    var $count;
    var $title;
    // Page header
    function Header()
    {
        // Logo
        $this->Image(base_url() . 'images/estimatorlogo.png',10,6,50);
        // Move to the right
        $this->Cell(60);
        if(is_array($this->title)){
            foreach($this->title as $k=>$v){
                // Title
                $this->SetFont('Arial','B',$v['size']);
                $this->Cell(40,2,$v['title'],0,0);
                //$this->Ln(4);
            }
        }else{
            $this->Cell(30,10,$this->title,0,0,'C');
        }
        $this->Ln(4);

        $this->SetFont('Arial','B',12);
        $this->SetXY(12, 12);
        $this->Cell(150,10,$this->search,0,0,'C');
        $this->SetX(180);
        $this->Cell(30,10,'(' . $this->count . ')',0,0,'C');
        // Line break
        $this->Ln(12);

        $this->SetFont('Arial','B',10);

        $a = array('C', 'C', 'C', 'C', 'C', 'C', 'C');
        $f = array(true, true, true, true, true, true, true);
        $this->SetAligns($a);
        $this->SetFillColor(119,119,119);
        $this->SetFills($f);
        $this->Row($this->headerColumn);

        $this->SetFont('Arial','',8);
        $a = array('C', 'C', 'L', 'C', 'C', 'C', 'C');
        $this->SetAligns($a);
        $f = array(false, false, false, false, false, false, false);
        $this->SetFills($f);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(20,10,date('d/m/Y - h:i a'),0,0);
        $this->Cell(0,10,'Page '.$this->PageNo() . " of {nb}",0,0,'C');
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function SetFills($f){
        //Set the array of column file
        $this->fills=$f;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $f=isset($this->fills[$i]) ? $this->fills[$i] : false;
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a,$f);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}