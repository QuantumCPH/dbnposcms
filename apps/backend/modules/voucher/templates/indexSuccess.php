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
        var oTable = $('#myTable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>voucher_server_processing.php",
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

        }).columnFilter({aoColumns: [{type: "text"}, {type: "text"}, {type: "text"}, {type: "text"}, null,null, {type: "text"}, {type: "text"},
                {type: "text"},
                null,null

            ]

        });

        oTable.fnSort( [ [4,'desc'] ] );
        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
        
        jQuery("#myTable_filter").hide();
    });
</script>

<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/delivery_notes_title.png' ?>" align="left" />&nbsp;<span>Gift Vouchers</span></h1>
</div>
<div  class="backimgDiv">
</div>
<div class="itemslist listviewpadding">
    <?php if ($sf_user->hasFlash('access_error')): ?>
        <div class="alert alert-warning">
            <?php echo $sf_user->getFlash('access_error') ?>
        </div>
    <?php endif; ?>
    <?php if ($sf_user->hasFlash('message')): ?>
        <div class="alert alert-success">
            <?php echo $sf_user->getFlash('message') ?>
        </div>
    <?php endif; ?>
    <?php if ($sf_user->hasFlash('edit_error')): ?>
        <div class="alert alert-danger">
            <?php echo $sf_user->getFlash('edit_error') ?>
        </div>
    <?php endif; ?>
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
            <tr>

                <th>Voucher</th>
                <th>Issuer Branch Number</th>
                <th>Issuer Name</th>
                <th>Amount</th>
                <th>Created At</th>
                <th>Is Used</th>
                <th>Consumer Branch Number</th>
                <th>Consumer Name</th>
                <th>Used Amount</th>
                <th>Used at</th>
                <th>Balance</th>
            </tr>

        </thead>
        <tbody>


        </tbody>
        <tfoot>
            <tr>
                <th>Voucher</th>
                <th>Issuer Branch Number</th>
                <th>Issuer Name</th>
                <th>Amount</th>
                <th>Created At</th>
                <th>Is Used</th>
                <th>Consumer Branch Number</th>
                <th>Consumer Name</th>
                <th>Used Amount</th>
                <th>Used at</th>
                 <th>Balance</th>

            </tr>
        </tfoot>
    </table>

</div>
