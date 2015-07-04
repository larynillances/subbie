<?php
include('subbie.php');
include('notification_controller.php');

class invoice_export_controller extends CI_Controller{
    var $data;

    function __construct(){
        parent::__construct();
        $subbie = new Subbie();
        $this->data = $subbie->getUserInfo(true);
    }
    
    function jobExportOption(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }
        DisplayArray($this->data);exit;
        $whatId = $this->uri->segment(2);
        if(!$whatId){
            exit;
        }

        $this->my_model->setSelectFields(array('fran_est_num', 'job_status'));
        $this->my_model->setShift();
        $jobInfo = (Object)$this->my_model->getInfo('tbl_trackinglog', $whatId, 'est_num');
        $isArchived = 0;
        if(!$jobInfo->fran_est_num){
            $this->my_model->setSelectFields(array('fran_est_num', 'job_status'));
            $this->my_model->setShift();
            $jobInfo = (Object)$this->my_model->getInfo('tbl_archive', $whatId, 'est_num');
            $isArchived = 1;
        }
        $fran_est_num = $jobInfo->fran_est_num;
        $job_status = $jobInfo->job_status;

        $this->my_model->setSelectFields(array('branch_id', 'handled_by', 'franchise_id'));
        $this->my_model->setShift();
        $bInfo = (Object)$this->my_model->getInfo('tbl_client', $whatId);
        $branch_id = $bInfo->branch_id;
        $handled_by = $bInfo->handled_by;

        $this->my_model->setSelectFields('tbl_franchise.group_by');
        $this->my_model->setLastId('group_by');
        $fgId = $this->my_model->getInfo('tbl_franchise', $bInfo->franchise_id, 'tbl_franchise.id');

        $this->my_model->setSelectFields('id');
        $this->my_model->setNormalized('id');
        $fId = array_shift(array_values($this->my_model->getInfo('tbl_franchise', $fgId, 'group_by')));

        $isExportActive = 1;
        $format = 1;
        if($branch_id){
            $fields = array('tbl_merchant.mname', 'tbl_branch.is_default_show_sku', 'tbl_branch.export_format', 'tbl_branch.bname', 'tbl_branch.branch_code');
            $take_off_field = ArrayWalk($this->my_model->getFields('tbl_client_email_return', array('id', 'branch_id')), 'tbl_client_email_return.');
            $fields = array_merge($fields, $take_off_field);
            $fields[] = 'tbl_branch.is_export_active';
            $fields[] = 'tbl_branch.export_date_configure';
            $fields[] = 'tbl_change_control_system_ip.name as server_name';

            $this->my_model->setJoin(array(
                'table' => array('tbl_merchant_franchise', 'tbl_merchant', 'tbl_client_email_return', 'tbl_change_control_system_ip'),
                'join_field' => array('id', 'id', 'branch_id', 'id'),
                'source_field' => array('tbl_branch.merchant_franchise_id', 'tbl_merchant_franchise.merchant_id', 'tbl_branch.id', 'tbl_client_email_return.request_url_id'),
                'type' => 'left'
            ));
            $this->my_model->setShift();
            $this->my_model->setSelectFields($fields);
            $branch = (Object)$this->my_model->getInfo('tbl_branch', $branch_id, 'tbl_branch.id');
            $isExportActive = !(!$branch->is_export_active || $branch->export_date_configure == "0000-00-00");
            $this->data['branch'] = $branch;

            $format = $branch->export_format ? $branch->export_format : 1;
            if($branch->is_direct && $branch->request_url_id){
                $this->my_model->setLastId('export_format');
                $export_format = $this->my_model->getInfo('tbl_change_control_system_ip', array($branch->request_url_id), array('id'));
                if($export_format){
                    $format = $export_format;
                }
            }
        }
        if($handled_by){
            $fields = array('tbl_merchant.mname', 'tbl_merchant.merchant_code', 'tbl_branch.is_default_show_sku', 'tbl_branch.export_format', 'tbl_branch.bname', 'tbl_branch.branch_code');
            $take_off_field = ArrayWalk($this->my_model->getFields('tbl_client_email_return', array('id', 'branch_id')), 'tbl_client_email_return.');
            $fields = array_merge($fields, $take_off_field);
            $fields[] = 'tbl_branch.is_export_active';
            $fields[] = 'tbl_branch.export_date_configure';
            $fields[] = 'tbl_change_control_system_ip.name as server_name';

            $this->my_model->setJoin(array(
                'table' => array('tbl_merchant_franchise', 'tbl_merchant', 'tbl_client_email_return', 'tbl_change_control_system_ip'),
                'join_field' => array('id', 'id', 'branch_id', 'id'),
                'source_field' => array('tbl_branch.merchant_franchise_id', 'tbl_merchant_franchise.merchant_id', 'tbl_branch.id', 'tbl_client_email_return.request_url_id'),
                'type' => 'left'
            ));
            $this->my_model->setShift();
            $this->my_model->setSelectFields($fields);
            $handler = (Object)$this->my_model->getInfo('tbl_branch', $handled_by, 'tbl_branch.id');
            $this->data['handler'] = $handler;
        }
        $this->my_model->setLastId('format');
        $this->data['currentExportFormat'] = $this->my_model->getInfo('tbl_export_formats', $format);

