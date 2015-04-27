<?php
require_once(realpath(APPPATH ."../plugins/fpdf/break_line_fpdf.php"));
ini_set("memory_limit","512M");
set_time_limit(900000);

$pdf = new PDF_Break_Line("L");

$pdf->lineBreak = 0;
$pdf->headerTitle = (Object)array(
    (Object)array(
        'title' => array($title),
        'FontFamily' => 'Arial',
        'FontStyle' => 'B',
        'FontSizePt' => 14,
        'TextColor' => (Object)array(
                'r' => 0,
                'g' => 0,
                'b' => 0
            ),
        'FillColor' => (Object)array(
                'r' => 255,
                'g' => 255,
                'b' => 255
            ),
        'Fills' => array(0),
        'Widths' => array(400),
        'Aligns' => array('L')
    )
);

$pdf->AddPage();
$pdf->AliasNbPages();

//Table with 20 rows and 4 columns
srand(microtime()*1000000);

$pdf->Ln(1);

if(count($dump_codes_array) > 0){
    $pdf->Ln(2);

    $widths = array(15, 150, 30, 30);

    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetFills(array(0));
    $pdf->aligns = array('L');
    $pdf->widths = array(array_sum($widths));
    $row = array('Dump Code:');
    $pdf->Row($row, 0);

    $pdf->SetFont('Helvetica', '', 9);
    $pdf->widths = $widths;
    $pdf->SetFills(array(1, 1, 1, 1));
    $pdf->aligns = array('C', 'C', 'C', 'C');
    $pdf->SetFillColor(150, 150, 150);
    $row = array('#', 'Description', 'Qty', 'Unit');
    $pdf->Row($row);

    foreach($dump_codes_array as $v){
        $pdf->SetFills(array(0, 0, 0, 0));
        $pdf->SetFillColor(0, 0, 0);
        $pdf->aligns = array('C', 'L', 'C', 'C');

        $row = array(
            $v->ref,
            $v->description,
            $v->qty,
            $v->unit
        );
        $pdf->Row($row);
    }
}

if(count($empty_sku_array) > 0){
    $pdf->Ln(2);

    $widths = array(15, 20, 100, 30, 20, 20, 20, 20);

    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetFills(array(0));
    $pdf->aligns = array('L');
    $pdf->widths = array(array_sum($widths));
    $row = array('Empty SKU Code:');
    $pdf->Row($row, 0);

    $pdf->SetFont('Helvetica', '', 9);
    $pdf->widths = $widths;
    $pdf->SetFills(array(1, 1, 1, 1, 1, 1, 1, 1));
    $pdf->SetFillColor(150, 150, 150);
    $pdf->aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
    $row = array('#', 'Code', 'Description', 'SKU', 'Qty', 'Unit', 'Price 1', 'Price 2');
    $pdf->Row($row);

    foreach($empty_sku_array as $v){
        $pdf->SetFills(array(0, 0, 0, 0));
        $pdf->SetFillColor(0, 0, 0);
        $pdf->aligns = array('C', 'C', 'L', 'C', 'C', 'C');

        $row = array(
            $v->ref,
            $v->code,
            $v->description,
            '',
            number_format((float)$v->qty, 2, '.', ''),
            $v->unit,
            '',
            ''
        );
        $pdf->Row($row);
    }
}

$dir = "export/" . $fId . "/" . $whatId . "/pdf/";
if(!is_dir($dir)){
    mkdir($dir, 0777, TRUE);
}
$pdf->Output($dir . str_replace(" ", "_", $filename) . ".pdf", "F");