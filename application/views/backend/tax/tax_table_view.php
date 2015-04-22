<!--<div class="row">
    <div class="col-lg-12">
        <table id="table-scrollable" class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th>Earnings</th>
                <th>M</th>
                <th>ME</th>
                <th>3%</th>
                <th>4%</th>
                <th>8%</th>
                <th>CEC</th>
                <th>10.5%</th>
                <th>CEC</th>
                <th>17.5%</th>
                <th>CEC</th>
                <th>30%</th>
                <th>CEC</th>
                <th>33%</th>
            </tr>
            </thead>
            <tbody>
            <?php
/*            if(count($tax)>0):
                foreach($tax as $v):
                    */?>
                    <tr>
                        <td><?php /*echo $v->earnings != '' ? '$'.number_format($v->earnings,2,'.',',') : '';*/?></td>
                        <td><?php /*echo '$'.number_format($v->m_paye,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->me_paye,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->kiwi_saver_3,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->kiwi_saver_4,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->kiwi_saver_8,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_1,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_1_10,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_2,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_2_17,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_3,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_3_30,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_4,2,'.',',');*/?></td>
                        <td><?php /*echo '$'.number_format($v->cec_4_33,2,'.',',');*/?></td>
                    </tr>
                <?php
/*                endforeach;
            else:
                */?>
                <tr>
                    <td colspan="3" class="empty-table">No data has found.</td>
                </tr>
            <?php
/*            endif;
            */?>
            </tbody>
        </table>
    </div>
</div>-->
<div class="row">
    <div class="col-lg-12">
        <div id="grid1" class="grid"></div>
    </div>
</div>
<script>
    $(function(e){
        var columnsBasic = [
            {id: "earnings", name: "Earnings", field: "earnings", width: 100},
            {id: "m-paye", name: "M", field: "m_paye", width: 80},
            {id: "me-paye", name: "ME", field: "me_paye", width: 80},
            {id: "kiwi-saver-3", name: "3%", field: "kiwi_saver_3", width: 80},
            {id: "kiwi-saver-4", name: "4%", field: "kiwi_saver_4", width: 80},
            {id: "kiwi-saver-8", name: "8%", field: "kiwi_saver_8", width: 80},
            {id: "cec-1", name: "CEC", field: "cec_1", width: 80},
            {id: "cec-1-10", name: "10.5%", field: "cec_1_10", width: 80},
            {id: "cec-2", name: "CEC", field: "cec_2", width: 80},
            {id: "cec-2-17", name: "17.5%", field: "cec_2_17", width: 80},
            {id: "cec-3", name: "CEC", field: "cec_3", width: 80},
            {id: "cec-3-30", name: "30%", field: "cec_3_30", width: 60},
            {id: "cec-4", name: "CEC", field: "cec_4", width: 60},
            {id: "cec-4-33", name: "33%", field: "cec_4_33", width: 60}
        ];

        var dataFull = <?php echo $tax;?>;

        $("#grid1").slickgrid({
            columns: columnsBasic,
            data: dataFull,
            slickGridOptions: {
                enableCellNavigation: true,
                enableColumnReorder: false,
                forceFitColumns: true,
                rowHeight: 35
            }
        });
    })
</script>