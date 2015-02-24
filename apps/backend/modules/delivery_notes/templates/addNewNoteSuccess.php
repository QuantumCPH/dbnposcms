<style>
</style>

<script type="text/javascript">

    $(document).ready(function() {
    $(".chosen-select").chosen({no_results_text: "No Item found."}); 
       $( ".itemsid" ).autocomplete(
	{
            source:'<?php echo url_for("delivery_notes/showItems")?>'
	})
        
         $("#frmAddItem").validate({ ignore: ":hidden:not(select)" });
       
    });
</script>

<div class="popupdiv" style="background-color:#fff;">
    <form method="post" name="frmAddItem" id="frmAddItem" action="<?php echo url_for("delivery_notes/saveNewNote")?>">
    <h2><input type="hidden" name="note_id" value="<?php echo $note_id;?>" />
        <label class="popuptitle"><img src="<?php echo sfConfig::get('app_web_url').'images/items_over.png'?>" />&nbsp;Add Item</label>
<!--        <button type="button" class="close" onclick="$.fn.custombox('close');"><img src="<?php echo sfConfig::get("app_web_url")?>images/popup-close.png" /></button>-->
    </h2>
    <input type="hidden" name="note_id" value="<?php echo $note_id?>" />
    <input type="hidden" name="shop_id" value="<?php echo $shop_id?>" />
    <input type="hidden" name="ddate" value="<?php echo $delivery_date?>" />
    <div class="lblleft">Item Number :&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
            <input type="text" name="itemnum" autocomplete="on" value="" class="form-control itemsid required digits" />
        </div>
    </div> 
    <br clear="all" />    
    <div class="lblleft">Quantity :&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
           <input type="text" name="qyantity" autocomplete="off" value="" class="form-control required digits" />
        </div>
    </div>    
    <br clear="all" />
    <div class="actionBtns">  
     <input type="submit" name="appitem" value="Add" class="btn btn-primary" />
     <input type="button" name="closepopup" value="Cancel" class="btn btn-cancel" onclick="$.fn.custombox('close');" />
    </div>
    </form>
</div>
