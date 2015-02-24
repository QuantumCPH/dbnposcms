<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/shops_over.png'?>" />&nbsp;<span>Edit Item</span></h1>

<script type="text/javascript">
 
    $(document).ready(function() {
        //$(":file").filestyle({classButton: "btn btn-primary",classInput: "filenamefld"});
        $("#edititem").validate();
   } );
</script>
<form action="<?php echo url_for('items/update')?>" method="post" id="edititem"  enctype="multipart/form-data">
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo $cancel_url ?>'" value="Cancel" class="btn btn-cancel"/>
          <input type="submit" value="Update" class="btn btn-primary" />
      </div>
 <div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
 
     <input type="hidden" name="itemId" value="<?php echo $item->getItemId();?>" />
     <div class="itemsfields">  
        <div class="left nobold">Description 1:&nbsp;</div>
    <div class="rightDesc">
            <div class="inputValueNobdr">
                <textarea class="form-control" cols="30" rows="4" name="desc1"><?php echo $item->getDescription1()?></textarea>
        </div>
    </div> 
    <div class="left nobold">Description 2:&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
          <textarea class="form-control " cols="30" rows="4" name="desc2"><?php echo $item->getDescription2()?></textarea>
        </div>
    </div> 
    <div class="left nobold">Description 3:&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
          <textarea class="form-control " cols="30" rows="4" name="desc3"><?php echo $item->getDescription3()?></textarea>
        </div>
    </div> 
    <br clear="all" />
    <div class="left nobold">Supplier No.:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getSupplierNumber()?>" name="supplier_number" class="form-control" />
        </div>
    </div> 
    <div class="left nobold">Sup Item No. :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getSupplierItemNumber()?>" name="supitemno" class="form-control" />
        </div>
    </div>
    <br clear="all" />
    <div class="left">Ean:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getEan()?>" name="ean" readonly="readonly" class="form-control required" />
        </div>
    </div> 
    <div class="left">Group:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getGroup()?>" name="group" class="form-control required" />
        </div>
    </div>
    <br clear="all" />
    <div class="left nobold">Color:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getColor()?>" name="color" class="form-control" />
        </div>
    </div> 
    <div class="left nobold">Size:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getSize()?>" name="size" class="form-control" />
        </div>
    </div>
    <br clear="all" />
    <div class="left">Buying Price:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getBuyingPrice()?>" name="buying_price" class="form-control required" />
        </div>
    </div> 
    <div class="left">Selling Price:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getSellingPrice()?>" name="selling_price" class="form-control required" />
        </div>
    </div>
    <br clear="all" />
    <div class="left">Tax Code :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="<?php echo $item->getTaxationCode()?>" name="taxation_code" class="form-control required" />
        </div>
    </div>   
    <div class="left">Status :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <select name="status_id" class="form-control">
                <option value="3" <?php echo $item->getStatusId()==3?"selected='selected'":""?>>Active</option>
                <option value="5" <?php echo $item->getStatusId()==5?"selected='selected'":""?>>Inactive</option>
            </select>
        </div>
    </div>    
    <br clear="all" />
 <div class="left">Image :&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
            <input type="file" name="product_image" class="btn btn-default" />
        </div>
    </div> 
    <br clear="all" />  
 </div><div class="itemslargePic">
        <img src="<?php  echo gf::checkLargeImage($item->getItemId());  ?>" />
    </div>
 </div>
</form>

</div>