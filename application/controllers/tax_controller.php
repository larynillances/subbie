<?php
include('subbie.php');
Class Tax_Controller extends Subbie{

    function importTaxTable(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        ini_set('max_execution_time', 0);
        $this->load->library('excel_reader');
        error_reporting(E_ALL ^ E_NOTICE);

        $action = $this->uri->segment(2);
        $this->my_model->setNormalized('type','id');
        $this->my_model->setSelectFields(array('id','type'));
        $this->data['wage_type'] = $this->my_model->getInfo('tbl_salary_type',array(1,2));

        $this->my_model->setNormalized('frequency','id');
        $this->my_model->setSelectFields(array('id','frequency'));
        $this->my_model->setOrder(array('frequency'));
        $this->data['frequency'] = $this->my_model->getInfo('tbl_salary_freq',array(1,2));

        if($action && $action == 'upload'){
            $uploadDir = realpath(APPPATH . '../uploads/tax');
            $file_name = '';
            if(!empty($_FILES)) {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, TRUE);
                }
                $file_name = $_FILES['Filedata']['name'];
                $file = $uploadDir.'/'. $file_name;

                $is_exist = $this->my_model->getInfo('tbl_tax_upload',$file_name,'file_name');

                if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $file)){
                    $post = array(
                        'file_name' => $file_name,
                        'uploader_id' => $this->session->userdata('user_id'),
                        'date' => date('Y-m-d')
                    );

                    if(count($is_exist) > 0){
                        foreach($is_exist as $v){
                            $this->my_model->update('tbl_tax_upload',$post,$v->id,'id',false);
                        }
                    }else{
                        $this->my_model->insert('tbl_tax_upload',$post,false);
                    }
                }
            }

            $data_exist = $this->my_model->getInfo('tbl_tax_upload',$file_name,'file_name');

            if($file_name){
                $data = new Spreadsheet_Excel_Reader($uploadDir.'/'.$file_name);
                $xls = $data->dump_csv(false, false);

                $whatFields = array('id');
                $fields = $this->my_model->getFields('tbl_tax',$whatFields);
                if(count($xls)>0){
                    foreach($xls as $k=>$row){
                        $temp = array();
                        $ref = 1;
                        foreach($row as $value){
                            $str = str_replace('  ',' ',$value);
                            $str = str_replace(',','',$str);
                            $str = str_replace('$','',$str);
                            $data = explode(' ',$str);
                            foreach($data as $key=>$val){
                                $temp[$fields[$ref]] = $val;
                                $temp[$fields[16]] = $_POST['start_date'] ? date('Y-m-d',strtotime($_POST['start_date'])) : '';
                                $temp[$fields[17]] = $_POST['end_date'] ? date('Y-m-d',strtotime($_POST['end_date'])) : '';
                                $temp[$fields[18]] = $_POST['wage_type'];
                                $temp[$fields[19]] = $_POST['frequency'];
                                $ref++;
                            }

                            $whatVal = array(
                                $temp['earnings'],
                                $temp['start_date'],
                                $temp['end_date'],
                                $temp['m_paye'],
                                $temp['me_paye'],
                                $temp['frequency_id']
                            );
                            $whatFld = array(
                                'earnings','start_date','end_date','m_paye','me_paye','wage_type_id','frequency_id'
                            );
                            $has_exist = $this->my_model->getInfo('tbl_tax',$whatVal,$whatFld);

                            if(count($has_exist) > 0){
                                foreach($has_exist as $row){
                                    $this->my_model->update('tbl_tax',$temp,$row->id);
                                }
                            }else{
                                $this->my_model->insert('tbl_tax',$temp,false);
                            }
                        }
                        //$ref++;
                    }
                    echo '1';
                }else{
                    echo '0';
                }
            }else{
                if(count($data_exist) > 0){
                    foreach($data_exist as $v){
                        $file = $v->filename;
                        $this->my_model->delete('tbl_tax_upload',$v->id);
                        if($file){
                            unlink($file);
                        }
                    }
                }
                echo '0';
            }
        }else{
            $this->load->view('backend/tax/import_tax_view',$this->data);
        }
    }
}