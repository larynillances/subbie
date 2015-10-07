<div class="row">
    <div class="col-lg-12">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Client</th>
                <th style="width: 15%;">Date</th>
                <th style="width: 15%;">NETT</th>
                <th style="width: 15%;">GST</th>
                <th style="width: 15%;">Gross</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_net = 0;
            $total_gst = 0;
            $total_gross = 0;
            if(count($client) >0):
                foreach($client as $key=>$val):
                    $total_net += $val->net;
                    $total_gst += $val->gst;
                    $total_gross += $val->gross;

                    ?>
                    <tr>
                        <td style="text-align: left;">
                            <?php
                            echo $val->name;
                            $ref = 0;
                            $str = '';
                            if(count(@$unpaid_inv[$val->id]) > 0){
                                foreach(@$unpaid_inv[$val->id] as $inv){
                                    $str .= $inv ."\n";
                                    //$str .= $ref > 0 ? "" : "\n";
                                    $ref++;
                                }
                                echo '<a href="#" style="float: right" class="tooltip-class" title="'.$str.'">view</a>';
                            }
                            ?>
                        </td>
                        <td style="text-align: right;"><?php echo $val->date;?></td>
                        <td style="text-align: right;"><?php echo $val->net ? '$'.number_format($val->net,2) : '$0.00';?></td>
                        <td style="text-align: right;"><?php echo $val->gst ? '$'.number_format($val->gst,2) : '$0.00';?></td>
                        <td style="text-align: right;"><?php echo $val->gross ? '$'.number_format($val->gross,2) : '$0.00';?></td>
                    </tr>
                    <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="5">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            <tr class="danger">
                <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_net,2)?></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_gst,2)?></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_gross,2)?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 text-right">
        <a href="<?php echo base_url().'outstandingBalance?print=true'?>" class="btn btn-success" target="_blank"><span class="glyphicon glyphicon-print"></span> Print</a>
    </div>
</div>