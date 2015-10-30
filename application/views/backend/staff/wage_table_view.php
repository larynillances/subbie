
<div class="row">
    <div class="col-lg-12">
        <?php
        echo form_open('','class="form-horizontal"');
        ?>
        <div class="form-group">
            <label class="col-sm-1 control-label">Status:</label>
            <div class="col-sm-1" style="margin-left: -20px;">
                <?php echo form_dropdown('staff_status',$staff_status,$status,'class="form-control input-sm" style="width:110%;"')?>
            </div>
            <div class="col-sm-2" style="margin-left: -10px;">
                <?php echo form_dropdown('project_type',$project_type,$project,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-1">
                <input type="submit" name="go" class="btn btn-success btn-sm" style="padding: 5px 10px;" value="Go" >
            </div>
            <div class="col-sm-1">
                <span style="font-size: 18px;margin: 2px 0 0 -58px;">
                    <a href="<?php echo base_url('manageStaff/add')?>" class="add-staff-btn"><i class="glyphicon glyphicon-plus"></i></a>
                </span>
            </div>
        </div>
        <?php
        echo form_close();
        ?>
        <table class="table table-colored-header table-responsive table-hover">
            <thead>
            <tr>
                <th rowspan="2">Name</th>
                <th rowspan="2">IRD</th>
                <th rowspan="2">Wage<br/>Type</th>
                <th rowspan="2">Rate<br/>Type</th>
                <th rowspan="2">Frequency</th>
                <th rowspan="2">PAYE<br/>Code</th>
                <th colspan="2">Kiwi</th>
                <th rowspan="2">ESCT</th>
                <th rowspan="2">CPR<br/>Start Date</th>
                <th rowspan="2">&nbsp;</th>
                <th rowspan="2">Employment<br/>Periods</th>
                <th rowspan="2">Bank Account</th>
                <th colspan="2">Loans</th>
                <th rowspan="2">Status</th>
                <th rowspan="2"></th>
            </tr>
            <tr>
                <th>Emp.</th>
                <th>Empr.</th>
                <th>Installment</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($employee)>0):
                foreach($employee as $v):
                    $print_option = '| <a href="#" id="'. $v->id .'" class="print-staff-btn">print</a>';
                    $has_last_pay = $v->has_final_pay && $v->status_id != 3 ? 'class="has-last-pay"' : '';
                    $v->date_employed = $v->date_employed != '0000-00-00' ? $v->date_employed : '';
                    $date_employed = $v->date_employed ? date('d/m/Y',strtotime($v->date_employed)) : '&nbsp;';
                    $employment_period = '';
                    $employment_ = @$employment_data[$v->id];
                    if(count($employment_) > 0){
                        foreach($employment_ as $val){
                            $unemployed_date = $val->date_last_pay  != '0000-00-00' && $val->date_last_pay  ? date('d/m/Y',strtotime($val->date_last_pay)) : 'Present';
                            $v->date_employed = $val->date_employed != '0000-00-00' && $val->date_employed ? $val->date_employed : '';
                            $date_employed = $v->date_employed ? date('d/m/Y',strtotime($val->date_employed)) : '&nbsp;';
                            $employment_period .= $date_employed." to ".$unemployed_date."\n";
                        }
                    }
                    ?>
                    <tr <?php echo $has_last_pay;?> >
                        <td style="text-align: left!important;"><?php echo $v->name;?></td>
                        <td><?php echo $v->ird_num;?></td>
                        <td><span class="tooltip-class" title="<?php echo $v->frequency.' '.$v->salary_type;?>"><?php echo $v->description;?></span></td>
                        <td><?php echo $v->rate_name_;?></td>
                        <td><?php echo $v->frequency;?></td>
                        <td><?php echo $v->tax_code;?></td>
                        <td><?php echo $v->kiwi ? $v->kiwi.'%' : '';?></td>
                        <td><?php echo $v->employeer_kiwi ? $v->employeer_kiwi.'%' : '';?></td>
                        <td><?php echo $v->esct_rate ? $v->esct_rate : '';?></td>
                        <td><?php echo $v->start_use ? date('d/m/Y',strtotime($v->start_use)) : '';?></td>
                        <td><a href="#" class="tooltip-class pay-rate-list-btn" id="<?php echo $v->id;?>" data-value="<?php echo $v->name.' Pay Rate List';?>" title="Pay Rate List"><i class="glyphicon glyphicon-list"></i></a></td>
                        <td><span class="tooltip-class" title="<?php echo $employment_period;?>"><?php echo $date_employed;?></span></td>
                        <td><?php echo $v->bank_account;?></td>
                        <td><?php echo $v->installment ? '$'.number_format($v->installment,2) : ''?></td>
                        <td><?php echo $v->balance ? '$'.number_format($v->balance,2) : ''?></td>
                        <td <?php echo $v->staff_status ? 'style="background:'.$v->color.'"' : ''?> ><?php echo $v->staff_status;?></td>
                        <td style="text-align: left;">
                            <a href="<?php echo base_url().'kiwiPayLetter/'.$v->id?>" target="_blank" class="tooltip-class" title="Print Kiwisaver Letter">Kiwi</a>&nbsp;
                            <?php
                            if($v->status_id == 3){
                                ?>
                                <a href="<?php echo base_url('manageStaff/edit/'.$v->id)?>" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id;?>" data-string="<?php echo $v->name;?>" class="delete-staff-btn"><span class="glyphicon glyphicon-arrow-right"></a>
                            <?php
                            }else if($v->status_id == 2){
                                ?>
                                <a href="<?php echo base_url('manageStaff/edit/'.$v->id)?>" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?current=1';?>" class="move-to-current-btn move-btn tooltip-class" data-value="Current" data-current="Active" data-string="<?php echo $v->name;?>" data-placement="left" title="Move to Current">Cur.</a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?archive=1';?>" class="move-to-archive-btn move-btn tooltip-class"  data-value="Archive" data-current="Active" data-string="<?php echo $v->name;?>" data-placement="left" title="Move to Archive">Arc.</a>
                            <?php
                            }else{
                                ?>
                                <a href="<?php echo base_url('manageStaff/edit/'.$v->id)?>" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?current=1';?>" class="move-to-current-btn move-btn tooltip-class" data-value="Move to Current" data-current="Archive" data-string="<?php echo $v->name;?>" data-placement="left" title="Move to Current">Cur.</a>&nbsp;
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="16" class="empty-table">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<style>
    .table .has-last-pay td{
        background: #e5e095;
    }
</style>
<script>
    $(function(e){
        $('.pay-rate-list-btn').click(function(e){
            var url = bu + 'payRatePeriods?id=' + this.id;
            $(this).modifiedModal({
                url: url,
                title: $(this).data('value')
            });
        });
        $('.delete-staff-btn').click(function(e){
            e.preventDefault();
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Do you want this Employee an Active Record, not a Current Employee Record?' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success btn-sm yesBtn-confirm" data-dismiss="modal">Yes</button>' +
                    '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Move to Active',
                type: 'small'
            });
            var link = this.href;
            $('.yesBtn-confirm').click(function(){
                $.post(link,{submit:1},
                    function(data){
                        console.log(data);
                        location.reload();
                    }
                );
            });
        });

        $('.move-btn').click(function(e){
            e.preventDefault();
            var title_ = 'Move to ' + $(this).data('value');
            var msg_str = 'Do you want to move <strong>' + $(this).data('string') + '</strong> from this list of ';
                msg_str += $(this).data('current') + ' Employee Records, to the ' + $(this).data('value') + ' Staff Members List?';
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                            msg_str +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success move-confirm" data-dismiss="modal">Yes</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: title_,
                type: 'small'
            });
            var link = this.href;
            $('.move-confirm').click(function(){
                $.post(link,{submit:1},
                    function(data){
                        console.log(data);
                        location.reload();
                    }
                );
            });
        });

        $('.print-staff-btn').click(function(e){
            window.open( bu + "printPaySlip/" + this.id,"_blank");
            //location.reload();
        });
    });
</script>