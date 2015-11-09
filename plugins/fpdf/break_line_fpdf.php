<?php
require(realpath(APPPATH ."../plugins/fpdf/fpdf.php"));

class PDF_Break_Line extends FPDF
{
    var $widths;
    var $aligns;
    var $fillColor;
    var $lineBreak = 1;
    var $borderSize = 1;
    var $fills;
    var $textColor;
    var $rowHeight = 5;

    var $header;
    var $headerTitle;
    var $headerIsSet = false;
    var $headerOption;

    var $defaultOption;

    var $currentMaxHeight;
    // Page header
    function Header()
    {
        //to get all the default option
        $this->defaultOption = (Object)array(
            'FontFamily' => $this->FontFamily,
            'FontStyle' => $this->FontStyle,
            'FontSizePt' => $this->FontSizePt,
            'TextColor' => $this->textColor,
            'FillColor' => $this->fillColor,
            'Fills' => $this->fills,
            'Widths' => $this->widths,
            'Aligns' => $this->aligns
        );

        //to get the header default option
        if(!$this->headerIsSet){
            $this->headerOption = (Object)array(
                'FontFamily' => $this->FontFamily,
                'FontStyle' => $this->FontStyle,
                'FontSizePt' => $this->FontSizePt,
                'TextColor' => $this->textColor,
                'FillColor' => $this->fillColor,
                'Fills' => $this->fills,
                'Widths' => $this->widths,
                'Aligns' => $this->aligns
            );

            $this->headerIsSet = true;
        }

        $headerOption = $this->headerOption;

        //if header titles exist
        if(count($this->headerTitle) > 0){
            foreach($this->headerTitle as $v){
                $this->SetFont($v->FontFamily, $v->FontStyle, $v->FontSizePt);
                $this->SetTextColor($v->TextColor->r, $v->TextColor->g, $v->TextColor->b);
                $this->SetFillColor($v->FillColor->r, $v->FillColor->g, $v->FillColor->b);
                $this->SetFills($v->Fills);
                $this->widths = $v->Widths;
                $this->aligns = $v->Aligns;

                $this->Row($v->title, array_key_exists('borderSize', $v) ? $v->borderSize : 0);
            }
        }
        $this->Ln($this->lineBreak);

        $this->SetFont($headerOption->FontFamily, $headerOption->FontStyle, $headerOption->FontSizePt);
        //$this->SetTextColor($headerOption->TextColor->r, $headerOption->TextColor->g, $headerOption->TextColor->b);
        //$this->SetFillColor($headerOption->FillColor->r, $headerOption->FillColor->g, $headerOption->FillColor->b);
        $this->SetFills($headerOption->Fills);
        $this->widths = $headerOption->Widths;
        $this->aligns = $headerOption->Aligns;

        $this->Row($this->header);

        //to roll back the options to normal
        $defaultOption = $this->defaultOption;
        $this->SetFont($defaultOption->FontFamily, $defaultOption->FontStyle, $defaultOption->FontSizePt);
        //$this->SetTextColor($defaultOption->TextColor->r, $defaultOption->TextColor->g, $defaultOption->TextColor->b);
        //$this->SetFillColor($defaultOption->FillColor->r, $defaultOption->FillColor->g, $defaultOption->FillColor->b);
        $this->SetFills($defaultOption->Fills);
        $this->widths = $defaultOption->Widths;
        $this->aligns = $defaultOption->Aligns;
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(20,10, date('d/m/Y - h:i a'), 0, 0);
        $this->Cell(0,10,'Page ' . $this->PageNo() . " of {nb}",0,0,'C');
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
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

    function SetTextsColor($r, $g = null, $b = null){
        $this->textColor = (Object)array(
            'r' => $r,
            'g' => $g,
            'b' => $r
        );
    }

    function Row($data, $border = 1)
    {
        //Calculate the height of the row
        $nb = 0;
        for($i=0;$i<count($data);$i++){
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        $h = $this->rowHeight * $nb;
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
            if($border > 0){
                $this->Rect($x,$y,$w,$h);
            }
            //Print the text
            $thisData = $data[$i];
            $thisHeight = $h;
            $thisBorder = $border;
            $thisNB = $this->NbLines($this->widths[$i], $data[$i]);
            if($thisNB > 1){
                //$thisHeight = $h - ($this->rowHeight * ($thisNB - 1));
                $thisHeight = 5;
                if($nb > $thisNB){
                    $thisBorder = 0;
                }
            }

            $this->MultiCell($w, $thisHeight, $thisData, $thisBorder, $a, $f);
            $this->currentMaxHeight = $thisHeight;
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
        $w_max=($w-2*$this->cMargin)*1000/$this->FontSize;
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
            if($l>$w_max)
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