<div class="form-horizontal">
    <div class="row">
        <div class="col-lg-12">
            <label class="control-label col-sm-1">Wage:</label>
            <div class="col-sm-1">
                <?php echo form_dropdown('wage_type',$wage_type,'','class="form-control input-sm wage_type" style="width:130%;"')?>
            </div>
            <label class="control-label col-sm-1">Frequency:</label>
            <div class="col-sm-1">
                <?php echo form_dropdown('frequency',$frequency,'','class="form-control input-sm frequency" style="width:130%;"')?>
            </div>
            <div class="col-sm-1">
                <input type="text" class="form-control input-sm earnings" placeholder="Earnings" style="width:130%;">
            </div>
            <div class="col-sm-1">
                <button class="btn btn-primary btn-sm import-btn" type="button"><i class="glyphicon glyphicon-import"></i> Import Tax</button>
            </div>
        </div>
    </div><br/>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="grid1" class="grid" style="border: 1px solid #000000"></div>
    </div>
</div>
<script>
    $(function(e){
        var columnsBasic = [
            {id: "earnings", name: "Earnings", field: "earnings_converted", width: 100},
            {id: "m-paye", name: "M", field: "m_paye", width: 80},
            {id: "me-paye", name: "ME", field: "me_paye", width: 80},
            {id: "sl-loan-ded", name: "SL Ded", field: "sl_loan_ded", width: 80},
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
            },
            handleCreate: function(){
                var o = this.wrapperOptions;
                var dataView = new Slick.Data.DataView();
                var grid = new Slick.Grid(this.element, dataView, o.columns, o.slickGridOptions);
                var earnings = "";
                var frequency = "";
                var wage_type = "";

                function requiredFieldValidator(value) {
                    if (value == null || value == undefined || !value.length) {
                        return {valid: false, msg: "This is a required field"};
                    }
                    else {
                        return {valid: true, msg: null};
                    }
                }

                function myFilter(item, args) {
                    if (args.frequency != "" && item["frequency"].indexOf(args.frequency) == -1) {
                        return false;
                    }

                    if (args.earnings != "" && item["earnings"].indexOf(args.earnings) == -1) {
                        return false;
                    }

                    if (args.wage_type != "" && item["wage_type"].indexOf(args.wage_type) == -1) {
                        return false;
                    }

                    return true;
                }
                grid.onCellChange.subscribe(function (e, args) {
                    dataView.updateItem(args.item.id, args.item);
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

                dataView.onRowsChanged.subscribe(function (e, args) {
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

                $(".earnings").keyup(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    earnings = this.value;
                    updateFilter();
                });

                $(".wage_type").change(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    wage_type = this.value;
                    updateFilter();
                });

                $(".frequency").change(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    frequency = this.value;
                    updateFilter();
                });

                function updateFilter() {
                    dataView.setFilterArgs({
                        earnings: earnings,
                        wage_type: wage_type,
                        frequency: frequency
                    });
                    dataView.refresh();
                }
                // set the initial sorting to be shown in the header
                if (sortCol) {
                    grid.setSortColumn(sortCol, sortDir);
                }

                // initialize the model after all the events have been hooked up
                dataView.setFilterArgs({
                    earnings: earnings,
                    wage_type: wage_type,
                    frequency: frequency
                });

                dataView.setFilter(myFilter);
                dataView.beginUpdate();
                dataView.setItems(o.data);
                dataView.endUpdate();

                grid.resizeCanvas();
            }
        });

        $('.import-btn').click(function(){
            $(this).modifiedModal({
                url: bu + 'importTaxTable',
                title: 'Import Tax'
            });
        });
    })
</script>