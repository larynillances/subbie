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
            {id: "email", name: "Email", field: "email", width: 150},
            {id: "date_registered", name: "Date registered", field: "date_registered", width: 100,cssClass: "column-center"},
            {id: "active", name: "Active", field: "active", width: 40,cssClass: "column-center"},
            {id: "account_type", name: "Account type", field: "account_type", width: 130,cssClass: "column-center"}
        ];
        var dataFull = <?php echo $users;?>;

         $("#userManagement_table").slickgrid({
            columns: columnsBasic,
            data: dataFull,
            slickGridOptions: {
                enableCellNavigation: true,
                enableColumnReorder: false,
                forceFitColumns: true,
                rowHeight: 30
            },
            handleCreate: function(){
                var o = this.wrapperOptions;
                var dataView = new Slick.Data.DataView();
                var grid = new Slick.Grid(this.element, dataView, o.columns, o.slickGridOptions);

                grid.onClick.subscribe(function(e, args) {
                    var currentRow = args.row;
                    rowData = dataView.getItem(currentRow);
                    check_data_click();
                });

                // initialize the model after all the events have been hooked up
                dataView.beginUpdate();
                dataView.setItems(o.data);
                dataView.endUpdate();

                grid.resizeCanvas();
            }
        });
    });
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="pull-left">
            <div class="col-lg-5">
                <div class="input-group" style="width: 200px;">
                    <input type="text" class="form-control input-sm" name="search" placeholder="Search..">
                        <span class="input-group-btn">
                            <input type="submit" class="btn btn-success btn-sm" name="submit" value="Go">
                        </span>
                </div>
            </div>
            <div class="col-lg-6" style="margin-left: 10px;">
                <?php echo form_dropdown('account_type',$account_type,'','class="form-control input-sm"')?>
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
    <div class="col-lg-12">
        <div id="userManagement_table" class="grid" style="border-bottom: 1px solid #a5a5a5;height: 600px;"></div>
    </div>
</div>
<!--<div id="myGrid" style="width:1000px;height:500px"></div>-->