<style>
    table.table thead .sorting,
    table.table thead .sorting_asc,
    table.table thead .sorting_desc,
    table.table thead .sorting_asc_disabled,
    table.table thead .sorting_desc_disabled {
        cursor: pointer;
        *cursor: hand;
    }

    table.table thead .sorting { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/asc_des.png') no-repeat center right; }
    table.table thead .sorting_asc { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/arrow_up1.png') no-repeat center right; }
    table.table thead .sorting_desc { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/arrow_down.png') no-repeat center right; }

    table.table thead .sorting_asc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/sort_asc_disabled.png') no-repeat center right; }
    table.table thead .sorting_desc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/sort_desc_disabled.png') no-repeat center right; }
</style>

<script type="text/javascript">

    $(document).ready(function() {
        $('#myTable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_pos_users.php",
            "sPaginationType": "full_numbers",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "<?php echo sfConfig::get("app_web_url") ?>media/swf/copy_csv_xls.swf",
                "aButtons": [
                    "csv",
                    "print"

                ]
            },
            "aLengthMenu": [[10, 25, 50,100,250 ,-1], [10, 25, 50,100,250,"All"]]

        });

        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
    });
</script>


<div class="itemslist">

    <h1 class="items-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/tablet_user_over.png' ?>" />&nbsp;Pos User List</h1>
</div>

<div class="itemslist listview">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
            <tr>
                <th>Id</th>
                <th>Login</th>
                <th>Password</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>User type</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    <a href="<?php echo url_for('users/add') ?>">New POS Users</a>
