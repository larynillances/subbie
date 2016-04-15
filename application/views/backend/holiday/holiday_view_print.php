<?php
require_once(realpath(APPPATH ."../plugins/fpdf/break_line_fpdf.php"));
ini_set("memory_limit","512M");
set_time_limit(900000);

$pdf = new PDF_Break_Line("L");

$width = array(80, 70, 20, 110);

$pdf->lineBreak = 0;
$pdf->widths = $width;
$pdf->headerTitle = (Object)array(
    (Object)array(
        'title' => array('Holiday' . (count($holidays) > 1 ? 's' : '')),
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
        'Widths' => array(array_sum($pdf->widths)),
        'Aligns' => array('L')
    ),
    (Object)array(
        'title' => array('Title', 'Date', 'Type', 'Description'),
        'FontFamily' => 'Arial',
        'FontStyle' => '',
        'FontSizePt' => 10,
        'TextColor' => (Object)array(
                'r' => 255,
                'g' => 255,
                'b' => 255
            ),
        'FillColor' => (Object)array(
                'r' => 0,
                'g' => 0,
                'b' => 0
            ),
        'Fills' => array(1, 1, 1, 1),
        'Widths' => $pdf->widths,
        'Aligns' => array('C', 'C', 'C', 'C'),
        'borderSize' => 1
    )
);

$pdf->AddPage();
$pdf->AliasNbPages();

//Table with 20 rows and 4 columns
srand(microtime()*1000000);

$pdf->SetFont('Helvetica', '', 9);
if(count($holidays) > 0){
    foreach($holidays as $v){
        $pdf->SetFills(array(0, 0, 0, 0));
        $pdf->aligns = array('L', 'C', 'C', 'L');
        $row = array(
            str_replace("&#039;", "'", $v->holiday),
            date('j/n/Y', strtotime($v->date)) . ($v->date_to ? ' to ' . date('j/n/Y', strtotime($v->date_to)) : ''),
            $v->type,
            $v->description
        );
        $pdf->Row($row);
    }
}
else{
    $pdf->SetFills(array(0));
    $pdf->aligns = array('C');
    $pdf->widths = array(array_sum($pdf->widths));
    $row = array('No Result');
    $pdf->Row($row);
}
$filename = "Holiday_" . date('Ymd-Hi');
$pdf->Output($filename . ".pdf", "I");