        $this->data['thisId'] = $whatId;
        $this->data['fId'] = $fId;
        $this->data['handled_by'] = $handled_by;
        $this->data['fran_est_num'] = $fran_est_num;
        $this->data['job_status'] = $job_status;
        $this->data['isArchived'] = $isArchived;
        $csvUrl = 'export/' . $fId . '/' . $whatId . '/csv/' . $fran_est_num . '.csv';
        $this->data['csvUrl'] = $this->my_model->encodeString($csvUrl);

        if(!$isExportActive){
            $this->load->view('backend/invoice/export/export_warning', $this->data);
        }
        else{
            $this->load->view('backend/invoice/export/exportOptions', $this->data);
        }
    }

    function setExportOption(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $whatId = $this->uri->segment(2);
        if(!$whatId){
            exit;
        }

        if(count($_POST)>0){
            $this->session->set_userdata(array(
                'exportOption' => $_POST
            ));
        }
    }

    function csvExport(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $exportOption = $this->session->userdata('exportOption');
        if(!$exportOption){
            exit;
        }
        $whatId = $this->uri->segment(2);
        if(!$whatId){
            exit;
        }

        $jobInfo = $this->my_model->getInfo('tbl_client', $whatId);
        if(count($jobInfo) == 0){
            exit;
        }

        $this->my_model->setLastId('fran_est_num');
        $fran_est_num = $this->my_model->getInfo('tbl_trackinglog', $whatId, 'est_num');
        if(!$fran_est_num){
            $this->my_model->setLastId('fran_est_num');
            $fran_est_num = $this->my_model->getInfo('tbl_archive', $whatId, 'est_num');
        }

        $tags = $this->my_model->getInfo('tbl_truss_tags', $whatId, 'est_num');

        $tblName = "";
        $mId = "";
        $bId = "";
        $fId = "";
        $fCode = "";
        $format = 1;
        $merchant = array();
        $mInfo = array();
        foreach($jobInfo as $v){
            $this->my_model->setSelectFields(array('tbl_franchise.group_by', 'tbl_franchise.franchise_code'));
            $this->my_model->setShift();
            $fInfo = (Object)$this->my_model->getInfo('tbl_franchise', $v->franchise_id, 'tbl_franchise.id');
            $fgId = $fInfo->group_by;
            $fCode = $fInfo->franchise_code;

            $this->my_model->setSelectFields('id');
            $this->my_model->setNormalized('id');
            $fId = array_shift(array_values($this->my_model->getInfo('tbl_franchise', $fgId, 'group_by')));

            $this->my_model->setSelectFields(array(
                'tbl_merchant_franchise.merchant_id',
                'tbl_branch.export_format',
                'tbl_client_email_return.is_direct',
                'tbl_client_email_return.request_url_id',
                'tbl_branch.bname',
                'tbl_branch.branch_code'
            ));
            $this->my_model->setJoin(array(
                'table' => array('tbl_client_email_return', 'tbl_merchant_franchise'),
                'join_field' => array('branch_id', 'id'),
                'source_field' => array('tbl_branch.id', 'tbl_branch.merchant_franchise_id'),
                'type' => 'left'
            ));
            $this->my_model->setShift();
            $mInfo = (Object)$this->my_model->getInfo('tbl_branch', $v->branch_id, 'tbl_branch.id');
            //Over Write Export Format if DIRECT has been ticked
            if($mInfo->is_direct && $mInfo->request_url_id){
                $this->my_model->setLastId('export_format');
                $export_format = $this->my_model->getInfo('tbl_change_control_system_ip', array($mInfo->request_url_id), array('id'));
                if($export_format){
                    $mInfo->export_format = $export_format;
                }
            }

            if($mInfo->export_format){
                $dir = realpath(APPPATH . '../application/views/backend/invoice/export/formats') . '/' . $mInfo->export_format . ".php";
                if(file_exists($dir)){
                    $format = $mInfo->export_format;
                }
            }

            $bId = $v->branch_id;
            $mId = $mInfo->merchant_id;
            $this->my_model->setShift();
            $merchant = (Object)$this->my_model->getInfo('tbl_merchant', $mInfo->merchant_id);
            $tblName = $merchant->tbl_name ? ($this->db->supplier_database . "." . $merchant->tbl_name) : "";
        }

        $detailsCompile = array();
        $subheading = "";
        $thisKey = "";
        $thisCode = "";
        $thisDescription = "";
        $thisSequence = "";
        $thisBMUsage = "";

        $this->my_model->setLastId('map_type');
        $map_type_id = $this->my_model->getInfo('tbl_pdf_archive', $whatId, 'est_num');

        $this->my_model->setOrder('tbl_job_project.ref * 1', 'ASC');
        $jobDetails = $this->my_model->getInfo('tbl_job_project', $whatId, 'est_num');
        $dumpCounter = 1;
        if(count($jobDetails)>0){
            foreach($jobDetails as $v){
                if($v->is_subhead){
                    $subheading = $v->subhead;
                    if($subheading){
                        if($exportOption['hide_mapping_deletion'] == "true" && $subheading == 990){
                            $thisKey = "";
                            $thisDescription = "";
                            $thisSequence = "";
                            continue;
                        }

                        $this->my_model->setSelectFields(array('code', 'description', 'sequence', 'bmsubhead'));
                        $this->my_model->setShift();
                        $this_sub_head_info = (Object)$this->my_model->getInfo('tbl_subheading', $subheading, 'id_num');
                        $this_sub_head_description = $this_sub_head_info->description;
                        $this_sub_head_code = $this_sub_head_info->code;
                        $this_sub_head_sequence = $this_sub_head_info->sequence;
                        $this_sub_head_bm = $this_sub_head_info->bmsubhead;

                        if($exportOption['no_alternate_drop_mapping'] == "false"){
                            $map_to = $this->getTypeOneMapping($mId, $bId, 0, $subheading);
                            if($map_to->drop){
                                $thisKey = "";
                                $thisDescription = "";
                                $thisSequence = "";
                                continue;
                            }

                            if($map_to->subhead){
                                $this->my_model->setSelectFields(array('code', 'description', 'sequence', 'bmsubhead'));
                                $this->my_model->setShift();
                                $map_to_info = (Object)$this->my_model->getInfo('tbl_subheading', $map_to->subhead);
                                $map_to_desc = $map_to_info->description;
                                $map_to_code = $map_to_info->code;
                                $map_to_sequence = $map_to_info->sequence;
                                $map_to_bmsubhead = $map_to_info->bmsubhead;

                                $this_sub_head_description = $map_to_desc ? $map_to_desc : $this_sub_head_description;
                                $this_sub_head_code = $map_to_code ? $map_to_code : $this_sub_head_code;
                                $this_sub_head_sequence = $map_to_sequence ? $map_to_sequence : $this_sub_head_sequence;
                                $this_sub_head_bm = $map_to_bmsubhead ? $map_to_bmsubhead : $this_sub_head_bm;
                            }
                        }

                        $v->subheading_code = $this_sub_head_code;
                        $v->subheading_description = htmlspecialchars_decode($this_sub_head_description);
                        $v->subheading_sequence = $this_sub_head_sequence;
                        $v->subheading_bm = $this_sub_head_bm;

                        $thisKey = $v->subheading_sequence;
                        $thisCode = $v->subheading_code;
                        $thisDescription = $v->subheading_description;
                        $thisSequence = $v->subheading_sequence;
                        $thisBMUsage = $v->subheading_bm;

                        if($exportOption['add_manufacturing_comments'] == "true"){
                            $whatMFFields = array('est_num', 'subhead');
                            $whatMDValues = array($whatId, $subheading);
                            $mfNotes = $this->my_model->getInfo('tbl_manufacturing_comments', $whatMDValues, $whatMFFields);
                            if(count($mfNotes) > 0){
                                foreach($mfNotes as $notes){
                                    $code = "99999_" . $dumpCounter;
                                    $dumpCounter ++;

                                    $detailsCompile[$thisKey]['subhead'] = $thisKey;
                                    $detailsCompile[$thisKey]['subhead_description'] = $thisDescription;
                                    $detailsCompile[$thisKey]['subhead_sequence'] = $thisSequence;
                                    $detailsCompile[$thisKey]['materials'][$code] = (Object)array(
                                        'is_mf_comment' => 1,
                                        'current_header' => $notes->header,
                                        'description' => $notes->note
                                    );
                                }
                            }
                        }
                    }
                }
                else{
                    if(!$thisKey){
                        continue;
                    }

                    if(!$v->code){
                        continue;
                    }

                    $ifInvalid = (strlen($v->code) == 5 && substr_count($v->code, '9') == 5);
                    $currentDetails = array();
                    $currentDetails[] = $v;
                    //region apply the Type 1 Mapping
                    if($exportOption['no_alternate_drop_mapping'] == "false"){
                        $map_to = $this->getTypeOneMapping($mId, $bId, 1, $subheading, $v->code, ($v->usage ? $v->usage : ''), ($v->usage2 ? $v->usage2 : ''), 1);
                        if(count($map_to) > 0){
                            foreach($map_to as $mt){
                                if($mt->remove_usage_usage2 || $ifInvalid){
                                    $mt->code = $v->code;
                                    $mt->description = $v->description;
                                }
                            }
                            $currentDetails = $map_to;
                        }
                    }
                    //endregion

                    //this is to allow Two or More Map To Products
                    if(count($currentDetails) > 0){
                        foreach($currentDetails as $details){
                            if(!$details->code){
                                continue;
                            }

                            $isMap = $exportOption['no_alternate_drop_mapping'] == "false" && $details->is_map;

                            $tempStd = new stdClass;

                            $tempKey = $thisKey;
                            $tempDescription = $thisDescription;
                            $tempSequence = $thisSequence;
                            $tempBMUsage = $thisBMUsage;

                            $desc = "";
                            $code = "";
                            $usage = "";
                            $code_to_use = $v->code;

                            if($ifInvalid){
                                $desc = $details->description;
                                $code = $details->code;
                                $tempStd->product_sku = $v->code;
                            }
                            else{
                                if($isMap){
                                    if(!$v->is_hold){
                                        if($details->drop){
                                            //skipped this PRODUCT if MARKED as DROP
                                            //if OVERRIDE can only be drop if Type not Match
                                            $isDrop = $details->map_type > 0 ? ($details->map_type != $map_type_id) : true;
                                            if($isDrop){
                                                continue;
                                            }
                                        }

                                        if($details->code){
                                            $this->my_model->setLastId('unit');
                                            $map_to_unit = $this->my_model->getInfo('tbl_material', $details->code, 'code');

                                            $v->unit = $map_to_unit;
                                            if($details->factor){
                                                $factor = (float)number_format($details->factor, 4, '.', '');
                                                $v->qty = $v->qty * $factor;
                                                $v->qty = $this->FixUnitQuantity($v->qty, $v->unit);
                                            }
                                        }
                                    }
                                    if($details->subhead){
                                        $this->my_model->setSelectFields(array('code', 'description', 'sequence', 'bmsubhead'));
                                        $this->my_model->setShift();
                                        $subhead_info = (Object)$this->my_model->getInfo('tbl_subheading', $details->subhead);

                                        $tempKey = $subhead_info->sequence ? $subhead_info->sequence : $tempKey;
                                        $thisCode = $subhead_info->code ? $subhead_info->code : $thisCode;
                                        $tempDescription = $subhead_info->description ? $subhead_info->description : $tempDescription;
                                        $tempSequence = $subhead_info->sequence ? $subhead_info->sequence : $tempSequence;
                                        $tempBMUsage = $subhead_info->bmsubhead ? $subhead_info->bmsubhead : $tempBMUsage;
                                    }
                                    if($details->usage){
                                        if($details->usage == -1){
                                            $v->usage = "";
                                        }
                                        else{
                                            $this->my_model->setLastId('code');
                                            $v->usage = $this->my_model->getInfo('tbl_usage', $details->usage);
                                        }
                                    }
                                    if($details->usage2){
                                        if($details->usage2 == -1){
                                            $v->usage2 = "";
                                        }
                                        else{
                                            $this->my_model->setLastId('code');
                                            $v->usage2 = $this->my_model->getInfo('tbl_usage', $details->usage2);
                                        }
                                    }
                                }

                                $code_to_use = $isMap && !$v->is_hold ? $details->code : $v->code;
                                $this->my_model->setLastId('description');
                                $d = $this->my_model->getInfo("tbl_material", $code_to_use, 'code');
                                $desc = $d ? $d : $v->description;
                                if($exportOption['show_merchant_description'] == "true" && $tblName){
                                    $skuFields = array('code', 'branch_id');
                                    $skuValues = array($code_to_use, $bId);

                                    $this->my_model->setLastId('description');
                                    $d = $this->my_model->getInfo($tblName, $skuValues, $skuFields);
                                    $desc = $d ? $d : $desc;
                                }

                                if($exportOption['numbers'] == "sku_numbers" && $tblName){
                                    $skuFields = array('code', 'branch_id');
                                    $skuValues = array($code_to_use, $bId);

                                    $this->my_model->setSelectFields(array("sku", "cost", "retail"));
                                    $this->my_model->setShift();
                                    $c = (Object)$this->my_model->getInfo($tblName, $skuValues, $skuFields);
                                    $code = $c->sku != "" ? $c->sku : $code_to_use;
                                    $tempStd->product_sku = $c->sku != "" ? str_pad($c->sku, 6, '0', STR_PAD_LEFT) : "";
                                    $tempStd->product_cost = $c->cost;
                                    $tempStd->product_retail = $c->retail;
                                }
                                else{
                                    $code = $code_to_use;
                                }
                            }

                            //region Create New Row
                            $code .= ($v->code && $v->usage) ? '.' : '';

                            if($v->usage){
                                $this->my_model->setSelectFields(array('code', 'description', 'bmusage'));
                                $this->my_model->setShift();
                                $usage_data = (Object)$this->my_model->getInfo('tbl_usage', $v->usage, 'code');

                                $tempStd->usage_code = $usage_data->code;
                                $code .= $usage_data->code;
                                $tempStd->usage_description = htmlspecialchars_decode($usage_data->description);
                                $tempStd->usage_bm = $usage_data->bmusage;
                            }

                            $this->my_model->setLastId('unit_to');
                            $unit_to = $this->my_model->getInfo('tbl_unit_conversion', array($format, $v->unit), array('export_id', 'unit_from'));
                            $tempStd->product_unit = $unit_to ? $unit_to : $v->unit;

                            $qty = '';
                            $wasteQty = '';
                            if($v->qty){
                                $waste = (float)number_format(($v->waste/100),2,'.','');
                                $wasteQty = (float)number_format(($v->qty * $waste),2,'.','');
                                $qty = (float)number_format(($v->qty),2,'.','');
                            }

                            $tempStd->current_header = $v->current_header;
                            $tempStd->product_code = $code_to_use;
                            $tempStd->product_description = htmlspecialchars_decode($desc);
                            $tempStd->product_waste = !$v->waste ? "" : $wasteQty;
                            $tempStd->product_qty = $this->FixUnitQuantity($qty, $tempStd->product_unit);

                            $this->my_model->setOrder('length * 1', 'DESC');
                            $length = $this->my_model->getInfo('tbl_job_project_length', $v->id, 'job_project_id');
                            if(count($length)>0){
                                $lenArray = array();
                                $numArray = array();
                                foreach($length as $lk=>$lv){
                                    if(!in_array($lv->length, $lenArray)){
                                        $lenArray[] = $lv->length;
                                    }
                                    $lenKey = array_search($lv->length, $lenArray);
                                    $numArray[$lenKey] += $lv->number;
                                }
                                $tempStd->product_length = $lenArray;
                                $tempStd->product_number = $numArray;
                            }

                            //region merge if it already exist
                            if(array_key_exists($code, $detailsCompile[$tempKey]['materials']) && !$ifInvalid){
                                $thisDetail = $detailsCompile[$tempKey]['materials'][$code];

                                if(count($length)>0){
                                    $thisDetail->product_length = array_merge($tempStd->product_length, $thisDetail->product_length);
                                    $thisDetail->product_number = array_merge($tempStd->product_number, $thisDetail->product_number);
                                }

                                $thisDetail->product_qty += $qty;
                                if($v->waste){
                                    $thisDetail->product_waste += $wasteQty;
                                }
                            }
                            else{
                                if($ifInvalid){
                                    $code = $code . "_" . $dumpCounter;
                                    $dumpCounter ++;
                                }
                                $detailsCompile[$tempKey]['subhead'] = $thisCode;
                                $detailsCompile[$tempKey]['subhead_description'] = $tempDescription;
                                $detailsCompile[$tempKey]['subhead_sequence'] = $tempSequence;
                                $detailsCompile[$tempKey]['subheading_bm'] = $tempBMUsage;
                                $detailsCompile[$tempKey]['materials'][$code] = $tempStd;
                            }
                            //endregion

                            //in case if it is HOLD only allow one map to product
                            if ($v->is_hold){
                                break;
                            }
                        }
                    }
                }
            }
        }

        ksort($detailsCompile);
        $isSave = isset($_GET['isSave']);
        $this->data['isSave'] = $isSave;
        $this->data['filename'] = $fran_est_num . '.csv';
        $this->data['jobInfo'] = $jobInfo;
        $this->data['jobDetails'] = $jobDetails;
        $this->data['detailsCompile'] = $detailsCompile;
        $this->data['tags'] = $tags;
        $this->data['whatId'] = $whatId;
        $this->data['fId'] = $fId;
        $this->data['fCode'] = $fCode;
        $this->data['merchant'] = $merchant;
        $this->data['branch'] = $mInfo;
        $this->data['exportOption'] = $exportOption;
        $this->data['fran_est_num'] = $fran_est_num;

        $this->my_model->setSelectFields(array('name', 'request_url'));
        $this->my_model->setNormalized('request_url', 'name');
        $request_url = $this->my_model->getInfo('tbl_change_control_system_ip', array('request_url IS NOT NULL'), array(''));
        $this->data['request_url'] = $request_url;

        $this->load->view('backend/invoice/export/formats/' . $format, $this->data);
    }

    function csvExportSendTakeOff(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $subbie = new Subbie();

        $client_id = $this->uri->segment(2);
        $whatId = $this->uri->segment(3);
        if(!$whatId && !$client_id){
            exit;
        }

        $this->my_model->setSelectFields(array('export_count', 'type'));
        $this->my_model->setShift();
        $epj_upload_info = (Object)$this->my_model->getInfo('tbl_pdf_archive', $whatId);
        $export_count = $epj_upload_info->export_count;

        $jobInfo = $this->my_model->getInfo('tbl_pdf_archive', $whatId);
        $dir = realpath(APPPATH.'../pdf');
        if(count($jobInfo) > 0){

            $subject = "Invoice for ";
            $jobName = "";
            $pdfName = "";
            $invoice_ref = "";
            $date = date('Y-m-d');

            $this->my_model->setShift();
            $take_off_setting = (Object)$this->my_model->getInfo('tbl_client_email_return', $client_id, 'client_id');
            $take_off_options = json_decode($take_off_setting->take_off_merchant_cc);

            //region Record what actions have been made
            $itExist = count($this->my_model->getInfo('tbl_email_export_return', $whatId ,'pdf_archive_id')) > 0;
            $post = array(
                'pdf_archive_id' => $whatId,
                'client_id' => $client_id,
                'return_date' => date('r'),
                'user_id' => $this->session->userdata('user_id'),
                'via_subbie' => $take_off_setting->via_subbie,
                'via_ftp' => $take_off_setting->via_ftp
            );
            if($itExist){
                $this->my_model->update('tbl_email_export_return', $post, $whatId, 'pdf_archive_id');
                echo 'working';
            }
            else{
                echo 'working';
                $id = $this->my_model->insert('tbl_email_export_return', $post);
                echo $id;
            }
            //endregion

            $this->my_model->setShift();
            $franchise = (Object)$this->my_model->getInfo('tbl_client', $client_id);

            foreach($jobInfo as $jv){
                $ref = explode(' ',$jv->file_name);
                $pdfName =  $jv->file_name;
                $invoice_ref = $ref[0];
                $date = $jv->date;
            }

            $this->data['franchise'] = $franchise;


            if($take_off_setting->via_email){
                $pdf = $dir . "/invoice/" .date('Y/F/',strtotime($date)). $pdfName;
                //echo $pdf;
                $url = array();
                $disposition = array();
                $file_names = array();
                if(file_exists($pdf)){
                    $url[] = $pdf;
                    $file_names[] = $pdfName;
                }

                $this->data['comment'] = isset($_POST['comment']) ? nl2br($_POST['comment']) : '';
                $msg = $this->load->view('backend/invoice/export/export_send_take_off', $this->data, TRUE);
                if(count($url) > 0 && $take_off_setting->take_off_merchant_email){
                    $cc = array();
                    $cc_alias = array();
                    $cc[] = $take_off_setting->take_off_franchise_email;
                    $cc_alias[] = "Franchise Admin";

                    $cc_one_email = $take_off_options->cc_one_email;
                    if($cc_one_email){
                        $cc[] = $cc_one_email;
                        $cc_alias[] = $take_off_options->cc_one_name;
                    }
                    $cc_two_email = $take_off_options->cc_two_email;
                    if($cc_two_email){
                        $cc[] = $cc_two_email;
                        $cc_alias[] = $take_off_options->cc_two_name;
                    }

                    $subject .= $franchise->client_name . ' (' . $invoice_ref.')';
                    $sendMailSetting = array(
                        'to' => $take_off_setting->take_off_merchant_email,
                        'to_alias' => $take_off_setting->take_off_merchant_name,
                        'cc' => $cc,
                        'cc_alias' => $cc_alias,
                        'name' => $franchise->client_name,
                        'from' => $take_off_setting->take_off_franchise_email,
                        'subject' => htmlspecialchars_decode($subject),
                        'url' => $url,
                        'disposition' => $disposition,
                        'file_names' => $file_names,
                        'debug_type' => 2,
                        'debug' => true
                    );

                    $debugResult = $subbie->sendMail(
                        $msg,
                        $sendMailSetting
                    );

                    //if Successful RECORD Notification
                    if($debugResult->type){
                        //region set Export Notification for SU, FA, QA and the QS
                        $title = 'Invoice <strong>' . $invoice_ref . '  for ' . $franchise->client_name . '</strong> has been Exported';
                        $notification = '<strong>Invoice:</strong> <a href="' . base_url() . 'pdf/invoice/' . date('Y/F/',strtotime($date)) . $pdfName .'">' .
                            $invoice_ref . '</a>  ' . $invoice_ref .
                            '<br />has been exported back to the Merchant' . ($export_count > 1 ? ' (again)' : '') . '.';

                        $n = new Notification_Controller();
                        $n->submitNotification($client_id, 1, $title, $notification);
                        //endregion
                    }

                    $sendMailSetting['comment'] = $this->data['comment'];
                    $post = array(
                        'date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'inv_id' => $whatId,
                        'client_id' => $client_id,
                        'type' => $debugResult->type,
                        'message' => json_encode($sendMailSetting),
                        'debug' => $debugResult->debug,
                        'export_setting' => json_encode(isset($_POST['exportOptions']) ? $_POST['exportOptions'] : array())
                    );
                    $this->my_model->insert('tbl_email_export_log', $post, false);
                }
            }
        }
        else{
            exit;
        }
    }

    function csvExportTakeOffRecord(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $whatId = $this->uri->segment(2);
        if(!$whatId){
            exit;
        }

        $this->my_model->setLastId('export_count');
        $export_count = $this->my_model->getInfo('tbl_pdf_archive', $whatId) + 1;
        $last_export_time = date('Y-m-d H:i:s');
        $post = array(
            'last_export_time' => $last_export_time,
            'export_count' => $export_count
        );
        $this->my_model->update('tbl_pdf_archive', $post, $whatId);

        echo $last_export_time;
    }

    function invoiceExportEmailLog(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/invoice/export/export_email_log';

        $fields = ArrayWalk($this->my_model->getFields('tbl_email_export_log', array('date', 'debug')), 'tbl_email_export_log.');
        $fields[] = 'DATE_FORMAT(tbl_email_export_log.date, "%Y%m%d %H%i") as date';
        $fields[] = 'IF(tbl_email_export_log.type = 1, "Success", "Failed") as status';
        $fields[] = 'tbl_user.name as user';
        $fields[] = 'tbl_pdf_archive.file_name';
        $fields[] = 'tbl_client.client_name';

        $this->my_model->setSelectFields($fields, false);
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_user','tbl_pdf_archive','tbl_client'
            ),
            'join_field' => array(
                'id','id','id'
            ),
            'source_field' => array(
                'tbl_email_export_log.user_id',
                'tbl_email_export_log.inv_id',
                'tbl_email_export_log.client_id'
            ),
            'type' => 'left'
        ));
        $this->my_model->setOrder('tbl_email_export_log.date', 'DESC');
        $log = $this->my_model->getInfo('tbl_email_export_log');
        if(count($log) > 0){
            foreach($log as $v){
                $file_name = explode(' ',$v->file_name);
                $v->message = json_decode($v->message);
                $v->export_setting = json_decode(($v->export_setting ? $v->export_setting : array()));
                $v->job = $file_name[0];
                
            }
        }

        $this->data['log'] = json_encode($log);

        $this->load->view('main_view',$this->data);
    }

    function exportArchiveInvoice(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $client_id = $this->uri->segment(2);
        $inv_ref = $this->uri->segment(3);

        $this->my_model->setShift();
        $this->data['client'] = $this->my_model->getinfo('tbl_client_email_return',$client_id,'client_id');

        $this->my_model->setLastId('client_name');
        $client_name = $this->my_model->getInfo('tbl_client',$client_id);
        $this->data['client']['client_name'] = $client_name;

        $this->data['client'] = (Object)$this->data['client'];

        $this->load->view('backend/invoice/export/exportOptions',$this->data);
    }
}