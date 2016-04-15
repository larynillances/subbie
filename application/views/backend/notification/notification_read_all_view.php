<style>
    .table-sm-font{
        font-size: 13px;
    }
    .table-sm-font > thead > tr > th,
    .table-sm-font > tbody > tr > td{
        border-bottom: 1px solid #808080;
    }
</style>
<table class="table table-sm-font table-responsive">
    <thead>
    <tr>
        <th colspan="5" style="text-align: left!important;">Message: <?php echo count($notification);?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $ref = 1;
    if(count($notification) > 0){
        foreach($notification as $nv){
            $job_name_arr = explode("\n",$nv->job_name);
            $check_account_txt = explode(" ",$job_name_arr[0]);
            $job_name = '';
            $reff = count($check_account_txt);
            if(count($check_account_txt) > 0){
                foreach($check_account_txt as $cv){
                    if($cv != ''){
                        $job_name .= $cv;
                        $job_name .= $reff != 1 ? ' ' : '';
                    }
                    $reff--;
                }
            }
            $job_name = str_replace('A/C','',$job_name);
            $job_name = str_replace('a/c','',$job_name);
            $job_name = str_replace(':','',$job_name);

            $url = $nv->is_new && $nv->is_archive && $nv->is_open ? 'editArchiveInvoice/'.$nv->client_id.'/'.$nv->inv_ref : 'jobInvoice/'.$nv->client_id.'/'.$nv->id;
            $link = $nv->is_new && $nv->is_archive && $nv->is_open ? '<strong>'.$nv->inv_ref.' ('.$job_name.')</strong>' : '<strong>'.$nv->job_ref.' ('.$job_name.')</strong>';
            $str = 'added by '.$user_data->name.'.';
            ?>
                <tr>
                    <td class="text-center" style="width: 50px;"><?php echo $ref;?></td>
                    <td>from: <strong><?php echo $user_data->name;?></strong></td>
                    <td style="width: 60%;"><a href="<?php echo base_url().$url?>"><?php echo $link;?></a></td>
                    <td>to: <strong><?php echo 'Administrator';?></strong></td>
                    <td style="font-size: 11px;font-style: italic"><?php echo date('d/m/Y',strtotime($nv->date));?></td>
                </tr>
            <?php
            $ref++;
        }
    }
    ?>

    </tbody>
</table>