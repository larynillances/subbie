<table class="table table-responsive table-colored-header">
    <thead>
    <tr>
        <th rowspan="2" style="vertical-align: middle">Name</th>
        <th rowspan="2" style="vertical-align: middle">Team</th>
        <th rowspan="2" style="vertical-align: middle">Tax Number</th>
        <th rowspan="2" style="vertical-align: middle">Wage Type</th>
        <th colspan="2">Rate Type</th>
        <th rowspan="2" style="vertical-align: middle">Currency</th>
        <th colspan="4">Deductions</th>
        <th colspan="2">Loans</th>
    </tr>
    <tr>
        <th>Type</th>
        <th>Amount</th>
        <th>Flight</th>
        <th>Visa</th>
        <th>Accommo</th>
        <th>Trans</th>
        <th>Balance</th>
        <th>Installment</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($employee)>0):
        foreach($employee as $v):
            ?>
            <tr>
                <td>
                    <a href="<?php echo base_url().'staffWageHistory/'.$v->id;?>" class="click-event"><?php echo $v->name;?></a>
                </td>
                <td>
                    <?php
                    echo $v->team_id != 0 ?
                        '<a href="#" class="edit-team tooltip-class" data-toggle="tooltip" data-placement="top" title="'.$v->team.'" id="'.$v->id.'">'.$v->team_code.'</a>' :
                        '<a href="#" class="add-team tooltip-class" data-toggle="tooltip" data-placement="top" title="Add Team" id="'.$v->id.'">Add</a>'
                    ?>
                </td>
                <td>
                    <?php echo $v->tax_number;?>
                </td>
                <td>
                    <?php echo $v->description;?>
                </td>
                <td>
                    <?php echo $v->rate_name;?>
                </td>
                <td>
                    <?php echo $v->rate_cost;?>
                </td>
                <td>
                    <?php echo $v->currency_code;?>
                </td>
                <td style="width: 7%;">
                    <?php echo $v->flight;?>
                </td>
                <td style="width: 7%;">
                    <?php echo $v->visa;?>
                </td>
                <td style="width: 7%;">
                    <?php echo $v->accommodation;?>
                </td>
                <td style="width: 7%;">
                    <?php echo $v->transport;?>
                </td>
                <td>
                    <?php echo $v->balance;?>
                </td>
                <td style="width: 8%;">
                    <?php echo $v->installment;?>
                </td>
            </tr>
        <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="12">No data has found.</td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>
<script>
    $(function(e){
        var page = $('.sm-load-page');
        var title = $('.sm-title');
        var url;

        $('.edit-team').on('click',function(e){
            e.preventDefault();
            title.html('Edit Team');
            url = bu + 'staffTeamManage/edit/' + this.id;
            page.load(url);
            $('.sm-modal').modal();
        });
        $('.add-team').on('click',function(e){
            e.preventDefault();
            title.html('Add Team');
            url = bu + 'staffTeamManage/add/' + + this.id;
            page.load(url);
            $('.sm-modal').modal();
        });
    });
</script>