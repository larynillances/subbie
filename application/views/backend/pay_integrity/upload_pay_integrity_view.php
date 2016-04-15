<?php
echo form_open_multipart('payIntegrityCheck/' . $this->uri->segment(2) . '?u=1','class="form-horizontal"')
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-2">Select File:</label>
        <div class="col-sm-9">
            <input type="file" name="file" data-preview-file-type="any" data-show-upload="false" class="file-input attachment required" accept=".csv">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-success btn-sm uploadBtn" type="submit" name="upload">Upload</button>
    <button class="btn btn-default btn-sm" type="button" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var fu = $('.file-input');
        fu.fileinput({
            overwriteInitial: true,
            showCaption: false,
            maxFileSize: 10000,
            maxFilesNum: 10
        });
        $('.uploadBtn').click(function(e){
           if(!$('.file-preview-other').length){
               e.preventDefault();
           }
        });
    });
</script>