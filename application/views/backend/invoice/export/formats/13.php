<?php
$ref = 0;
$list = array();

if(count($detailsCompile) > 0){
    foreach($detailsCompile as $subhead_key=>$material){
        $material_items = array_key_exists('materials', $material) ? $material['materials'] : array();
        if(count($material_items) > 0){
            foreach($material_items as $k=>$v){
                if($v->is_mf_comment){
                    continue;
                }

                $s = str_replace(",", ";", htmlspecialchars_decode($material['subhead_description']));
                $u = str_replace(",", ";", htmlspecialchars_decode($v->usage_description));
                $list[] = array(
                    $v->product_sku ? $v->product_sku : 'NO SKU',
                    $v->product_qty,
                    $s,
                    $u
                );
            }
        }

        $ref++;
    }
}

$delimiter = ",";
$enclosure = '';
$fp = fopen('php://output', 'w');
foreach ($list as $fields) {
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