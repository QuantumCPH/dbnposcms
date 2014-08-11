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
        var oTable = $('#myTablesale').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_byshop_sales.php",
            "sPaginationType": "full_numbers",
            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
            "fnServerParams": function(aoData) {
                aoData.push({"name": "item_id", "value": $("#itemId").val()});
            }

       }).columnFilter({aoColumns: [{type: "text"}, null, null, {type: "text"}, null,null,{type: "text"}, {type: "text"},
                {type: "date-range"},
                null

            ]

        });
        oTable.fnSort([[7, 'desc']]);
        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });

        jQuery("#myTablesale_filter").hide();
    });



    ////////////////////////////////////////////////////////////////////////////////////
    $(document).ready(function() {
        /////////////////////////////////////////////////////////////////////////////

        $('#myTable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bFilter": false,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_item_inventory.php",
            "sPaginationType": "full_numbers",
            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
            "fnServerParams": function(aoData) {
                aoData.push({"name": "item_id", "value": $("#itemId").val()});
            }

        });
        /*  .columnFilter()   */
        ///////////////////////////////////////////////////////////////////////////////////////

        $('#myTableItem').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bFilter": false,
            "bUseRendered": false,
            "aaSorting": [[0, 'desc']],
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_item_log.php",
            "sPaginationType": "full_numbers",
            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
            "fnServerParams": function(aoData) {
                aoData.push({"name": "item_id", "value": $("#itemId").val()});
            }

        }).columnFilter();
        ////////////////////////////////////////////////////////////////////////////////////  

        ////////////////////////////////////////////////////////////////////////////////////  
        $.datepicker.regional[""].dateFormat = 'yy-mm-dd 00:00:00';
        $.datepicker.setDefaults($.datepicker.regional['']);
//        $('#myTablesale').dataTable({
//            "bProcessing": true,
//            "bServerSide": true,
//            "bFilter": false,
//            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_item_byshop_sales_view.php",
//            "sPaginationType": "full_numbers",
//            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
//            "fnServerParams": function(aoData) {
//                aoData.push({"name": "item_id", "value": $("#itemId").val()});
//            }
//
//        });



        /* .columnFilter({ aoColumns: [ 	{ type: "text" }, null, null, { type: "text" }, { type: "text" },
         { type: "date-range" },
         null
         ]
         
         })  */
        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });

        $("#inventory-block").hide();
        $("#sales-block").hide();
        $("#item-history-block").hide();
