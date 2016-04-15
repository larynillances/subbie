<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($inv_data) >0):
        foreach($inv_data as $v):
            ?>
            <div class="modal-body">
                <?php
                if($v->job_id != 0):
                    ?>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <strong>Tracking Log Job Name:</strong> <?php echo form_dropdown('job_id',$job_data,$v->job_id,'class="form-control input-sm"');?>
                        </div>
                    </div>
                    <?php
                endif;
                ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-invoice">
                            <thead>
                            <tr style="border-top: 2px solid #000000">
                                <th style="width: 15%">Your Ref</th>
                                <th style="width: 15%">Our Ref</th>
                                <th>Job Name</th>
                                <th style="width: 15%">m&sup2;/hrs</th>
                                <th style="width: 15%">Unit Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr style="border-bottom: 2px solid #000000">
                                <td><input type="text" name="your_ref" class="form-control input-sm required" value="<?php echo $v->your_ref?>"></td>
                                <td><?php echo $_GET['ref']?></td>
                                <td style="height: 300px;">
                                    <textarea name="job_name" class="form-control input-sm job_name" style="height: 100%;width: 529px;"><?php echo $v->job_name?></textarea>
                                </td>
                                <td>
                                    <textarea name="meter" class="form-control input-sm this-textarea" style="height: 100%;"><?php echo $v->meter;?></textarea>
                                </td>
                                <td>
                                    <textarea name="unit_price" class="form-control input-sm this-textarea" style="height: 100%;"><?php echo $v->unit_price;?></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
            <?php
        endforeach;
    endif;
echo form_close();
?>
<script>
    $(function(e){
        var job_name = $('.job_name');
        var textarea = $('.this-textarea');
        textarea.focusin(function(e){
            var total_line = job_name.val();
            var count_line = total_line.split("\n");
            var arr = $(this).val().split("\n");
            var newArr = [];
            var i;
            for(i = 0; i <= count_line.length; i++){
                var thisVal = "";
                if(arr[i]){
                    thisVal = arr[i];
                }
                newArr.push(thisVal);
            }
            $(this).val(newArr.join("\n"));
        });
    })
</script>