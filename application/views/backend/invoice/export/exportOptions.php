<script language="javascript" src="<?php echo base_url();?>plugins/js/number.js"></script>
<script language="javascript" src="<?php echo base_url() . "plugins/js/email.validation.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "plugins/css/email.validation.css"; ?>" />
<script language="JavaScript">
    var executeGenerate, isSend = 1;
    var comment = "";
    $(function(e){
        var IS_TEST_SERVER = <?php echo IS_TEST_SERVER ? 1 : 0; ?>;
        var thisId = '<?php echo $thisId; ?>';
        var $isArchived = <?php echo $isArchived ? 1 : 0; ?>;
        var exportBtn = $('.exportBtn');
        var exportOptions = {};

        exportBtn.click(function(e){
            if(!$('.is_test').is(':checked')){ //!$isArchived &&
                if(IS_TEST_SERVER && $('.via_email').is(':checked')){
                    $(this).newForm.formDeleteQuery({
                        title: "Warning",
                        msg:
                            'Do you really wish to email this Jobs CSV & PDF output<br />' +
                            'from the Test Server back to the email addresses defined for<br />' +
                            'this Branch <strong>' + $('.branchNameText').html() + '</strong>?<br /><br />',
                        button: {
                            yesTestBtn: 'Yes',
                            noTestBtn: 'No'
                        }
                    });

                    $('.yesTestBtn')
                        .unbind('click')
                        .bind('click', function(e){
                            $(this).newForm.forceClose({
                                callBack: function(e){
                                    markedAsComplete();
                                }
                            });
                        });
                    $('.noTestBtn')
                        .unbind('click')
                        .bind('click', function(e){
                            $(this).newForm.forceClose();
                        });
                }
                else{
                    markedAsComplete();
                }
            }
            else{
                isSend = 1;
                executeGeneratePopOut();
            }
        });

        function markedAsComplete(){
            $(this).newForm.formDeleteQuery({
                title: "Job Completion",
                msg: 'Mark this job as Completed?',
                button: {
                    yesCompleteBtn: 'Yes',
                    noCompleteBtn: 'No'
                }
            });

            $('.yesCompleteBtn')
                .unbind('click')
                .bind('click', function(e){
                    isSend = 1;
                    var thisUrl = bu + "jobComplete/" + thisId + '?isExport=1&noRedirect=1';
                    if($isArchived){
                        thisUrl += '&isArchived=1'
                    }

                    $(this).newForm.forceClose({
                        callBack: function(e){
                            $(this).newForm.addNewForm({
                                title: "Job Completion Form",
                                url: thisUrl,
                                toFind: '.jobCompleteTable'
                            });
                        }
                    });
                });
            $('.noCompleteBtn')
                .unbind('click')
                .bind('click', function(e){
                    isSend = 1; //0;
                    executeGenerate("");
                });
        }

        executeGenerate = function(c){
            comment = c;
            executeGeneratePopOut();
        };

        function executeGeneratePopOut(){
            $(this).newForm.addLoadingForm();

            if($('.exportWhat[value="pdf"]').is(':checked')){
                generatePdf();
            }
            else{
                if($('.exportWhat[value="csv"]').is(':checked')){
                    generateCsv();
                }
                else{
                    $(this).newForm.removeLoadingForm({
                        callBack: function(e){
                            var message = '<table class="messageTable" style="width: 280px;font-size: 12px;"><tr><td style="word-wrap: break-word;">' +
                                'No Export File Selected!'
                                + '<br /></td></tr></table>';
                            $(this).newForm.addNewForm({
                                title: "Alert!",
                                elemento: message,
                                autoHide: true,
                                autoDelay: 4500,
                                callBack: function(e){
                                    $(this).newForm.formSizeChange({
                                        toFind: '.messageTable'
                                    });
                                },
                                customFormHeader: "background: #FF4F4A;"
                            });
                        }
                    });
                }
            }
        }

        $('.closedOption').click(function(e){
            $('#certainDetail').html('');
        });

        function generatePdf(){
            var optionData =  {
                description_shown: $('.description_shown').is(':checked'),
                select_length: $('.select_length').is(':checked'),
                show_user_description: $('.show_user_description').is(':checked'),
                hide_mapping_deletion: $('.hide_mapping_deletion_pdf').is(':checked'),
                numbers: $('.numbers:checked').val(),
                no_alternate_drop_mapping: $('.no_alternate_drop_mapping_pdf').is(':checked'),
                add_manufacturing_comments: $('.add_manufacturing_comments_pdf').is(':checked'),
                apply_tag_code: $('.apply_tag_code_pdf').is(':checked'),
                print_tag_sheet: true
            };
            exportOptions.pdf = optionData;
            $.post(
                bu + 'setPrintJobDetails/' + thisId,
                optionData,
                function(data){
                    $.post(
                        bu + 'printJobDetails/' + thisId + '?isSave=1',
                        function(e){
                            if($('.exportWhat[value="csv"]').is(':checked')){
                                generateCsv();
                            }
                            else{
                                closeLoading();
                            }
                        }
                    );
                }
            );
        }

        function generateCsv(){
            var optionData =  {
                show_job_header: $('.show_job_header').is(':checked'),
                no_alternate_drop_mapping: $('.no_alternate_drop_mapping').is(':checked'),
                show_user_description: $('.show_merchant_description').is(':checked'),
                numbers: $('.numbers_csv:checked').val(),
                hide_mapping_deletion: $('.hide_mapping_deletion_csv').is(':checked'),
                add_manufacturing_comments: $('.add_manufacturing_comments_csv').is(':checked'),
                apply_tag_code: $('.apply_tag_code_csv').is(':checked')
            };
            exportOptions.csv = optionData;
            $.post(
                bu + 'setExportOption/' + thisId,
                optionData,
                function(data){
                    $.post(
                        bu + 'csvExport/' + thisId + '?isSave=1',
                        function(e){
                            closeLoading();
                        }
                    );
                }
            );
        }

        //region Take Off Return Setting
        var changedTakeOffSetting = $('.changedTakeOffSetting');
        var takeOffReturnArea = $('.takeOffReturnArea');
        var takeOffReturnType = $('.takeOffReturnType');
        var takeOffEmailInfo = $('.takeOffEmailInfo');
        var eotlReturnTypeOnly = $('.eotlReturnTypeOnly');
        var isTest = $('.is_test');
        var testDisabled = $('.testDisabled');

        if(IS_TEST_SERVER){
            isTest.trigger('click');
            testDisabled.attr('disabled', 'disabled');
        }
        isTest.click(function(e){
            if($(this).is(':checked')){
                testDisabled.attr('disabled', 'disabled');
            }
            else{
                testDisabled.removeAttr('disabled');
            }
        });

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
                    parent.exportEnableCounter(59);
                }
            );

            if(isSend){
                var optionData = {};
                <?php
                if(in_array($account_type, array(1, 2))){
                    ?>
                    var via_eotl_only = $('.via_eotl_only').is(':checked');
                    var via_email = via_eotl_only ? 0 : $('.via_email').is(':checked');
                    var via_ftp = via_eotl_only ? 0 : $('.via_ftp').is(':checked');
                    var is_test = isTest.is(':checked');
                    var is_direct = $('.is_direct').is(':checked');

                    //var send_dump_code = $('.send_dump_code').is(':checked');
                    //var send_missing_sku = $('.send_missing_sku').is(':checked');
                    var send_dump_code_to_merchant = $('.send_dump_code_to_merchant').is(':checked');
                    var send_missing_sku_to_merchant = $('.send_missing_sku_to_merchant').is(':checked');
                    var send_missing_sku_to_data = $('.send_missing_sku_to_data').is(':checked');

                    var export_pdf = $('.exportWhat[value="pdf"]').is(':checked');
                    var export_csv = $('.exportWhat[value="csv"]').is(':checked');

                    optionData =  {
                        via_eotl: $('.via_eotl').is(':checked') ? 1 : 0,
                        via_eotl_only: via_eotl_only ? 1 : 0,
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
                        is_test: is_test ? 1 : 0,
                        //send_dump_code: send_dump_code ? 1 : 0,
                        //send_missing_sku: send_missing_sku ? 1 : 0
                        send_dump_code_to_merchant: send_dump_code_to_merchant ? 1 : 0,
                        send_missing_sku_to_merchant: send_missing_sku_to_merchant ? 1 : 0,
                        send_missing_sku_to_data: send_missing_sku_to_data ? 1 : 0,

                        export_pdf: export_pdf ? 1 : 0,
                        export_csv: export_csv ? 1 : 0
                    };
                    <?php
                }
                ?>
                optionData.comment = comment;
                optionData.exportOptions = exportOptions;
                $.post(
                    bu + 'csvExportSendTakeOff/' + thisId,
                    optionData,
                    function(e){
                        $(this).newForm.removeLoadingForm();
                        $('#certainDetail').html('');
                    }
                );
            }
            else{
                $(this).newForm.removeLoadingForm();
                $('#certainDetail').html('');
            }
        }

        var viewExportBtn = $('.viewExportBtn');
        viewExportBtn.click(function(e){
            var thisExportArea = $(this).parent('').parent('').find('.exportArea');
            if(!$(this).hasClass('up')){
                $(this).addClass('up');
                thisExportArea.slideToggle();
            }
            else{
                $(this).removeClass('up');
                thisExportArea.slideToggle();
            }
        });

        var handledByTitle;
        var hoverHandleEle =
            '<span class="handledByTitle" style="padding: 10px;"></span>';

        $('.handleBy').hover(
            function(e){
                var title = '<strong>' + $(this).attr('alt') + '</strong>';
                $(this).after(hoverHandleEle);
                handledByTitle = $('.handledByTitle');
                handledByTitle.html(title);

                var thisTop = $(this).offset().top - (handledByTitle.innerHeight()/2) + parseFloat(handledByTitle.css('paddingTop'));
                var thisLeft = parseFloat($(this).offset().left);
                thisLeft += -1 * handledByTitle.innerWidth();
                thisLeft += -1 * parseFloat(handledByTitle.css('paddingTop'));
                handledByTitle.css({
                    top: thisTop + 'px',
                    left: thisLeft + "px"
                });
            },
            function(e){
                handledByTitle.remove();
            }
        );
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
                $take_off_merchant_cc = json_decode($client->take_off_merchant_cc);
                if(in_array($account_type, array(1, 2))){
                    ?>
                    <fieldset>
                        <legend>
                            <?php
                            echo '<h4 style="font-weight: bold">' . $client->client_name . '</h4>';
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
                                        <input type="checkbox" name="via_subbie" class="via_eotl takeOffReturnType testDisabled" value="1" <?php
                                        echo $client->via_subbie ? 'CHECKED' : '';
                                        ?> />
                                    </td>
                                    <td>
                                        Subbie
                                    </td>

                                    <td style="width: 10px;">
                                        <input type="checkbox" name="via_subbie_only" class="via_eotl_only eotlReturnTypeOnly testDisabled" value="1" <?php
                                        echo $client->via_subbie ? ($client->via_subbie_only ? 'CHECKED' : '') : 'disabled';
                                        ?>/>
                                    </td>
                                    <td>
                                        Only?
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="via_email" class="via_email takeOffReturnType testDisabled" value="2" <?php
                                        echo $client->via_subbie_only ? 'disabled' : ($client->via_email  ? 'CHECKED' : '');
                                        ?>/>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        via Email?
                                    </td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_name" autocomplete="off" class="form-control input-sm take_off_merchant_name takeOffEmailInfo testDisabled" placeholder="Merchant Staff Name" <?php
                                        echo $client->via_email  ? 'value="' . $client->take_off_merchant_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_email" autocomplete="off" class="form-control input-sm take_off_merchant_email takeOffEmailInfo merchantReturnEmail testDisabled" placeholder="Merchant Staff Email Address" <?php
                                        echo $client->via_email  ? 'value="' .  $client->take_off_merchant_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_one_name]" id="cc_one_name" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Merchant CC1 Name" <?php
                                        echo $client->via_email  ? 'value="' . $take_off_merchant_cc->cc_one_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_one_email]" id="cc_one_email" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Merchant CC1 Email Address" <?php
                                        echo $client->via_email  ? 'value="' . $take_off_merchant_cc->cc_one_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_two_name]" id="cc_two_name" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Merchant CC2 Name" <?php
                                        echo $client->via_email  ? 'value="' . $take_off_merchant_cc->cc_two_name . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_merchant_cc[cc_two_email]" id="cc_two_email" autocomplete="off" class="form-control input-sm takeOffEmailInfo merchantCCReturnEmail testDisabled" placeholder="Merchant CC2 Email Address" <?php
                                        echo $client->via_email  ? 'value="' . $take_off_merchant_cc->cc_two_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="input-data">
                                        <input type="text" name="take_off_franchise_email" id="cc_two_email" autocomplete="off" class="form-control input-sm take_off_franchise_email takeOffEmailInfo franchiseReturnEmail testDisabled" placeholder="Copy to Franchise Administrator" <?php
                                        echo $client->via_email  ? 'value="' . $client->take_off_franchise_email . '"' : 'disabled';
                                        ?>/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="via_ftp" class="via_ftp takeOffReturnType testDisabled" value="3" <?php
                                        echo $client->via_subbie_only ? 'disabled' : ($client->via_ftp ? 'CHECKED' : '');
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
                    echo $client->client_name;
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