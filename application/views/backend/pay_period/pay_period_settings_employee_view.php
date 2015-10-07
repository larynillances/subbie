<div class="container-fluid">
    <div class="row">
        <div class="col-sm-9">
            <div class="pull-right">
                <a href="<?php echo base_url('payPeriodSettings')?>" class="btn btn-sm btn-success"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
                <a href="<?php echo base_url('payPeriodSettings/'.$this->uri->segment(2).'?p=1')?>" class="btn btn-sm btn-primary" target="_blank"><i class="glyphicon glyphicon-print"></i> Print</a>
            </div>
        </div>
    </div><br/>
    <div class="row">
        <div class="col-sm-9">
            <div class="week_number_view grid" style="border: 1px solid #000000;height: 600px;"></div>
        </div>
    </div>
</div>
<style>
    .slick-row:hover {
        background: #9fa7c1 !important;
    }
    .slick-row.active{
        background: #c68c5a !important;
    }
    .slick-cell{
        cursor: pointer;
    }
    .user_management{
        z-index: 20;
    }
    .column-center{
        text-align: center!important;
    }
    .right-align{
        text-align: right;
    }
    .not_locked .slick-cell.l2{
        background: #c66363;
    }
    .is_locked{
        background: #63c65c!important;
    }

    .is_this_week{
        background: #b2f1f9 !important;
    }
    .ui-widget-content .slick-cell{
        height: 35px;
    }
    .slick-header,.slick-header-columns,.grid .slick-header-columns .slick-header-column.ui-state-default{
        height: 40px;
    }
</style>

<script>
    $(function(){
        var rowData = '';
        var data = <?php echo $staff_data ? $staff_data : '[]'?>;
        var columnsBasic = [
            {id: "year", name: "Year", field: "year", width: 20, cssClass: "text-center",sortable: true},
            {id: "week", name: "Week&nbsp;&nbsp;<br/>No.", field: "week", width: 25, cssClass: "text-center",sortable: true},
            {id: "week_ending", name: "Week&nbsp;&nbsp;&nbsp;&nbsp;<br/>Ending", field: "week_ending", width: 40, cssClass: "text-center",sortable: true},
            {id: "frequency", name: "Frequency", field: "frequency", width: 30,cssClass: "text-center"},
            {id: "tax_code", name: "PAYE<br/>Code", field: "tax_code", width: 30,cssClass: "text-center"},
            {id: "description", name: "Wage&nbsp;&nbsp;<br/>Type", field: "description", width: 30,cssClass: "text-center",sortable: true},
            {id: "rate_name", name: "Rate&nbsp;&nbsp;&nbsp;<br/>Type", field: "rate_name", width: 30,cssClass: "text-center",sortable: true},
            {id: "kiwi", name: "Kiwi&nbsp;&nbsp;&nbsp;&nbsp;<br/>Emp.", field: "kiwi", width: 30,cssClass: "text-center",sortable: true},
            {id: "emp_kiwi", name: "Kiwi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>Empr.", field: "emp_kiwi", width: 30,cssClass: "text-center",sortable: true},
            {id: "esct_rate", name: "ESCT", field: "esct_rate", width: 30,cssClass: "text-center",sortable: true},
            {id: "pay_increase", name: "Pay&nbsp;&nbsp;&nbsp;<br/>Increase", field: "pay_increase", width: 30,cssClass: "right-align",sortable: true},
        ];
        $(".week_number_view").slickgrid({
            columns: columnsBasic,
            data: data,
            slickGridOptions: {
                enableCellNavigation: true,
                enableColumnReorder: true,
                forceFitColumns: true,
                inlineFilters: true,
                asyncEditorLoading: true,
                editable: true,
                rowHeight: 30
            },
            sortCol: undefined,
            sortDir: true,
            handleCreate: function(){
                var o = this.wrapperOptions;
                var dataView = new Slick.Data.DataView();
                var grid = new Slick.Grid(this.element, dataView, o.columns, o.slickGridOptions);

                grid.onCellChange.subscribe(function (e, args) {
                    dataView.updateItem(args.item.id, args.item);
                });

                grid.onClick.subscribe(function(e, args) {
                    var currentRow = args.row;
                    rowData = dataView.getItem(currentRow);
                    $('.slick-row').removeClass('active');
                });
                grid.onDblClick.subscribe(function(e, args){
                    var currentRow = args.row;
                    rowData = dataView.getItem(currentRow);
                });

                var sortCol = o.sortCol;
                var sortDir = o.sortDir;
                function compare(a, b) {
                    var x = a[sortCol], y = b[sortCol];
                    return (x == y ? 0 : (x > y ? 1 : -1));
                }
                grid.onSort.subscribe(function (e, args) {
                    sortDir = args.sortAsc;
                    sortCol = args.sortCol.field;
                    dataView.sort(compare, sortDir);
                    grid.invalidateAllRows();
                    grid.render();
                });

                dataView.onRowCountChanged.subscribe(function (e, args) {
                    grid.updateRowCount();
                    grid.render();
                });

                grid.onDblClick.subscribe(function(e, args){
                    var row = dataView.getItem(args.row);
                });

                grid.onCellChange.subscribe(function (e, args) {
                    dataView.updateItem(args.item.id, args.item);
                });

                dataView.onRowsChanged.subscribe(function (e, args) {
                    dataView.getItemMetadata = function (row) {
                        if (dataView.getItem(row).has_pay_increase) {
                            return {
                                'cssClasses': 'is_locked'
                            };
                        }
                    };
                    grid.invalidateRows(args.rows);
                    grid.render();
                });


                grid.onKeyDown.subscribe(function (e) {
                    // select all rows on ctrl-a
                    if (e.which != 65 || !e.ctrlKey) {
                        return false;
                    }

                    var rows = [];
                    for (var i = 0; i < dataView.getLength(); i++) {
                        rows.push(i);
                    }

                    grid.setSelectedRows(rows);
                    e.preventDefault();
                });

                dataView.beginUpdate();
                dataView.setItems(o.data);
                dataView.endUpdate();

                grid.resizeCanvas();
            }
        });

        $('.slick-row').live('click',function(){
            $('.slick-row').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>