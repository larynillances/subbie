<?php
echo form_open('','class="form-horizontal" role="form"');
$date = $thisYear.'-'.$thisMonth.'-01';
$gst_num = '';
$employer_name = '';
$name_address = '';
$contact_num = '';
if(count($info_array) >0){
    foreach($info_array as $inv_data){
        $gst_num = $inv_data->gst_num;
        $employer_name = $inv_data->company_name;
        $name_address = $inv_data->info_text;
        $contact_num = $inv_data->contact_num;
    }
}
?>
<div class="form-group" style="border-bottom: 1px solid #d2d2d2;padding: 5px;">
    <label class="col-sm-1 control-label" >Date:</label>
    <div class="col-sm-2">
        <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-2">
        <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-7">
        <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
        <a href="<?php echo base_url().'employerMonthlySched?print=deductions&year='.$thisYear.'&month='.$thisMonth.'&payment=1'?>" class="btn btn-primary btn-sm deduction-btn" target="_blank">
            <span class="glyphicon glyphicon-print"></span> Deduction
        </a>
        <a href="<?php echo base_url().'employerMonthlySched?print=schedule&year='.$thisYear.'&month='.$thisMonth?>" class="btn btn-primary btn-sm sched-btn" target="_blank">
            <span class="glyphicon glyphicon-print"></span> Schedule
        </a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" style="font-size: 12px;">
        <table class="table">
            <thead>
            <tr>
                <th style="width: 10%;vertical-align: top;border-bottom: 1px solid #000000;">
                    <img src="<?php echo base_url().'images/ird-logo.png'?>">
                </th>
                <th style="line-height: 2px;padding-top: 0;border-bottom: 1px solid #000000;">
                    <h3>Employer monthly schedule</h3>
                    <h5 style="font-style: italic;">For help, refer to notes on employer deductions form.</h5>
                </th>
                <th style="width: 45%;font-size: 12px;padding-left: 15%;" rowspan="2">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th>Employer’s<br/>IRD number</th>
                            <th>
                                <img src="<?php echo base_url().'images/arrow-1.png'?>">
                            </th>
                            <th style="width: 60%;"><?php echo $gst_num?></th>
                        </tr>
                        <tr>
                            <th>Period ended</th>
                            <th>
                                <img src="<?php echo base_url().'images/arrow-2.png'?>">
                            </th>
                            <th style="width: 60%;"><input type="text" class="form-control input-sm input-text" value="<?php echo date('t/m/Y',strtotime($date));?>"></th>
                        </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2" style="font-size: 12px;">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 15%;">Employer Name</th>
                            <th style="width: 40%;"><input type="text" class="form-control input-sm input-text" value="<?php echo $employer_name?>"></th>
                            <th style="width: 20%;">This schedule is due</th>
                            <th><input type="text" class="form-control input-sm input-text" value="<?php echo date('20/m/Y',strtotime('+1 month '.$date));?>"></th>
                        </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            </thead>
            <tbody class="content-data">
            <tr>
                <td colspan="3">
                    <table style="width: 100%;" class="table-info">
                        <thead>
                        <tr>
                            <th>Gross earnings<br/>and/or schedular<br/>payments</th>
                            <th>Earnings and/or schedular<br/>payments not liable<br/>for ACC earners’ levy</th>
                            <th>
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-3.png'?>"></th>
                                        <th>PAYE and/or<br/>schedular tax<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th>
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-4.png'?>"></th>
                                        <th>Child support<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th>
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-5.png'?>"></th>
                                        <th>Student loan<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th>
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-6.png'?>"></th>
                                        <th>KiwiSaver<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th>
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-7.png'?>"></th>
                                        <th>Net KiwiSaver<br/>employer<br/>contributions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </td>
            </tr>
            <?php
            $gross_total = 0;
            $tax_total = 0;
            $st_loan_total = 0;
            $kiwi_total = 0;
            $emp_kiwi_total = 0;
            $esct_total = 0;
            $ref = 0;
            if(count($staff) > 0):
                foreach($staff as $v):
                ?>
                <tr>
                    <td colspan="3">
                        <table style="width: 100%;" class="staff-info">
                            <thead>
                            <?php
                            if($ref == 0):
                                ?>
                                <tr>
                                    <th colspan="6"><strong style="font-size: 12px;">Employee name and IRD number</strong></th>
                                    <th colspan="4">Employment start and/or finish date</th>
                                </tr>
                                <?php
                            endif;
                            ?>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Surname</td>
                                <td>First name(s)</td>
                                <td colspan="3">IRD number</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr class="info">
                                <td><input type="text" class="form-control input-sm input-text" value="<?php echo $v->lname?>" disabled></td>
                                <td><input type="text" class="form-control input-sm input-text" value="<?php echo $v->fname?>" disabled></td>
                                <td style="width: 12%;"><input type="text" class="form-control input-sm input-text" disabled value="<?php echo $v->ird_num;?>"></td>
                                <td style="width: 5%;color: #000000;">Tax Code:</td>
                                <td style="width: 7%;"><input type="text" class="form-control input-sm input-text" value="<?php echo $v->tax_code;?>" disabled></td>
                                <td style="color: #000000;width: 5%;text-align: right;">Start</td>
                                <td style="width: 15%;">
                                    <table style="width: 100%" class="date-table">
                                        <thead>
                                        <tr>
                                            <th><input type="text" class="form-control input-sm input-text" value="<?php echo $v->date_employed != '' ? date('d',strtotime($v->date_employed)) : '';?>"></th>
                                            <th><input type="text" class="form-control input-sm input-text" value="<?php echo $v->date_employed != '' ? date('m',strtotime($v->date_employed)) : '';?>"></th>
                                            <th style="width: 50%;"><input type="text" class="form-control input-sm input-text" value="<?php echo $v->date_employed != '' ? date('Y',strtotime($v->date_employed)) : '';?>"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 10px;">Day</td>
                                            <td style="font-size: 10px;">Month</td>
                                            <td style="font-size: 10px;">Year</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width: 5%;color: #000000;text-align: right;">Finished</td>
                                <td style="width: 15%;">
                                    <table style="width: 100%" class="date-table">
                                        <thead>
                                        <tr>
                                            <th><input type="text" class="form-control input-sm input-text"></th>
                                            <th><input type="text" class="form-control input-sm input-text"></th>
                                            <th style="width: 50%;"><input type="text" class="form-control input-sm input-text"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 10px;">Day</td>
                                            <td style="font-size: 10px;">Month</td>
                                            <td style="font-size: 10px;">Year</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding-left: 35%;">
                                    <table style="width: 100%;" class="table-bordered">
                                        <tbody>
                                        <tr>
                                            <td><input type="checkbox" name="checkbox"></td>
                                            <td>Tick if lump sum payment made<br/>and taxed at lowest rate</td>
                                            <td style="width: 10%;padding-right: 5px;"><input type="text" class="form-control input-sm input-text"></td>
                                            <td>CS Code</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <?php
                                $staff_wage = @$monthly_pay[$v->id];
                                if($staff_wage['hours'] != 0):
                                    $gross_total += $staff_wage['gross'];
                                    $tax_total += $staff_wage['tax'];
                                    $st_loan_total += $staff_wage['st_loan'];
                                    $kiwi_total += $staff_wage['kiwi'];
                                    $emp_kiwi_total += $staff_wage['emp_kiwi'];
                                    $esct_total += $staff_wage['esct'];
                                    $gross_ = explode('.',number_format($staff_wage['gross'],2));
                                        ?>
                                        <td colspan="10">
                                            <table style="width: 100%;font-size: 13px;">
                                                <tbody>
                                                <tr>
                                                    <td style="width: 2%;">$</td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm input-text" value="<?php echo $gross_[0]?>">
                                                    </td>
                                                    <td>.<?php echo $gross_[1] ? $gross_[1] : '00'?></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm input-text" value="nil" disabled>
                                                    </td>
                                                    <td>.00</td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm input-text" value="<?php echo number_format($staff_wage['tax'],2)?>">
                                                    </td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><input type="text" class="form-control input-sm input-text" value="<?php echo $staff_wage['st_loan'] ? number_format($staff_wage['st_loan'],2) : 'nil'?>" disabled></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><input type="text" class="form-control input-sm input-text" value="<?php echo $staff_wage['kiwi'] ? number_format($staff_wage['kiwi'],2) : 'nil' ?>" disabled></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><input type="text" class="form-control input-sm input-text" value="<?php echo $staff_wage['emp_kiwi'] ? number_format($staff_wage['emp_kiwi'],2) : 'nil'?>" disabled></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <?php
                                else:
                                ?>
                                    <td colspan="10">
                                        <table style="width: 100%;font-size: 13px;">
                                            <tbody>
                                            <tr>
                                                <td style="width: 2%;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text"></td>
                                                <td >.00</td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                                <td>.00</td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text"></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                <?php
                                endif;
                                ?>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php
                    $ref++;
                endforeach;
            endif;
            ?>
            <tr>
                <td colspan="3">
                    <table style="width: 100%;" class="table-total">
                        <thead>
                        <tr>
                            <th colspan="3">Total gross earnings and/<br/>or schedular payments</th>
                            <th colspan="3">Total earnings not liable<br/>for ACC earners’ levy</th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-3.png'?>"></th>
                                        <th>Total PAYE and/or<br/>schedular tax</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-4.png'?>"></th>
                                        <th>Total<br/>child support</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-5.png'?>"></th>
                                        <th>Total<br/>student loan</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-6.png'?>"></th>
                                        <th>Total KiwiSaver<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th style="width: 13%;" colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-7.png'?>"></th>
                                        <th>NetTotal Net<br/>KiwiSaver employer<br/>contributions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 2%;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($gross_total)?>"></td>
                            <td >.00</td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                            <td>.00</td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($tax_total,2)?>"></td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="nil" disabled></td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo $st_loan_total ? number_format($st_loan_total,2) : 'nil'?>" disabled></td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo $kiwi_total ? number_format($kiwi_total,2) : 'nil'?>" disabled></td>
                            <td style="width: 2%;padding-left: 5px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo $emp_kiwi_total ? number_format($emp_kiwi_total,2) : 'nil'?>" disabled></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="footer" colspan="2">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td colspan="3">If your correct daytime phone number is not shown below, print in the box</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"></td>
                            <td style="width: 5%;"><img src="<?php echo base_url().'images/right-arrow-background.png'?>"></td>
                            <td><input type="text" class="form-control input-sm input-text" style="width: 50%;" value="<?php echo $contact_num;?>"></td>
                        </tr>
                        <tr>
                            <td colspan="3">If your correct contact person’s name is not shown below, print in the box</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"></td>
                            <td style="width: 5%;"><img src="<?php echo base_url().'images/right-arrow-background.png'?>"></td>
                            <td><input type="text" class="form-control input-sm input-text" style="width: 50%;"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td class="footer">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th><strong>Declaration</strong></th>
                            <th rowspan="2">
                                <table class="indicator-table">
                                    <thead>
                                    <tr>
                                        <th colspan="4"><img src="<?php echo base_url().'images/right-arrow-background.png'?>" style="width: 25%;">OFFICE USE ONLY</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Operator<br/>Code</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Corresp.<br/>indicator</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Payment<br/>attached</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Return<br/>cat.</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th style="font-style: italic;">I declare that the information given in this return is true and correct.</th>
                        </tr>
                        <tr>
                            <th><span style="font-size: 10px;">Signature</span></th>
                        </tr>
                        <tr>
                            <th style="text-align: right;"><?php echo date('d/m/Y');?><br/>Date</th>
                        </tr>
                        </thead>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" style="font-size: 12px;">
        <table class="table">
            <thead>
            <tr>
                <th style="width: 50%;vertical-align: top;border-bottom: 1px solid #000000;">
                    <img src="<?php echo base_url().'images/ird-logo.png'?>"><br/>
                    <h3>Employer deductions</h3>
                </th>
                <th style="border-bottom: 1px solid #000000;text-align: right;"><h5><?php echo date('F Y',strtotime($date));?></h5></th>
            </tr>
            <tr>
                <th style="border-bottom: 1px solid #000000">
                    <label>Name and Address</label>
                    <textarea class="form-control input-sm input-text" style="height: 90px;width: 50%;"><?php echo $name_address;?></textarea>
                </th>
                <th style="border-bottom: 1px solid #000000">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="text-align: right;">
                                <p style="font-size: 11px;">Please see notes on the back to<br/>
                                    help you complete this form and<br/>
                                    the EMS schedule.<br/><br/>
                                    Formore information:<br/>
                                    website <strong>www.ird.govt.nz</strong><br/>
                                    telephone 0800 377 772<br/>
                                    <strong>INFOexpress 0800 257 773</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="text-align: right;padding-right: 3px;">Employer’s<br/>IRD number</th>
                                        <th style="width: 8%;">
                                            <img src="<?php echo base_url().'images/arrow-1.png'?>">
                                        </th>
                                        <th style="width: 40%;"><?php echo $gst_num?></th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;padding-right: 3px;">Period ended</th>
                                        <th style="width: 8%;">
                                            <img src="<?php echo base_url().'images/arrow-2.png'?>">
                                        </th>
                                        <th style="width: 40%;"><input type="text" class="form-control input-sm input-text" value="<?php echo date('t/m/Y',strtotime($date));?>"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </th>
            </tr>
            </thead>
            <tbody style="border:1px solid #000000!important;" class="table-deduction">
            <?php
            $total = 0;
            $over_all_total = 0;
            if(count($monthly_pay) >0){
                foreach($monthly_pay as $k=>$pay){
                    $this_pay = $monthly_pay[$k];
                    if($this_pay['hours'] != 0){
                        $total += @$this_pay['tax'];
                    }
                }
            }
            $over_all_total = $total + $esct_total + $st_loan_total + $emp_kiwi_total + $kiwi_total;
            ?>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td>If your postal address is different from that printed<br/>above, please enter your new address below.</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 30%;">PAYE (incl. tax on schedular payments)</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-3-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($total,2)?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <textarea class="form-control input-sm input-text" style="width: 50%;" placeholder="Street or PO Box"></textarea>
                </td>
                <td>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 30%;">Child support deductions</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-4-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="nil"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control input-sm input-text" style="width: 50%;" placeholder="Suburb">
                </td>
                <td>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 30%;">Student loan deductions</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-5-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($st_loan_total,2)?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control input-sm input-text" style="width: 50%;" placeholder="Town or City">
                </td>
                <td>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 30%;">KiwiSaver deductions</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-6-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($kiwi_total,2)?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td rowspan="2">
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td>
                                <table style="width: 20%;" class="indicator-table">
                                    <thead>
                                    <tr>
                                        <th colspan="4"><img src="<?php echo base_url().'images/right-arrow-background.png'?>">OFFICE USE ONLY</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Operator<br/>Code</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Corresp.<br/>indicator</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Payment<br/>attached</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Return<br/>cat.</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;font-style: italic;">Declaration I declare that the information given in this return is true and correct.</td>
                        </tr>
                        <tr>
                            <td style="width: 30%;background: #FFFFFF;"><span style="font-size: 10px;">Signature</span></td>
                        </tr>
                        <tr style="background: #FFFFFF;">
                            <td style="text-align: right;padding-right: 20%;">
                                <strong><?php echo date('d/m/Y');?><br/>Date</strong>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width: 30%;">Net KiwiSaver employer contributions</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-7-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($emp_kiwi_total,2)?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tbody>
                        <tr style="border-bottom: 1px solid #000000;">
                            <td style="width: 30%;">ESCT deductions</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-8-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($esct_total,2)?>"></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #000000;">
                            <td style="width: 30%;font-weight: bold;">Add Boxes 3, 4, 5, 6, 7 and 8.This is the amount you need to pay</td>
                            <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-9-right.png'?>"></td>
                            <td style="font-size: 13px;">$</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo number_format($over_all_total,2);?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #000000;">
                <td>
                    <span style="font-weight: bold;font-size: 17px;">Inland Revenue copy</span><br/>Please make a copy for your own records
                </td>
                <td>
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td>Has payment been made electronically?</td>
                            <td>(Tick one) </td>
                            <td>
                                <label class="radio-inline">
                                    <input type="radio" name="option" class="payment" value="1" checked> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="option" class="payment" value="0"> No
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th><img src="<?php echo base_url().'images/ird-logo.png'?>"></th>
                            <th><span style="font-weight: bold;font-size: 17px;">Payment slip</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="2"><strong>Name and Address</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea class="form-control input-sm input-text" style="width: 50%;height: 90px;"><?php echo $name_address;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><br/><br/><strong>This return and any payment are due</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table style="width: 100%">
                        <thead>
                        <tr>
                            <th style="text-align: right;padding-right: 20px;height: 50px;" colspan="4">DED</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3">IRD Number</td>
                            <td><input type="text" class="form-control input-sm input-text" value="<?php echo $gst_num;?>" style="width: 40%;"></td>
                        </tr>
                        <tr>
                            <td colspan="3">Period ended</td>
                            <td><input type="text" class="form-control input-sm input-text" style="width: 40%;" value="<?php echo date('t/m/Y',strtotime($date))?>"></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Amount of payment</td>
                            <td style="width: 5%;"><img src="<?php echo base_url().'images/black-arrow-10-right.png'?>"></td>
                            <td style="font-size: 17px;padding-left: 5px;"><strong>$</strong></td>
                            <td><input type="text" class="form-control input-sm input-text" style="width: 60%;" value="<?php echo number_format($over_all_total,2);?>"></td>
                        </tr>
                        <tr>
                            <td colspan="4"><br/><br/><strong>Copy your total from Box 9 and include any late payment penalties and interest, for this period only.</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
