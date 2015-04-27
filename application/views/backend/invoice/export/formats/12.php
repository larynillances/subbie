<?php
$ref = 0;
$list = array();

$sort = array();
$order_by_subhead = array();
$order_by_usage = array();
$order_by_sku = array();

if(count($detailsCompile) > 0){
    foreach($detailsCompile as $subhead_key=>$material){
        $material_items = array_key_exists('materials', $material) ? $material['materials'] : array();
        if(count($material_items) > 0){
            foreach($material_items as $k=>$v){
                if($v->is_mf_comment){
                    continue;
                }

                $s = str_replace(",", ";", htmlspecialchars_decode($material['subheading_bm']));
                $u = str_replace(",", ";", htmlspecialchars_decode($v->usage_bm ? $v->usage_bm : "600 OTHER ITEMS"));
                $list[] = array(
                    $s,
                    $u,
                    $v->product_sku,
                    $v->product_qty,
                    $v->product_code,
                    $v->product_description
                );

                $sort['s'][] = $s;
                $sort['u'][] = $u;
                $sort['c'][] = $v->product_sku;
            }
        }

        $ref++;
    }
}

array_multisort($sort['s'], SORT_ASC, $sort['u'], SORT_ASC, $sort['c'], SORT_ASC, $list);

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