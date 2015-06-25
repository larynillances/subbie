<div class="row">
    <div class="col-lg-4">
        <h4>Wage Type Table</h4>
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th>Description</th>
                <th>Frequency</th>
                <th>Type</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($wage_type)>0):
                foreach($wage_type as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->description;?></td>
                        <td><?php echo $v->frequency;?></td>
                        <td><?php echo $v->salary_type;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="3" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-2">
        <h4>Salary Type Table</h4>
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th>Frequency</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($salary_type)>0):
                foreach($salary_type as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->type;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-2">
        <h4>Frequency Table</h4>
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th>Frequency</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($salary_freq)>0):
                foreach($salary_freq as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->frequency;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <div class="col-md-8">
                <h4>Rate Table</h4>
            </div>
            <div class="col-md-4">
                <input type="button" class="btn btn-primary add-rate-btn" value="Add Rate">
            </div>
        </div>
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th>Description</th>
                <th>Rate Cost</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($rate)>0):
                foreach($rate as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->rate_name;?></td>
                        <td style="text-align: center"><?php echo '$'.number_format($v->rate_cost,2)?></td>
                        <td><a href="#" class="edit-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="3" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <div class="col-md-9">
                <h4>Staff Table</h4>
            </div>
            <div class="col-md-3">
                <input type="button" class="btn btn-primary add-staff-btn" value="Add Staff">
            </div>
        </div>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Rate</th>
                <th>Tax Number</th>
                <th>Tax Code</th>
                <th>IRD</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($employee)>0):
                foreach($employee as $v):
                    $print_option = '| <a href="#" id="'. $v->id .'" class="print-staff-btn">print</a>';
                    //$has_print = $v->has_wage ? $print_option : '';
                    $is_fired = $v->is_unemployed ? 'class="danger"' : '';
                    ?>
                    <tr <?php echo $is_fired;?>>
                        <td><?php echo $v->name;?></td>
                        <td><?php echo $v->description;?></td>
                        <td><?php echo $v->rate_name;?></td>
                        <td><?php echo $v->tax_number;?></td>
                        <td><?php echo $v->tax_code;?></td>
                        <td><?php echo $v->ird_num;?></td>
                        <td>
                            <a href="#" class="edit-staff-btn" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></a>&nbsp;
                            <a href="<?php echo base_url().'manageStaff/delete/'.$v->id;?>" class="delete-staff-btn"><span class="glyphicon glyphicon-remove"></a>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="5" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-3">
        <div class="col-md-12">
            <h4>Currency Table</h4>
        </div>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Name</th>
                <th>Currency</th>
                <th>FX Rate</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($employee)>0):
                foreach($employee as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->name;?></td>
                        <td style="text-align: center"><?php echo 'NZD - '.$v->currency_code;?></td>
                        <td><?php echo $v->rate;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="3" class="empty-table">No data has found.</td>
                </tr>
                <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-3">
        <div class="col-md-12">
            <h4>Loans Table</h4>
        </div>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Installment</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($loans)>0):
                foreach($loans as $v):
                    ?>
                    <tr>
                        <td><?php echo $v->fname.' '.$v->lname;?></td>
                        <td style="text-align: center"><?php echo '$ '.$v->balance;?></td>
                        <td><?php echo '$ '.$v->installment;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="3" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-7">
        <div class="col-md-12">
            <h4>Deduction Table</h4>
        </div>
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">Name</th>
                <th colspan="2">Flight</th>
                <th colspan="2">Visa</th>
                <th rowspan="2" style="vertical-align: middle;">Accom</th>
                <th rowspan="2" style="vertical-align: middle;">Trans</th>
                <th rowspan="2" style="vertical-align: middle;"></th>
            </tr>
            <tr>
                <th>Debt</th>
                <th>Deduction</th>
                <th>Debt</th>
                <th>Deduction</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($deductions)>0):
                foreach($deductions as $v):
                    $edit_link = '<a href="#" class="edit-deduction-btn" id="'.$v->id.'"><span class="glyphicon glyphicon-pencil"></a>';
                    $add_link = '<a href="#" class="add-deduction-btn" id="'.$v->employee.'"><span class="glyphicon glyphicon-plus"></a>';
                    $options = $v->flight_debt != '' || $v->visa_debt != '' || $v->accommodation != '' || $v->transport != '' ? $edit_link : $add_link;
                    ?>
                    <tr>
                        <td><?php echo $v->name;?></td>
                        <td><?php echo $v->flight_debt;?></td>
                        <td><?php echo $v->flight_deduct;?></td>
                        <td><?php echo $v->visa_debt;?></td>
                        <td><?php echo $v->visa_deduct;?></td>
                        <td><?php echo $v->accommodation;?></td>
                        <td><?php echo $v->transport;?></td>
                        <td><?php echo $options;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="8" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-5">
        <div class="col-md-12">
            <h4>Fixed Amount Table</h4>
        </div>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Name</th>
                <th>Acc Two</th>
                <th>NZ ACC</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($employee)>0):
                foreach($employee as $v):
                    $link_name = $v->nz_account != '' || $v->account_two != '' ? '<span class="glyphicon glyphicon-pencil">' : '<span class="glyphicon glyphicon-plus">';
                    ?>
                    <tr>
                        <td><?php echo $v->name;?></td>
                        <td><?php echo $v->account_two;?></td>
                        <td><?php echo $v->nz_account;?></td>
                        <td><a href="#" class="edit-fixed-btn" id="<?php echo $v->id;?>"><?php echo $link_name;?></a></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="4" class="empty-table">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade modal-load">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="content-loader"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    $(function(e){
        var content = $('.content-loader');
        var modal_title = $('.modal-title');
        $('.add-rate-btn').click(function(e){
            modal_title.html('Add Rate');
            var url = bu + 'rateManage/add';
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.edit-btn').click(function(e){

            modal_title.html('Edit Rate');
            var url = bu + 'rateManage/edit/' + this.id;
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.add-staff-btn').click(function(e){
            modal_title.html('Add Staff');
            var url = bu + 'manageStaff/add';
            content.load(url);
            $('.modal-load').modal();
        });
        $('.edit-staff-btn').click(function(e){
            modal_title.html('Edit Staff');
            var url = bu + 'manageStaff/edit/' + this.id;
            content.load(url);
            $('.modal-load').modal();
        });
        $('.edit-fixed-btn').click(function(e){
            modal_title.html('Edit Fixed Amount');
            var url = bu + 'manageStaff/fixed/' + this.id;
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.add-deduction-btn').click(function(e){
            modal_title.html('Add Deductions');
            var url = bu + 'manageDeduction/add/' + this.id;
            content.load(url);
            $('.modal-load').modal();
        });
        $('.edit-deduction-btn').click(function(e){
            modal_title.html('Edit Deductions');
            var url = bu + 'manageDeduction/edit/' + this.id;
            content.load(url);
            $('.modal-load').modal();
        });
        $('.print-staff-btn').click(function(e){
            window.open( bu + "printPaySlip/" + this.id,"_blank");
            //location.reload();
        });
    });
</script>