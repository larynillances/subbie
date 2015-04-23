<div class="row">
    <div class="col-lg-12">
        <button class="btn btn-primary btn-sm import-btn" type="button"><i class="glyphicon glyphicon-import"></i> Import Tax</button>
    </div>
</div><br/>
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

        $('.import-btn').live('click',function(){
            $(this).modifiedModal({
                url: bu + 'importTaxTable',
                title: 'Import Tax'
            });
        });
    })
</script>