 
<style>
    .search-field input{ height: 25px !important;}
</style>
<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/user_add_title.png' ?>" />&nbsp;<span>Update Promotion</span></h1>
    <?php if ($sf_user->hasFlash('error_user')): ?>
        <div class="alert alert-success">
            <h2><?php echo $sf_user->getFlash('error_user') ?></h2>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#frmUser").validate({
            });

            $("#start_date").datepicker({minDate: '0m +0w', dateFormat: 'yy-mm-dd'});
            $("#end_date").datepicker({minDate: '0m +0w', dateFormat: 'yy-mm-dd'});
            $("#item_id_type").change(function() {
                var itemidtypeval = $("#item_id_type option:selected").val();

                if (itemidtypeval == 1) {
                    $("#itemIdContainer").hide();
                    $("#itemRangeContainer").hide();
                } else if (itemidtypeval == 2) {
                    $("#itemIdContainer").show();
                    $("#itemRangeContainer").hide();
                } else {
                    $("#itemRangeContainer").show();
                    $("#itemIdContainer").hide();

                }
            });
             $("#group_type").change(function() {
                var itemidtypeval = $("#group_type option:selected").val();

                if (itemidtypeval == 1) {
                    $("#groupIdContainer").hide();
                    $("#groupRangeContainer").hide();
                } else if (itemidtypeval == 2) {
                    $("#groupIdContainer").show();
                    $("#groupRangeContainer").hide();
                } else {
                    $("#groupRangeContainer").show();
                    $("#groupIdContainer").hide();

                }
            });
             $("#price_type").change(function() {
                var pricetypeval = $("#price_type option:selected").val();

                if (pricetypeval == 1) {
                    $("#priceIdContainerLess").hide();
                      $("#priceIdContainerGreater").hide();
                    $("#priceRangeContainer").hide();
                } else if (pricetypeval == 2) {
                  $("#priceIdContainerLess").show();
                      $("#priceIdContainerGreater").hide();
                    $("#priceRangeContainer").hide();
                }else if (pricetypeval == 3) {
                   $("#priceIdContainerLess").hide();
                      $("#priceIdContainerGreater").show();
                    $("#priceRangeContainer").hide();
                } else {
                   $("#priceIdContainerLess").hide();
                      $("#priceIdContainerGreater").hide();
                    $("#priceRangeContainer").show();

                }
            });
             $('#on_all_branch').click(function(){
    if (this.checked) {
        $('.branchIdContainer').hide();
    }else{
      $('.branchIdContainer').show();
     
    }
       });    
            
             $('#on_all_item').click(function(){
    if (this.checked) {
        $('.allDataContainer').hide();
    }else{
      $('.allDataContainer').show();
     
    }
       });      
  jQuery(".chosen").data("placeholder","Select Branches...").chosen();
        });
 
    </script>
    <form action="<?php echo url_for('promotion/update') ?>" method="post" id="frmUser">
        <div  class="backimgDiv">


            <input type="submit" value="Save" class="btn btn-primary" />

        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>
            <input type="hidden" name="id"   value="<?php echo $promotion->getId();  ?>"  />
            <div class="lblleft">Promotion Title :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="promotion_title" value="<?php echo $promotion->getPromotionTitle();  ?>" class="form-control" required="required" />
                </div>
            </div> 
            <div class="lblleft">Start Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="start_date" id="start_date" value="<?php echo $promotion->getStartDate();  ?>" class="form-control" required="required" />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Promotion Type:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="promotion_type"  autocomplete="off" class="form-control" required="required" >
                        <option value="1"  <?php if($promotion->getPromotionType()==1){ ?>  selected="selected" <?php }   ?>  >Percentage(%)</option>
                        <option value="2" <?php if($promotion->getPromotionType()==2){ ?>  selected="selected" <?php }   ?>  >Value</option>
                    </select>
                </div>
            </div> 
            <div class="lblleft">End Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="end_date" id="end_date" value="<?php echo $promotion->getEndDate();  ?>" class="form-control" required="required" /> 
                </div> 
            </div>



            

            <br clear="all" />

            <div class="lblleft">Promotion Value:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="promotion_value" value="<?php echo $promotion->getPromotionValue();  ?>" class="form-control" required="required" />
                </div>
            </div> 


            <div class="lblleft">On All Item :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="checkbox" name="on_all_item" id="on_all_item" <?php if($promotion->getOnAllItem()==1){ ?> checked="checked" <?php }?>  value="1"/>
                </div>
            </div> 
            <div class="allDataContainer"   <?php if($promotion->getOnAllItem()==1){ ?> style="display:none;" <?php }?>  >
            
            <br clear="all" />
            <div class="lblleft">Description 1:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description1"   value="<?php echo $promotion->getDescription1();  ?>"  class="form-control"  />
                </div>
            </div> 
             <div class="lblleft">Color:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="color"   value="<?php echo $promotion->getColor();  ?>"   class="form-control"  />
                </div>
            </div>
               <br clear="all" />
            <div class="lblleft">Description 2 :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description2"   value="<?php echo $promotion->getDescription2();  ?>"   class="form-control"  />
                </div>
            </div> 
              <div class="lblleft">Size:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="size"   value="<?php echo $promotion->getSize();  ?>"   class="form-control"  />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Description 3:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description3"   value="<?php echo $promotion->getDescription3();  ?>"  class="form-control"  />
                </div>
            </div> 
          

           
           
  <div class="lblleft">Supplier Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="supplier_number"   value="<?php echo $promotion->getSupplierNumber();  ?>"   class="form-control"  />
                </div>
            </div> 
   <br clear="all" />
           
            <div class="lblleft">On Group Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="group_type" id="group_type"  autocomplete="off" class="form-control" >
                        <option value="1"  <?php if($promotion->getGroupType()==1){ ?>  selected="selected" <?php }?> >Not Define</option>
                        <option  value="2" <?php if($promotion->getGroupType()==2){ ?>  selected="selected" <?php }?>>Single Group</option>
                        <option  value="3" <?php if($promotion->getGroupType()==3){ ?>  selected="selected" <?php }?>>Group Range</option>
                    </select>
                </div>
            </div>
           <div class="lblleft">Supplier Item Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="supplier_item_number"   value="<?php echo $promotion->getSupplierItemNumber();  ?>"   class="form-control"  />
                </div>
            </div>  
            <div id="groupIdContainer"     <?php if($promotion->getGroupType()==2){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?> >
                  <br clear="all" />
                <div class="lblleft">Group ID:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="group_name"   value="<?php echo $promotion->getGroupName();  ?>"  autocomplete="off" class="form-control required" />
                    </div>
                </div> 
            </div> 
            <div id="groupRangeContainer"    <?php if($promotion->getGroupType()==3){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?> >
                                <br clear="all" />
                <div class="lblleft">Group ID  From:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="group_from"   value="<?php echo $promotion->getGroupFrom();  ?>"  autocomplete="off"  class="form-control required"  />
                    </div>
                </div> 
                <br clear="all" />
                <div class="lblleft">Group ID To :&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="group_to"   value="<?php echo $promotion->getGroupTo();  ?>"  autocomplete="off" class="form-control required"  />
                    </div>
                </div>
  
            </div> 
             <br clear="all" />
            <div class="lblleft">On Item Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="item_id_type" id="item_id_type"  autocomplete="off" class="form-control" >
                        <option value="1"  <?php if($promotion->getItemIdType()==1){ ?>  selected="selected" <?php }?> >Not Define</option>
                        <option  value="2" <?php if($promotion->getItemIdType()==2){ ?>  selected="selected" <?php }?>>Single Item</option>
                        <option  value="3" <?php if($promotion->getItemIdType()==3){ ?>  selected="selected" <?php }?>>Item Range</option>
                    </select>
                </div>
            </div>
            <div id="itemIdContainer"    <?php if($promotion->getItemIdType()==2){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?> >
                 <br clear="all" />
                <div class="lblleft">Item ID:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="item_id"     value="<?php echo $promotion->getItemId();  ?>" autocomplete="off"  class="form-control required" />
                    </div>
                </div> 
            </div>
            <div id="itemRangeContainer"   <?php if($promotion->getItemIdType()==3){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?>> 
               
                                 <br clear="all" />
                <div class="lblleft">Item ID From :&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="item_id_from"   value="<?php echo $promotion->getItemIdFrom();  ?>"  autocomplete="off"  class="form-control required"  />
                    </div>
                </div> 
                <br clear="all" />
                <div class="lblleft">Item ID To:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="text" name="item_id_to"   value="<?php echo $promotion->getItemIdTo();  ?>"  autocomplete="off" class="form-control required"  />
                    </div>
                </div> 

            </div>
             <br clear="all" />
            <div class="lblleft">On Price Type :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="price_type" id="price_type"  autocomplete="off" class="form-control" >
                        <option value="1" <?php if($promotion->getPriceType()==1){ ?>  selected="selected" <?php }?>>Not Define</option>
                        <option  value="2" <?php if($promotion->getPriceType()==2){ ?>  selected="selected" <?php }?>>Price Less Then</option>
                        <option  value="3" <?php if($promotion->getPriceType()==3){ ?>  selected="selected" <?php }?>>Price Greater Then</option>
                        <option  value="4" <?php if($promotion->getPriceType()==4){ ?>  selected="selected" <?php }?>>Price Range</option>
                    </select>
                </div>
            </div>
              <div id="priceIdContainerLess"    <?php if($promotion->getPriceType()==2){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?>>
              <br clear="all" />
            <div class="lblleft">Price Less Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_less"   value="<?php echo $promotion->getPriceLess();  ?>"  autocomplete="off" class="form-control required" />
                </div>
            </div> 
              </div>
              <div id="priceIdContainerGreater"    <?php if($promotion->getPriceType()==3){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?>>
                   <br clear="all" />
            <div class="lblleft">Price Greater Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_greater"   value="<?php echo $promotion->getPriceGreater();  ?>"  autocomplete="off" class="form-control required"  />
                </div>
            </div>
              </div>
              <div id="priceRangeContainer"    <?php if($promotion->getPriceType()==4){ ?> style="display:block;" <?php }else{ ?>   style="display: none;"  <?php } ?> >
             <br clear="all" />
            <div class="lblleft">Price From:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_from"    value="<?php echo $promotion->getPriceFrom();  ?>"   autocomplete="off" class="form-control required"  />
                </div>
            </div> 
                  <br clear="all" />
            <div class="lblleft">Price To:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_to" autocomplete="off"   value="<?php echo $promotion->getPriceTo();  ?>"   class="form-control required"  />
                </div>
            </div>   
            
              </div>
            <br clear="all" />
          

            </div>
            
            
             <div class="lblleft">Promotion Status :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
               <select  name="promotion_status" id="promotion_status"  autocomplete="off" class="form-control" >
                   <option value="3" <?php if($promotion->getPromotionStatus()==3){ ?>  selected="selected" <?php }?>>Active</option>
                        <option  value="2" <?php if($promotion->getPromotionStatus()==2){ ?>  selected="selected" <?php }?>>Deactive</option>
 
                    </select>
                
                </div>
            </div> 
             <br clear="all" />
            <div class="lblleft">On All Branches :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                <?php if($promotion->getOnAllBranch()==1){ ?>  Yes <?php }else{ ?>  No  <?php } ?>
                
                </div>
            </div> 
 
           
            
            
            
             <br clear="all" /> 
            <div class="branchIdContainer">
            <div class="lblleft">Branches  list:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                  
	    <?php
              $branchIds = explode(",", $promotion->getBranchId());
            foreach ($shops as $shop){  
                    
                  

               
                if (in_array($shop->getId(), $branchIds)) {  
                 
                  echo   $shop->getBranchNumber()."<br/>";
                    
                } 
               } ?>
 
                </div>
            </div> 

            </div>




















            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>
</div>
</div>
