<script src="<?php echo base_url();?>plugins/js/number.js"></script>
<script src="<?php echo base_url() . "plugins/js/email.validation.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "plugins/css/email.validation.css"; ?>" />
<script>
    var executeGenerate, isSend = 1;
    var comment = "";
    $(function(e){
        var thisId = <?php echo $this->uri->segment(3)?>;
        var client_id = <?php echo $this->uri->segment(2)?>;
        var $isArchived = 0;
        var exportBtn = $('.exportBtn');
        var exportOptions = {};

        exportBtn.click(function(e){
            $(this).newForm.addLoadingForm();
            closeLoading();
        });


        executeGenerate = function(c){
            comment = c;
        };

        //region Take Off Return Setting
        var changedTakeOffSetting = $('.changedTakeOffSetting');
        var takeOffReturnArea = $('.takeOffReturnArea');
        var takeOffReturnType = $('.takeOffReturnType');
        var takeOffEmailInfo = $('.takeOffEmailInfo');
        var eotlReturnTypeOnly = $('.eotlReturnTypeOnly');
        var testDisabled = $('.testDisabled');

        changedTakeOffSetting.click(function(e){
            takeOffReturnArea.css({
                display: 'none'
            });

            if($(this).is(':checked')){
                takeOffReturnArea.css({
                    display: 'inline'
                });
            }
        });

        takeOffReturnType.click(function(e){
            var thisType = parseInt($(this).val());
            var thisElement;

            switch (thisType){
                case 1:
                    thisElement = eotlReturnTypeOnly;

                    break;
                case 2:
                    thisElement = takeOffEmailInfo;

                    break;
                default :
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
                        .removeClass('hasError required')
                        .attr('disabled', 'disabled');
                }
            }
        });

        eotlReturnTypeOnly.click(function(e){
            takeOffReturnType.removeAttr('disabled');
            if($(this).is(':checked')){
                takeOffReturnType
                    .not('[value="1"]')
                    .prop("checked", false)
                    .attr('disabled', 'disabled');

                takeOffEmailInfo
                    .removeClass('required')
                    .attr('disabled', 'disabled');
            }
        });

        //region Check Mails
        $('#take_off_merchant_email').checkingMail({
            submit: exportBtn,
            fieldName: 'take_off_merchant_email'
        });
        $('#cc_one_email').checkingMail({
            submit: exportBtn,
            fieldName: 'cc_one_email'
        });
        $('#cc_two_email').checkingMail({
            submit: exportBtn,
            fieldName: 'cc_two_email'
        });
        $('#take_off_franchise_email').checkingMail({
            submit: exportBtn,
            fieldName: 'take_off_franchise_email'
        });
        //endregion

        function closeLoading(){
            $.post(
                bu + 'csvExportTakeOffRecord/' + thisId,
                {},
                function(e){
                    console.log(e);
                    //parent.exportEnableCounter(59);
                }
            );
            var optionData = {};
            var via_subbie_only = $('.via_subbie_only').is(':checked');
            var via_email = via_subbie_only ? 0 : $('.via_email').is(':checked');
            var via_ftp = via_subbie_only ? 0 : $('.via_ftp').is(':checked');
            var is_direct = $('.is_direct').is(':checked');

            optionData =  {
                via_subbie: $('.via_subbie').is(':checked') ? 1 : 0,
                via_subbie_only: via_subbie_only ? 1 : 0,
                is_direct: is_direct ? 1 : 0,
                via_email: via_email ? 1 : 0,
                take_off_merchant_name: via_email ? $('.take_off_merchant_name').val() : '',
                take_off_merchant_email: via_email ? $('.take_off_merchant_email').val() : '',
                cc_one_name: via_email ? $('#cc_one_name').val() : '',
                cc_one_email: via_email ? $('#cc_one_email').val() : '',
                cc_two_name: via_email ? $('#cc_two_name').val() : '',
                cc_two_email: via_email ? $('#cc_two_email').val() : '',
                take_off_franchise_email: via_email ? $('.take_off_franchise_email').val() : '',
                via_ftp: via_ftp ? 1 : 0,
                is_test: 0
            };

            optionData.comment = comment;
            optionData.exportOptions = exportOptions;
            $.post(
                bu + 'csvExportSendTakeOff/' + client_id + '/' + thisId,
                optionData,
                function(e){
                    if(e){
                        $(this).newForm.removeLoadingForm();
                        $('.modal').modal('hide');
                    }
                }
            );
        }
    });
