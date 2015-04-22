<?php
echo form_open('','class="form-horizontal" role="form" style="min-height:380px;"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-4 control-label">Job Name</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('job_id',$job,'','class="form-control input-sm is_required"')?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label text-left">Supplier</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('supplier_id',$supplier,'','class="form-control input-sm is_required supplier"')?>
        </div>
    </div>
    <div class="table-load"></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary submit-btn-class" name="save">Submit</button>
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>

<script>
    $(function(e){
        var tableLoader = $('.table-load');
        var supplier = $('.supplier');
        tableLoader.load('<?php echo base_url().'productTableLoad/'?>' + supplier.val());
        supplier.change(function(e){
            $('.table-load').load('<?php echo base_url().'productTableLoad/'?>' + $(this).val());
        });
    });
</script>