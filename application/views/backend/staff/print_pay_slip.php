<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script language="javascript" src="<?php echo base_url()?>plugins/js/jquery.js"></script>
        <script language="javascript" src="<?php echo base_url()?>plugins/js/jquery-ui.js"></script>
        <script language="javascript">
            /*$(document).ready(function(e) {
                $(window).bind("load",function(){
                    print();
                });

                $("html, body").click(function() {
                    print();
                });
            });*/
        </script>
    </head>
    <body>
    <?php
    if(count($staff)>0):
        foreach($staff as $v):
        ?>
        <table class="print-table">
            <thead>
            <tr>
                <th colspan="4" style="text-transform: uppercase;">
                    <?php echo $v->company?>
                </th>
            </tr>
            <tr>
                <th colspan="4" style="padding: 10px;border: 1px solid #000000">Pay Slip Period End (<?php echo date('j F Y');?>)</th>
            </tr>
            </thead>
            <tbody>
            <tr style="border: 1px solid #000000">
                <td class="bold-text">
                    Name: <span><?php echo $v->name;?></span>
                </td>
                <td>
                    Tax Number: <span><?php echo $v->tax_number;?></span>
                </td>
                <td>
                    Hours: <span><?php echo $v->hours;?></span>
                </td>
                <td>
                    Hourly Rate: <span><?php echo $v->rate_cost;?></span>
                </td>
            </tr>
            <tr>
                <td class="bold-text" style="text-align: center">Income <span></span></td>
                <td class="bold-text" style="text-align: center">Deduct</td>
                <td class="bold-text" style="text-align: center">Start Bal</td>
                <td class="bold-text" style="text-align: center">Bal Outs</td>
            </tr>
            <tr style="vertical-align: top">
                <td >Wage Gross: <span><?php echo $v->gross ? '$ '.$v->gross : '';?></span></td>
                <td class="deduction-column">
                    Tax: <span><?php echo $v->tax;?></span><br/>
                    Flight: <span><?php echo $v->flight;?></span><br/>
                    Visa: <span><?php echo $v->visa;?></span><br/>
                    Accom: <span><?php echo $v->accommodation;?></span><br/>
                    Transport: <span><?php echo $v->transport;?></span><br/>
                    Recruit: <span><?php echo $v->recruit;?></span><br/>
                    Admin: <span><?php echo $v->admin;?></span><br/>
                    Loans: <span><?php echo $v->installment;?></span><br/>
                    <strong>Total:
                        <span style="float: right;border-top: 1px solid #000000"><?php echo $v->total;?></span>
                    </strong>
                </td>
                <td style="padding-left: 20px">
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div><?php echo $v->star_balance;?></div>
                </td>
                <td style="text-align: center">
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div>&nbsp;</div>
                    <div><?php echo $v->balance != 0 ? number_format($v->balance,2,'.',',') : '&nbsp;';?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: left;">Subtotal/Net Pay: <span><?php echo $v->net ? '$ '.$v->net : '';?></span></td>
            </tr>
            <tr>
                <td style="text-align: center;padding: 2px!important;">
                    <strong>Distribution</strong>
                </td>
                <td style="text-align: center;padding: 2px!important;">
                    <?php echo $v->currency_code;?> One
                </td>
                <td style="text-align: center;padding: 2px!important;">
                    <?php echo $v->currency_code;?> Two
                </td>
                <td style="text-align: center;padding: 2px!important;">
                    NZ ACC
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <strong><?php echo $v->distribution ? '$ '.$v->distribution : '';?></strong>
                </td>
                <td>
                    <span><?php echo $v->account_one;?></span><br/>
                    <span style="color: #ff0000;font-weight: bold"><?php echo $v->account_one_;?></span>
                </td>
                <td>
                    <span><?php echo $v->account_two;?></span><br/>
                    <span style="color: #ff0000;font-weight: bold"><?php echo $v->account_two_;?></span>
                </td>
                <td>
                   <span><?php echo $v->nz_account ? '$ '.$v->nz_account : '';?></span>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
        endforeach;
    endif;
    ?>
    <style>
        body{
            font-family: arial, sans-serif;
            font-size: 13px;
        }
        .print-table{
            margin: 0 auto;
            border-collapse: collapse;
        }
        .print-table > tbody > tr > td{
            padding: 5px 15px;
            border: 1px solid #000000;
        }
        .bold-text{
            font-weight: bold;
        }
        .print-table > tbody > tr > .deduction-column > span{
            float: right;
        }
    </style>
    </body>
</html>