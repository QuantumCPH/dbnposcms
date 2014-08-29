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
 
$(document).on('click change','input[name="check_all"]',function() {
    var checkboxes = $('.idRow');
    if($(this).is(':checked')) {
        checkboxes.each(function(){
            this.checked = true;
        });
    } else {
        checkboxes.each(function(){
            this.checked = false;
        });
    }
});
 
</script>

<form enctype="multipart/form-data"  action="<?php echo sfConfig::get("app_admin_url"); ?>transactions/stockAdjustSubmit" method="post">
<div class="itemslist">
    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_table_id; ?>">
 
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Stock Adjust Page (Stock# <?php echo $stock->getStockId();  ?>)</h1>
    <div class="backimgDiv">
        <input type="button"   onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>shops/view/id/<?php echo $stock->getShopId();  ?>';" value="Back" class="btn btn-primary"/>
   <input type="submit" value="Update" class="btn btn-primary" />
      
    </div>

    <br/> <br/>

   <?php if ($sf_user->hasFlash('message')): ?>
            <div class="alert alert-success">
                <?php echo $sf_user->getFlash('message') ?>
            </div>
        <?php endif; ?>

    <div class="itemslist listviewpadding "><br />
        <?php if ($sf_user->hasFlash('access_error')): ?>
            <div class="alert alert-warning">
                <?php echo $sf_user->getFlash('access_error') ?>
            </div>
        <?php endif; ?>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered itemlistTable" id="myTable" >
            <thead>
                <tr>
                     <th> <input type="checkbox" name="check_all"></th>
                    <th>Item Id</th>
                    <th>Name</th>
                    <th>Total </th>
                    <th>Sold </th>
                    <th>Return </th>
                    <th>Remaining </th>
                    <th>Bookout </th>
                    <th>Stock </th>
                      <th>Stock Diff</th>

                </tr>
            </thead>
            <tbody>

                <?php   foreach($stockItems as $stockItem){ ?>
                
 <tr>
                     <th> <?php if($stockItem->getProcessStatus()==1){  ?>  <input class="idRow" type="checkbox" name="stockItemId[]" value="<?php echo $stockItem->getId();   ?>"  />  <?php  } ?></th>
                    <th><?php echo $stockItem->getItemId();   ?></th>
                    <th><?php $items=ItemsPeer::retrieveByPK($stockItem->getCmsItemId()); echo $items->getDescription1();   ?></th>
                    <th><?php echo $stockItem->getTotalQty();   ?></th>
                    <th><?php echo $stockItem->getSoldQty();   ?></th>
                    <th><?php echo $stockItem->getReturnQty();   ?></th>
                    <th><?php echo $stockItem->getRemainingQty();   ?></th>
                    <th><?php echo $stockItem->getBookoutQty();   ?></th>
                    <th><?php echo $stockItem->getStockQty();   ?></th>
                     <th><?php echo $stockItem->getStockValue();   ?></th>

                </tr>
                
                
                <?php } ?>
            </tbody>

        </table>

    </div>
</div>
    </form>