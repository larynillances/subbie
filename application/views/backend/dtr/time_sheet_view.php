<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal" role="form"');
    ?>
    <div>
        <div class="row">
            <div class="col-sm-3">
                <label class="control-label col-sm-2" style="margin-left: -20px;">Year:</label>
                <div class="col-sm-5">
                    <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm select year-dp"')?>
                </div>

                <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
            </div>

        </div>
    </div>
    <?php
    echo form_close();
    ?><br/>
    <div class="row">
        <div class="col-sm-6">
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
    .is_locked .slick-cell.l2{
        background: #63c65c!important;
    }
</style>

<script>
    $(function(){
        var rowData = '';
        var columnsBasic = [
            {id: "week_num", name: "Week No.", field: "week_num", width: 30, cssClass: "text-center",sortable: true},
            {id: "pay_period", name: "Pay Period", field: "pay_period", width: 60, cssClass: "text-center",sortable: true},
            {id: "locked", name: "Locked", field: "locked", width: 30,cssClass: "text-center"},
            {id: "no_employee", name: "# Emp.", field: "no_employee", width: 30,cssClass: "text-center"},
            {id: "total_pay", name: "Total Pay", field: "total_pay", width: 30,cssClass: "right-align",sortable: true},
            {id: "total_paye", name: "Total PAYE", field: "total_paye", width: 30,cssClass: "right-align",sortable: true}
        ];

        $(this).newForm.addLoadingForm();
        $.ajax({
            dataType: "json",
            url: bu + 'timeSheetDefault?json=1',
            success: function(json){
                executeThis(json);
                $(this).newForm.removeLoadingForm();
            }
        });

        function executeThis(data) {
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
                        $.post(bu + 'timeSheetEdit',
                            {
                                search: true,
                                year: row.year,
                                month: row.month,
                                week: row.week_num
                            },
                            function(data){
                                location.replace(bu + 'timeSheetEdit');
                            }
                        );
                    });

                    grid.onCellChange.subscribe(function (e, args) {
                        dataView.updateItem(args.item.id, args.item);
                    });

                    dataView.onRowsChanged.subscribe(function (e, args) {
                        dataView.getItemMetadata = function (row) {
                            if (dataView.getItem(row).is_locked) {
                                return {
                                    'cssClasses': 'is_locked'
                                };
                            }else{
                                return {
                                    'cssClasses': 'not_locked'
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
        }

        $('.slick-row').live('click',function(){
            $('.slick-row').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>