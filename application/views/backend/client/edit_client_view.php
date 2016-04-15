<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <?php
    $take_off_merchant_cc = json_decode(@$client_email->take_off_merchant_cc);
    if(count($client_data)>0):
        foreach($client_data as $v):
        ?>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="client_name">Client Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="client_name" id="client_name" class="form-control input-sm required" value="<?php echo $v->client_name?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="client_code">Code</label>
                        <div class="col-sm-8">
                            <input type="text" name="client_code" id="client_code" class="form-control input-sm required" value="<?php echo $v->client_code?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="contact_name">Contact Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="contact_name" id="contact_name" class="form-control input-sm required" value="<?php echo $v->contact_name?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="phone">Phone No.</label>
                        <div class="col-sm-8">
                            <input type="text" name="phone" id="phone" class="form-control input-sm" value="<?php echo $v->phone?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="mobile">Mobile No.</label>
                        <div class="col-sm-8">
                            <input type="text" name="mobile" id="mobile" class="form-control input-sm" value="<?php echo $v->mobile?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="address">Address</label>
                        <div class="col-sm-8">
                            <textarea name="address" id="address" class="form-control input-sm required"><?php echo $v->address?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="email">Email</label>
                        <div class="col-sm-8">
                            <input type="text" name="email" id="email" class="form-control input-sm required" value="<?php echo $v->email?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <fieldset>
                        <legend><h6 style="text-transform: uppercase;font-weight: bold;">Email Return</h6></legend>
                        <table class="takeOffReturnTable">
                            <tr>
                                <td rowspan="12" style="width: 30px;"></td>
                                <td>
                                    <input type="checkbox" name="via_subbie" id="via_eotl" class="takeOffReturnType" value="1"
                                        <?php echo @$client_email->via_subbie ? 'CHECKED' : '';?> />
                                </td>
                                <td><label for="via_eotl">Subbie</label></td>

                                <td style="width: 10px;">
                                    <input type="checkbox" name="via_subbie_only" id="via_eotl_only" class="eotlReturnTypeOnly" value="1"
                                        <?php echo @$client_email->via_subbie ? (@$client_email->via_subbie_only ? 'CHECKED' : '') : 'disabled';?>/>
                                </td>
                                <td><label for="via_eotl_only">Only?</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="via_email" id="via_email" class="takeOffReturnType" value="2"
                                        <?php echo @$client_email->via_subbie_only ? 'disabled' : (@$client_email->via_email ? 'CHECKED' : '')?>/>
                                </td>
                                <td style="white-space: nowrap;">
                                    <label for="via_email">via Email?</label>
                                </td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_name" autocomplete="off" class="takeOffEmailInfo form-control input-sm" placeholder="Client Staff Name"
                                        <?php echo @$client_email->via_email ? 'value="' . @$client_email->take_off_merchant_name . '"' : 'disabled';?>/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_email" id="take_off_merchant_email" autocomplete="off" class="takeOffEmailInfo form-control input-sm merchantReturnEmail" placeholder="Client Staff Email Address"
                                        <?php echo @$client_email->via_email ? 'value="' . @$client_email->take_off_merchant_email . '"' : 'disabled';?>/>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_cc[cc_one_name]" autocomplete="off" class="takeOffEmailInfo form-control input-sm merchantCCReturnEmail" placeholder="Client CC1 Name"
                                        <?php echo @$client_email->via_email ? 'value="' . $take_off_merchant_cc->cc_one_name . '"' : 'disabled';?>/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_cc[cc_one_email]" id="cc_one_email" autocomplete="off" class="takeOffEmailInfo form-control input-sm merchantCCReturnEmail" placeholder="Client CC1 Email Address"
                                        <?php echo @$client_email->via_email ? 'value="' . $take_off_merchant_cc->cc_one_email . '"' : 'disabled';?>/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_cc[cc_two_name]" autocomplete="off" class="takeOffEmailInfo form-control input-sm merchantCCReturnEmail" placeholder="Client CC2 Name"
                                        <?php echo @$client_email->via_email ? 'value="' . $take_off_merchant_cc->cc_two_name . '"' : 'disabled';?>/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_merchant_cc[cc_two_email]" id="cc_two_email" autocomplete="off" class="takeOffEmailInfo form-control input-sm merchantCCReturnEmail" placeholder="Client CC2 Email Address"
                                        <?php echo @$client_email->via_email ? 'value="' . $take_off_merchant_cc->cc_two_email . '"' : 'disabled';?>/>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="input-data">
                                    <input type="text" name="take_off_franchise_email" id="take_off_franchise_email" autocomplete="off" class="takeOffEmailInfo form-control input-sm franchiseReturnEmail" placeholder="Copy to Franchise Administrator"
                                        <?php echo @$client_email->via_email ? 'value="' . @$client_email->take_off_franchise_email . '"' : 'disabled';?>/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="via_ftp" id="via_ftp" class="takeOffReturnType" value="3"
                                        <?php echo @$client_email->via_ftp ? 'CHECKED' : '';?>/>
                                </td>
                                <td style="white-space: nowrap;">
                                    <label for="via_ftp">via FTP?</label>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        <?php
        endforeach;
    endif;
    ?>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary submit-btn" name="submit">Update</button>
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
    <?php
