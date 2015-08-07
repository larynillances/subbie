
<div class="row">
    <div class="col-lg-12">
        <?php
        echo form_open('','class="form-horizontal"');
        ?>
        <div class="form-group">
            <label class="col-sm-1 control-label">Status:</label>
            <div class="col-sm-1" style="margin-left: -20px;">
                <?php echo form_dropdown('staff_status',$staff_status,$status,'class="form-control input-sm" style="width:120%;"')?>
            </div>
            <div class="col-sm-1">
                <input type="submit" name="go" class="btn btn-success input-sm" style="padding: 5px 10px;" value="Go" >
            </div>
            <div class="col-sm-1">
                <span style="font-size: 18px;margin: 2px 0 0 -58px;">
                    <a href="#" class="add-staff-btn"><i class="glyphicon glyphicon-plus"></i></a>
                </span>
            </div>
        </div>
        <?php
        echo form_close();
        ?>
        <table class="table table-colored-header table-responsive">
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
                    ?>
                    <tr>
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
                        <td><?php echo $v->bank_account;?></td>
                        <td><?php echo $v->installment ? '$'.number_format($v->installment,2) : ''?></td>
                        <td><?php echo $v->balance ? '$'.number_format($v->balance,2) : ''?></td>
                        <td <?php echo $v->staff_status ? 'style="background:'.$v->color.'"' : ''?> ><?php echo $v->staff_status;?></td>
                        <td>
                            <?php
                            if($v->status_id == 3){
                                ?>
                                <a href="#" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id;?>" class="delete-staff-btn"><span class="glyphicon glyphicon-remove"></a>
                            <?php
                            }else if($v->status_id == 2){
                                ?>
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?current=1';?>" class="move-to-current-btn move-btn tooltip-class" data-value="Move to Current" data-placement="left" title="Move to Current">Cur.</a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?archive=1';?>" class="move-to-archive-btn move-btn tooltip-class"  data-value="Move to Archive" data-placement="left" title="Move to Archive">Arc.</a>
                            <?php
                            }else{
                                ?>
                                <a href="#" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                                <a href="<?php echo base_url().'manageStaff/delete/'.$v->id.'?current=1';?>" class="move-to-current-btn move-btn tooltip-class" data-value="Move to Current" data-placement="left" title="Move to Current">Cur.</a>&nbsp;
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
                    <td colspan="15" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(e){
        $('.add-staff-btn').click(function(e){
            var url = bu + 'manageStaff/add';
            $(this).modifiedModal({
                url: url,
                title: 'Add Staff'
            });
        });
        $('.edit-staff-btn').click(function(e){
            var url = bu + 'manageStaff/edit/' + this.id;
            $(this).modifiedModal({
                url: url,
                title: 'Edit Staff'
            });
        });
        $('.delete-staff-btn').click(function(e){
            e.preventDefault();
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Do you want to delete this Employee from the List of Current Staff Members?' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success yesBtn-confirm" data-dismiss="modal">Yes</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Delete Staff',
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
            var title_ = $(this).data('value');
            var msg = $(this).hasClass('move-to-archive-btn') ? 'Archive' : 'Current';
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                            'Do you want to move this Employee from the List of ' + msg + ' Staff Members?' +
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