<?php
include('subbie.php');

class Staff_Controller extends Subbie{

    function __construct(){
        parent::__construct();
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
    }
    //region wage functions
    function wageTable(){

        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();

        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
            $this->session->set_userdata(array(
                '_year' => $_POST['year'],
                '_month' => $_POST['month']
            ));

            redirect('wageTable');
        }

        $this->data['thisYear'] = $this->session->userdata('_year') ? $this->session->userdata('_year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('_month') ? $this->session->userdata('_month') : date('m');

        $this->getWageData($this->data['thisYear'],$this->data['thisMonth']);

        $this->data['page_load'] = 'backend/staff/wage_summary_view';
        $this->load->view('main_view',$this->data);
    }

    function taxTable(){
        $this->my_model->setSelectFields(
            array(
                'id',
                'CONCAT("$",FORMAT(earnings,2)) AS earnings_converted',
                'earnings',
                'CONCAT("$",FORMAT(m_paye,2)) AS m_paye',
                'CONCAT("$",FORMAT(sl_loan_ded,2)) AS sl_loan_ded',
                'CONCAT("$",FORMAT(me_paye,2)) AS me_paye',
                'CONCAT("$",FORMAT(kiwi_saver_3,2)) AS kiwi_saver_3',
                'CONCAT("$",FORMAT(kiwi_saver_4,2)) AS kiwi_saver_4',
                'CONCAT("$",FORMAT(kiwi_saver_8,2)) AS kiwi_saver_8',
                'CONCAT("$",FORMAT(cec_1,2)) AS cec_1',
                'CONCAT("$",FORMAT(cec_1_10,2)) AS cec_1_10',
                'CONCAT("$",FORMAT(cec_2,2)) AS cec_2',
                'CONCAT("$",FORMAT(cec_2_17,2)) AS cec_2_17',
                'CONCAT("$",FORMAT(cec_3,2)) AS cec_3',
                'CONCAT("$",FORMAT(cec_3_30,2)) AS cec_3_30',
                'CONCAT("$",FORMAT(cec_4,2)) AS cec_4',
                'CONCAT("$",FORMAT(cec_4_33,2)) AS cec_4_33',
                'frequency_id as frequency',
                'wage_type_id as wage_type'
            )
        );
        $tax = $this->my_model->getinfo('tbl_tax');
        $this->data['tax'] = json_encode($tax);

        $this->my_model->setNormalized('type','id');
        $this->my_model->setSelectFields(array('id','type'));
        $this->data['wage_type'] = $this->my_model->getInfo('tbl_salary_type',array(1,2));
        $this->data['wage_type'][''] = 'All';

        $this->my_model->setNormalized('frequency','id');
        $this->my_model->setSelectFields(array('id','frequency'));
        $this->my_model->setOrder(array('frequency'));
        $this->data['frequency'] = $this->my_model->getInfo('tbl_salary_freq',array(1,2));
        $this->data['frequency'][''] = 'All';

        $this->data['page_load'] = 'backend/tax/tax_table_view';
        $this->load->view('main_view',$this->data);
    }