echo form_close();
?>
<style>
    .takeOffReturnTable tr .input-data{
        width: 300px!important;
        padding: 3px;
    }
</style>
<script>
    $(function(e){
        var addBtn = $('.addBtn');

        $('.email').checkingMail({
            submit: addBtn,
            fieldName: 'email',
            postName: 'email',
            checkOption: {
                url: bu + 'checkBranchEmail'
            }
        });

        //region Check Mails
        $('#take_off_merchant_email').checkingMail({
            submit: addBtn,
            fieldName: 'take_off_merchant_email'
        });
        $('#cc_one_email').checkingMail({
            submit: addBtn,
            fieldName: 'cc_one_email'
        });
        $('#cc_two_email').checkingMail({
            submit: addBtn,
            fieldName: 'cc_two_email'
        });
        $('#email').checkingMail({
            submit: addBtn,
            fieldName: 'email'
        });
        $('#take_off_franchise_email').checkingMail({
            submit: addBtn,
            fieldName: 'take_off_franchise_email'
        });
        //endregion

        var takeOffReturnType = $('.takeOffReturnType');
        var takeOffEmailInfo = $('.takeOffEmailInfo');
        var eotlReturnTypeOnly = $('.eotlReturnTypeOnly');
        var is_export_active = $('.is_export_active');
        var dateConfigure = $('.dateConfigure');
        var dateConfigureErase = $('.fa-eraser');
        takeOffReturnType.live('click',function(e){
            var thisType = parseInt($(this).val());
            var thisElement;

            switch(thisType){
                case 1:
                    thisElement = eotlReturnTypeOnly;
                    break;
                case 2:
                    thisElement = takeOffEmailInfo;

                    break;
                default:
                    break;
            }

            if(thisElement){
                if($(this).is(':checked')){
                    thisElement
                        .addClass('required')
                        .removeAttr('disabled');

                    $('.merchantCCReturnEmail').removeClass('required');
                }
                else{
                    thisElement
                        .css({
                            border: '1px solid #CCC',
                            padding: '7px'
                        })
                        .val('')
                        .removeClass('hasError required')
                        .attr('disabled', 'disabled');
                }
            }

            isExportActive();
        });

        function isExportActive(){
            if($('.takeOffReturnType:checked').length > 0){
                is_export_active.removeAttr('disabled');
            }
            else{
                is_export_active
                    .removeAttr('checked')
                    .attr('disabled', 'disabled');
            }
        }

        eotlReturnTypeOnly.click(function(e){
            takeOffReturnType.removeAttr('disabled');
            if($(this).is(':checked')){
                takeOffReturnType
                    .not('[value="1"]')
                    .prop("checked", false)
                    .attr('disabled', 'disabled');

                takeOffEmailInfo
                    .val('')
                    .removeClass('required')
                    .attr('disabled', 'disabled');
            }
            isDirect();
        });

        var im_type = $('.im_type');
        var im_num = $('.im_num');
        imTypeChange();
        im_type.change(function(e){
            imTypeChange();
        });

        function imTypeChange(){
            var thisVal = im_type.val();
            if(thisVal == "-"){
                im_num
                    .val('')
                    .removeClass('required')
                    .attr('disabled', 'disabled');
            }
            else{
                im_num
                    .addClass('required')
                    .removeAttr('disabled');
            }
        }

        is_export_active.click(function(e){
            if($(this).is(':checked')){
                dateConfigure.datepicker( "show" );
            }
        });
        dateConfigureErase.click(function(e){
            dateConfigure.val("");
            $('.dateTxt').html("dd/mm/yyyy");
        });

        dateConfigure.datepicker({
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: bu + "images/calendar.gif",
            buttonImageOnly: true,
            onSelect: function() {
                var d = dateConfigure.datepicker('getDate');
                var str = $.strPad(d.getDate(), 2) + '/' + $.strPad(d.getMonth() + 1, 2) + '/' + d.getFullYear();
                $('.dateTxt').html(String(str));
            },
            onClose: function(dateText){
                if(!dateText){
                    is_export_active.removeAttr('checked');
                }
            }
        });

        var is_direct = $('input[name="is_direct"]');
        var request_url_id = $('.request_url_id');
        is_direct.click(function(e){
            isDirect();
        });

        function isDirect(){
            if(is_direct.is(':checked')){
                request_url_id.removeAttr('disabled');
            }
            else{
                request_url_id.attr('disabled', 'disabled');
            }
        }

        $('#bill_to_email').checkingMail({
            submit: addBtn,
            fieldName: 'bill_to_email'
        });
    });
</script>