//     $("#image-history-block").hide();

        $("#inventory-head").click(function() {
            var toggle_switch = $("#headingarrow1 img");
            $("#inventory-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });
        $("#sales-head").click(function() {
            var toggle_switch = $("#headingarrow2 img");
            $("#sales-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });
        $("#item-head").click(function() {
            var toggle_switch = $("#headingarrow3 img");
            $("#item-history-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });
//     $("#image-head").click(function(){
//         var toggle_switch = $("#headingarrow4 img");
//         $("#image-history-block").toggle(function(){
//             if ($(this).css('display') == 'none') {
//                toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
//            } else {
//                toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
//            }
//         });
//     });
    });
</script>
<input type="hidden" name="itemId" id="itemId" value="<?php echo $item->getItemId(); ?>">
<div class="itemslist">

    <h1 class="items-head list-page">
        <div class="titleicon"><img src="<?php echo sfConfig::get('app_web_url') . 'images/items_over.png' ?>" />&nbsp;Item Detail - <?php //echo $item->getId()  ?><?php echo $item->getItemId() ?> &nbsp;</div> 
        <div  class="backimgDiv">
            <?php if ($nextid) { ?>
                <a href="<?php echo url_for("items/view?id=" . $nextid) ?>" class="btn btn-primary viewNext">Next </a>
            <?php } ?>
            <?php if ($previousid) { ?>
                <a href="<?php echo url_for("items/view?id=" . $previousid) ?>" class="btn btn-primary viewPrev">Previous </a>
            <?php } ?>      
            <a href="<?php echo sfConfig::get("app_admin_url") ?>items/edit/id/<?php echo $item->getItemId(); ?>"><button type="button" class="btn  btn-primary">Edit</button> </a>   
            <a href="<?php echo sfConfig::get("app_admin_url") ?>items/index"><button type="button" class="btn  btn-cancel">Back</button> </a>
        </div>
    </h1>

    <div class="regForm" style="padding-top:47px;">  <br />
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
        <?php if ($sf_user->hasFlash('update_error')): ?>
            <div class="alert alert-danger">
                <?php echo $sf_user->getFlash('update_error') ?>
            </div>
        <?php endif; ?>
        <div class="itemsfields">  
            <div class="left">Description 1:&nbsp;</div>
            <div class="rightDesc">
                <div class="inputValue"><?php echo $item->getDescription1() ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Description 2:&nbsp;</div>
            <div class="rightDesc">
                <div class="inputValue"><?php echo $item->getDescription2() ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Description 3:&nbsp;</div>
            <div class="rightDesc">
                <div class="inputValue"><?php echo $item->getDescription3() ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Supplier No.:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getSupplierNumber() ?></div>
            </div>
            <div class="left">Sup Item No.:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getSupplierItemNumber() ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Group:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getGroup() ?></div>
            </div>
            <div class="left">Color:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getColor() ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Size:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getSize() ?></div>
            </div>
            <div class="left">Buying Price:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $num = number_format($item->getBuyingPrice(), 2, ',', ','); ?></div>
            </div> 
            <br clear="all" />
            <div class="left">Selling price:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $num = number_format($item->getSellingPrice(), 2, ',', ','); ?></div>
            </div>
            <div class="left">Tax Code:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getTaxationCode() ?></div>
            </div> 

            <br clear="all" />


            <div class="left">Updated At:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getItemUpdatedAt("Y-m-d"); ?></div>
            </div>   
  <div class="left">Status:&nbsp;</div>
            <div class="right">
                <div class="inputValue"><?php echo $item->getStatusId() == 3 ? "Active" : "Inactive"; ?></div>
            </div>   
            <br clear="all" />
              <br />
                        <div class="left">Ean:&nbsp;</div>
            <div class="right">
                <div class=" ean"><?php
                    // echo $item->getEan();
                    //  echo $barcode;
//          $bc = new barCode('png');
//          $bc->build($item->getEan());
                    ?>
    <!--           <img alt="<?php echo $item->getEan() ?>" src="<?php echo sfConfig::get("app_web_url") ?>barcode.php?codetype=Codabar&size=40&text=<?php echo $item->getEan() ?>" />-->
                    <img alt="<?php echo $item->getEan() ?>" src="<?php echo sfConfig::get("app_web_url") ?>barcode-show.php?text=<?php echo $item->getEan() ?>" />

                </div>
            </div>  
          
        </div>
        <div class="itemslargePic">
            <img src="<?php echo gf::checkLargeImage($item->getItemId()); ?>" />
        </div>
    </div>

    <br clear="all" />
    <br />
    <h1 class="items-head" id="inventory-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/inventory_over.png' ?>" />&nbsp;Inventory
        <span id="headingarrow1" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
    </h1>   
    <div class="regForm" id="inventory-block">   
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Total</th>
                    <th>Sold</th>
                    <th>Bookout</th>
                    <th>Returned</th>
                    <th>Available</th>
                    <th>Delivery</th>
                    <th>Item</th>

                </tr>
            </thead>
            <tbody>


            </tbody>

        </table>
    </div>     
    <br clear="all" />
    <h1 class="items-head" id="sales-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Sale
        <span id="headingarrow2" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
    </h1>  
      <div class="regForm" id="sales-block">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTablesale" >
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>User</th>
                    <th>Item</th>
                     <th>Description</th>
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
                <th>Amount</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Item</th>
                   <th>Description</th>
                <th>Status</th>
                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>
                 

            </tr>
        </tfoot>
        </table>
    </div> 
    <br clear="all" />
    <?php
    $items = $item;
    $itemsCount = 0;
    ?>
    <h1 class="items-head" id="item-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/items_over.png' ?>" />&nbsp;Item History
        <span id="headingarrow3" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
    </h1>
    <div  id="item-history-block">
        <div class="regForm">


            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered historyTbl" id="myTableItem" >
                <thead>
                    <tr>
                        <th style="display:none;visibility: hidden;">ID</th>
                        <th>Image</th>
                        <th>Des.1</th>
                        <th>Des.2</th>
                        <th>Des.3</th>
                        <th>Supp No.</th>
                        <th>Supp item No.</th>
                        <th>Group</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Buy Price</th>
                        <th>Sell price</th>
                        <th>Tax Code</th>
                        <th>Status</th>   
                        <th>Updated At</th>
                        <th>Updated By</th>
                    </tr>
                </thead>   
                <tbody> 

                </tbody>

            </table>   
        </div>
        <br clear="all" />

    </div>




