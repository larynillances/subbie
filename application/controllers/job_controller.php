<?php
include('subbie.php');

class Job_Controller extends Subbie{

    function __construct(){
        parent::__construct();
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
    }

    function trackingLog(){

        $this->my_model->setNormalized('status','id');
        $this->my_model->setSelectFields(array('id','status'));
        $this->data['job_status'] = $this->my_model->getInfo('tbl_job_status');
        $this->data['job_status'][''] = 'All Job Status';
        ksort($this->data['job_status']);

        $this->my_model->setNormalized('list_type','id');
        $this->my_model->setSelectFields(array('id','list_type'));
        $this->data['list_type'] = $this->my_model->getInfo('tbl_list_type');
        $this->data['list_type'][''] = 'All List';
        ksort($this->data['list_type']);

        if(isset($_POST['go'])){
            $this->session->set_userdata(array(
                'job_status_id'=> $_POST['status_id'],
                'list_type_id' => $_POST['list_id']
            ));

            redirect('trackingLog');
        }

        $this->data['job_status_id'] = !array_key_exists('job_status_id',$this->session->all_userdata()) ? 4 : $this->session->userdata('job_status_id');
        $this->data['list_type_id'] = $this->session->userdata('list_type_id');

        $whatVal = '';
        $whatFld = '';

        if($this->data['job_status_id']){
            $whatVal = array($this->data['job_status_id']);
            $whatFld = array('tbl_tracking_log.status_id');
        }
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_client',
                'tbl_quotation',
                'tbl_invoice',
                'tbl_job_allocated',
                'tbl_tracking_log',
                'tbl_job_status'
            ),
            'join_field' => array(
                'id','job_id','job_id','job_id','job_id','id'
            ),
            'source_field' => array(
                'tbl_registration.client_id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_tracking_log.status_id'
            ),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','client_id','address'),'tbl_registration.');
        $fields[] = 'IF(tbl_registration.start_date != "0000-00-00",
                        DATE_FORMAT(tbl_registration.start_date,"%d/%m/%y"),
                         "") as start_date';
        $fields[] = 'IF(tbl_registration.tender_date != "0000-00-00",
                        DATE_FORMAT(tbl_registration.tender_date,"%d/%m/%y"),
                         "") as tender_date';
        $fields[] ='IF(tbl_registration.date_added != "0000-00-00",
                        DATE_FORMAT(tbl_registration.date_added,"%d/%m/%y"),
                         "") as date_added';
        $fields[] ='IF(tbl_tracking_log.completed_date != "0000-00-00",
                        DATE_FORMAT(tbl_tracking_log.completed_date,"%d/%m/%y"),
                         "") as completed_date';
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $fields[] = 'tbl_job_status.code as job_code';
        $fields[] = 'tbl_job_status.status';
        $fields[] = 'tbl_client.client_code';
        $fields[] = 'IF(tbl_quotation.accepted_date, DATE_FORMAT(tbl_quotation.accepted_date,"%d/%m/%y"),"") as accepted_date';
        $fields[] = 'tbl_registration.is_invoice';
        $fields[] = 'tbl_job_allocated.duration';
        $fields[] = 'tbl_registration.job_name';
        $fields[] = 'tbl_tracking_log.meter';
        $fields[] = 'tbl_tracking_log.job_rating';
        $fields[] = 'tbl_tracking_log.team';
        $fields[] = 'tbl_tracking_log.status_id';
        $fields[] = 'tbl_tracking_log.notes';

        $this->my_model->setSelectFields($fields,false);
        $this->my_model->setGroupBy('id');
        $this->data['job_data'] = $this->my_model->getinfo('tbl_registration',$whatVal,$whatFld);
        if(count($this->data['job_data']) >0){
            foreach($this->data['job_data'] as $jv){
                $team = json_decode($jv->team);
                $jv->team_arr = array();
                $jv->hours_gain = array();
                $jv->hours = 0;

                if(count($team) >0){
                    foreach($team as $team_id){
                        $team_arr = $this->my_model->getInfo('tbl_team',$team_id);
                        if(count($team_arr) >0){
                            foreach($team_arr as $team_row){
                                $jv->team_arr[] = $team_row->code;
                            }
                        }
                    }
                }
                $job_assign = $this->my_model->getInfo('tbl_job_assign',$jv->id,'job_id');
                if(count($job_assign)>0){
                    foreach($job_assign as $val){
                        $totalHours = array();
                        $this->my_model->setSelectFields(array(
                            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
                            'time_in','time_out','staff_id','date',
                            'id as dtr_id'
                        ));
                        $dtr = $this->my_model->getinfo('tbl_login_sheet', $val->staff_id,'staff_id');
                        if(count($dtr) >0){
                            foreach($dtr as $dv){
                                if($val->start_date == $dv->date){
                                    $jv->hours_gain[$val->staff_id][$val->start_date] = $dv->hours;
                                }
                            }
                        }
                        $thisDtr = array_key_exists($val->staff_id, $jv->hours_gain) ? $jv->hours_gain[$val->staff_id] : array();

                        if(count($thisDtr) > 0){
                            $hasInfo = array_key_exists($val->start_date, $thisDtr);
                            if($hasInfo){
                                $thisTime = $thisDtr[$val->start_date];
                                @$totalHours[$val->staff_id] += @$thisTime;
                            }
                        }

                        $minutes = (int)(@$totalHours[$val->staff_id]/60);
                        $hoursValue = (int)($minutes/60);
                        $minutesValue = $minutes - ($hoursValue * 60);
                        //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
                        $hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
                        $jv->hours += floatval($hours);
                    }
                }
            }
        }

        if(isset($_POST['change_status'])){
            unset($_POST['change_status']);
            $id = $_POST['job_id'];
            $this->my_model->update('tbl_tracking_log',$_POST,$id,'job_id');
            redirect('trackingLog');
        }

        $this->data['page_load'] = 'backend/tracking/tracking_log_view';
        $this->load->view('main_view',$this->data);
    }


    function invoiceList(){

        $action = $this->uri->segment(2);
        $id = $this->uri->segment(3);

        if($action == 'list'){
            if($id){
                $this->getInvoiceData($id);
                $this->data['page_name'] = 'Available Invoice';
                $this->data['page_load'] = 'backend/invoice/available_invoice_view';
            }
        }else{
            $this->data['client_list'] = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');
            $this->data['invoice_data'] = array();
            if(count($this->data['client_list']) >0){
                foreach($this->data['client_list'] as $cv){
                    $cv->subtotal = 0;
                    $cv->total = 0;
                    $cv->gst_total = 0;
                    $invoice = $this->my_model->getInfo('tbl_invoice',array($cv->id,false),array('client_id','is_archive'));
                    if(count($invoice) >0){
                        foreach($invoice as $iv){
                            //$cv->total = $cv->subtotal + $cv->gst_total;
                            $cv->meter_array = explode("\n",$iv->meter);
                            $cv->unit_price_array = explode("\n",$iv->unit_price);
                            if(count($cv->meter_array) >0){
                                foreach($cv->meter_array as $key=>$meter){
                                    @$cv->subtotal +=  $meter != 0 ? $cv->unit_price_array[$key] * $meter : $cv->unit_price_array[$key];
                                }
                            }
                            $cv->gst_total = $cv->subtotal * 0.15;
                            $cv->total = $cv->subtotal + $cv->gst_total;
                        }
                    }
                }
            }
            $this->data['page_load'] = 'backend/invoice/invoice_list_view';
        }



        $this->load->view('main_view',$this->data);
    }

    function invoiceSummary(){

        $id = $this->uri->segment(2);
        if(!$id){
            exit;
        }

        $this->data['invoice'] = array();
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
        $this->data['whatYear'] = date('Y');
        $this->data['whatMonth'] = date('m');

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];
        $whatVal = '(tbl_statement.client_id = "'.$id.'" AND tbl_statement.type = "INVOICE" AND tbl_statement.date LIKE "%'.$date.'%")';

        if(isset($_POST['submit'])){
            $this->data['whatYear'] = $_POST['year'];
            $this->data['whatMonth'] = $_POST['month'];

            $date = $_POST['year'].'-'.$_POST['month'];
            $whatVal = '(tbl_statement.client_id = "'.$id.'" AND tbl_statement.type = "INVOICE" AND tbl_statement.date LIKE "%'.$date.'%")';
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_statement.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','reference','debits','type','date'),'tbl_statement.');
        $fields[] = 'DATE_FORMAT(tbl_statement.date,"%d/%m/%Y") as save_date';
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'tbl_client.client_code';

        $this->my_model->setSelectFields($fields);
        $invoice = $this->my_model->getInfo('tbl_statement',$whatVal,'');

        if(count($invoice) > 0){
            foreach($invoice as $v){
                $day = date('j',strtotime($v->date));
                if($day <= 15){
                    $this->data['invoice'][date('15 F Y',strtotime($v->date))][] = (object)array(
                        'client_name' => $v->client_name,
                        'date' => $v->save_date,
                        'client_code' => $v->client_code,
                        'debits' => $v->debits,
                        'reference' => $v->reference
                    );
                }else{
                    $this->data['invoice'][date('t F Y',strtotime($v->date))][] = (object)array(
                        'client_name' => $v->client_name,
                        'date' => $v->save_date,
                        'client_code' => $v->client_code,
                        'debits' => $v->debits,
                        'reference' => $v->reference
                    );
                }

            }
        }

        $this->data['page_load'] = 'backend/invoice/invoice_summary_view';
        $this->load->view('main_view',$this->data);
    }

    function jobInvoice(){
        $id = $this->uri->segment(2);
        $inv_id = $this->uri->segment(3);
        if(!$id && !$inv_id){
            exit;
        }

        $this->getInvoiceData($id,$inv_id);
        $this->data['page_load'] = 'backend/invoice/job_invoice_view';
        $this->load->view('main_view',$this->data);
    }

    function getInvoiceData($id,$inv_id = '',$ref='',$archive = false){
        $this->my_model->setJoin(array(
            'table' => array('tbl_client','tbl_registration','tbl_quotation'),
            'join_field' => array('id','id','job_id'),
            'source_field' => array('tbl_invoice.client_id','tbl_invoice.job_id','tbl_registration.id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(
            array(
                'id','your_ref','job_id','meter','date','job_name','is_archive'
            ),
            'tbl_invoice.'
        );
        $fields[] = 'tbl_quotation.price';
        $fields[] = 'IF(tbl_quotation.price != 0 ,
                        tbl_quotation.price,
                        tbl_invoice.unit_price
                        )
                    as unit_price';
        $fields[] = 'IF(tbl_invoice.job_id != 0 ,
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.job_id, 5,"0")),
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.id, 5,"0"),"-I")
                        )
                    as job_ref';
        $fields[] = 'tbl_registration.address';
        $fields[] = 'tbl_invoice.inv_ref';
        $fields[] = 'tbl_registration.job_name as reg_job_name';
        $fields[] = 'tbl_client.client_code';
        $fields[] = 'tbl_client.id as client_id';

        $this->my_model->setSelectFields($fields);

        if(!$archive){
            $whatVal = array(false,$id);
            $whatFld = array('tbl_invoice.is_archive','tbl_invoice.client_id');
            if($inv_id){
                $whatVal[] = $inv_id;
                $whatFld[] = 'tbl_invoice.id';
            }
        }else{
            $whatVal = array(true,$id,$ref);
            $whatFld = array('tbl_invoice.is_archive','tbl_invoice.client_id','tbl_invoice.inv_ref');
        }

        $this->data['invoice'] = $this->my_model->getInfo('tbl_invoice',$whatVal,$whatFld);

        $this->data['date'] = date('Y-m-d');
        $this->data['total_row'] = 0;
        if(count($this->data['invoice']) >0){
            foreach($this->data['invoice'] as $v){
                $v->total = array();
                $v->job_name_array = explode("\n",$v->job_name);
                if($v->job_id != 0 && $v->job_name != ''){
                    $v->job_name = $v->reg_job_name."\n".$v->job_name;
                    $v->meter = "\n".$v->meter;
                    $v->unit_price = "\n".$v->unit_price;
                }else if($v->job_id != 0 && $v->job_name == ''){
                    $v->job_name = $v->reg_job_name;
                }
                $this->data['date'] = $v->date;
                $v->job_name = str_replace("\n","<br/>",$v->job_name);
                $meter_array = explode("\n",$v->meter);
                $v->unit_price_array = explode("\n",$v->unit_price);
                $v->meter = str_replace("\n","<br/>",$v->meter);
                $v->unit_price = str_replace("\n","<br/>",$v->unit_price);
                $v->count_unit_price = count($v->unit_price_array);
                //$this->displayarray($meter_array);
                //@$this->data['total_row'] += count(@$v->unit_price_array);
                //@$this->data['total_row'] += $v->count_unit_price;
                if(count($meter_array) >0){
                    foreach($meter_array as $key=>$meter){
                        if($v->price != '' && $meter != 0){
                            $v->total[] =  @$v->unit_price_array[$key];
                        }else if($meter != 0 && $v->price == ''){
                            $v->total[] = @$v->unit_price_array[$key] * $meter;
                        }else{
                            $v->total[] =  @$v->unit_price_array[$key];
                        }
                    }
                }
            }
        }

        //$this->displayarray($this->data['invoice']);exit;
        if($ref != ''){
            $this->data['inv_code'] = $ref;
            $this->data['page_name'] = $this->data['page_name'].' '.$this->data['inv_code'];
        }else{
            $this->my_model->setLastId('client_code');
            @$code = $this->my_model->getInfo('tbl_client',$id);
            $gen_inv_code = date('d') <= 15 ? $code.date('my').'A' : $code.date('my').'B';

            $whatVal = array($id,true);
            $whatFld = array('client_id','is_archive');
            @$inv_ref = $this->my_model->getInfo('tbl_invoice',$whatVal,$whatFld);
            $ref_inv = 0;
            //$array = array();
            if(count($inv_ref) > 0){
                foreach($inv_ref as $val){
                    $inv = explode('-',$val->inv_ref);
                    $array[] = $inv[1];
                    asort($array);
                    if(count($array) > 0){
                        foreach($array as $array_val){
                            $ref_inv = $array_val;
                        }
                    }
                }
            }

            @$count = $ref_inv ? $ref_inv + 1 : 1;

            $this->data['inv_code'] = $gen_inv_code.'-'.$count;
            $this->data['page_name'] = $this->data['page_name'].' '.$this->data['inv_code'];
        }

        $this->data['client'] = $this->my_model->getInfo('tbl_client',$id);
    }

    function invoiceManage(){
        $action = $this->uri->segment(2);
        $client_id = $this->uri->segment(3);
        $inv_ref = $this->uri->segment(4);

        if(!$action && !$client_id){
            exit;
        }

        switch($action){
            case 'edit':
                $inv_id = $this->uri->segment(4);
                $ref = $_GET['ref'];
                if(!$inv_id && $ref){
                    exit;
                }
                $this->my_model->setNormalized('job_name','id');
                $this->my_model->setSelectFields(array('id','job_name'));
                $this->data['job_data'] = $this->my_model->getInfo('tbl_registration',$client_id,'client_id');
                $this->data['inv_data'] = $this->my_model->getInfo('tbl_invoice',$inv_id);

                if(isset($_POST['submit'])){
                    unset($_POST['submit']);

                    $this->my_model->update('tbl_invoice',$_POST,$inv_id);
                    if($this->uri->segment(6)){
                        redirect('editArchiveInvoice/'.$client_id.'/'.$this->uri->segment(5));
                    }else{
                        redirect('jobInvoice/'.$client_id.'/'.$inv_id);
                    }
                }

                $this->load->view('backend/invoice/edit_invoice_view',$this->data);
                break;
            case 'print':
                $total = $_GET['total'] ? (int)$_GET['total'] : '';
                $date = $_GET['date'] ? $_GET['date'] : '';

                if(!$total && !$date){
                    exit;
                }

                $this->getInvoiceData($client_id,'',$inv_ref,true);
                $this->load->view('backend/invoice/print_invoice_view',$this->data);
                break;
            default:
                $total = $_GET['total'] ? (int)$_GET['total'] : '';
                $date = $_GET['date'] ? $_GET['date'] : '';

                if(!$total && !$date){
                    exit;
                }

                $has_ref = $this->my_model->getInfo('tbl_statement',$inv_ref,'reference');
                if(count($has_ref) >0){
                    foreach($has_ref as $val){
                        if(isset($_GET['archive'])){
                            $debits = $total;
                        }else{
                            $debits = $val->debits + floatval($total);

                        }

                        $post = array(
                            'debits' => $debits
                        );
                        $this->my_model->update('tbl_statement',$post,$inv_ref,'reference',false);
                    }
                }else{
                    $post = array(
                        'client_id' => $client_id,
                        'date' => date('Y-m-d',strtotime($date)),
                        'type' => 'INVOICE',
                        'reference' => $inv_ref,
                        'debits' => $total
                    );
                    $this->my_model->insert('tbl_statement',$post,false);
                }

                if(isset($_GET['archive'])){
                    $this->getInvoiceData($client_id,'',$inv_ref,true);
                }else{
                    $uri = $this->uri->segment(5) ? $this->uri->segment(5) : '';
                    $this->getInvoiceData($client_id,$uri);
                }
                $this->data['dir'] = 'pdf/invoice/'.date('Y/F',strtotime($date));
                if(!is_dir($this->data['dir'])){
                    mkdir($this->data['dir'], 0777, TRUE);
                }

                $whatVal = array($client_id,$inv_ref.' '.date('d-F-y',strtotime($date)).'.pdf','invoice');
                $whatFld = array('client_id','file_name','type');
                $has_data = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);

                $post = array(
                    'client_id' => $client_id,
                    'file_name' => $inv_ref.' '.date('d-F-y',strtotime($date)).'.pdf',
                    'type' => 'invoice',
                    'inv_ref' => $inv_ref,
                    'date' => date('Y-m-d',strtotime($date))
                );

                if(count($has_data) > 0){
                   foreach($has_data as $v){
                       $this->my_model->update('tbl_pdf_archive',$post,$v->id);
                   }
                }else{
                    $this->my_model->insert('tbl_pdf_archive',$post);
                }

                $this->my_model->update('tbl_invoice',array('inv_ref' => $inv_ref,'is_archive' => true),
                    array($client_id,$this->uri->segment(5),true),
                    array('client_id','id','is_archive !=')
                );
                //$this->displayarray($this->data['invoice']);exit;
                $this->load->view('backend/invoice/print_invoice_view',$this->data);
                //redirect('invoiceList');
                break;
        }

    }

    function editArchiveInvoice(){
        
        $ref = $this->uri->segment(3);
        $client_id = $this->uri->segment(2);

        if(!$ref && !$client_id){
            exit;
        }

        $this->getInvoiceData($client_id,'',$ref,true);

        $this->data['page_load'] = 'backend/invoice/edit_job_invoice_view';
        $this->load->view('main_view',$this->data);
    }

    function statement(){
        
        $id = $this->uri->segment(2);
        if(!$id){
            exit;
        }
        $this->statementData($id);

        $this->data['page_load'] = 'backend/statement/statement_view';
        $this->load->view('main_view',$this->data);
    }

    function statementData($id){
        $this->data['statement_data'] = array();

        $whatVal = array($id,true);
        $whatFld = array('client_id','is_archive');

        $fields = array('max(date) as date', '(sum(debits) - sum(credits)) as outstanding');
        $this->my_model->setSelectFields($fields,false);

        $statement_archive = $this->my_model->getInfo('tbl_statement',$whatVal,$whatFld);
        $balance = 0;
        if(count($statement_archive) >0){
            foreach($statement_archive as $v){
                $date = $v->date ? date('d-M-y',strtotime($v->date)) : '';
                $this->data['statement_data'][$date][] = (object)array(
                    'id' => '',
                    'job_ref' => '',
                    'date' => $date,
                    'type' => 'opening',
                    'reference' => '<strong>Opening Balance</strong>',
                    'debits' => '',
                    'credits' => '',
                    'balance' => '$'.number_format($v->outstanding,2)
                );
                $balance += $v->outstanding;
            }
        }

        $whatVal = array($id,true);
        $whatFld = array('client_id','is_archive !=');
        $this->my_model->setOrder('date');
        $statement_data = $this->my_model->getInfo('tbl_statement',$whatVal,$whatFld);

        if(count($statement_data) >0){
            foreach($statement_data as $v){
                $balance += $v->debits;
                $balance -= $v->credits;
                $date = date('d-M-y',strtotime($v->date));

                $this->my_model->setJoin(array(
                    'table' => array('tbl_client'),
                    'join_field' => array('id'),
                    'source_field' => array('tbl_invoice.client_id'),
                    'type' => 'left'
                ));
                $this->my_model->setSelectFields(array(
                    'IF(tbl_invoice.job_id != 0 ,
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.job_id, 5,"0")),
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.id, 5,"0"),"-I")
                        )
                    as job_ref'
                ));
                $this->my_model->setShift();
                $invoice = (Object)$this->my_model->getInfo('tbl_invoice',array($v->reference,$v->client_id),array('inv_ref','client_id'));

                $this->data['statement_data'][$date][] = (object)array(
                    'id' => $v->id,
                    'job_ref' => @$invoice->job_ref,
                    'date' => $date,
                    'type' => $v->type,
                    'reference' => $v->reference,
                    'debits' => $v->debits ? '$'.number_format($v->debits,2) : '',
                    'credits' => $v->credits ? '$'.number_format($v->credits,2) : '',
                    'balance' => '$'.number_format($balance,2)
                );
            }
        }
        $pay = $this->my_model->getInfo('tbl_statement',array($id,true,'INVOICE'),array('client_id','is_payed !=','type'));
        $credit = $this->my_model->getInfo('tbl_statement',$id,'client_id');
        $this->data['statement_count'] = array(
            'has_info' => count($statement_data),
            'payment' => count($pay),
            'credit' => count($credit)
        );
        $this->data['client_data'] = $this->my_model->getInfo('tbl_client',$id);
    }

    function archiveStatement(){
        
        $this->data['pdf_statement'] = array();
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
        $this->data['whatYear'] = date('Y');
        $this->data['whatMonth'] = date('m');
        $this->data['page_load'] = 'backend/statement/archive_statement_view';

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];
        $whatVal = '(tbl_pdf_archive.type = "statement" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';

        if(isset($_POST['submit'])){
            $this->data['whatYear'] = $_POST['year'];
            $this->data['whatMonth'] = $_POST['month'];

            $date = $_POST['year'].'-'.$_POST['month'];
            if($_POST['type'] == 1){
                $whatVal = '(tbl_pdf_archive.type = "statement" AND tbl_pdf_archive.date LIKE "%'.$_POST['year'].'%")';
            }else{
                $whatVal = '(tbl_pdf_archive.type = "statement" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';
            }
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_pdf_archive.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('file_name','client_id','date'),'tbl_pdf_archive.');
        $fields[] = 'DATE_FORMAT(tbl_pdf_archive.date,"%d/%m/%Y") as archive_date';
        $fields[] = 'tbl_client.client_name';

        $this->my_model->setSelectFields($fields);
        $pdf_statement = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,'');

        if(count($pdf_statement) > 0){
            foreach($pdf_statement as $v){
                $this->data['pdf_statement'][date('m/Y',strtotime($v->date))][] = array(
                    'file_name' => $v->file_name,
                    'date' => $v->date,
                    'archive_date' => $v->archive_date,
                    'client_name' => $v->client_name
                );
            }
        }

        $this->load->view('main_view',$this->data);
    }

    function manageStatement(){
        $action = $this->uri->segment(2);
        $client_id = $this->uri->segment(3);
        if(!$action){
            exit;
        }

        switch($action){
            case 'payment':
                if(!$client_id){
                    exit;
                }
                $whatVal = array('INVOICE',true,$client_id);
                $whatFld = array('type','is_payed !=','client_id');
                $this->my_model->setNormalized('reference','id');
                $this->my_model->setSelectFields(array('CONCAT(id,"_",reference) as id','reference'));
                $this->data['inv_ref_list'] = $this->my_model->getInfo('tbl_statement',$whatVal,$whatFld);

                $this->my_model->setNormalized('name','id');
                $this->my_model->setSelectFields(array('id','name'));
                $this->data['payment_type'] = $this->my_model->getInfo('tbl_payment_type');
                $this->data['payment_type'][''] = '-';

                if(isset($_POST['submit'])){
                    $this->my_model->setLastId('reference');
                    $inv_ref = $this->my_model->getInfo('tbl_statement',$_POST['reference']);

                    $ref = explode('_',$_POST['reference']);
                    $whatValue = array('PAYMENT',$ref[1]);
                    $whatFlied = array('type','reference');
                    $this->my_model->setSelectFields(array('sum(credits) as balance'));
                    $this->my_model->setShift();
                    $balance = (Object)$this->my_model->getInfo('tbl_statement',$whatValue,$whatFlied);

                    $this->my_model->setShift();
                    $this->my_model->setSelectFields(array('debits'));
                    $debits = (Object)$this->my_model->getInfo('tbl_statement',$ref[0]);

                    $total = $debits->debits - $balance->balance;
                    if($_POST['payment_type'] == 1){
                        $_POST['amount'] = $total;
                        $this->my_model->update('tbl_statement',array('is_payed' => true),$ref[0]);
                    }

                    $post = array(
                        'date' => date('Y-m-d',strtotime($_POST['date'])),
                        'client_id' => $client_id,
                        'credits' => $_POST['amount'],
                        'type' => 'PAYMENT',
                        'reference' => $inv_ref
                    );
                    $this->my_model->insert('tbl_statement',$post);
                    redirect('statement/'.$client_id);
                }

                $this->load->view('backend/statement/add_payment_view',$this->data);
                break;
            case 'credit':
                if(!$client_id){
                    exit;
                }
                break;
            case 'print':
                $date = $_GET['date'];
                if(!$date && !$client_id){
                    exit;
                }

                $this->statementData($client_id);
                $this->data['dir'] = 'pdf/statement/'.date('Y',strtotime($date)).'/'.date('F',strtotime($date));
                if(!is_dir($this->data['dir'])){
                    mkdir($this->data['dir'], 0777, TRUE);
                }

                $whatVal = array($client_id,$client_id.' Statement '.date('d-F-y',strtotime($date)).'.pdf','statement');
                $whatFld = array('client_id','file_name','type');
                $has_data = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);

                $post = array(
                    'client_id' => $client_id,
                    'file_name' => $client_id.' Statement '.date('d-F-y',strtotime($date)).'.pdf',
                    'type' => 'statement',
                    'date' => date('Y-m-d',strtotime($date))
                );

                if(count($has_data) > 0){
                    foreach($has_data as $v){
                        $this->my_model->update('tbl_pdf_archive',$post,$v->id);
                    }
                }else{
                    $this->my_model->insert('tbl_pdf_archive',$post);
                }

                $this->load->view('backend/statement/print_statement_view',$this->data);
                break;
            case 'edit':
                $id = $this->uri->segment(4);
                if(!$id && !$client_id){
                    exit;
                }
                $this->data['credits'] = $this->my_model->getInfo('tbl_statement',$id);

                if(isset($_POST['submit'])){
                    unset($_POST['submit']);
                    $this->my_model->update('tbl_statement',$_POST,$id);
                    redirect('statement/'.$client_id);
                }
                $this->load->view('backend/statement/edit_credits_view',$this->data);
                break;

            case 'archiveCronJob':
                $client = $this->my_model->getInfo('tbl_client');
                if(count($client) > 0){
                    foreach($client as $v){
                        $statement = $this->my_model->getInfo('tbl_statement',array(false,$v->id),array('is_archive','client_id'));
                        if(count($statement) >= 30 ){
                            foreach($statement as $sv){
                                $fld = array('client_id','is_archive !=');
                                $val = array($sv->client_id,true);
                                $this->my_model->update('tbl_statement',array('is_archive' => true),$val,$fld);
                            }
                        }
                    }
                }
                break;
            default:
                if(!$client_id){
                    exit;
                }
                $fld = array('client_id','is_archive !=');
                $val = array($client_id,true);
                $this->my_model->update('tbl_statement',array('is_archive' => true),$val,$fld);

                redirect('statement/'.$client_id);
                break;
        }
    }

    function creditNote(){
        $id = $this->uri->segment(2);
        if(!$id){
            exit;
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_invoice','tbl_registration','tbl_client'),
            'join_field' => array('id','id','id'),
            'source_field' => array('tbl_credit_note.invoice_id','tbl_invoice.job_id','tbl_invoice.client_id'),
            'type' => 'left'
        ));

        $fields = ArrayWalk(array('id','date','invoice_id','client_id','client_ref','area','price','groupid'),'tbl_credit_note.');
        $fields[] = 'tbl_invoice.job_name';
        $fields[] = 'tbl_registration.address';
        $fields[] = 'IF(tbl_invoice.job_id != 0 ,
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.job_id, 5,"0")),
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.id, 5,"0"),"-I")
                        )
                    as job_ref';
        $fields[] = 'tbl_registration.job_name as reg_job_name';
        $fields[] = 'tbl_invoice.job_id';

        $this->my_model->setSelectFields($fields);
        $whatVal = array($id,true);
        $whatFld = array('tbl_credit_note.client_id','tbl_credit_note.is_archived !=');
        $this->data['credit'] = $this->my_model->getInfo('tbl_credit_note',$whatVal,$whatFld);

        $this->my_model->setSelectFields(array('id', 'is_archived'));
        $this->my_model->setOrder('id', 'DESC');
        $this->my_model->setConfig(1, 0, 1);
        $this->my_model->setShift();
        $creditRef = (Object)$this->my_model->getInfo('tbl_credit_note');
        @$creditRef = $creditRef->id ? $creditRef->id + (!$creditRef->is_archived ? 0 : 1) : 1;
        $this->data['credit_ref'] = str_pad($creditRef,5,'0',STR_PAD_LEFT);

        $this->data['page_name'] = $this->data['page_name'] .' '.$this->data['credit_ref'];
        $this->data['client_data'] = $this->my_model->getInfo('tbl_client',$id);

        if(isset($_POST['archive'])){
            $this->data['dir'] = 'pdf/credit note/'.date('Y').'/'.date('F');
            if(!is_dir($this->data['dir'])){
                mkdir($this->data['dir'], 0777, TRUE);
            }
            $this->data['file_name'] = 'CREDIT_NOTE_'.$this->data['credit_ref'].'_'.date('Ymd_Hi');
            $post = array(
                'client_id' => $id,
                'file_name' => $this->data['file_name'].'.pdf',
                'type' => 'credit',
                'date' => date('Y-m-d')
            );
            $this->my_model->insert('tbl_pdf_archive',$post);
            $this->load->view('backend/credit_note/print_credit_note_view',$this->data);

            $this->my_model->update('tbl_credit_note',array('is_archived' => true),$id,'client_id');
            $post = array(
                'date' => date('Y-m-d'),
                'type' => 'CREDIT NOTE',
                'reference' => $this->data['credit_ref'].' ('.$_POST['reference'].')',
                'credits' => $_POST['total'],
                'client_id' => $id
            );
            $this->my_model->insert('tbl_statement',$post);
            redirect('statement/'.$id);
        }

        if(isset($_GET['print'])){
            $this->data['dir'] = 'pdf/credit note/'.date('Y').'/'.date('F');
            if(!is_dir($this->data['dir'])){
                mkdir($this->data['dir'], 0777, TRUE);
            }
            $this->data['file_name'] = 'CREDIT_NOTE_'.$this->data['credit_ref'].'_'.date('Ymd_Hi');
            $post = array(
                'client_id' => $id,
                'file_name' => $this->data['file_name'].'.pdf',
                'type' => 'credit',
                'date' => date('Y-m-d')
            );
            $this->my_model->insert('tbl_pdf_archive',$post);
            $this->load->view('backend/credit_note/print_credit_note_view',$this->data);
        }
        $this->data['page_load'] = 'backend/credit_note/credit_note_view';
        $this->load->view('main_view',$this->data);
    }

    function archiveCreditNote(){
        $this->data['invoice_list'] = array();
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
        $this->data['whatYear'] = date('Y');
        $this->data['whatMonth'] = date('m');
        $this->data['client_key'] = '';

        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');
        $this->data['client'][''] = 'Select All';

        ksort($this->data['client']);

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];
        $whatVal = 'tbl_pdf_archive.type = "credit" AND tbl_pdf_archive.date LIKE "%'.$date.'%"';

        if(isset($_POST['submit'])){
            $this->data['whatYear'] = $_POST['year'];
            $this->data['whatMonth'] = $_POST['month'];
            $this->data['client_key'] = $_POST['client'];

            $date = $_POST['year'].'-'.$_POST['month'];
            if($_POST['type'] == 1){
                $whatVal = 'tbl_pdf_archive.type = "credit" AND tbl_pdf_archive.date LIKE "%'.$_POST['year'].'%"';
            }else{
                $whatVal = 'tbl_pdf_archive.type = "credit" AND tbl_pdf_archive.date LIKE "%'.$date.'%"';
            }
            if($_POST['client'] != ''){
                $whatVal .= ' AND tbl_pdf_archive.client_id ="'.$_POST['client'].'"';
            }
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client','tbl_credit_note','tbl_invoice'),
            'join_field' => array('id','client_id','id'),
            'source_field' => array('tbl_pdf_archive.client_id','tbl_pdf_archive.client_id','tbl_credit_note.invoice_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','file_name','client_id','type','date','download'),'tbl_pdf_archive.');
        $fields[] = 'DATE_FORMAT(tbl_pdf_archive.date,"%d/%m/%Y") as archive_date';
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'tbl_client.client_code';
        $fields[] = 'tbl_credit_note.inv_ref';
        $fields[] = 'tbl_credit_note.invoice_id';

        $this->my_model->setSelectFields($fields);
        $this->my_model->setGroupBy('invoice_id');
        $invoice_list = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,'');

        if(count($invoice_list) > 0 ){
            foreach($invoice_list as $v){
                $file_name = explode('_',$v->file_name);
                $whatVal = 'reference LIKE "%'.$file_name[2].'%" AND type="CREDIT NOTE"';
                $this->my_model->setShift();
                $getAmount = @(Object)$this->my_model->getInfo('tbl_statement',$whatVal,'');
                $this->data['invoice_list'][date('m/Y',strtotime($v->date))][] = (object)array(
                    'id' => $v->id,
                    'file_name' => $v->file_name,
                    'date' => $v->date,
                    'archive_date' => $v->archive_date,
                    'amount' => @number_format(@$getAmount->credits,2),
                    'original_amount' => @$getAmount->credits,
                    'client_name' => $v->client_name,
                    'client_code' => $v->client_code,
                    'client_id' => $v->client_id
                );
            }
        }
        $this->data['page_load'] = 'backend/credit_note/archive_credit_note_view';
        $this->load->view('main_view',$this->data);
    }

    function manageCreditNote(){
        $action = $this->uri->segment(2);
        $id = $this->uri->segment(3);
        if(!$action && !$id){
            exit;
        }

        $this->my_model->setNormalized('inv_ref','inv_ref');
        $this->my_model->setSelectFields(array('id', 'inv_ref'),false);
        $whatVal = array($id,'');
        $whatFld = array('tbl_invoice.client_id','tbl_invoice.inv_ref !=');
        $this->data['inv_ref'] = $this->my_model->getInfo('tbl_invoice',$whatVal,$whatFld);

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_invoice.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(
            array(
                'tbl_invoice.id',
                'tbl_invoice.inv_ref',
                'IF(tbl_invoice.job_id != 0 ,
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.job_id, 5,"0")),
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.id, 5,"0"),"-I")
                        )
                    as job_ref'
            ),false
        );
        $job_list = $this->my_model->getInfo('tbl_invoice',$id,'tbl_invoice.client_id');
        $job_list_ = array();
        if(count($job_list) > 0){
            foreach($job_list as $row){
                $job_list_[$row->inv_ref][1][$row->id] = $row->job_ref;
            }
        }
        $this->data['job_list_json'] = json_encode($job_list_);
        switch($action){
            case 'add':
                $this->load->view('backend/credit_note/add_credit_note',$this->data);
                if(isset($_POST['submit'])){
                    unset($_POST['submit']);
                    $_POST['date'] = date('Y-m-d');
                    $_POST['client_id'] = $id;

                    $this->my_model->insert('tbl_credit_note',$_POST);
                    redirect('creditNote/'.$id);
                }
                break;
            case 'edit':
                $_id = $this->uri->segment(4);
                $this->my_model->setShift();
                $this->data['credit_note'] = (Object)$this->my_model->getInfo('tbl_credit_note',$_id);
                $this->load->view('backend/credit_note/edit_credit_note',$this->data);
                if(isset($_POST['submit'])){
                    unset($_POST['submit']);
                    $this->my_model->update('tbl_credit_note',$_POST,$_id);
                    redirect('creditNote/'.$id);
                }
                break;
            default:
                break;
        }

    }

    function newJobRequestForm(){

        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getinfo('tbl_client',true,'is_exclude !=');
        $this->data['client'][''] = '-';
        $this->data['job_type'] = $this->my_model->getInfo('tbl_job_type');
        $this->data['option'] = $this->my_model->getInfo('tbl_drop_down');

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            if(isset($_POST['address'])){
                $fields = array('number','name','suburb','city');
                $address = array_combine($fields,$_POST['address']);
                $_POST['address'] = json_encode($address);
            }

            if($_POST['client_id'] == ''){
                $post = array(
                    'client_name' => $_POST['contractor'],
                    'address' => $_POST['contractor_address'],
                    'phone' => $_POST['contractor_tel'],
                    'email' => $_POST['contractor_email'],
                    'date_registered' => date('Y-m-d')
                );
                $id = $this->my_model->insert('tbl_client',$post);
                $_POST['client_id'] = $_POST['client_id'] != '' ? $_POST['client_id'] : $id;
            }
            if($_POST['quote_purpose'] == 1){
                $_POST['job_type_id'] = '';
                $_POST['option_1'] = '';
                $_POST['option_2'] = '';
            }

            $uploaddir = realpath(APPPATH.'../uploads');
            if (!empty($_FILES['scope'])) {
                $dir = $uploaddir.'/registration/'.date('Y').'/'.date('F').'/scope of work/'.$_POST['client_id'].'/';
                if(!is_dir($dir)){
                    mkdir($dir,0755,TRUE);
                }
                $file = basename($_FILES['scope']['name']);

                $destination = $dir . $file;
                if(move_uploaded_file($_FILES['scope']['tmp_name'], $destination)){
                    $_POST['scope'] = $file;
                }
            }
            if (!empty($_FILES['color_scheme'])) {
                $dir = $uploaddir.'/registration/'.date('Y').'/'.date('F').'/color scheme/'.$_POST['client_id'].'/';
                if(!is_dir($dir)){
                    mkdir($dir,0755,TRUE);
                }
                $file = basename($_FILES['color_scheme']['name']);

                $destination = $dir . $file;
                if(move_uploaded_file($_FILES['color_scheme']['tmp_name'], $destination)){
                    $_POST['color_scheme'] = $file;
                }
            }
            $_POST['start_date'] = $_POST['start_date'] != '' ? date('Y-m-d',strtotime($_POST['start_date'])) : '';
            $_POST['tender_date'] = $_POST['tender_date'] != '' ? date('Y-m-d',strtotime($_POST['tender_date'])) : '';
            $_POST['date_added'] = date('Y-m-d');

            unset($_POST['quote_purpose']);
            unset($_POST['contractor']);
            unset($_POST['contractor_address']);
            unset($_POST['contractor_tel']);
            unset($_POST['contractor_email']);

            $id = $this->my_model->insert('tbl_registration',$_POST,false);

            $post = array(
                'job_id' => $id,
                'status_id' => 1,
                'date' => date('Y-m-d')
            );

            $this->my_model->insert('tbl_tracking_log',$post);

            redirect('newJobRequestForm');
        }
        $this->data['page_load'] = 'backend/job_area/new_job_request_view';
        $this->load->view('main_view',$this->data);
    }

    function jobList(){
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_client',
                'tbl_drop_down as story',
                'tbl_job_type' ,
                'tbl_drop_down as inside_outside'
            ),
            'join_field' => array('id','id','id', 'id'),
            'source_field' => array(
                'tbl_registration.client_id',
                'tbl_registration.option_1',
                'tbl_registration.job_type_id',
                'tbl_registration.option_2',
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_client',
                'story',
                'tbl_job_type' ,
                'inside_outside'
            )
        ));
        $fields = ArrayWalk(array('id','client_id','address','owner_name'),'tbl_registration.');
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'story.type as story_type';
        $fields[] = 'inside_outside.type as inside_type';
        $fields[] = 'tbl_job_type.job_type';
        $fields[] = 'tbl_registration.job_name';
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';

        $this->my_model->setSelectFields($fields,false);
        $job_list = $this->my_model->getInfo('tbl_registration');
        $this->data['job_list'] = array();
        $this->data['client'] = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');
        if(count($job_list) > 0){
            foreach($job_list as $v){
                $address = (object)json_decode($v->address);
                $this_add = $address->number!= '' ? $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city : '';
                $this->data['job_list'][$v->client_id][] = array(
                    'address' => $this_add,
                    'job_id' => $v->id,
                    'story_type' => $v->story_type,
                    'inside_type' => $v->inside_type,
                    'job_ref' => $v->job_ref,
                    'job_type' => $v->job_type,
                    'job_name' => $v->job_name
                );
            }
        }
        $this->data['page_load'] = 'backend/job_area/job_list_view';
        $this->load->view('main_view',$this->data);
    }

    function jobEdit(){
        $page = $this->uri->segment(2);
        $id = $this->uri->segment(3);

        if(!$id && !$page){
            exit;
        }

        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getInfo('tbl_client');

        $this->my_model->setNormalized('job_type','id');
        $this->my_model->setSelectFields(array('id','job_type'));
        $this->data['job_type'] = $this->my_model->getInfo('tbl_job_type');

        $this->my_model->setNormalized('type','id');
        $this->my_model->setSelectFields(array('id','type'));
        $config = $this->my_model->model_config;
        $this->data['option_1'] = $this->my_model->getInfo('tbl_drop_down',1,'order_type');
        $this->data['option_1'][0] = '-';

        $this->my_model->model_config = $config;
        $this->data['option_2'] = $this->my_model->getInfo('tbl_drop_down',2,'order_type');
        $this->data['option_2'][0] = '-';

        $this->my_model->setJoin(array(
            'table' => array('tbl_team'),
            'join_field' => array('id'),
            'source_field' => array('tbl_staff.team_id'),
            'type' => 'left'
        ));
        $this->my_model->setNormalized('code','id');
        $this->my_model->setSelectFields(array('tbl_team.id','tbl_team.code'));
        $this->data['team'] = $this->my_model->getInfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

        $this->my_model->setJoin(array(
            'table' => array('tbl_tracking_log'),
            'join_field' => array('job_id'),
            'source_field' => array('tbl_registration.id'),
            'type' => 'left'
        ));
        $registration = ArrayWalk(
            array(
                'id','job_name','address','job_type_id','option_1','option_2','start_date','tender_date',
                'owner_name','contact_name','phone','email','client_id'
            ),
            'tbl_registration.'
        );
        $tracking = ArrayWalk(
            array(
                'status_id','meter','job_rating','team','notes'
            ),
            'tbl_tracking_log.'
        );
        $fields = array_merge($registration,$tracking);
        $this->my_model->setSelectFields($fields);
        $this->data['job_list'] = $this->my_model->getInfo('tbl_registration',$id,'tbl_registration.id');

        if(isset($_POST['submit'])){
            unset($_POST['submit']);

            $post = array(
                'meter' => $_POST['meter'],
                'team' => json_encode($_POST['team']),
                'notes' => $_POST['notes']
            );

            $this->my_model->update('tbl_tracking_log',$post,$id,'id',false);

            unset($_POST['meter']);
            unset($_POST['team']);
            unset($_POST['notes']);

            if(isset($_POST['address'])){
                $fields = array('number','name','suburb','city');
                $address = array_combine($fields,$_POST['address']);
                $_POST['address'] = json_encode($address);
            }

            $_POST['start_date'] = $_POST['start_date'] != '' ? date('Y-m-d',strtotime($_POST['start_date'])) : '';
            $_POST['tender_date'] = $_POST['tender_date'] != '' ? date('Y-m-d',strtotime($_POST['tender_date'])) : '';
            $this->my_model->update('tbl_registration',$_POST,$id,'id',false);

            $page == 'tracking' ? redirect('trackingLog') : redirect('jobList');
        }

        $this->load->view('backend/job_area/job_edit_view',$this->data);
    }

    function jobCostSheet(){
        
        $id = $this->uri->segment(2);

        if(!$id){
            exit;
        }

        $this->data['cost_sheet'] = array();
        $this->data['labor'] = array();
        $this->data['hours'] = array();
        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        //$this->my_model->setNormalized('job_ref','id');
        $this->my_model->setSelectFields(array('tbl_registration.id',
            'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref',
            'tbl_registration.address'
        ));

        $this->data['job_name'] = $this->my_model->getinfo('tbl_registration',$id,'tbl_registration.id');

        if(count($this->data['job_name']) >0){
            foreach($this->data['job_name'] as $v){
                $this->data['page_name'] = $v->job_ref;

                $this->my_model->setJoin(array(
                    'table' => array('tbl_login_sheet','tbl_staff','tbl_rate'),
                    'join_field' => array('date','id','id'),
                    'source_field' => array('tbl_job_assign.start_date','tbl_job_assign.staff_id','tbl_staff.rate'),
                    'type' => 'right'
                ));
                $this->my_model->setSelectFields(array(
                    'TIMESTAMPDIFF(SECOND, tbl_login_sheet.time_in, tbl_login_sheet.time_out) as hours',
                    'tbl_login_sheet.time_in','tbl_login_sheet.time_out','tbl_login_sheet.date',
                    'tbl_login_sheet.id','tbl_job_assign.job_id','tbl_rate.rate_cost','tbl_staff.id as staff_id'
                ));
                $this->my_model->setOrder('date');
                $dtr = $this->my_model->getinfo('tbl_job_assign',$v->id,'tbl_job_assign.job_id');
                $_hours = array();
                if(count($dtr) > 0){
                    foreach($dtr as $dv){
                        $minutes = (int)($dv->hours/60);
                        $hoursValue = (int)($minutes/60);
                        $minutesValue = $minutes - ($hoursValue * 60);
                        $hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
                        @$_hours[$dv->date][$dv->job_id] += floatval($hours);
                        $this->data['hours'] = $_hours;

                        $rate = $this->getStaffRate($dv->staff_id,$dv->date);
                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $dv->rate_name = $val->rate_name;
                                $dv->rate_cost = $val->rate;
                            }
                        }

                        $this->data['labor'][$dv->date][$dv->job_id] = array(
                            'rate' => $dv->rate_cost
                        );
                        //$this->data['cost_sheet'][$dv->date][$dv->job_id][] = $hours;
                    }
                }
            }
        }

        //$this->displayarray($this->data['labor']);exit;
        $this->data['page_load'] = 'backend/job_area/cost_sheet_view';
        $this->load->view('main_view',$this->data);
    }

    function workFlowCalendar(){
        $whatVal = array(4,false);
        $whatFld = array('status_id','is_allocated');
        $job_data = new Job_Helper();
        $this->data['job_allocated'] = $job_data->job_details($whatVal,$whatFld);
        $this->data['page_load'] = 'backend/work_flow/work_flow_calendar_view';
        $this->load->view('main_view',$this->data);
    }

    function allocateJob(){
        
        $this->data['job_list'] = array();
        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        //$this->my_model->setNormalized('job_ref','id');
        $this->my_model->setSelectFields(array('tbl_registration.id',
            'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref',
            'tbl_registration.address','tbl_registration.job_name'
        ));
        $job_data = new Job_Helper();
        $whatVal = array(4,false);
        $whatFld = array('status_id','is_allocated');
        $job_list = $job_data->job_details($whatVal,$whatFld);

        if(count($job_list) >0){
            foreach($job_list as $jv){
                $address = (object)json_decode($jv->address);
                $this_add = $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city;
                $this->data['job_list'][$jv->id] = $jv->job_ref.' ('.$jv->job_name.')';
            }
        }

        $this->data['color'] = $this->my_model->getInfo('tbl_events_color_pick');


        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $_POST['date_allocated'] = date('Y-m-d');
            $convert_str = str_replace('/','-',$_POST['days']);
            $days = $convert_str ? explode(',',$convert_str) : '';
            $_POST['days'] = json_encode($days);

            $this->my_model->insert('tbl_job_allocated',$_POST,false);
            $this->my_model->update('tbl_registration',array('is_allocated'=>true),$_POST['job_id']);
            $this->my_model->update('tbl_tracking_log',array('status_id'=>2),$_POST['job_id'],'job_id');
            redirect('workFlowCalendar');
        }

        $this->load->view('backend/work_flow/job_allocation_view',$this->data);
    }

    function addSchedule(){
        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $_POST['date_sched'] = date('Y-m-d');
            $convert_str = str_replace('/','-',$_POST['days']);
            $days = $convert_str ? explode(',',$convert_str) : '';
            $_POST['days'] = json_encode($days);

            $this->my_model->insert('tbl_event_schedule',$_POST,false);
            redirect('workFlowCalendar');
        }
        $this->load->view('backend/work_flow/add_schedule_view',$this->data);
    }

    function setJobComplete(){
        
        $id = $this->uri->segment(2);
        $inv = $this->uri->segment(3);
        if(!$id){
            exit;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $_POST['completed_date'] = date('Y-m-d',strtotime($_POST['completed_date']));
            $_POST['status_id'] = 3;

            $this->my_model->update('tbl_tracking_log',$_POST,$id,'id',false);

            $registration = $this->my_model->getInfo('tbl_registration',$id);
            if(count($registration) >0){
                foreach($registration as $v){
                    $post = array(
                        'client_id' => $v->client_id,
                        'job_id' => $v->id,
                        'date' => date('Y-m-d'),
                        'meter' => $inv
                    );
                    $this->my_model->insert('tbl_invoice',$post,false);
                }
            }
            redirect('trackingLog');
        }
        $this->load->view('backend/tracking/set_job_complete_view',$this->data);
    }

    function eventsJson(){
        ini_set("memory_limit","512M");
        set_time_limit(900000);
        header("Content-type: application/json");
        $out = array();

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration','tbl_client','tbl_events_color_pick'),
            'join_field' => array('id','id','id'),
            'source_field' => array('tbl_job_allocated.job_id','tbl_registration.client_id','tbl_job_allocated.color_pick_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','job_id','duration','days','date_allocated'),'tbl_job_allocated.');
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_job_allocated.job_id, 5,"0")) as job_ref';
        $fields[] = 'tbl_registration.client_id';
        $fields[] = 'tbl_events_color_pick.class';

        $this->my_model->setSelectFields($fields);
        $job_allocated = $this->my_model->getInfo('tbl_job_allocated');

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_quotation.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','closure_date','announce_date'),'tbl_quotation.');
        $fields[] = 'CONCAT("SQ",LPAD(tbl_quotation.id + 30, 5,"0")) as job_ref';
        $this->my_model->setSelectFields($fields);
        $quotation = $this->my_model->getInfo('tbl_quotation');

        $event_sched = $this->my_model->getInfo('tbl_event_schedule');

        $this->my_model->setJoin(array(
            'table' => array('tbl_absent_type','tbl_staff'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_absent_reason.absent_type_id','tbl_absent_reason.staff_id'),
            'type' => 'left'
        ));

        $fields = ArrayWalk(array('id','staff_id','start_date','end_date'),'tbl_absent_reason.');
        $fields[] = 'tbl_absent_type.string';
        $fields[] = 'tbl_absent_type.color';
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';

        $this->my_model->setSelectFields($fields);
        $absent = $this->my_model->getinfo('tbl_absent_reason');


        if(count($job_allocated) >0){
            foreach($job_allocated as $v){
                $dates = json_decode($v->days);
                $ref = 1;
                if(count($dates) >0){
                    foreach($dates as $date){
                        $out[] = array(
                            'id' => $ref,
                            'title' => $v->job_ref . ' Day ' .$ref,
                            'display' => character_limiter($v->job_ref . ' Day '.$ref,7),
                            'class' => $v->class,
                            'start' => strtotime(date('Y-m-d',strtotime('+1 day '.$date))) * 1000,
                            'end' => strtotime(date('Y-m-d',strtotime('+1 day '.$date))) * 1000
                        );
                        $ref++;
                    }
                }
            }
        }
        if(count($event_sched) >0){
            foreach($event_sched as $ev){
                $dates = json_decode($ev->days);
                $ref = 1;
                if(count($dates) >0){
                    foreach($dates as $date){
                        $id = count($dates) == 1 ? $ev->id : $ref;
                        $event = count($dates) == 1 ? $ev->event : $ev->event.' Day '.$ref;
                        $out[] = array(
                            'id' => $id,
                            'title' => $event,
                            'display' => character_limiter($event,7),
                            'class' => 'event-inverse',
                            'start' => strtotime(date('Y-m-d',strtotime('+1 day '.$date))) * 1000,
                            'end' => strtotime(date('Y-m-d',strtotime('+1 day '.$date))) * 1000
                        );
                        $ref++;
                    }
                }
            }
        }
        if(count($quotation) >0){
            foreach($quotation as $qv){
                $title = $qv->job_ref . '-Tender Closure';
                $out[] = array(
                    'id' => $qv->id,
                    'title' => $title,
                    'display' => character_limiter($title,7),
                    'class' => 'event-default',
                    'start' => strtotime(date('Y-m-d H:i:s',strtotime('+1 day '.$qv->closure_date))) * 1000,
                    'end' => strtotime(date('Y-m-d H:i:s',strtotime('+1 day '.$qv->closure_date))) * 1000
                );
            }
        }
        if(count($absent) >0){
            foreach($absent as $av){
                $title = $av->name . ' ' .$av->string;
                $out[] = array(
                    'id' => $av->id,
                    'title' => $title,
                    'display' => character_limiter($title,2),
                    'class' => $av->color,
                    'start' => strtotime(date('Y-m-d',strtotime('+1 day '.$av->start_date))) * 1000,
                    'end' => strtotime(date('Y-m-d',strtotime('+1 day '.$av->start_date))) * 1000
                );
            }
        }

        echo json_encode(array('success' => 1, 'result' => $out));
    }

    function jobQuoting(){
        $this->data['page_load'] = 'backend/quote/job_quoting_view';
        $this->load->view('main_view',$this->data);
    }

    function workOrder(){
        $this->data['page_load'] = 'backend/work_order/work_order_view';
        $this->load->view('main_view',$this->data);
    }
}