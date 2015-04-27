<?php
$ref = 0;
$list = array();

foreach($jobInfo as $v){
    $list[] = array(
        str_replace(",", ";", htmlspecialchars_decode($v->cusname)),
        str_replace(",", ";", htmlspecialchars_decode($merchant->merchant_code . "-" . $branch->branch_code)),
        '',
        ''
    );
    $ref++;
    $list[] = array(
        str_replace(",", ";", htmlspecialchars_decode($v->jobname)),
        str_replace(",", ";", htmlspecialchars_decode($v->road_name)),
        str_replace(",", ";", htmlspecialchars_decode($v->suburb_name)),
        str_replace(",", ";", htmlspecialchars_decode($v->city))
    );
    $ref++;
}

if(count($detailsCompile) > 0){
    foreach($detailsCompile as $subhead_key=>$material){
        $subhead_description = array_key_exists('subhead_description', $material) ? $material['subhead_description'] : "";
        $list[] = array(
            $subhead_description
        );

        $material_items = array_key_exists('materials', $material) ? $material['materials'] : array();
        if(count($material_items) > 0){
            foreach($material_items as $k=>$v){
                if($v->is_mf_comment){
                    continue;
                }

                $list[] = array(
                    $v->product_sku,
                    $v->product_description . " " . $v->usage_description,
                    $v->product_unit,
                    $v->product_qty,
                    $v->product_cost,
                    $v->product_retail
                );
            }
        }

        $ref++;
    }
}

if(count($tags) > 0){
    foreach($tags as $tv){
        $thisTags = str_replace(",", ";", htmlspecialchars_decode($tv->tag));
        if($exportOption['apply_tag_code'] == "false"){
            $pattern = "[\[TR\d{3,4}H\d{1,2}\] TRUSS CENTRES \d{3,4}mm; TRUSS TREATMENT H\d\.\d{1,2}]";
            $thisTags = preg_replace($pattern, "", $thisTags);
        }
        $thisTags = preg_replace("/<br\W*?\/>/", "\n", $thisTags);
        $pieces = preg_split("/[\n]/", $thisTags);
        foreach($pieces as $v){
            $tag = $v;
            $tagAr = split_string($tag);

            foreach($tagAr as $key=>$tg){
                $thisTg = trim($tg);
                if($thisTg){
                    $list[] = array($thisTg);
                }
            }
        }
    }
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