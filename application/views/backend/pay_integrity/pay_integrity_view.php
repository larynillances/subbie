<div class="container-fluid">
    <div>
        <div class="row">
            <div class="col-sm-3">
                <label class="control-label col-sm-2" style="margin-left: -20px;">Year:</label>
                <div class="col-sm-5">
                    <?php echo form_dropdown('year',$year,$_year,'class="form-control input-sm"')?>
                </div>
            </div>
        </div>
    </div>
    <br/>
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
    .not_locked .slick-cell.l2,.not_locked .slick-cell.l3{
        background: #c66363;
    }
    .is_locked .slick-cell.l2,.is_locked .slick-cell.l3{
        background: #63c65c!important;
    }

    .is_this_week{
        background: #b2f1f9 !important;
    }
    .ui-widget-content .slick-cell{
        height: 26px;
    }
</style>

<script>
    $(function(){
        var rowData = '';

        function htmlFormatter(row, cell, value, columnDef, dataContext) {
            return value;
        }

        var columnsBasic = [
            {id: "date_export", name: "Date & Time Exported", field: "date_export", width: 30,cssClass: "text-center",sortable: true},
            {id: "file_name", name: "File Name", field: "file_name", width: 70,cssClass: "text-center",sortable: true},
            {id: "commit", name: "Commit?", field: "committed", width: 20,cssClass: "text-center", formatter: htmlFormatter, sortable: true},
            {id: "uploaded", name: "Uploaded?", field: "uploaded", width: 20,cssClass: "text-center", formatter: htmlFormatter, sortable: true},
            {id: "upload", name: "&nbsp;", field: "upload_btn", width: 10,cssClass: "text-center", formatter: htmlFormatter, sortable: true},
            {id: "download", name: "Download", field: "download_file", width: 15, formatter: htmlFormatter,cssClass:"text-center"}
        ];
        Date.prototype.getWeek = function() {
            var onejan = new Date(this.getFullYear(),0,1);
            return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
        };

        var today = new Date();
        var weekNumber = today.getWeek();

        $(this).newForm.addLoadingForm();
        $.ajax({
            dataType: "json",
            url: bu + 'payIntegrityCheck?json=1',
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
                        //$(this).newForm.addLoadingForm();
                    });

                    grid.onCellChange.subscribe(function (e, args) {
                        dataView.updateItem(args.item.id, args.item);
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

                    dataView.beginUpdate();
                    dataView.setItems(o.data);
                    dataView.endUpdate();

                    grid.resizeCanvas();
                }
            });
        }

        $('.download-btn')
            .die()
            .live('click', function(e){
                e.stopPropagation();
                var url = this.href;
                location.replace(url);
            });

        $('.fa-upload')
            .die()
            .live('click', function(e){
                e.stopPropagation();
                $(this).modifiedModal({
                    url: bu + 'payIntegrityCheck/' + this.id + '?u=1',
                    title: 'Upload Pay Integrity'
                });
            });

        $('.slick-row').live('click',function(){
            $('.slick-row').removeClass('active');
            $(this).addClass('active');
        });

        $('select[name=year]').change(function(e){
            $(this).newForm.addLoadingForm();
            $.post(bu + 'payIntegrityCheck',{year:$(this).val(),submit:1},function(data){
                location.reload();
            });
        });

    });
</script>