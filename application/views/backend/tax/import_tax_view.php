<link rel="stylesheet" href="<?php echo base_url();?>plugins/css/uploadify.css" />
<script src="<?php echo base_url();?>plugins/js/swfobject.js"></script>
<script src="<?php echo base_url();?>plugins/js/jquery.uploadify-3.1.js"></script>
<div class="form-horizontal">
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-3 text-left">Wage Type:</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('wage_type',$wage_type,'','class="form-control input-sm wage_type_"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 text-left">Frequency Type:</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('frequency',$frequency,'','class="form-control input-sm frequency_"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 text-left">Date Range:</label>
        <div class="col-sm-4">
            <div class='input-group date-picker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                <input type='text' id="start_date" name="start_date" class="form-control input-sm date-class" value="<?php echo date('01-04-Y')?>" placeholder="Start Date"/>
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <label class="col-sm-1 control-label">To</label>
        <div class="col-sm-4">
            <div class='input-group date-picker' id='datetimepicker2' data-date-format="DD-MM-YYYY">
                <input type='text' id="end_date" name="end_date" class="form-control input-sm date-class" value="<?php echo date('31-03-Y',strtotime('+1year'))?>" placeholder="End Date"/>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
            </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3 text-left">&nbsp;</label>
        <div class="col-sm-9">
            <input type="file" name="file" id="input-file" accept="application/vnd.ms-excel">
        </div>
    </div>

    <div class="errorMsg"></div>
</div>
<div class="modal-footer">
    <a href="javascript:$('#input-file').uploadify('upload','*')" class="uploadBtn btn-primary btn btn-sm disabled">Import</a>
    <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">Cancel</button>
</div>
</div>
<style>
    .date-class{
        pointer-events: none;
        background: #d5d5d5;
    }
    .errorMsg{
        text-align: right;
        font-size: 11px;
    }
</style>
<script>
    $(function (e) {
        var fu = $('#input-file');
        $('.date-picker').datetimepicker({
            pickTime: false
        });

        var frequency = function(){
            var dp = $('.frequency_');
            var result = dp.val();
            dp.change(function(){
                result = $(this).val();
            });

            return result;
        },
        wage_type = function(){
            var dp = $('.wage_type_');
            var result = dp.val();
            dp.change(function(){
                result = $(this).val();
            });

            return result;
        };
        fu.uploadify({
            'auto'     : false,
            'fileSizeLimit' : '10000KB',
            'fileTypeExts' : '*.xls',
            'buttonText': 'Browse...',
            'multi'    : false,
            'queueSizeLimit' : 1,
            'swf':  bu + 'uploadify/uploadify.swf',
            'uploader' : bu + 'importTaxTable/upload' ,
            'onSelect': function(file){
                $('.uploadBtn').removeClass('disabled');
            },
            'onUploadStart' : function(file) {
                $(this).newForm.addLoadingForm();
                $('.uploadBtn').addClass('disabled');
                fu.uploadify("settings", 'formData',{
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    wage_type: wage_type(),
                    frequency: frequency()
                });
            },
            'onUploadSuccess' : function(file, data, response) {
                $(this).newForm.removeLoadingForm();

                var errorMsg = $('.errorMsg');
                errorMsg.html('<div class="alert alert-success" role="alert">Successfully importing file.</div>');
                location.replace(bu + 'taxTable');
                /*if(data){
                    switch (data){
                        case 1:
                            errorMsg.html('<div class="alert alert-success" role="alert">Successfully importing file.</div>');
                            break;
                        case 2:
                            errorMsg.html('<div class="alert alert-danger" role="alert">No file has been found.</div>');
                            break;
                        case 3:
                            errorMsg.html('<div class="alert alert-danger" role="alert">Error file format.</div>');
                            break;
                        default:
                            errorMsg.html('<div class="alert alert-danger" role="alert">File is not readable.</div>');
                            break;
                    }
                }*/
            },
            'onUploadError': function(file, errorCode, errorMsg, errorString) {
                $(this).newForm.removeLoadingForm();
                $('.errorMsg').html('<div class="alert alert-danger" role="alert">' + errorString + '</div>');
            },
            'onCancel' : function(file) {
                $('.uploadBtn').addClass('disabled');
            }
        });
    });
</script>