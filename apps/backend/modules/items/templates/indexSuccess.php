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
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_processing.php",
            "sPaginationType": "full_numbers",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "<?php echo sfConfig::get("app_web_url") ?>media/swf/copy_csv_xls.swf",
                "aButtons": [
                    "csv",
                    "print"

                ]
            },
            "aLengthMenu": [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]]
        });

        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
    });
</script>
<script type="text/javascript" charset="utf-8">
    /* 
     *   "bPaginate": true,
     "bLengthChange": true,
     "bFilter": false,
     "bSort": true,
     "bInfo": true,
     "bAutoWidth": false,
     *   
     *     
     *       
     *           $(document).ready(function() {
     $('#example').dataTable( {
     "bProcessing": true,
     "bServerSide": true,
     "sAjaxSource": "../111/server_processing.php"
     } );
     } );*/
</script>

<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/items_over.png' ?>" />&nbsp;Items</h1>
</div>
<div  class="backimgDiv">
    <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>items/add';" value="Add" class="btn btn-primary"/>
</div>    
<!-- <div class="searchFrm">
      <div class="frmborder">  
   
      <form name="searchFrm" action="">
            <input type="submit" name="searchbtn" value="Submit" class="searchbtn" /><input type="text" name="searchFld" class="searchFld" placeholder="Type Item Number" value="<?php echo $searchFld; ?>" />
        </form>
    </div>    
</div> -->
<div class="itemslist listviewpadding "><br />
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
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered itemlistTable" id="myTable" >
        <thead>
            <tr>
                <th>Pic</th>
                <th>Item No.</th>
                <th>Item Name</th>
                <th>D2</th>
                <th>D3</th>
                <th>Group</th>
                <th>Color</th>
                <th>Supplier</th>
                <th>Selling price</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>


        </tbody>

    </table>


</div>
<?php //foreach ($items_list as $items): ?>
<!--  <tr>
     <td><img src="<?php //echo gf::checkImage($items->getSmallPic());      ?>" /> </td>
    <td><a href="<?php //echo sfConfig::get("app_admin_url")     ?>items/view/id/<?php //echo $items->getId()     ?> "><?php //echo $items->getItemId()     ?></a></td>
     <td><?php //echo $items->getDescription1();     ?></td>
    <td><?php //echo $items->getGroup()     ?></td>
    <td><?php //echo $items->getSupplierNumber()     ?></td>
   
    <td><?php
//echo $num = number_format($items->getSellingPrice(), 2, ',', ',');
?></td>
    <td><?php //echo date("Y-m-d",strtotime($items->getUpdatedAt()));    ?></td>
  </tr>-->
<?php
//endforeach; ?>