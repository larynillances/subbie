<?php
$ref = 0;
$list = array();

if($exportOption['show_job_header'] == "true"){
    foreach($jobInfo as $v){
        $list[] = array(
            $fran_est_num,
            str_replace(",", ";", htmlspecialchars_decode($v->merref)),
            str_replace(",", ";", htmlspecialchars_decode($v->jobname))
        );
        $ref++;
    }
}

if(count($detailsCompile) > 0){
    foreach($detailsCompile as $subhead_key=>$material){
        //add a break indicator
        if($ref > 0){
            $list[] = array('11111111');
        }

        $subhead_description = array_key_exists('subhead_description', $material) ? $material['subhead_description'] : "";
        $list[] = array(
            $subhead_key,
            $subhead_description
        );

        $material_items = array_key_exists('materials', $material) ? $material['materials'] : array();
        if(count($material_items) > 0){
            foreach($material_items as $v){
                if($v->is_mf_comment){
                    continue;
                }

                $thisMaterial = array(
                    $v->product_code,
                    $v->product_description,
                    $v->usage_description,
                    $v->product_qty
                );
                if($v->product_length){
                    uasort($v->product_length, 'order_by_length');
                    if(count($v->product_length) > 0){
                        foreach($v->product_length as $key=>$len){
                            if(ctype_digit($len)){
                                $len = number_format($len, 1, '.', '');
                            }

                            $product_number = $v->product_number;
                            $num = $product_number[$key];

                            $thisMaterial[] = $num . "/" . $len;
                        }
                    }
                }

                $list[] = $thisMaterial;
            }
        }

        $ref++;
    }
}

function order_by_length($a, $b) {
    return $b > $a ? 1 : -1;
}

$delimiter = ",";
$enclosure = '';
$fp = fopen('php://output', 'w');
foreach ($list as $fields) {
    //fputcsv($fp, $fields, ",", "");
    fwrite($fp, $enclosure . implode($enclosure . $delimiter . $enclosure, $fields) . $enclosure . "\n");
}
fclose($fp);

if($isSave){
    $dir = "export/" . $fId . "/" . $whatId . "/csv/";
    if(!is_dir($dir)){
        mkdir($dir, 0777, TRUE);
    }
    file_put_contents($dir . $filename, ob_get_contents());
}
else{
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename=' . $filename);
}