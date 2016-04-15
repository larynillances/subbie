<?php
$last_url = $this->session->userdata('last_access_url');
?>
<div class="modal-body">
   <div class="col-sm-6">
       <input type="text" name="description" placeholder="Search.." class="input-sm form-control">
   </div>
    <div class="col-sm-4">
        <?php echo form_dropdown('supplier',$supplier,'','class="form-control input-sm"')?>
    </div>
    <div class="row">
        <div class="col-sm-12"><br/>
            <div class="product-table-content"></div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary btn-sm submit-btn-class" name="save">Select</button>
    <button type="button" class="btn btn-default btn-sm return-btn">Cancel</button>
</div>
<script>
    $(function(e){
        var job_id = <?php echo $this->uri->segment(2);?>;
        var url = bu + 'productTableLoad/' + job_id + '?search=1';
        var supplier = $('select[name=supplier]');
        var description = $('input[name=description]');
        $('.btn').click(function(e){
            e.preventDefault();
            if($(this).hasClass('return-btn')){
                $(this).modifiedModal({
                    url: bu + 'orderBookInput',
                    title: 'New Material Order'
                });
            }
            else{

            }
        });

        description.keyup(function(e){
            e.preventDefault();
            $.post(url,
                {
                    data_search:1,
                    input: $(this).val(),
                    supplier_id: supplier.val()
                },
                function(data){
                    $('.product-table-content').load(url);
                }
            );
        });

        supplier.change(function(e){
            e.preventDefault();
            $.post(url,
                {
                    data_search:1,
                    input: description.val(),
                    supplier_id: $(this).val()
                },
                function(data){
                    $('.product-table-content').load(url);
                }
            );
        });
        //$('.product-table-content').load(url);
    })
</script>