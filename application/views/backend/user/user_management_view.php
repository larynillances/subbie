<style>
    .slick-row:hover {
        background: #9fa7c1 !important;
    }
    .slick-row.active{
        background: #8c7b99 !important;
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
</style>

<script>
    $(function(){
        var rowData = '';
        var columnsBasic = [
            {id: "name", name: "Name", field: "name", width: 120, sortable: true},
            {id: "username", name: "Username", field: "username", width: 80, sortable: true,cssClass: "column-center"},
            {id: "alias", name: "Alias", field: "alias", width: 80, sortable: true,cssClass: "column-center"},
            {id: "email", name: "Email", field: "email", width: 150},
            {id: "active", name: "Status", field: "is_active", width: 40,cssClass: "column-center"},
            {id: "account_type", name: "Account type", field: "account_type", width: 130,cssClass: "column-center"}
        ];
        var dataFull = <?php echo $users;?>;

         $("#userManagement_table").slickgrid({
            columns: columnsBasic,
            data: dataFull,
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
                var searchString = "";
                var accountStatus = "";
                var accountType = "";

                function requiredFieldValidator(value) {
                    if (value == null || value == undefined || !value.length) {
                        return {valid: false, msg: "This is a required field"};
                    }
                    else {
                        return {valid: true, msg: null};
                    }
                }

                function myFilter(item, args) {
                    var name = item["name"].toLowerCase();
                    if (args.accountType != "" && item["account_type"].indexOf(args.accountType) == -1) {
                        return false;
                    }

                    if (args.accountStatus != "" && item["active"].indexOf(args.accountStatus) == -1) {
                        return false;
                    }

                    if (args.searchString != "" && name.indexOf(args.searchString) == -1) {
                        return false;
                    }

                    return true;
                }
                grid.onCellChange.subscribe(function (e, args) {
                    dataView.updateItem(args.item.id, args.item);
                });

                grid.onClick.subscribe(function(e, args) {
                    var currentRow = args.row;
                    rowData = dataView.getItem(currentRow);
                    $('.slick-row').removeClass('active');
                    check_data_click();
                });
                grid.onDblClick.subscribe(function(e, args){
                    var currentRow = args.row;
                    rowData = dataView.getItem(currentRow);
                    var link = bu + 'manageUser/edit/' + rowData.id;
                    $('.modal-title').html('Edit User');
                    $('.lg-page-load').load(link);
                    $('.largeModal').modal();
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

                $(".search-class").keyup(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    searchString = this.value;
                    updateFilter();
                });

                $(".account-type").change(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    accountType = this.value;
                    updateFilter();
                });

                $(".status").change(function (e) {
                    Slick.GlobalEditorLock.cancelCurrentEdit();

                    // clear on Esc
                    if (e.which == 27) {
                        this.value = "";
                    }

                    accountStatus = this.value;
                    updateFilter();
                });

                function updateFilter() {
                    dataView.setFilterArgs({
                        accountStatus: accountStatus,
                        accountType: accountType,
                        searchString: searchString
                    });
                    dataView.refresh();
                }
                // set the initial sorting to be shown in the header
                if (sortCol) {
                    grid.setSortColumn(sortCol, sortDir);
                }

                // initialize the model after all the events have been hooked up
                dataView.setFilterArgs({
                    accountStatus: accountStatus,
                    accountType: accountType,
                    searchString: searchString
                });
                dataView.setFilter(myFilter);
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
    var user_btn = $('.user_management');
    user_btn.tooltip();

    var check_data_click = function(){
        var btn = $('.disabled-btn');
        btn.css({
            'pointer-events' : 'none',
            'background' : '#484848',
            'border-color' : '#484848'
        });
        if(rowData != ''){
            btn.css({
                'pointer-events' : 'inherit',
                'background' : '#428bca',
                'border-color' : '#357ebd'
            });
        }
    };
    check_data_click();

    user_btn.on('click', function(){
        var btn_type = $(this).attr('id');
        var selected_id = rowData.id;
        var link;
        if(rowData != ''){
            if(rowData.id.length){
                if(btn_type == 'edit'){
                    link = bu + 'manageUser/edit/' + selected_id;
                    $('.modal-title').html('Edit User');
                    $('.lg-page-load').load(link);
                    $('.largeModal').modal();
                }else if(btn_type == 'delete'){
                    link = bu + 'manageUser/delete/' + selected_id;
                    $('.modal-title').html('Delete User');
                    $('.sm-page-load').load(link);
                    $('.smallModal').modal();
                }
            }
        }
        if(btn_type == 'add'){
            link = bu + 'manageUser/add';
            $('.modal-title').html('Add User');
            $('.lg-page-load').load(link);
            $('.largeModal').modal();
        }

    });

    });
</script>
<div class="row">
    <div class="col-lg-7">
        <div class="pull-left">
            <div class="col-lg-5">
                <input type="text" class="form-control input-sm search-class" name="search" placeholder="Search..">
            </div>
            <div class="col-lg-6" style="margin-left: 10px;">
                <?php echo form_dropdown('account_type',$account_type,'','class="form-control input-sm account-type"')?>
            </div>
        </div>
        <div class="pull-left" style="margin-left: -30px;">
            <div class="col-lg-12">
                <?php echo form_dropdown('status',$status,'','class="form-control input-sm status"')?>
            </div>
        </div>
        <div class="pull-right">
            <button class="btn btn-primary btn-sm user_management" data-toggle="tooltip" data-placement="top" id="add" title="Add user"><i class="fa fa-fw fa-user"></i> </button>
            <button class="btn btn-primary btn-sm user_management disabled-btn" data-toggle="tooltip" data-placement="top" id="delete" title="Delete user"><i class="fa fa-fw fa-minus-circle"></i> </button>
            <button class="btn btn-primary btn-sm user_management disabled-btn" data-toggle="tooltip" data-placement="top" id="edit" title="Edit user"><i class="fa fa-fw fa-pencil"></i> </button>
        </div>
    </div>
</div><br/>
<div class="row">
    <div class="col-lg-7">
        <div id="userManagement_table" class="grid" style="border: 1px solid #a5a5a5;height: 500px;"></div>
    </div>
</div>
<!--<div id="myGrid" style="width:1000px;height:500px"></div>-->