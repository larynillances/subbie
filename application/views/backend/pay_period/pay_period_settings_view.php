<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal" role="form"');
    ?>
    <div>
        <div class="row">
            <div class="col-sm-11">
                <label class="control-label col-sm-1" style="margin-left: -25px;">Year:</label>
                <div class="col-sm-2">
                    <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm select year-dp"')?>
                </div>
                <label class="control-label col-sm-1" style="margin-left: -25px;">Month:</label>
                <div class="col-sm-2">
                    <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm select month-dp"')?>
                </div>
                <label class="control-label col-sm-1" style="margin-left: -25px;">Week:</label>
                <div class="col-sm-1 week-display">
                    <?php echo form_dropdown('week',$week,$thisWeek,'class="form-control input-sm"')?>
                </div>
                <div class="col-sm-3">
                    <?php echo form_dropdown('project_type',$project_type,$thisProject,'class="form-control input-sm"')?>
                </div>
                <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
            </div>

        </div>
    </div>
    <?php
    echo form_close();
    ?><br/>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive table-colored-header">
                <thead>
                <tr>
                    <th rowspan="2" width="200">Employee Name</th>
                    <th rowspan="2">Frequency</th>
                    <th rowspan="2">PAYE<br/>Code</th>
                    <th rowspan="2">Wage<br/>Type</th>
                    <th rowspan="2">Rate<br/>Type</th>
                    <th colspan="2">Kiwi</th>
                    <th rowspan="2">ESCT</th>
                    <th rowspan="2">Hourly<br/>Rate</th>
                    <th rowspan="2">Status</th>
                </tr>
                <tr>
                    <th>Emp.</th>
                    <th>Empr.</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($staff_data) > 0){
                    foreach($staff_data as $sv){
                        ?>
                        <tr>
                            <td style="text-align: left;"><a href="<?php echo base_url('payPeriodSettings/'.$sv->id)?>"><?php echo $sv->name;?></a></td>
                            <td><?php echo $sv->frequency;?></td>
                            <td><?php echo $sv->tax_code;?></td>
                            <td><?php echo $sv->description;?></td>
                            <td><?php echo $sv->rate_name;?></td>
                            <td><?php echo $sv->kiwi;?></td>
                            <td><?php echo $sv->emp_kiwi;?></td>
                            <td><?php echo $sv->esct_rate;?></td>
                            <td><?php echo $sv->hourly_rate ? '$ '.number_format($sv->hourly_rate,2) : '&nbsp;';?></td>
                            <td style="background: <?php echo $sv->color;?>"><?php echo $sv->staff_status;?></td>
                        </tr>
                    <?php
                    }
                }
                else{
                    ?>
                    <tr>
                        <td colspan="11">No data was found.</td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
