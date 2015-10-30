<?php
require_once(realpath(APPPATH ."../plugins/fpdf/break_line_fpdf.php"));
ini_set("memory_limit","512M");
set_time_limit(900000);

$pdf = new PDF_Break_Line("L");

$width = array(10, 40, 30, 198);

$pdf->lineBreak = 0;
$pdf->widths = $width;

$pdf->headerTitle = (Object)array(
    (Object)array(
        'title' => array('Leave Audit Log Print'),
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
        'title' => array('Type', 'Date', 'User', 'Changes'),
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

if(count($log) > 0){
    foreach($log as $v){
        $pdf->SetFills(array(0, 0, 0, 0));
        $pdf->aligns = array('C', 'C', 'C', 'L');

        $v->changes = str_replace("<br />", "\n", $v->changes);
        $t = preg_replace('/<[^<|>]+?>/', '', htmlspecialchars_decode($v->changes));
        $t = htmlentities($t, ENT_QUOTES, "UTF-8");

        $row = array(
            $v->type,
            $v->date,
            $v->name,
            $t
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

$filename = "Alternate_Drop_Mapping_Audit_" . date('Ymd-Hi');
$pdf->Output($filename . ".pdf", 'I');