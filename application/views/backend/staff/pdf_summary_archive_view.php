<div class="row">
    <div class="col-md-5">
        <h4>Monthly Summary Table</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th style="width: 25%;">Date</th>
                <th>File Name</th>
                <th style="width: 30%;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($monthly_archive)>0):
                foreach($monthly_archive as $v):
                    ?>
                    <tr>
                        <td><?php echo date('j F Y',strtotime($v->date))?></td>
                        <td><?php echo $v->file_name;?></td>
                        <td style="text-align: center">
                            <a href="<?php echo base_url().'pdf/summary/monthly/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/'.$v->file_name?>" target="_blank">view</a>&nbsp;
                            <a href="<?php echo base_url().'download/'.$v->id;?>" class="download-btn">download</a> <?php echo '('.$v->download.')'?>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="4">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-5">
        <h4>Wage Summary Table</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th style="width: 25%;">Date</th>
                <th>File Name</th>
                <th style="width: 30%;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($wage_archive)>0):
                foreach($wage_archive as $v):
                    ?>
                    <tr>
                        <td><?php echo date('j F Y',strtotime($v->date))?></td>
                        <td><?php echo $v->file_name;?></td>
                        <td style="text-align: center">
                            <a href="<?php echo base_url().'pdf/summary/wage/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/'.$v->file_name?>" target="_blank">view</a>&nbsp;
                            <a href="<?php echo base_url().'download/'.$v->id;?>" class="download-btn">download</a> <?php echo '('.$v->download.')'?>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="4">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <h4>Pay Slip Table</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 25%;">Name</th>
                <th>File Name</th>
                <th style="width: 20%;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($pay_slip_archive)>0):
                foreach($pay_slip_archive as $v):
                    ?>
                    <tr>
                        <td><?php echo date('j F Y',strtotime($v->date))?></td>
                        <td><?php echo $v->name;?></td>
                        <td><?php echo $v->file_name;?></td>
                        <td style="text-align: center">
                            <a href="<?php echo base_url().'pdf/payslip/'.date('Y',strtotime($v->date)).'/'.date('F',strtotime($v->date)).'/'.$v->file_name?>" target="_blank">view</a>&nbsp;
                            <a href="<?php echo base_url().'download/'.$v->id;?>" class="download-btn">download</a> <?php echo '('.$v->download.')'?>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="4">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    /*$(function(e){
        $('.download-btn').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            window.open(link,'_self');
            location.reload();
        });
    })*/
</script>