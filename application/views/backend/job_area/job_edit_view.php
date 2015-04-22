<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($job_list) > 0):
        foreach($job_list as $v):
            $address = json_decode($v->address);
            $v->start_date = $v->start_date == '0000-00-00' ? '' : date('d-m-Y',strtotime($v->start_date));
            $v->tender_date = $v->tender_date == '0000-00-00' ? '' : date('d-m-Y',strtotime($v->tender_date));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Client Name</label>
                            <div class="col-sm-8">
                                <?php echo form_dropdown('client_id',$client,$v->client_id,'class="form-control required input-sm contractor"')?>
                            </div>
                        </div>
                    </div>
                </div>
                <fieldset>
                    <legend><h4>Job Details</h4></legend>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Job Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="job_name" class="form-control input-sm required" placeholder="Job Name" value="<?php echo $v->job_name;?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Owner's Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="owner_name" class="form-control input-sm required" placeholder="Owner's Name" value="<?php echo $v->owner_name;?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="contact_name" class="form-control input-sm" placeholder="Contact Name" value="<?php echo $v->contact_name;?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Phone Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="phone" class="form-control input-sm number change-class" placeholder="Phone Number" value="<?php echo $v->phone;?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Email Address</label>
                                <div class="col-sm-8">
                                    <input type="email" name="email" class="form-control input-sm change-class"  placeholder="Email Address" value="<?php echo $v->email;?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Street</label>
                                <div class="col-sm-2">
                                    <input type="text" name="address[]" class="form-control input-sm number"  placeholder="#" value="<?php echo $address->number?>">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="address[]" class="form-control input-sm"  placeholder="Name" value="<?php echo $address->name?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Suburb</label>
                                <div class="col-sm-8">
                                    <input type="text" name="address[]" class="form-control input-sm"  placeholder="Suburb" value="<?php echo $address->suburb?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">City</label>
                                <div class="col-sm-8">
                                    <input type="text" name="address[]" class="form-control input-sm" placeholder="City" value="<?php echo $address->city?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Job Type</label>
                                <div class="col-sm-8">
                                    <?php echo form_dropdown('job_type_id',$job_type,$v->job_type_id,'class="form-control required input-sm job_type"')?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">&nbsp;</label>
                                <div class="col-sm-4">
                                    <?php echo form_dropdown('option_1',$option_1,$v->option_1,'class="form-control required input-sm client"')?>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo form_dropdown('option_2',$option_2,$v->option_2,'class="form-control required input-sm client"')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend></legend>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Start Date</label>
                                <div class="col-sm-8">
                                    <div class='input-group date datepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                                        <input type='text' name="start_date" class="form-control change-class input-sm" placeholder="Start Date" value="<?php echo $v->start_date?>"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Tender Date</label>
                                <div class="col-sm-8">
                                    <div class='input-group date datepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                                        <input type='text' name="tender_date" class="form-control change-class input-sm" placeholder="Tender Date" value="<?php echo $v->tender_date?>"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Team</label>
                                <?php
                                $this_team = json_decode($v->team);
                                if(count($team) > 0):
                                    foreach($team as $tk=>$tv):
                                        $selected = array();
                                        if(count($this_team) >0):
                                            foreach($this_team as $team_id):
                                                if($team_id == $tk):
                                                    $selected[$tk] = 'checked';
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                            <div class="col-lg-1">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="team[]" value="<?php echo $tk;?>" <?php echo @$selected[$tk]?>> <?php echo $tv;?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Area</label>
                                <div class="col-sm-8">
                                    <input type="text" name="meter" class="form-control input-sm change-class number"  placeholder="Area" value="<?php echo $v->meter;?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">In House Notes</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control input-sm" name="notes" style="height: 100px;"><?php echo $v->notes;?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary submit-btn" name="submit">Save Changes</button>
            </div>
        <?php
    endforeach;
endif;
echo form_close();
?>
<script>
    $(function(e){
        var jobType = $('.job_type');
        var not_required = $('.change-class');
        $('.datepicker').datetimepicker({
            pickTime: false
        });
        var checkJobType = function(){
            if(jobType.val() == 5){
                not_required.removeClass('required');
            }
        };
        checkJobType();
        jobType.change(function(e){
            not_required.addClass('required');
            not_required.removeAttr('style');
            if($(this).val() == 5){
                not_required.removeClass('required');
                not_required.removeAttr('style');
            }
        });
    })
</script>
