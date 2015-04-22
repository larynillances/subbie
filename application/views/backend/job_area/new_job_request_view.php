<?php
echo form_open_multipart('','role="form"');
?>  <div class="form-group row">
        <div class="col-lg-2">
            <label class="form-inline"><strong>For quote purpose?</strong></label>
        </div>
        <div class="col-lg-1">
            <label class="radio-inline">
                <input type="radio" name="quote_purpose" class="yes-option quote_purpose" value="1"> Yes
            </label>
        </div>
        <div class="col-lg-2">
            <label class="radio-inline">
                <input type="radio" name="quote_purpose" class="no-option quote_purpose" value="0" checked> No
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-2">
            <label>Main Contractor</label>
            <?php echo form_dropdown('client_id',$client,'','class="form-control input-sm contractor"')?>
        </div>
        <div class="col-lg-3">
            <label>If not in dropdown</label>
            <input type="text" class="form-control contractor-form input-sm" name="contractor" placeholder="Main Contractor">
        </div>
        <div class="col-lg-3">
            <label>&nbsp;</label>
            <textarea class="form-control contractor-form input-sm" name="contractor_address" placeholder="Number/Name/Suburb/City"></textarea>
        </div>
        <div class="col-lg-2">
            <label>&nbsp;</label>
            <input type="text" class="form-control number input-sm" name="contractor_tel" placeholder="Tel. No.">
        </div>
        <div class="col-lg-2">
            <label>&nbsp;</label>
            <input type="email" class="form-control input-sm" name="contractor_email" placeholder="Email">
        </div>
    </div>
    <fieldset>
        <legend>Job Details</legend>
        <div class="form-group row">
            <div class="col-lg-3">
                <label>Job Name</label>
                <input type="text" name="job_name" class="form-control input-sm required" placeholder="Job Name">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-3">
                <label>Owner's Name</label>
                <input type="text" name="owner_name" class="form-control input-sm required" placeholder="Owner's Name">
            </div>
            <div class="col-lg-3">
                <label>Contact Name</label>
                <input type="text" name="contact_name" class="form-control input-sm" placeholder="Contact Name">
            </div>
            <div class="col-lg-2">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control number input-sm" placeholder="Phone Number">
            </div>
            <div class="col-lg-4">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control input-sm"  placeholder="Email Address">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-1">
                <label>Street</label>
                <input type="text" name="address[]" class="form-control number input-sm"  placeholder="#">
            </div>
            <div class="col-lg-3">
                <label>&nbsp;</label>
                <input type="text" name="address[]" class="form-control input-sm"  placeholder="Name">
            </div>
            <div class="col-lg-4">
                <label>Suburb</label>
                <input type="text" name="address[]" class="form-control input-sm"  placeholder="Suburb">
            </div>
            <div class="col-lg-4">
                <label>City</label>
                <input type="text" name="address[]" class="form-control input-sm" placeholder="City">
            </div>
        </div>
        <fieldset>
        <div class="form-group row">
            <div class="col-lg-12">
                <label>Job Type</label>
            </div>
        </div>
        <div class="form-group row">
            <?php
            $ref = 0;
            if(count($job_type) > 0):
                foreach($job_type as $v):
                    $selected = $ref == 0 ? 'checked' : '';
                    ?>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" name="job_type_id" class="job_type" value="<?php echo $v->id;?>" <?php echo $selected;?>> <?php echo $v->job_type;?>
                        </label>
                    </div>
                    <?php
                    $ref++;
                endforeach;
            endif;
            ?>
        </div>
        <div class="form-group row">
            <?php
            $ref = 0;
            if(count($option) > 0):
                foreach($option as $v):
                    $selected = $ref == 0 || $v->type == 'Inside'? 'checked' : '';
                    ?>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" name="option_<?php echo $v->order_type?>" class="for_eq" value="<?php echo $v->id;?>" <?php echo $selected;?>> <?php echo $v->type;?>
                        </label>
                    </div>
                    <?php
                    $ref++;
                endforeach;
            endif;
            ?>

        </div>
        <div class="form-group row">
            <div class="col-lg-2">
                <label class="form-inline">Scope of work attach?</label>
            </div>
            <div class="col-lg-3">
                <label class="radio-inline">
                    <input type="file" name="scope"  class="required input-sm file-input change-class" value="dasdfa">
                </label>
            </div>
            <div class="col-lg-1">
                <label class="radio-inline">
                    <input type="radio" name="scope_attach" class="yes-option option-select" value="1"> Yes
                </label>
            </div>
            <div class="col-lg-1">
                <label class="radio-inline">
                    <input type="radio" name="scope_attach" class="no-option option-select" value="0" checked> No
                </label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-2">
                <label class="form-inline">Color Scheme attach?</label>
            </div>
            <div class="col-lg-3">
                <label class="radio-inline">
                    <input type="file" name="color_scheme" class="required input-sm file-input change-class" value="dasdfa">
                </label>
            </div>
            <div class="col-lg-1">
                <label class="radio-inline">
                    <input type="radio" name="scheme_attach" class="yes-option option-select" value="1"> Yes
                </label>
            </div>
            <div class="col-lg-1">
                <label class="radio-inline">
                    <input type="radio" name="scheme_attach" class="no-option option-select" value="0" checked> No
                </label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-3">
                <label>Start Date</label>
                <div class='input-group date datepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                    <input type='text' name="start_date" class="form-control change-class input-sm required" placeholder="Start Date"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-lg-3">
                <label>Tender Date</label>
                <div class='input-group date datepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                    <input type='text' name="tender_date" class="form-control input-sm" placeholder="Tender Date"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-lg-6"></div>
        </div>
    </fieldset>
    <div class="row">
        <div class="col-lg-12 text-right">
            <input type="submit" class="btn btn-primary" name="submit" value="Submit">
            <input type="reset" class="btn btn-success" value="Reset">
        </div>
    </div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var file_input = $('.file-input');
        var check_file_input = function(){
            $('.file-input').each(function(e){
                var no = $(this).parent().parent().parent().find('.no-option');
                if(!$(this).val()){
                    no.attr('checked','checked');
                    $(this).removeClass('required');
                    $(this).removeAttr('style','');
                }
            })
        };
        check_file_input();

        file_input.change(function(e){
            var yes = $(this).parent().parent().parent().find('.yes-option');
            var no = $(this).parent().parent().parent().find('.no-option');
            if(!$(this).val()){
                yes.prop('checked',true);
                no.prop('checked', true);
            }else{
                yes.prop('checked', true);
                no.prop('checked',false);
            }
        });
        $('.option-select').change(function(e){
           var file_input = $(this).parent().parent().parent().find('.file-input');
            file_input.addClass('required');
           if($(this).val() == 0){
               file_input.removeClass('required');
               file_input.removeAttr('style','');
           }
        });
        //$('.number').numberOnly();
        var contractor = $('.contractor');
        var contractor_form = $('.contractor-form');
        var dropdownCheck = function(){
            if(contractor.val() == ''){
                contractor_form.each(function(e){
                    $(this).addClass('required');
                   //console.log($(this).attr('name'));
                });
                //console.log('empty');
            }
        };
        dropdownCheck();
        contractor.change(function(e){
            contractor_form.removeClass('required');
            contractor_form.removeAttr('style');
            if($(this).val() == ''){
                contractor_form.each(function(e){
                    $(this).addClass('required');
                    //console.log($(this).attr('name'));
                });
            }
        });
        $('.quote_purpose').change(function(e){
            var not_required = $('.change-class');
            not_required.addClass('required');
            if($(this).val() == 1){
                not_required.removeClass('required');
                not_required.removeAttr('style');
                contractor.removeAttr('style');
            }
        });

        $('input:submit[name="submit"]').click(function(e){
            var hasEmpty = false;
            var required = $('.required');
            required.each(function(e){
                if(!$(this).val()){
                    hasEmpty = true;
                    $(this).css({
                        border:'1px solid #a94442'
                    });
                }
            });
            if(hasEmpty){
                e.preventDefault();
            }
        });
    });
</script>