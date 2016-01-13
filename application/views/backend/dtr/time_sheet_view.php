<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal" role="form"');
    ?>
    <div>
        <div class="row">
            <div class="col-sm-3">
                <label class="control-label col-sm-2" style="margin-left: -20px;">Year:</label>
                <div class="col-sm-5">
                    <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm"')?>
                </div>
                <div class="col-sm-2">
                    <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
                </div>
            </div>
        </div>
    </div>
    <?php
    echo form_close();
    ?><br/>
    <div class="row">
        <div class="col-sm-8">
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
        var columnsBasic = [
            {id: "week_num", name: "Week No.", field: "week_num", width: 30, cssClass: "text-center",sortable: true},
            {id: "pay_period", name: "Pay Period", field: "pay_period", width: 60, cssClass: "text-center",sortable: true},
            {id: "locked", name: "Locked", field: "locked", width: 30,cssClass: "text-center"},
            {id: "no_employee", name: "# Emp.", field: "no_employee", width: 30,cssClass: "text-center"},
            {id: "total_pay", name: "Total Pay", field: "total_pay", width: 30,cssClass: "right-align",sortable: true},
            {id: "total_paye", name: "Total PAYE", field: "total_paye", width: 30,cssClass: "right-align",sortable: true}
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
                    var isMobile = false; //initiate as false
                    // device detection
                    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

                    grid.onCellChange.subscribe(function (e, args) {
                        dataView.updateItem(args.item.id, args.item);
                    });

                    if(!isMobile){
                        grid.onClick.subscribe(function(e, args) {
                            var currentRow = args.row;
                            rowData = dataView.getItem(currentRow);
                            $('.slick-row').removeClass('active');
                        });

                        grid.onDblClick.subscribe(function(e, args){
                            var row = dataView.getItem(args.row);
                            var currentRow = args.row;
                            rowData = dataView.getItem(currentRow);
                            $(this).newForm.addLoadingForm();
                            if(row.week_has_passed){

                                $.post(bu + 'timeSheetEdit',
                                    {
                                        submit_week_pay: true,
                                        search: true,
                                        year: row.year,
                                        month: row.month,
                                        week: row.week_num
                                    },
                                    function(data){
                                        location.replace(bu + 'timeSheetEdit');
                                    }
                                );

                            }else{

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
                            }
                        });
                    }
                    else{
                        grid.onClick.subscribe(function(e, args){
                            var row = dataView.getItem(args.row);
                            var currentRow = args.row;
                            rowData = dataView.getItem(currentRow);
                            $(this).newForm.addLoadingForm();
                            if(row.week_has_passed){

                                $.post(bu + 'timeSheetEdit',
                                    {
                                        submit_week_pay: true,
                                        search: true,
                                        year: row.year,
                                        month: row.month,
                                        week: row.week_num
                                    },
                                    function(data){
                                        location.replace(bu + 'timeSheetEdit');
                                    }
                                );

                            }else{

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
                            }
                        });
                    }


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
                                if (dataView.getItem(row).is_this_week) {
                                    return {
                                        'cssClasses': 'is_this_week'
                                    };
                                }else{
                                    return {
                                        'cssClasses': 'not_locked'
                                    };
                                }
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
                    var scrollToGridRef = function(scrollToRef){
                        var scrollToRowMiddleRow = scrollToRef - 11;
                        scrollToRowMiddleRow = scrollToRowMiddleRow > 0 ? scrollToRowMiddleRow : scrollToRef;
                        grid.scrollRowIntoView(scrollToRowMiddleRow, 1);
                    };

                    scrollToGridRef(weekNumber);
                }
            });
        }

        $('.slick-row').live('click',function(){
            $('.slick-row').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>