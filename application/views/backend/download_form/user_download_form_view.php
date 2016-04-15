<div class="row">
    <div class="col-sm-4">
        <button class="btn btn-sm btn-primary upload-form"><i class="fa fa-file-pdf-o"></i> Upload</button>
    </div>
    <div class="col-sm-4">
        <button class="btn btn-sm btn-primary new-setup"><i class="fa fa-plus"></i> New</button>
        <button class="btn btn-sm btn-primary edit-setup"><i class="fa fa-pencil"></i> Edit All</button>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-sm-4">
        <div class="form_list grid" style="border: 1px solid #000000;height: 500px;"></div>
    </div>
    <div class="col-sm-4">
        <div class="grid_wrapper">
            <div class="available_file grid" style="border: 1px solid #000000;height: 500px;"></div>
        </div>
    </div>
</div>
<style>
    .slick-row.active{
        background: #c68c5a !important;
    }
    .slick-row{
        cursor: pointer!important;
    }
</style>
<script>
    $(document).ready(function() {
        var rowData = '';
        var form_list = $('.form_list');
        var available_file = $('.available_file');

        var user_form_columns = [
            {id: "id", name: "ID", field: "id", width: 10, cssClass: "text-center",sortable: true},
            {id: "account_type", name: "Account Type", field: "account_type", width: 60, cssClass: "text-center",sortable: true},
            {id: "menu_name", name: "Form Name", field: "menu_name", width: 60, cssClass: "text-center",sortable: true}
        ];

        var download_form_columns = [
            {id: "id", name: "ID", field: "id", width: 10, cssClass: "text-center",sortable: true},
            {id: "file_name", name: "File Name", field: "file_name", width: 60, cssClass: "text-center",sortable: true},
            {id: "menu_name", name: "Form Name", field: "menu_name", width: 60, cssClass: "text-center",sortable: true}
        ];

        function loadSlickGrid(url,class_name,column,type){
            $.ajax({
                dataType: "json",
                url: url,
                success: function(json){
                    var json_data = json.length > 0 ? json : [];
                    executeThis(class_name,column,json_data,type);
                }
            });
        }

        function executeThis(class_name,column,data,type) {
            class_name.slickgrid({
                columns: column,
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
                        var currentRow = args.row;
                        rowData = dataView.getItem(currentRow);
                        if(!type){
                            $(this).modifiedModal({
                                url: bu + 'downloadForm/edit_form/' + rowData.id,
                                title: 'Edit Downloadable Form'
                            });
                        }
                    });

                    grid.onCellChange.subscribe(function (e, args) {
                        dataView.updateItem(args.item.id, args.item);
                    });

                    dataView.onRowsChanged.subscribe(function (e, args) {
                        grid.invalidateRows();
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
                    dataView.setItems(data);
                    dataView.endUpdate();

                    grid.resizeCanvas();
                }
            });
        }

        var url_menu = bu + 'downloadForm/menu';
        loadSlickGrid(url_menu,available_file,user_form_columns,1);

        var url_download = bu + 'downloadForm/form';
        loadSlickGrid(url_download,form_list,download_form_columns,0);

        $('.slick-row').click(function(){
            $('.slick-row').removeClass('active');
            $(this).addClass('active');
        });

        $('.upload-form').click(function(){
            $(this).modifiedModal({
                url: bu + 'downloadForm/upload',
                title: 'Upload Form'
            });
        });

        $('.new-setup').click(function(){
            $(this).modifiedModal({
                url: bu + 'downloadForm/new',
                title: 'Set Account Downloadable Form'
            });
        });

        $('.edit-setup').click(function(){
            $(this).modifiedModal({
                url: bu + 'downloadForm/edit_menu',
                title: 'Edit Account Downloadable Form'
            });
        });
    });
</script>