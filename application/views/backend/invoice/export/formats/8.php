<?php
//this is the Default Format

$list = array();
$entryType = 1;
$ref = 0;
$groupNumber = 0;
$extraCounter = 1;

foreach($jobInfo as $v){
    if($exportOption['show_job_header'] == "true"){
        $list[] = array(
            $fran_est_num,
            str_replace(",", ";", htmlspecialchars_decode($v->merref)),
            str_replace(",", ";", htmlspecialchars_decode($v->jobname))
        );
        $ref++;
    }

    $list[] = array(
        $entryType,
        str_replace(",", ";", htmlspecialchars_decode($v->merref)),
        $ref
    );
    $ref++;
}

$entryType = 2;
foreach($tags as $tv){
    $thisTags = str_replace(",", ";", htmlspecialchars_decode($tv->tag));
    if($exportOption['apply_tag_code'] == "false"){
        $pattern = "[\[TR\d{3,4}H\d{1,2}\] TRUSS CENTRES \d{3,4}mm; TRUSS TREATMENT H\d\.\d{1,2}]";
        $thisTags = preg_replace($pattern, "", $thisTags);
    }

    $thisTags = preg_replace("/<br\W*?\/>/", "\n", $thisTags);
    $pieces = preg_split("/[\n]/", $thisTags);
    foreach($pieces as $v){
        $tag = str_replace(",", "-", $v);
        $tagAr = split_string($tag);

        $tagRef = 1;
        foreach($tagAr as $key=>$tg){
            $thisTg = trim($tg);
            if($thisTg){
                $list[] = array(
                    $entryType,
                    "9998",
                    $ref,
                    $tagRef,
                    $thisTg
                );
                $tagRef ++;
                $ref ++;
            }
        }
    }
}

if(count($detailsCompile) > 0){
    foreach($detailsCompile as $subhead_key=>$material){
        $subhead_description = array_key_exists('subhead_description', $material) ? $material['subhead_description'] : "";
        if($subhead_description){
            $groupNumber++;
            $extraCounter = 1;
            $entryType = 3;

            $list[] = array(
                $entryType,
                $groupNumber,
                $subhead_description
            );
        }

        $material_items = array_key_exists('materials', $material) ? $material['materials'] : array();
        if(count($material_items) > 0){
            foreach($material_items as $v){
                if($v->is_mf_comment){
                    $entryType = 7;
                    $list[] = array(
                        $entryType,
                        $groupNumber,
                        $ref,
                        $extraCounter,
                        strtoupper($v->current_header),
                        '"' . strtoupper($v->description) . '"'
                    );

                    $extraCounter++;
                }
                else{
                    $extraCounter = 1;
                    $entryType = 4;

                    $sku_code = $v->product_code;
                    $description = $v->product_description;

                    $list[] = array(
                        $entryType,
                        $groupNumber,
                        $ref,
                        "D",
                        $sku_code,
                        $description,
                        $v->usage_description,
                        number_format($v->product_qty, 2, '.', ''),
                        $v->product_unit
                    );

                    if($v->product_length){
                        uasort($v->product_length, 'order_by_length');
                        if(count($v->product_length) > 0){
                            $newRef = 1;
                            foreach($v->product_length as $key=>$len){
                                if(ctype_digit($len)){
                                    $len = number_format($len, 1, '.', '');
                                }

                                $product_number = $v->product_number;
                                $num = $product_number[$key];

                                $entryType = 5;
                                $list[] = array(
                                    $entryType,
                                    $groupNumber,
                                    $ref,
                                    $newRef,
                                    $sku_code,
                                    $num,
                                    $len
                                );
                                $newRef++;
                            }
                        }
                    }
                }

                $ref++;
            }
        }
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