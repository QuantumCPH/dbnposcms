<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
       
        $("#dNumber").keyup(function(event)
            {
                name = $("#dNumber").val();
                name = name.replace(/[^a-zA-Z 0-9.-]+/g,'');
                $("#dNumber").val(name);
            });

    $("#addnotes").validate({
        focusCleanup: false,
          rules: {
             delivery_number:{
                required: true,
               
                remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>delivery_notes/validateDeliverNote" ,
                    type: "get",
                    dataType: 'json'
                    ,
                    complete: function(data){
                        if( data.responseText == "true" ) {
                            $("#dNumber").removeClass("error");
                            $("#add_item").attr("disabled",false);  
                        }else {
                            $("#dNumber").addClass("error");
                            $("#add_item").attr("disabled","disabled");  
                        }    
                     }
                } 
            }
         },messages: {
            delivery_number:{
                remote: "<?php echo __("Delivery Number Already Exist."); ?>"
            }
         }
      });
    $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 
    $( "#delivery_date" ).datepicker({ minDate: '0m +0w',dateFormat: 'yy-mm-dd'});
    $( ".itemsid" ).autocomplete(
	{
            source:'<?php echo url_for("delivery_notes/showItems")?>'
	});
//    
     $("#add_item").attr("disabled","disabled");   
    
});
function openPopUp(){ 
            var ddate = document.getElementById("delivery_date").value;
            var dnum = document.getElementById("dNumber").value;
            if($("#dNumber").hasClass("error")){
                $("#add_item").attr("disabled","disabled");
                return false;
            }else if(dnum==""){
                $("#dNumber").addClass("error");
                $("#dnum_err").append('<label for="dNumber" class="error">This field is required.</label>');
                $("#add_item").attr("disabled","disabled");
                return false;
            }else{
                $("#dNumber").removeClass("error");
                $("#add_item").attr("disabled",false);
                var e = document.getElementById("shop_id");
                var s_id = e.options[e.selectedIndex].value; 
                if(!$("#dNumber").hasClass("error")){
               //  alert('<?php echo url_for("delivery_notes/addNewNote?shop_id=")?>'+s_id+'?ddate='+ddate+'&dnumber='+dnum);
                $.fn.custombox({
                    url: '<?php echo url_for("delivery_notes/addNewNote?shop_id=")?>'+s_id+'?ddate='+ddate+'&dnumber='+dnum+'&k'
                });
                }
            }
    }
 
  
</script>
<form action="<?php echo sfConfig::get("app_admin_url"); ?>delivery_notes/add"  id="addnotes" method="post"  enctype="multipart/form-data" >
<div class="itemslist deliverynotesDiv">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/delivery_notes_title.png'?>" />&nbsp;<span>Add Delivery Notes</span>
        
    </h1>
    <div  class="backimgDiv">
        <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>delivery_notes/index';" value="Cancel" class="btn btn-cancel"/>
<!--        <input type="submit" value="Add" class="btn btn-primary" />-->
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
        <div class="lblleft">Delivery Number :&nbsp;</div>
        <div class="lblright">
            <div class="col-lg-6" id="dnum_err"><input type="text" name="delivery_number" class="form-control" required="required" id="dNumber"></div>
        </div> 
        <div class="lblleft">Branch:&nbsp;</div>
        <div class="lblright">
            <div class="col-lg-6">
            <select data-placeholder="Choose an Branch..." style="width:242px;" class="chosen-select required" name="shop_id" id="shop_id" >
    <?php foreach ($shops as $shop){  ?>              
                  <option value="<?php  echo $shop->getId(); ?>"><?php  echo $shop->getBranchNumber(); ?></option> 
             <?php     } ?>
                  
    </select></div>
        </div> 
        <br clear="all" />   
        <div class="lblleft">Delivery Date:&nbsp;</div>
        <div class="lblright">
            <div class="col-lg-6"><input type="text" name="delivery_date" class="form-control required" id="delivery_date" value="<?php echo date("Y-m-d");?>"></div>
        </div> 
        
        
        <br clear="all" />
        <p><a data-toggle="modal" href="#" onclick="return openPopUp();" id="add_item" class="btn btn-primary addItem" style="margin-left: 0">Add New Item</a></p>
    </div>
</div></form>
