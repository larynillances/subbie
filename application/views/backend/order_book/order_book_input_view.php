<?php
echo form_open('','class="form-horizontal" role="form" style="min-height:380px;"');
$job_id = $this->session->userdata('job_id');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-4 control-label">Job Name</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('job_id',$job,$job_id,'class="form-control input-sm is_required job_id"')?>
        </div>
    </div>
    <div class="table-load" style="min-height: 200px;"></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-sm submit-btn-class" name="save">Submit</button>
    <button type="button" class="btn btn-default btn-sm cancel-btn" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>

<script>
    $(function(e){
        var tableLoader = $('.table-load');
        var job_id = $('.job_id');
        var selected_id = job_id.val();
        tableLoader.load('<?php echo base_url().'productTableLoad/'?>' + job_id.val());
        job_id.change(function(e){
            check_if_job_id_exists($(this).val());
        });

        var check_if_job_id_exists = function(job_id){
            $('.table-load').load(bu + 'productTableLoad/' + job_id);
        };

        check_if_job_id_exists(selected_id);

        $('.submit-btn-class').click(function(e){
            e.preventDefault();
        });
    });
</script>