echo form_close();
?>
<style>
    .table > tbody.table-deduction > tr > td{
        background: #e0e0e0;
        border: none;
    }
    .indicator-table{
        width: 100%;
        font-size: 10px;
        font-weight: normal;
        border:1px solid #ffffff;
    }
    .indicator-table > thead > tr > th,
    .indicator-table > tbody > tr > td
    {
        padding: 5px;
    }
    table.table > tbody.content-data > tr > td{
        border: 1px solid #000000;
        background: #e0e0e0;
        font-size: 11px;
    }
    table.table > tbody.content-data > tr > td.footer{
        background: none;
        border: none;
        font-size: 12px;
    }
    table.table > thead > tr > th{
        border-bottom: 1px solid #000000;
    }
    .staff-info > tbody > tr > td{
        font-weight: bold;
    }
    .date-table > thead > tr > th,
    .date-table > tbody > tr > td{
        text-align: center;
    }
    .indicator-table{
        width: 100%;
        font-size: 10px;
        font-weight: normal;
        border:1px solid #d2d2d2;
    }
    .indicator-table > thead > tr > th,
    .indicator-table > tbody > tr > td
    {
        padding: 5px;
    }
    .table-total > tbody > tr > td{
        font-size: 13px;
        font-weight: bold;
    }
    .input-text{
        color: #271dff;
        font-weight: bold;
    }
</style>
<script>
    $(function(e){
        var payment = $('.payment');
        var deduction = $('.deduction-btn');
        var link = bu + 'employerMonthlySched?print=deductions&&year=<?php echo $thisYear;?>&&month=<?php echo $thisMonth;?>';
        payment.change(function(e){
            deduction.attr('href',link + "&&payment=" + $(this).val());
        });

        deduction.click(function(e){
            e.preventDefault();
            window.open($(this).attr('href'),"_blank");
        });
        $('.sched-btn').click(function(e){
            e.preventDefault();
            window.open($(this).attr('href'),"_blank");
        });
    })
</script>