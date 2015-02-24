<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/items_over.png'?>" />&nbsp;<span>Add Item</span></h1>

<script type="text/javascript">
 
    $(document).ready(function() {
        //$(":file").filestyle({classButton: "btn btn-primary",classInput: "filenamefld"});
        $("#additem").validate({
            rules: {
             item_id:{
                required: true,
                remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>items/validateItemId" ,
                    type: "get",
                    dataType: 'json'                   
                }
             },   
              ean:{
                required: true,
                remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>items/validateEan" ,
                    type: "get",
                    dataType: 'json'                   
                }
             }   
         },messages: {
           item_id:{
                remote: "<?php echo __("Item No already exist."); ?>"
            },
            ean:{
                remote: "<?php echo __("EAN already exist."); ?>"
            }
         }
        });
   } );
</script>
<form action="<?php echo url_for('items/add')?>" method="post" id="additem"   enctype="multipart/form-data">
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo $cancel_url ?>'" value="Cancel" class="btn btn-cancel"/>
          <input type="submit" value="Save" class="btn btn-primary" />
      </div>
 <div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
     <?php if ($sf_user->hasFlash('add_error')): ?>
    <div class="alert alert-danger">
        <?php echo $sf_user->getFlash('add_error') ?>
    </div>
    <?php endif;?>
     <div class="itemsfields">  
        <div class="left nobold">Description 1:&nbsp;</div>
    <div class="rightDesc">
            <div class="inputValueNobdr">
                <textarea class="form-control" cols="30" rows="4" name="desc1"></textarea>
        </div>
    </div> 
    <div class="left nobold">Description 2:&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
          <textarea class="form-control " cols="30" rows="4" name="desc2"></textarea>
        </div>
    </div> 
    <div class="left nobold">Description 3:&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
          <textarea class="form-control " cols="30" rows="4" name="desc3"></textarea>
        </div>
    </div> 
    <br clear="all" />
    <div class="left">Item No.:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="item_id" class="form-control required digits" />
        </div>
    </div>
    <div class="left nobold">Supplier No.:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="supplier_number" class="form-control" />
        </div>
    </div>     
    <br clear="all" />
    <div class="left nobold">Sup Item No. :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="supitemno" class="form-control" />
        </div>
    </div>
    <div class="left">Ean:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="ean" class="form-control required" />
        </div>
    </div>     
    <br clear="all" />
    <div class="left nobold">Color:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="color" class="form-control" />
        </div>
    </div> 
    <div class="left">Group:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="group" class="form-control required" />
        </div>
    </div>    
    <br clear="all" />
    <div class="left nobold">Size:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="size" class="form-control" />
        </div>
    </div>
    <div class="left">Buying Price:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="buying_price" class="form-control required" />
        </div>
    </div>     
    <br clear="all" />
    <div class="left">Selling Price:&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="selling_price" class="form-control required" />
        </div>
    </div>
    <div class="left">Tax Code :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr">
            <input type="text" value="" name="taxation_code" class="form-control required" />
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
 </div>
 </div>
</form>

</div>