    function wageManage(){

        $this->data['total_bal'] = $this->getYearTotalBalance(date('Y'));
        $week = $this->getWeekInYear(date('Y'));

        $this->my_model->setNormalized('staff_status','id');
        $this->my_model->setSelectFields(array('id','staff_status'));
        $this->data['staff_status'] = $this->my_model->getinfo('tbl_staff_status');
        $this->data['staff_status'][''] = 'All';

        ksort($this->data['staff_status']);


        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_wage_type',
                'tbl_salary_freq',
                'tbl_salary_type',
                'tbl_currency',
                'tbl_tax_codes',
                //'tbl_staff_rate',
                'tbl_kiwi as employee',
                'tbl_kiwi as employeer',
                'tbl_esct_rate',
                'tbl_staff_status'
            ),
            'join_field' => array(
                'id','id','id','id','id', 'id'/*,'staff_id'*/,'id','id','id','id','id'
            ),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.wage_type',
                'tbl_wage_type.frequency',
                'tbl_wage_type.type',
                'tbl_staff.currency',
                'tbl_staff.tax_code_id',
                /*'tbl_staff.id',*/
                'tbl_staff.kiwi_id',
                'tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id',
                'tbl_staff.status_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_wage_type',
                'tbl_salary_freq',
                'tbl_salary_type',
                'tbl_currency',
                'tbl_tax_codes',
                'employee',
                'employeer',
                'tbl_esct_rate',
                'tbl_staff_status'
            )
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'tbl_wage_type.description',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.tax_number',
            'tbl_currency.currency_code',
            'CONCAT("$",tbl_rate.rate_cost) as rate_cost',
            'tbl_staff.balance',
            'tbl_staff.installment',
            'tbl_currency.symbols',
            'IF(tbl_staff.nz_account != "", CONCAT("$",tbl_staff.nz_account), "") as nz_account',
            'IF(tbl_staff.account_two != "", CONCAT("$",tbl_staff.account_two), "") as account_two',
            'IF(tbl_tax_codes.tax_code !="",tbl_tax_codes.tax_code,"") as tax_code',
            'IF('.'tbl_staff.ird_num != "-" ,LPAD('.'tbl_staff.ird_num,11,"0"),'.'tbl_staff.ird_num) as ird_num',
            'CONCAT(FORMAT('.'tbl_esct_rate.esct_rate,2),"%") as esct_rate',
            'CONCAT('.'tbl_salary_freq.frequency," (",'.'tbl_salary_freq.code,")") as frequency',
            'CONCAT('.'tbl_salary_type.type," (",'.'tbl_salary_type.code,")") as salary_type',
            'tbl_salary_type.code as salary_code',
            'tbl_staff.bank_account',
            'employee.kiwi',
            'employeer.kiwi as employeer_kiwi',
            'tbl_staff_status.color',
            'tbl_staff.status_id',
            'tbl_staff_status.staff_status'
        ));


        if(isset($_POST['go'])){
            if(!$_POST['staff_status']){
                $_POST['staff_status'] = 4;
            }
            $this->session->set_userdata(array('status_selected'=>$_POST['staff_status']));
            redirect('wageManage');
        }
        $this->data['status'] = $this->session->userdata('status_selected') ? $this->session->userdata('status_selected') : 3;

        $whatVal = array($this->data['status']);
        $whatFld = array('status_id');

        if($this->session->userdata('status_selected') == 4){
            $whatVal = '';
            $whatFld = '';
        }

        $this->my_model->setOrder(array('tbl_staff.lname','tbl_staff.fname'));
        $this->my_model->setGroupBy('tbl_staff.id');
        $this->data['employee'] = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
        $date = $this->getFirstNextLastDay(date('Y'),date('m'),'tuesday');
        if(count($this->data['employee']) > 0){
            foreach($this->data['employee'] as $v){
                $whatWeek = date('W');
                $balance = @$this->data['total_bal'][$week[$whatWeek]][$v->id]['balance'];
                $v->balance = $balance ? $balance : '';
                if($v->bank_account){
                    $bank_number = json_decode($v->bank_account);
                    $bank_code = '';
                    if($bank_number[0]){
                        $this->my_model->setLastId('bank_prefix');
                        $bank_code = $this->my_model->getInfo('tbl_bank_account_number',$bank_number[0]);
                    }
                    $v->bank_account = $bank_code ? $bank_code . '-' . $bank_number[1] . '-' . $bank_number[2] . '-' . $bank_number[3] : '';
                }
                $salary_type = explode(' ',$v->description);
                $v->description = end($salary_type).' ('.$v->salary_code.')';
                $rate = $this->getStaffRate($v->id);
                $v->start_use = '';
                $v->rate_name_ = '';
                if(count($rate) > 0){
                    foreach($rate as $row){
                        $rate_name = explode(' ',$row->rate_name);
                        $v->rate_name = $row->rate_name;
                        $v->rate_cost = '$'.$row->rate;
                        $v->start_use = $row->start_use;
                        $v->rate_name_ = end($rate_name).' ('.$v->rate_cost.')';
                    }
                }
                $hours = '';
                if(count($date) > 0){
                    foreach($date as $dv){
                        $hours = $this->getTotalHours($dv,$v->id);
                    }
                }
                $v->has_wage = $hours != 0 ? true : false;
                switch($v->currency_code){
                    case 'NZD':
                        $v->rate = '$1';
                        break;
                    default:
                        $converted_amount = CurrencyConverter($v->currency_code);
                        $v->rate = $v->symbols.' '.$converted_amount;
                        break;
                }
            }
        }

        $this->data['page_load'] = 'backend/staff/wage_table_view';
        $this->load->view('main_view',$this->data);
    }

    function printPaySlip(){

        $id = $this->uri->segment(2);
        $this_date = $this->uri->segment(3);

        if(!$id && !$this_date){
            exit;
        }
        $this->data['total_paid'] = $this->getOverAllWageTotalPay($this_date,$id);
        $payslip = $this->getPaySlipData($id,$this_date);
        $dir = realpath(APPPATH.'../pdf');
        $path = 'payslip/'.date('Y/F',strtotime($this_date));
        $this->data['dir'] = $dir.'/'.$path;
        $this->data['total_holiday_leave'] = $this->getAnnualLeave($id,$this_date);
        $this->data['total_sick_leave'] = $this->getSickLeave($id,$this_date);

        $this->my_model->setSelectFields(array('MIN(start_use) as start_use'));
        $start_date = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');
        $this->data['start_date'] = '';
        if(count($start_date) > 0){
            foreach($start_date as $sv){
                $this->data['start_date'] = date('d/m/Y',strtotime($sv->start_use));
            }
        }
        if(!is_dir($this->data['dir'])){
            mkdir($this->data['dir'], 0777, TRUE);
        }

        $filename = date('Ymd',strtotime($this_date)).'_Payslip_' .str_replace(' ','',$payslip['staff_name']);
        $staff_id = $id;
        $this->data['has_email'] = $payslip['has_email'];
        $this->data['staff'] = $payslip['staff'];
        $this->data['file_name'] = $filename;
        if(isset($_GET['view']) && $_GET['view'] == 1){
            $this->data['page_name'] .= ' for <strong>'.$payslip['staff_name'].'</strong>';
            $this->data['page_load'] = 'backend/staff/payslip_view';
            $this->load->view('main_view',$this->data);
        }
        else{
            $has_value = $this->my_model->getInfo('tbl_pdf_archive',array($filename,$staff_id),array('file_name','staff_id'));
            $post = array(
                'staff_id' => $id,
                'file_name' => $filename.'.pdf',
                'type' => 'payslip',
                'date' => date('Y-m-d',strtotime($this_date))
            );

            if(count($has_value) > 0){
                $this->my_model->update('tbl_pdf_archive',$post,array($filename,$staff_id),array('file_name','staff_id'));
            }else{
                $this->my_model->insert('tbl_pdf_archive',$post);
            }

            $this->load->view('backend/staff/print_pdf_payslip',$this->data);
        }
    }

    function sendStaffPaySlip(){

        $id = $this->uri->segment(2);
        $this_date = $this->uri->segment(3);

        if(!$id && !$this_date){
            exit;
        }
        $this->my_model->setSelectFields(array('CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name','email'));
        $staff = $this->my_model->getInfo('tbl_staff',$id);
        $this->data['email'] = '';
        $staff_name = '';
        if(count($staff) > 0){
            foreach($staff as $v){
                $this->data['email'] = $v->email;
                $staff_name = $v->name;
            }
        }

        if(isset($_POST['send_email'])){
            $this->my_model->setLastId('file_name');
            $filename = $this->my_model->getInfo('tbl_pdf_archive',array($this_date,$id),array('date','staff_id'));
            $filename = $filename ? $filename : '';
            $dir = realpath(APPPATH.'../pdf');
            $path = 'payslip/'.date('Y/F',strtotime($this_date));
            $_path = $dir.'/'.$path.'/'.$filename;
            if(file_exists($_path)){
                $this->data['dir'] = $dir.'/'.$path;
                $file_to_save = $this->data['dir'].'/'.$filename;
                //save the pdf file on the server

                $sendMailSetting = array(
                    'to' => $_POST['email'],
                    'name' => $staff_name,
                    'from' => 'admin@subbiesolution.co.nz',
                    'subject' => 'Pay Slip for '.date('d-F-y',strtotime($this_date)),
                    'url' => $file_to_save,
                    'file_names' => $filename,
                    'debug_type' => 2,
                    'debug' => true
                );

                $msg = 'Good day. <br/>Here is your attach pay slip for the Month of <strong>'.date('d-F-Y',strtotime($this_date)).'</strong>.';

                $debugResult = $this->sendMail(
                    $msg,
                    $sendMailSetting
                );

                if($debugResult->type){

                    $post = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'message' => json_encode($sendMailSetting),
                        'email_type' => 2,
                        'staff_id' => $id,
                        'type' => $debugResult->type,
                        'debug' => $debugResult->debug,
                        'date' => date('Y-m-d H:i:s')
                    );

                    $this->my_model->insert('tbl_email_export_log', $post, false);

                    redirect('printPaySlip/'.$id.'/'.$this_date.'?view=1');
                }
            }
        }else{
            $this->load->view('backend/staff/send_pay_slip_view',$this->data);
        }
    }

    function monthlyTotalPay(){

        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();

        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];

            $this->session->set_userdata(array(
                'year' => $_POST['year'],
                'month' => $_POST['month']
            ));
        }

        $this->data['thisYear'] = $this->session->userdata('year') != '' ? $this->session->userdata('year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('month') != '' ? $this->session->userdata('month') : date('m');

        $this->getWageData($this->data['thisYear'],$this->data['thisMonth'],'monthly');
        $this->getYearTotalBalance($this->data['thisYear'],'monthly');

        $this->data['page_load'] = 'backend/staff/monthly_pay_view';
        $this->load->view('main_view',$this->data);
    }

    function printSummary(){
        
        $type = $this->uri->segment(2);
        $month = $this->uri->segment(3);
        $year = $this->uri->segment(4);

        if(!$type && !$month && !$year){
            exit;
        }

        $this->data['this_month_year'] = date('F Y',strtotime($year.'-'.$month.'-01'));
        switch($type){
            case 'wage':
                $this->getWageData($year,$month);
                $this->getYearTotalBalance($year);

                $this->data['dir'] = 'pdf/summary/wage/'.date('Y').'/'.date('F');
                if(!is_dir($this->data['dir'])){
                    mkdir($this->data['dir'], 0777, TRUE);
                }
                $post = array(
                    'file_name' => $this->data['this_month_year'].'.pdf',
                    'type' => 'wage',
                    'date' => date('Y-m-d')
                );
                $this->my_model->insert('tbl_pdf_archive',$post);
                $this->load->view('backend/print/print_wage_summary_view',$this->data);
                break;
            default:
                $this->getWageData($year,$month,'monthly');
                $this->getYearTotalBalance($year,'monthly');
                $this->data['dir'] = 'pdf/summary/monthly/'.date('Y').'/'.date('F');
                if(!is_dir($this->data['dir'])){
                    mkdir($this->data['dir'], 0777, TRUE);
                }
                $post = array(
                    'file_name' => $this->data['this_month_year'].'.pdf',
                    'type' => 'monthly',
                    'date' => date('Y-m-d')
                );
                $this->my_model->insert('tbl_pdf_archive',$post);
                $this->load->view('backend/print/print_monthly_pay_view',$this->data);
                break;
        }
    }

    function manageDeduction(){
        $action = $this->uri->segment(2);
        $id = $this->uri->segment(3);
        if(!$action && !$id){
            exit;
        }
        switch($action){
            case 'add':
                $this->load->view('backend/staff/add_manage_deductions',$this->data);
                break;
            default:
                $this->data['deductions'] = $this->my_model->getinfo('tbl_deductions',$id);
                $this->load->view('backend/staff/edit_manage_deductions',$this->data);
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($action){
                case 'add':
                    $_POST['staff_id'] = $id;
                    $this->my_model->insert('tbl_deductions',$_POST);
                    break;
                default:
                    $this->my_model->update('tbl_deductions',$_POST,$id);
                    break;
            }
            redirect('wageManage');
        }

    }

    function rateManage(){
        $action = $this->uri->segment(2);
        if(!$action){
            exit;
        }
        switch($action){
            case 'add':
                $this->load->view('backend/staff/add_manage_rate',$this->data);
                break;
            default:
                $id = $this->uri->segment(3);
                if(!$id){
                    exit;
                }
                $this->data['rate'] = $this->my_model->getinfo('tbl_rate',$id);
                $this->load->view('backend/staff/edit_manage_rate',$this->data);
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($action){
                case 'add':
                    $this->my_model->insert('tbl_rate',$_POST);
                    redirect('wageManage');
                    break;
                default:
                    $id = $this->uri->segment(3);
                    if(!$id){
                        exit;
                    }

                    $this->my_model->update('tbl_rate',$_POST,$id);
                    redirect('wageManage');
                    break;
            }
        }
    }

    function addWage(){
        $this->my_model->setNormalized('name','id');
        $this->my_model->setSelectFields(array('id','name'));
        $this->data['employee'] = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

        $date = date('N');

        $this->data['date'] = $date == 7 ? date('j F Y',strtotime('next sunday',strtotime(date('j F Y',strtotime('-1 day'))))) : date('j F Y',strtotime('next sunday'));

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $_POST['date'] = date('Y-m-d',strtotime($_POST['date']));
            $this->my_model->insert('tbl_wage',$_POST,false);
            redirect('wageTable');
        }

        $this->load->view('backend/staff/wage_input_form',$this->data);
    }

    function manageStaff(){
        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }

        $this->my_model->setNormalized('bank_name','id');
        $this->my_model->setSelectFields(array('id','CONCAT(bank_prefix," - ",bank_name) as bank_name','bank_prefix'));
        $this->my_model->setOrder(array('bank_prefix','bank_name'));
        $this->data['bank_number'] = $this->my_model->getinfo('tbl_bank_account_number');
        $this->data['bank_number'][''] = '-';

        $this->my_model->setNormalized('esct_rate','id');
        $this->my_model->setSelectFields(array('id','CONCAT(FORMAT(esct_rate,2)," %") as esct_rate'));
        $this->data['esct_rate'] = $this->my_model->getinfo('tbl_esct_rate');
        $this->data['esct_rate'][''] = '-';

        ksort($this->data['esct_rate']);

        $this->my_model->setNormalized('rate_name','id');
        $this->my_model->setSelectFields(array('id','CONCAT(rate_name," ($" ,rate_cost ,")" ) as rate_name'));
        $this->data['rate'] = $this->my_model->getinfo('tbl_rate');
        $this->data['rate'][''] = '-';

        ksort($this->data['rate']);

        $this->my_model->setNormalized('hourly_rate','id');
        $this->my_model->setSelectFields(array('id','hourly_rate'));
        $this->data['hourly_rate'] = $this->my_model->getinfo('tbl_hourly_nz_rate');
        $this->data['hourly_rate'][''] = '-';

        ksort($this->data['hourly_rate']);

        $this->my_model->setNormalized('kiwi','id');
        $this->my_model->setSelectFields(array('id','CONCAT(kiwi," %") as kiwi'));
        $this->data['kiwi'] = $this->my_model->getinfo('tbl_kiwi');
        $this->data['kiwi'][''] = 'Opt. Out';

        ksort($this->data['kiwi']);

        $this->my_model->setJoin(array(
            'table' => array('tbl_salary_type','tbl_salary_freq'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_wage_type.type','tbl_wage_type.frequency'),
            'type' => 'left'
        ));
        $this->my_model->setNormalized('description','id');
        $this->my_model->setSelectFields(array(
            'tbl_wage_type.id',
            'CONCAT(tbl_wage_type.description, " (", tbl_salary_type.code ," - ", tbl_salary_freq.code ,")" ) as description'
        ));
        $this->my_model->setOrder('tbl_wage_type.type');
        $this->data['wage_type'] = $this->my_model->getinfo('tbl_wage_type',array(1,2),'tbl_wage_type.type');
        $this->data['wage_type'][''] = '-';

        ksort($this->data['wage_type']);

        $this->my_model->setNormalized('tax_code','id');
        $this->my_model->setSelectFields(array('id','tax_code'));
        $this->data['tax_code'] = $this->my_model->getinfo('tbl_tax_codes');
        $this->data['tax_code'][''] = '-';

        ksort($this->data['tax_code']);

        $this->my_model->setNormalized('tax_code','id');
        $this->my_model->setSelectFields(array('id','tax_code'));
        $has_st_loan = (Object)$this->my_model->getinfo('tbl_tax_codes',1,'has_st_loan');
        $this->data['has_st_loan'] = json_encode($has_st_loan);

        $this->my_model->setNormalized('currency_code','id');
        $this->my_model->setSelectFields(array('id','currency_code'));
        $this->data['currency'] = $this->my_model->getinfo('tbl_currency');

        ksort($this->data['currency']);

        $action_array = array('edit','delete','fixed');
        $id = '';
        if(in_array($action,$action_array)){
            $id = $this->uri->segment(3);
            if(!$id){
                exit;
            }
        }

        switch($action){
            case 'add':
                $this->load->view('backend/staff/add_manage_staff',$this->data);
                break;
            case 'edit':
                $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$id);
                if(count($this->data['staff']) > 0){
                    foreach($this->data['staff'] as $row){
                        $this->my_model->setLastId('rate_id');
                        $rate = $this->my_model->getInfo('tbl_staff_rate',$row->id,'staff_id');
                        $row->rate = $rate ? $rate : $row->rate;

                        $this->my_model->setLastId('start_use');
                        $start_use = $this->my_model->getInfo('tbl_staff_rate',$row->id,'staff_id');
                        $row->start_use = $rate ? date('d-m-Y',strtotime($start_use)) : date('d-m-Y');
                    }
                }
                $this->load->view('backend/staff/edit_manage_staff',$this->data);
                break;
            case 'fixed':

                $this->my_model->setJoin(array(
                    'table' => array('tbl_staff_nz_rate','tbl_hourly_nz_rate'),
                    'join_field' => array('staff_id','id'),
                    'source_field' => array('tbl_staff.id','tbl_staff_nz_rate.hourly_nz_rate_id'),
                    'type' => 'left'
                ));
                $fld = ArrayWalk($this->my_model->getFields('tbl_staff'),'tbl_staff.');
                $fld[] = 'tbl_hourly_nz_rate.hourly_rate';
                $fld[] = 'tbl_staff_nz_rate.hourly_nz_rate_id';

                $this->my_model->setSelectFields($fld);
                $this->data['fixed_amount'] = $this->my_model->getinfo('tbl_staff',$id,'tbl_staff.id');
                $this->load->view('backend/staff/edit_manage_fixed_amount',$this->data);
                break;
            default:
                if(isset($_GET['archive'])){
                    $post = array(
                        'is_unemployed' => true,
                        'status_id' => 1
                    );
                }else if(isset($_GET['current'])){
                    $post = array(
                        'is_unemployed' => false,
                        'status_id' => 3
                    );
                }else{
                    $post = array(
                        'is_unemployed' => true,
                        'status_id' => 2
                    );
                }

                $this->my_model->update('tbl_staff',$post,$id);
                redirect('wageManage');
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($action){
                case 'add':

                    $_POST['bank_account'] = json_encode($_POST['bank_account']);
                    $_POST['has_loans'] = $_POST['balance'] != '' ? true : false;
                    $start_use = $_POST['start_use'];
                    unset($_POST['start_use']);

                    $_POST['has_loans'] = $_POST['balance'] != '' ? true : false;
                    $_POST['status_id'] = 3;
                    $_POST['date_employed'] = $_POST['date_employed'] ? date('Y-m-d') : '';

                    if(isset($_POST['kiwi_id']) && !$_POST['kiwi_id']){
                        unset($_POST['kiwi_id']);
                    }
                    if(isset($_POST['employeer_kiwi']) && !$_POST['employeer_kiwi']){
                        unset($_POST['employeer_kiwi']);
                    }
                    if(isset($_POST['esct_rate_id']) && !$_POST['esct_rate_id']){
                        unset($_POST['esct_rate_id']);
                    }
                    $id = $this->my_model->insert('tbl_staff',$_POST,false);

                    $this->my_model->setLastId('rate_cost');
                    @$rate_value = $this->my_model->getInfo('tbl_rate',$_POST['rate']);

                    $post = array(
                        'staff_id' => $id,
                        'rate_id' => $_POST['rate'],
                        'date_added' => date('Y-m-d'),
                        'start_use' => date('Y-m-d',strtotime($start_use)),
                        'rate' => $rate_value
                    );

                    $this->my_model->insert('tbl_staff_rate',$post,false);
                    redirect('wageManage');
                    break;
                case 'fixed':
                    $post = array(
                        'staff_id' => $id,
                        'hourly_nz_rate_id' => $_POST['hourly_nz_rate_id']
                    );
                    $this->my_model->update('tbl_staff_nz_rate',$post,$id,'staff_id');
                    unset($_POST['hourly_nz_rate_id']);
                    $this->my_model->update('tbl_staff',$_POST,$id);
                    redirect('paySetup');
                    break;
                default:
                    $id = $this->uri->segment(3);
                    if(!$id){
                        exit;
                    }
                    $this->my_model->setLastId('rate_cost');
                    @$rate_value = $this->my_model->getInfo('tbl_rate',$_POST['rate']);

                    $post = array(
                        'staff_id' => $id,
                        'rate_id' => $_POST['rate'],
                        'date_added' => date('Y-m-d'),
                        'start_use' => date('Y-m-d',strtotime($_POST['start_use'])),
                        'rate' => $rate_value
                    );

                    $this->my_model->setLastId('id');
                    @$rate = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');

                    $this->my_model->setLastId('rate_id');
                    @$rate_type = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');

                    $this->my_model->setLastId('star_use');
                    @$start_use_ = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');

                    $post_rate = array(
                        'start_use' => date('Y-m-d',strtotime($_POST['start_use']))
                    );

                    $has_rate = $this->my_model->getInfo('tbl_staff_rate',array($rate,$rate_type),array('id','rate_id'));
                    if(count($has_rate) > 0){
                        foreach($has_rate as $value){
                            if($rate_type != $_POST['rate']){
                                $post_rate['end_use'] = date('Y-m-d',strtotime('-1 day '.$_POST['start_use']));

                                $this->my_model->insert('tbl_staff_rate',$post);
                            }
                            $this->my_model->update('tbl_staff_rate',$post_rate,$value->id);
                        }
                    }else{
                        $this->my_model->insert('tbl_staff_rate',$post);
                    }

                    unset($_POST['rate']);
                    unset($_POST['start_use']);
                    if(isset($_POST['kiwi_id']) && !$_POST['kiwi_id']){
                        unset($_POST['kiwi_id']);
                        $mysql_str = 'UPDATE tbl_staff SET  kiwi_id = NULL WHERE  id ='. $id ;
                        $this->db->query($mysql_str);
                    }
                    if(isset($_POST['employeer_kiwi']) && !$_POST['employeer_kiwi']){
                        unset($_POST['employeer_kiwi']);
                        $mysql_str = 'UPDATE tbl_staff SET  employeer_kiwi = NULL WHERE  id ='. $id ;
                        $this->db->query($mysql_str);
                    }
                    if(isset($_POST['esct_rate_id']) && !$_POST['esct_rate_id']){
                        unset($_POST['esct_rate_id']);
                        $mysql_str = 'UPDATE tbl_staff SET  esct_rate_id = NULL WHERE  id ='. $id ;
                        $this->db->query($mysql_str);
                    }
                    $_POST['bank_account'] = json_encode($_POST['bank_account']);
                    $_POST['has_loans'] = $_POST['balance'] != '' ? true : false;
                    $_POST['date_employed'] = $_POST['date_employed'] ? date('Y-m-d',strtotime($_POST['date_employed'])) : '';
                    $this->my_model->update('tbl_staff',$_POST,$id,'id',false);
                    redirect('wageManage');
                    break;
            }
        }
    }

    function manageTax(){
        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }
        if($action == 'edit'){
            $id = $this->uri->segment(3);
            if(!$id){
                exit;
            }
        }

        switch($action){
            case 'add':
                $this->load->view('backend/staff/add_manage_tax');
                break;
            default:
                $this->data['tax'] = $this->my_model->getinfo('tbl_tax',$id);
                $this->load->view('backend/staff/edit_manage_tax',$this->data);
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);

            switch($action){
                case 'add':
                    $this->my_model->insert('tbl_tax',$_POST);
                    break;
                default:
                    $this->my_model->update('tbl_tax',$_POST,$id);
                    break;
            }

            redirect('wageManage');
        }
    }

    function pdfSummaryArchive(){

        $this->my_model->setJoin(array(
            'table' => array('tbl_staff'),
            'join_field' => array('id'),
            'source_field' => array('tbl_pdf_archive.staff_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_pdf_archive.id','CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'DATE_FORMAT(tbl_pdf_archive.date,"%d-%M-%Y") as date',
            'tbl_pdf_archive.file_name','tbl_pdf_archive.download'
        ));
        $this->my_model->setOrder('date','DESC');
        $this->data['pay_slip_archive'] = $this->my_model->getinfo('tbl_pdf_archive','payslip','type');

        $this->my_model->setSelectFields(array(
            'tbl_pdf_archive.id','DATE_FORMAT(tbl_pdf_archive.date,"%d-%M-%Y") as date',
            'tbl_pdf_archive.file_name','tbl_pdf_archive.download'
        ));
        $this->my_model->setOrder('date','DESC');
        $this->data['monthly_archive'] = $this->my_model->getinfo('tbl_pdf_archive','monthly','type');

        $this->my_model->setSelectFields(array(
            'tbl_pdf_archive.id','DATE_FORMAT(tbl_pdf_archive.date,"%d-%M-%Y") as date',
            'tbl_pdf_archive.file_name','tbl_pdf_archive.download'
        ));
        $this->my_model->setOrder('date','DESC');
        $this->data['wage_archive'] = $this->my_model->getinfo('tbl_pdf_archive','wage','type');
        $this->data['page_load'] = 'backend/staff/pdf_summary_archive_view';
        $this->load->view('main_view',$this->data);
    }

    function download() {

        $id = $this->uri->segment(2);

        if(!$id){
            exit;
        }

        $file = $this->my_model->getinfo('tbl_pdf_archive',$id);
        $file_path = '';
        $file_name = '';

        if(count($file)>0){
            foreach($file as $v){
                switch($v->type){
                    case 'payslip':
                        $path = 'payslip/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/';
                        break;
                    case 'wage':
                        $path = 'summary/wage/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/';
                        break;
                    default:
                        $path = 'summary/monthly/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/';
                        break;
                }

                $post = array(
                    'download' => $v->download + 1
                );
                $file_name = $v->file_name;
                $file_path = $path.$v->file_name;
                $this->my_model->update('tbl_pdf_archive',$post,$v->id);
            }
        }

        $uploaddir = realpath(APPPATH . '../pdf'); // change the path to fit your websites document structure
        $fullPath = $uploaddir.'/'.$file_path;

        $data = file_get_contents($fullPath); // Read the file's contents
        $name = $file_name;
        force_download($name, $data);
    }

    function staffList(){

        $this->my_model->setJoin(array(
            'table' => array('tbl_rate','tbl_wage_type','tbl_currency','tbl_deductions','tbl_team'),
            'join_field' => array('id','id','id','staff_id','id'),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.wage_type',
                'tbl_staff.currency',
                'tbl_staff.id','tbl_staff.team_id'
            ),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id', 'tbl_rate.rate_name','tbl_wage_type.description',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.tax_number','tbl_currency.currency_code',
            'CONCAT("$ ",tbl_rate.rate_cost) as rate_cost',
            'IF(tbl_staff.balance, CONCAT("$ ",FORMAT(tbl_staff.balance,2)),"") as balance',
            'IF(tbl_staff.installment, CONCAT("$ ",FORMAT(tbl_staff.installment,2)),"") as installment',
            'tbl_currency.symbols',
            'IF(tbl_staff.nz_account != "", CONCAT("$ ",tbl_staff.nz_account), "") as nz_account',
            'IF(tbl_staff.account_two != "", CONCAT("$ ",tbl_staff.account_two), "") as account_two',
            'IF(tbl_deductions.flight_debt, CONCAT("$ ",FORMAT(tbl_deductions.flight_debt,2)),"") as flight',
            'IF(tbl_deductions.visa_debt, CONCAT("$ ",FORMAT(tbl_deductions.visa_debt,2)),"") as visa',
            'IF(tbl_deductions.accommodation, CONCAT("$ ",FORMAT(tbl_deductions.accommodation,2)),"") as accommodation',
            'IF(tbl_deductions.transport, CONCAT("$ ",FORMAT(tbl_deductions.transport,2)),"") as transport',
            'tbl_staff.team_id','tbl_team.code as team_code','tbl_team.team'
        ));
        $this->my_model->setOrder('tbl_staff.id');
        $this->data['employee'] = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

        if(count($this->data['employee']) > 0){
            foreach($this->data['employee'] as $row){
                $rate = $this->getStaffRate($row->id);
                if(count($rate) > 0){
                    foreach($rate as $val){
                        $row->rate_name = $val->rate_name;
                        $row->rate_cost = '$'.$val->rate;
                    }
                }
            }
        }
        $this->data['page_load'] = 'backend/staff/staff_list_view';
        $this->load->view('main_view',$this->data);
    }

    function staffTeamManage(){

        $this->data['action'] = $this->uri->segment(2);
        $id = $this->uri->segment(3);

        if(!$this->data['action'] && !$id){
            exit;
        }

        $this->my_model->setNormalized('team','id');
        $this->my_model->setSelectFields(array('id','team'));
        $this->data['team'] = $this->my_model->getInfo('tbl_team');

        $this->my_model->setLastId('team_id');
        $team_id = $this->my_model->getInfo('tbl_staff',$id);
        $this->data['team_id'] = $team_id ? $team_id : '';

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $this->my_model->update('tbl_staff',$_POST,$id);
            redirect('staffList');
        }

        $this->load->view('backend/staff/manage_team_view',$this->data);
    }

    function staffWageHistory(){

        $id = $this->uri->segment(2);

        $this->data['month_val'] = date('m');
        $this->data['year_val'] = date('Y');
        $this->data['year'] = $this->getYear();
        $this->data['thisMonth'] = date('F');
        $this->data['month'] = $this->getMonth();
        $this->data['type'] = 1;
        $this->data['start'] = date('d-m-Y');
        $this->data['end'] = date('d-m-Y',strtotime('+12months'));

        if(isset($_POST['submit'])){
            $thisDate = $_POST['year'].'-'.$_POST['month'];
            $this->data['thisMonth'] = date('F',strtotime($thisDate));
            $this->data['month_val'] = $_POST['month'];
            $this->data['year_val'] = $_POST['year'];
            $this->data['type'] = $_POST['type'];
            $this->data['start'] = isset($_POST['start_date']) ? $_POST['start_date'] : '';
            $this->data['end'] = isset($_POST['end_date']) ? $_POST['end_date'] : '';
        }

        if(isset($_GET['print'])){
            $type = $this->uri->segment(3);
            $year = $this->uri->segment(4);
            $month = $this->uri->segment(5);
            $start = $this->uri->segment(6);
            $end = $this->uri->segment(7);

            if($_GET['print'] == 1){
                $thisDate = $year.'-'.$month;
                $this->data['thisMonth'] = date('F',strtotime($thisDate));
                $this->data['month_val'] = $month;
                $this->data['year_val'] = $year;
                $this->data['type'] = $type;
                $this->data['start'] = $start;
                $this->data['end'] = $end;
            }
        }

        if($this->data['type'] == 1){
            $date = $this->getFirstNextLastDay($this->data['year_val'],$this->data['month_val'],'tuesday');
        }else if($this->data['type'] == 2){
            $date = $this->getWeekInYear($this->data['year_val']);
        }else{
            $date = $this->getWeekBetweenDates($this->data['start'],$this->data['end']);
        }

        $this->data['date'] = $date;

        if(!$id){
            exit;
        }

        $this->data['staff'] = array();
        $this->data['name'] = $this->my_model->getinfo('tbl_staff',$id);

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_currency','tbl_rate',
                'tbl_deductions','tbl_wage_type',
                'tbl_kiwi as employee','tbl_kiwi as employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            ),
            'join_field' => array('id','id','staff_id','id','id','id','id','id','id'),
            'source_field' => array(
                'tbl_staff.currency','tbl_staff.rate',
                'tbl_staff.id','tbl_staff.wage_type',
                'tbl_staff.kiwi_id','tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id',
                'tbl_staff.tax_code_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_currency','tbl_rate',
                'tbl_deductions','tbl_wage_type',
                'employee','employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            )
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.tax_number','tbl_currency.currency_code',
            'tbl_staff.balance',
            'tbl_staff.installment',
            'tbl_rate.rate_cost',
            'tbl_deductions.flight_debt',
            'tbl_deductions.flight_deduct',
            'tbl_deductions.visa_debt','tbl_deductions.visa_deduct',
            'tbl_deductions.accommodation','tbl_deductions.transport',
            'tbl_currency.symbols',
            'tbl_staff.nz_account',
            'tbl_wage_type.type as wage_type',
            'tbl_wage_type.frequency as frequency_id',
            'tbl_staff.account_two',
            'employee.kiwi',
            'employeer.kiwi as emp_kiwi',
            'tbl_esct_rate.field_name',
            'tbl_esct_rate.cec_name',
            'tbl_tax_codes.has_st_loan'
        ));
        $staff_history = $this->my_model->getinfo('tbl_staff',$id,'tbl_staff.id');

        $this->getYearTotalBalance($this->data['year_val']);

        $this->data['balance'] = array();
        $this->data['start_week'] = '';
        if(count($this->data['date']) >0){
            foreach($this->data['date'] as $dv){
                if(count($staff_history)>0){
                    foreach($staff_history as $sv){
                        $this->data['balance'][$sv->id] = $sv->balance;
                        $sv->hours = $sv->wage_type != 1 ? $this->getTotalHours($dv,$sv->id) : 1;

                        $rate = $this->getStaffRate($sv->id,$dv);
                        $sv->start_use = '';
                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $sv->rate_name = $val->rate_name;
                                $sv->rate_cost = $val->rate;
                                $sv->start_use = $val->start_use;
                            }
                        }

                        $hourly_rate = $this->getStaffHourlyRate($sv->id);
                        $sv->hourly_rate = 0;
                        if(count($hourly_rate) > 0){
                            foreach($hourly_rate as $val){
                                $sv->hourly_rate = $val->hourly_rate;
                            }
                        }

                        $sv->gross = $sv->hours * $sv->rate_cost;
                        $sv->gross_ = $sv->gross != 0 ? number_format($sv->gross,2,'.','') : '0.00';
                        //$sv->gross = $sv->gross != 0 ? number_format($sv->gross,0,'.','') : '0.00';

                        $sv->gross = floatval($sv->gross);

                        $sv->nz_account = $sv->nz_account ? ($sv->nz_account + ($sv->hours * $sv->hourly_rate)) : 0;

                        $sv->recruit = $sv->visa_debt != '' || $sv->visa_debt != 0? $sv->gross * 0.03 : '';
                        $sv->admin = $sv->visa_debt != '' || $sv->visa_debt != 0 ? $sv->gross * 0.01 : '';

                        $sv->kiwi_ = 0;
                        $sv->st_loan = 0;
                        $sv->emp_kiwi_ = 0;
                        $sv->cec = 0;
                        $sv->esct = 0;
                        $kiwi = $sv->kiwi ? 'kiwi_saver_'.$sv->kiwi : '';
                        $emp_kiwi = $sv->emp_kiwi ? 'kiwi_saver_'.$sv->emp_kiwi : '';
                        $cec = $sv->cec_name ? $sv->cec_name : '';
                        $esct = $sv->field_name ? $sv->field_name : '';
                        $data_ = $this->getPayeValue($sv->frequency_id,$dv,$sv->gross,$kiwi,$emp_kiwi,$cec,$esct);
                        if(count($data_) > 0){
                            $sv->tax = $data_['tax'];
                            $sv->m_paye = $data_['m_paye'];
                            $sv->me_paye = $data_['me_paye'];
                            $sv->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                            $sv->st_loan = $sv->has_st_loan ? $data_['st_loan'] : 0;
                            $sv->emp_kiwi_ = $emp_kiwi ? $data_['emp_kiwi'] : 0;
                            $sv->cec = $cec ? $data_['cec'] : 0;
                            $sv->esct = $esct ? $data_['esct'] : 0;
                        }

                        $sv->nett = $sv->gross - ($sv->kiwi_ + $sv->tax + $sv->flight_deduct + $sv->visa_deduct + $sv->accommodation + $sv->transport + $sv->recruit + $sv->admin);
                        $this->data['staff'][$dv] = array(
                            'hours' => $sv->wage_type != 1 ? $sv->hours : 40,
                            'flight' => $sv->flight_deduct != '' ? '$'.number_format($sv->flight_deduct,2,'.','') : '',
                            'visa' => $sv->visa_deduct != '' ? '$'.number_format($sv->visa_deduct,2,'.','') : '',
                            'accommodation' => $sv->accommodation != '' ? '$'.number_format($sv->accommodation,2,'.','') : '',
                            'transport' => $sv->transport != '' ? '$'.number_format($sv->transport,2,'.','') : '',
                            'balance' => $sv->balance,
                            'installment' => $sv->installment,
                            'nz_account' => $sv->nz_account != '' ? number_format($sv->nz_account,2,'.','') : '',
                            'account_two' => $sv->account_two != '' ? number_format($sv->account_two,2,'.','') : '',
                            'gross' => $sv->gross_ != 0 ? '$'.$sv->gross_ : '',
                            'recruit' => $sv->recruit != 0 ? number_format($sv->recruit,2,'.','') : '',
                            'admin' => $sv->admin != 0 ? number_format($sv->admin,2,'.','') : '',
                            'nett' => $sv->nett != 0 ? number_format($sv->nett,2,'.','') : '',
                            'staff_id' => $sv->id,
                            'st_loan' => $sv->st_loan,
                            'start_use' => $sv->start_use,
                            'wage_type' => $sv->wage_type,
                            'kiwi' => $sv->kiwi_,
                            'emp_kiwi' => $sv->emp_kiwi_,
                            'cec' => $sv->cec,
                            'esct' => $sv->esct,
                            'tax' => $sv->tax != 0 ? number_format($sv->tax,2,'.','') : ''
                        );
                        $this->data['start_week'] = $this->getWeekNumberOfDateInYear($sv->start_use);
                    }
                }
            }
        }

        if(isset($_GET['print'])){
            if($_GET['print'] == 1){
                //$this->displayarray($this->data['staff']);
                $this->load->view('backend/print/print_staff_wage_history_view',$this->data);
            }
        }else{
            $this->data['page_load'] = 'backend/staff/staff_wage_history_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function employerMonthlySched(){

        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
        $this->data['thisYear'] = date('Y');
        $this->data['thisMonth'] = date('m');


        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
        }

        $this->getEmployerData($this->data['thisYear'],$this->data['thisMonth']);
        if(isset($_GET['print'])){
            $this->data['thisYear'] = $_GET['year'];
            $this->data['thisMonth'] = $_GET['month'];
            switch($_GET['print']){
                case 'deductions':
                    $this->getEmployerData($_GET['year'],$_GET['month']);
                    $this->load->view('backend/staff/print_employer_deduction_view',$this->data);
                    break;
                case 'schedule':
                    $this->getEmployerData($_GET['year'],$_GET['month']);
                    $this->load->view('backend/staff/print_employer_monthly_sched_view',$this->data);
                    break;
                default:
                    break;
            }
        }else{
            $this->data['page_load'] = 'backend/staff/employer_monthly_sched_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function employerDeduction(){
        

        $this->data['thisYear'] = date('Y');
        $this->data['thisMonth'] = date('m');
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();

        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
        }

        $this->getEmployerData($this->data['thisYear'],$this->data['thisMonth']);

        $this->data['page_load'] = 'backend/staff/employer_deduction_view';
        $this->load->view('main_view',$this->data);
    }

    function payPeriodSummaryReport(){
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();


        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
            $this->data['thisWeek'] = $_POST['week'];
            $this->session->set_userdata(array(
                '_year' => $_POST['year'],
                '_month' => $_POST['month'],
                '_week' => $_POST['week']
            ));
            redirect('payPeriodSummaryReport');
        }
        $_this_date = new DateTime();
        $this->data['thisYear'] = $this->session->userdata('_year') != '' ? $this->session->userdata('_year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('_month') != '' ? $this->session->userdata('_month') : date('m');
        $this->data['thisWeek'] = $this->session->userdata('_week') != '' ? $this->session->userdata('_week') : $_this_date->format('W');
        $this->data['week'] = $this->getWeeksNumberInMonth($this->data['thisYear'],$this->data['thisMonth']);

        $week_ = isset($_GET['week']) ? $_GET['week'] : $this->data['thisWeek'];
        $year_ = isset($_GET['year']) ? $_GET['year'] : $this->data['thisYear'];
        $month_ = isset($_GET['month']) ? $_GET['month'] : $this->data['thisMonth'];

        $this->getWageData($year_,$month_);
        $week = $this->getWeekDateInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $date = @$week[$this->data['thisWeek']];
        $this->data['thisDate'] = $date;

        $dir = realpath(APPPATH.'../pdf');
        $path = 'pay period/'.date('Y/F',strtotime($date));
        $this->data['dir'] = $dir.'/'.$path;

        if(!is_dir($this->data['dir'])){
            mkdir($this->data['dir'], 0777, TRUE);
        }

        if(isset($_GET['print']) && $_GET['print'] == 1){
            $date = $week[$week_];
            $this->getWageData($year_,$month_);

            $_date = new DateTime($date);
            $week = $_date->format("W");
            $_week_end = $week != 30 ? date('j F Y',strtotime('+6 days '.$date)) : date('j F Y',strtotime('+5 days '.$date));
            $this->data['page_name'] .= ' <br/>from '.date('j F Y',strtotime($date)).' to '.$_week_end;
            $this->data['pdf_name'] = 'Subbies_PPSR_WE'.date('Ymd',strtotime($_week_end));

            if(isset($_GET['week'])){
                $filename = $this->data['pdf_name'].'.pdf';
                $whatDate = date('Y-m-d',strtotime($date));
                $whatVal = array(
                    $week_,
                    $whatDate
                );
                $whatFld = array('week_num','date');
                $this->my_model->setShift();
                $this->data['pay_period'] = (Object)$this->my_model->getInfo('tbl_week_pay_period',$whatVal,$whatFld);
                //region Set PDF File
                $post = array(
                    'file_name' => $filename,
                    'date' => $whatDate,
                    'type' => 'report'
                );
                $whatVal = array($filename,$whatDate,'report');
                $whatFld = array('file_name','date','type');
                $is_exist = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);
                if(count($is_exist) > 0){
                    foreach($is_exist as $val){
                        $this->my_model->update('tbl_pdf_archive',$post,$val->id,'id',false);
                    }
                }else{
                    $this->my_model->insert('tbl_pdf_archive',$post,false);
                }
                //endregion

                //Set Week Pay Period Action
                $post = array(
                    'week_num' => $week_,
                    'date' => $whatDate
                );
                $whatVal = array($week_,$whatDate);
                $whatFld = array('week_num','date');
                $is_exist = $this->my_model->getInfo('tbl_week_pay_period',$whatVal,$whatFld);
                if(count($is_exist) > 0){
                    foreach($is_exist as $val){
                        $this->my_model->update('tbl_week_pay_period',$post,$val->id,'id',false);
                    }
                }else{
                    $this->my_model->insert('tbl_week_pay_period',$post,false);
                }
                //endregion
            }

            $this->load->view('backend/staff/print_pay_summary_report_view',$this->data);
        }else{
            $_date = new DateTime($date);
            $week = $_date->format("W");
            $_week_end = $week != 30 ? date('j F Y',strtotime('+6 days '.$date)) : date('j F Y',strtotime('+5 days '.$date));
            $this->data['page_name'] .= ' from '.date('j F Y',strtotime($date)).' to '.$_week_end;

            $this->data['page_load'] = 'backend/staff/pay_summary_report_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function cronPayPeriodSummaryReport(){
        $pay_setup = $this->my_model->getInfo('tbl_pay_setup');
        if(count($pay_setup) > 0){
            $cc = array();
            foreach($pay_setup as $val){

            }

            /*$sendMailSetting = array(
                'to' => $take_off_setting->take_off_merchant_email,
                'to_alias' => $take_off_setting->take_off_merchant_name,
                'cc' => $cc,
                'cc_alias' => $cc_alias,
                'name' => $franchise->franchise_branch_name,
                'from' => $take_off_setting->take_off_franchise_email,
                'subject' => 'Pay Period Summary Report from ' . date('j F Y') .' to '.date('j F Y',strtotime('+6 days')),
                'url' => $url,
                'disposition' => $disposition,
                'file_names' => $file_names,
                'debug_type' => 2,
                'debug' => true
            );
            $debugResult = $liberty->sendingEmail(
                $msg,
                $sendMailSetting
            );*/
        }
    }

    function paySetup(){
        $page = $this->uri->segment(2);

        $this->my_model->setSelectFields(array(
            'id','CONCAT(type," (",code,")") as type'
        ));
        $this->data['salary_type'] = $this->my_model->getinfo('tbl_salary_type',array(1,2));
        $this->my_model->setSelectFields(array(
            'id','CONCAT(frequency," (",code,")") as frequency'
        ));
        $this->data['salary_freq'] = $this->my_model->getinfo('tbl_salary_freq');

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_salary_freq',
                'tbl_salary_type'
            ),
            'join_field' => array('id','id'),
            'source_field' => array(
                'tbl_wage_type.frequency',
                'tbl_wage_type.type'
            ),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_wage_type.id',
            'CONCAT('.'tbl_salary_type.type," (",'.'tbl_salary_type.code,")") as salary_type',
            'CONCAT('.'tbl_salary_freq.frequency," (",'.'tbl_salary_freq.code,")") as frequency',
            'tbl_wage_type.description',
            'tbl_wage_type.type'
        ));
        $this->my_model->setOrder(array('type','description'));
        $this->data['wage_type'] = $this->my_model->getinfo('tbl_wage_type',array(1,2),'tbl_wage_type.type');

        $Val = array(true,false);
        $Fld = array('has_loans','is_unemployed');

        $this->data['loans'] = $this->my_model->getinfo('tbl_staff',$Val,$Fld);

        $this->data['rate'] = $this->my_model->getinfo('tbl_rate');
        $this->my_model->setShift();
        $this->data['pay_setup'] = (Object)$this->my_model->getInfo('tbl_pay_setup');

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_wage_type',
                'tbl_currency',
                'tbl_tax_codes',
                'tbl_staff_rate',
                'tbl_staff_nz_rate',
                'tbl_hourly_nz_rate'
            ),
            'join_field' => array('id','id','id','id','staff_id','staff_id','id'),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.wage_type',
                'tbl_staff.currency',
                'tbl_staff.tax_code_id',
                'tbl_staff.id',
                'tbl_staff.id',
                'tbl_staff_nz_rate.hourly_nz_rate_id',
            ),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'tbl_rate.rate_name',
            'tbl_wage_type.description',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.tax_number',
            'tbl_currency.currency_code',
            'CONCAT("$",tbl_rate.rate_cost) as rate_cost',
            'tbl_staff.balance',
            'tbl_staff.installment',
            'tbl_currency.symbols',
            'IF(tbl_staff.nz_account != "", CONCAT("$",tbl_staff.nz_account), "") as nz_account',
            'IF(tbl_staff.account_two != "", CONCAT("$",tbl_staff.account_two), "") as account_two',
            'IF(tbl_tax_codes.tax_code !="",tbl_tax_codes.tax_code,"") as tax_code',
            'IF('.'tbl_staff.ird_num != "-" ,LPAD('.'tbl_staff.ird_num,11,"0"),'.'tbl_staff.ird_num) as ird_num',
            'tbl_staff_rate.start_use',
            'tbl_staff.bank_account',
            'tbl_staff.is_unemployed',
            'tbl_hourly_nz_rate.hourly_rate'
        ));
        $whatVal = false;
        $whatFld = 'is_unemployed';

        $this->my_model->setGroupBy('tbl_staff.id');
        $this->my_model->setOrder(array('lname','fname'));
        $this->data['employee'] = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);

        $this->my_model->setJoin(array(
            'table' => array('tbl_deductions'),
            'join_field' => array('staff_id'),
            'source_field' => array('tbl_staff.id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_deductions.id',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.id as employee',
            'IF(tbl_deductions.flight_deduct != "", CONCAT("$",FORMAT(tbl_deductions.flight_deduct,2)),"") as flight_deduct',
            'IF(tbl_deductions.flight_debt != "", CONCAT("$ ",FORMAT(tbl_deductions.flight_debt,2)),"") as flight_debt',
            'IF(tbl_deductions.accommodation != "", CONCAT("$",FORMAT(tbl_deductions.accommodation,2)),"") as accommodation',
            'IF(tbl_deductions.visa_deduct != "", CONCAT("$",FORMAT(tbl_deductions.visa_deduct,2)),"") as visa_deduct',
            'IF(tbl_deductions.visa_debt != "", CONCAT("$",FORMAT(tbl_deductions.visa_debt,2)),"") as visa_debt',
            'IF(tbl_deductions.transport != "", CONCAT("$",FORMAT(tbl_deductions.transport,2)),"") as transport'
        ));
        $this->my_model->setOrder('tbl_staff.id');
        $this->data['deductions'] = $this->my_model->getinfo('tbl_staff',array(false),array('is_unemployed'));

        if(count($this->data['employee'])>0){
            foreach($this->data['employee'] as $v){
                $rate = $this->getStaffRate($v->id);
                if(count($rate) > 0){
                    foreach($rate as $row){
                        $v->rate_name = $row->rate_name;
                        $v->rate_cost = '$'.$row->rate;;
                    }
                }

                switch($v->currency_code){
                    case 'NZD':
                        $v->rate = '$1';
                        break;
                    default:
                        $converted_amount = CurrencyConverter($v->currency_code);
                        $v->rate = $v->symbols.' '.$converted_amount;
                        break;
                }
            }
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $is_exist = $this->my_model->getInfo('tbl_pay_setup','franchise_id');
            if(count($is_exist) == 1){
                foreach($is_exist as $val){
                    $this->my_model->update('tbl_pay_setup',$_POST,$val->id);
                }
            }else{
                //$_POST['franchise_id'] = $this->data['franchise_id'];
                $this->my_model->insert('tbl_pay_setup',$_POST);
            }
            redirect('paySetup');
        }
        if($page == 'franchise'){
            $this->load->view('backend/staff/add_pay_setup_view',$this->data);
        }else{
            $this->data['page_load'] = 'backend/staff/pay_setup_view';
            $this->load->view('main_view',$this->data);
        }
    }
    //endregion

    function monthWeeks(){
        if(isset($_POST['month'])){
            $week = $this->getWeeksNumberInMonth($_POST['year'],$_POST['month']);
            echo json_encode($week);
        }
    }

    function yearToDateReport(){
        $page = $this->uri->segment(2);

        if(isset($_POST['submit'])){
            $this->session->set_userdata(array('_year_val'=>$_POST['year']));
            redirect('yearToDateReport');
        }
        $this->data['_year'] = $this->session->userdata('_year_val') ? $this->session->userdata('_year_val') : date('Y');
        $this_date = date('Y-m-d',strtotime($this->data['_year'].'-'.date('m-d')));

        if($page){
            $id = $this->uri->segment(3);
            if(!$id){
                exit;
            }
            $this->my_model->setShift();
            $staff_name = (Object)$this->my_model->getInfo('tbl_staff',$id);
            switch($page){
                case 'summary':
                    $this->data['page_name'] .= ' for <strong>'.$staff_name->fname.' '.$staff_name->lname.'</strong>';
                    $this->data['page_load'] = 'backend/staff/year_to_date_summary';

                    $set_wage_date = $this->getPaymentStartDate(date('Y',strtotime($this_date)));
                    $total_array = array();
                    $monthly_total_array = array();
                    $wage_date_array = array();
                    $monthly_date = array();
                    $monthly_details = $this->getOverAllWageTotalPay($this_date,$id,true);
                    if(count($monthly_details[$id]) > 0){
                        foreach($monthly_details[$id] as $key=>$val){
                            $year = date('Y',strtotime($key));
                            $month = date('F',strtotime($key));
                            $monthly_total_array[$key] = array(
                                'distribution' => $val['distribution'],
                                'gross' => $val['gross']
                            );
                            $total_array[$year][$month][] =  array(
                                'distribution' => $val['distribution'],
                                'gross' => $val['gross']
                            );
                        }
                    }
                    if(count($set_wage_date) > 0){
                        foreach($set_wage_date as $key=>$val){
                            $year = date('Y',strtotime($key));
                            $month = date('F',strtotime($key));
                            $wage_date_array[$year][$month] = $val;
                            $monthly_date[$year][$month][$key] = $val;
                        }
                    }
                    $current_wage_date = $this->getPaymentStartDate(date('Y'));
                    $current_month = array();
                    if(count($current_wage_date) > 0){
                        foreach($current_wage_date as $key=>$val){
                            $year = date('Y',strtotime($key));
                            $month = date('F',strtotime($key));
                            $current_month[$year][$month][$key] = $val;
                        }
                    }
                    $this->data['total_paid'] = $total_array;
                    $this->data['monthly_total_paid'] = $monthly_total_array;
                    $this->data['wage_date'] = $wage_date_array;
                    $this->data['monthly_date'] = $monthly_date;
                    $this->data['current_month'] = $current_month;
                    $this->load->view('main_view',$this->data);
                    break;
                default:
                    break;
            }
        }else{
            $this->my_model->setOrder(array('lname','fname'));
            $this->data['staff'] = $this->my_model->getInfo('tbl_staff',array('false'),array('is_unemployed'));
            $this->data['year'] = $this->getYear();

            $total_paid = $this->getOverAllWageTotalPay($this_date);
            if(count($this->data['staff']) > 0){
                foreach($this->data['staff'] as $row){
                    $pay = @$total_paid[$row->id];
                    $earn_nett = 0;
                    $earn_gross = 0;
                    $fin_year = '';
                    if(count($pay) > 0){
                        foreach($pay as $val){
                            $earn_nett = $val['distribution'];
                            $earn_gross = $val['gross'];
                            $fin_year = $val['financial_year'];
                        }
                    }
                    $row->earn_nett = $earn_nett > 0 ? $earn_nett : 0;
                    $row->earn_gross = $earn_gross > 0 ? $earn_gross : 0;
                    $row->financial_year = $fin_year;
                }
            }

            $this->data['page_load'] = 'backend/staff/year_to_date_report_view';
            $this->load->view('main_view',$this->data);
        }
    }
}