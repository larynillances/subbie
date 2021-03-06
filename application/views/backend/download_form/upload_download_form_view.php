<?php
echo form_open_multipart('','class="form-horizontal"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-3" for="menu">Menu Name:</label>
        <div class="col-sm-8">
            <input type="text" class="form-control input-sm" id="menu" name="menu_name">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3">File:</label>
        <div class="col-sm-8">
            <input type="file" name="file_attachment" data-preview-file-type="any" data-show-upload="false" class="file-input attachment required" accept=".pdf">
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-sm btn-primary" name="submit" value="Upload">
    <input type="button" class="btn btn-sm btn-default" value="Cancel" data-dismiss="modal">
</div>
<?php
echo form_close();
?>
<script>
    $(function(){
        $(".file-input")
            .fileinput({
                overwriteInitial: false,
                maxFileSize: 10000,
                maxFilesNum: 10,
                showCaption: false
            });
    })
</script>