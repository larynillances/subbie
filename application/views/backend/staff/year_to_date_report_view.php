<?php
echo form_open('','class="form-horizontal"');
?>
<div class="row">
    <div class="form-group">
        <label class="col-sm-1 control-label">Year:</label>
        <div class="col-sm-1">
            <?php echo form_dropdown('year',$year,$_year,'class="form-control input-sm" style="width:120%;"')?>
        </div>
        <div class="col-sm-2">
            <?php echo form_dropdown('project_type',$project_type,$_project,'class="form-control input-sm"')?>
        </div>
        <div class="col-sm-1">
            <input type="submit" name="submit" class="btn btn-sm btn-success" value="Go">
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<div class="row">
    <div class="col-sm-9">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">Name</th>
                <th colspan="2">Pay Earned</th>
                <th rowspan="2" style="vertical-align: middle;">Financial Year</th>
                <th rowspan="2" style="vertical-align: middle;">Option</th>
            </tr>
            <tr>
                <th>Gross</th>
                <th>Nett</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($staff) > 0){
                foreach($staff as $row){
                    $financial_year = date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($_year)))) .' to ';
                    $financial_year .= date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$_year))));
                    ?>
                    <tr>
                        <td style="text-align: left!important;"><?php echo $row->fname.' '.$row->lname;?></td>
                        <td><?php echo '$ '.number_format($row->earn_gross,2);?></td>
                        <td><?php echo '$ '.number_format($row->earn_nett,2);?></td>
                        <td><?php echo $row->financial_year ? $row->financial_year : $financial_year;?></td>
                        <td style="white-space: nowrap;width: 15%;">
                            <a href="<?php echo base_url('yearToDateReport/summary/'.$row->id)?>" class="tooltip-class" title="Show All Summary" data-placement="left">Summary</a>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>