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

        $.datepicker.regional[""].dateFormat = 'yy-mm-dd 00:00:00';
        $.datepicker.setDefaults($.datepicker.regional['']);
       var oTable =  $('#myTable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_byshop_sales.php",
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

        }).columnFilter({aoColumns: [{type: "text"}, null,{type: "text"}, null, null, {type: "text"}, {type: "text"}, {type: "text"},{type: "text"}, {type: "text"},
                {type: "date-range"},
                null

            ]

        });
        oTable.fnSort( [ [10,'desc'] ] );
        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
        
        jQuery("#myTable_filter").hide();

    });
</script>


<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Sales</h1>
</div>

<div class="itemslist listviewpadding "><br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
        <div class="alert alert-warning">
            <?php echo $sf_user->getFlash('access_error') ?>
        </div>
    <?php endif; ?>
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
          <tr>
                <th>Branch</th>
                <th>Sold Price</th>
                <th>Invoice no</th>
                <th>Qty</th>
                <th>Sell. Price</th>
                <th>User</th>
                <th>Item</th>
                 <th>Desc</th>
                <th>Status</th>
                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>


            </tr>
        </thead>
        <tbody>


        </tbody>
        <tfoot>
             <tr>
                <th>Branch</th>
                <th>Sold Price</th>
                <th>Invoice no</th>
                <th>Qty</th>
                <th>Sell. Price</th>
                <th>User</th>
                <th>Item</th>
                 <th>Desc</th>
                <th>Status</th>
                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>


            </tr>
        </tfoot>
    </table>


</div>