</script>
<style>
    .takeOffReturnTable tr .input-data{
        width: 300px!important;
        padding: 3px;
    }
</style>
<div class="modal-body">
    <table id="printOptionMenu">
        <tr>
            <td colspan="2">
                <?php
                $take_off_merchant_cc = json_decode(@$client->take_off_merchant_cc);
                if(in_array($account_type, array(1, 2))){
                    ?>
                    <fieldset>
                        <legend>
                            <?php
                            echo '<h4 style="font-weight: bold">' . @$client->client_name . '</h4>';
                            ?>
                        </legend>
                        <div class="exportArea">
                            <table class="takeOffReturnTable">
                                <tr>
                                    <td colspan="6" style="white-space: nowrap;text-align: left;padding-bottom:5px; ">
                                        <strong>Email Return Setting</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="via_subbie" class="via_subbie takeOffReturnType testDisabled" value="1" <?php
                                        echo @$client->via_subbie ? 'CHECKED' : '';
                                        ?> />
                                    </td>
                                    <td>
                                        Subbie
                                    </td>

                                    <td style="width: 10px;">
                                        <input type="checkbox" name="via_subbie_only" class="via_subbie_only eotlReturnTypeOnly testDisabled" value="1" <?php
                                        echo @$client->via_subbie ? (@$client->via_subbie_only ? 'CHECKED' : '') : 'disabled';
                                        ?>/>
                                    </td>
                                    <td>
                                        Only?
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="via_email" class="via_email takeOffReturnType testDisabled" value="2" <?php
                                        echo @$client->via_subbie_only ? 'disabled' : (@$client->via_email  ? 'CHECKED' : '');
                                        ?>/>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        via Email?
                                    </td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_name" autocomplete="off" class="form-control input-sm take_off_merchant_name takeOffEmailInfo testDisabled" placeholder="Client Staff Name" <?php
                                        echo @$client->via_email  ? 'value="' . @$client->take_off_merchant_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_email" autocomplete="off" class="form-control input-sm take_off_merchant_email takeOffEmailInfo merchantReturnEmail testDisabled" placeholder="Client Staff Email Address" <?php
                                        echo @$client->via_email  ? 'value="' .  @$client->take_off_merchant_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_one_name]" id="cc_one_name" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Client CC1 Name" <?php
                                        echo @$client->via_email  ? 'value="' . $take_off_merchant_cc->cc_one_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_one_email]" id="cc_one_email" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Client CC1 Email Address" <?php
                                        echo @$client->via_email  ? 'value="' . $take_off_merchant_cc->cc_one_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_two_name]" id="cc_two_name" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Client CC2 Name" <?php
                                        echo @$client->via_email  ? 'value="' . $take_off_merchant_cc->cc_two_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_two_email]" id="cc_two_email" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Client CC2 Email Address" <?php
                                        echo @$client->via_email  ? 'value="' . $take_off_merchant_cc->cc_two_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_franchise_email" id="cc_two_email" autocomplete="off" class="form-control input-sm take_off_franchise_email takeOffEmailInfo franchiseReturnEmail testDisabled" placeholder="Copy to Franchise Administrator" <?php
                                        echo @$client->via_email  ? 'value="' . @$client->take_off_franchise_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="via_ftp" class="via_ftp takeOffReturnType testDisabled" value="3" <?php
                                        echo @$client->via_subbie_only ? 'disabled' : (@$client->via_ftp ? 'CHECKED' : '');
                                        ?>/>
                                    </td>
                                    <td colspan="4" style="white-space: nowrap;">
                                        via FTP?
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </fieldset>
                <?php
                }
                else{
                    echo @$client->client_name;
                }
                ?>

            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <input type="button" name="export" value="Export" class="exportBtn btn btn-primary btn-sm"  />
    <input type="button" name="cancel" value="Cancel" class="closedOption btn btn-default btn-sm"  data-dismiss="modal"/>
</div>