 
<style>
    .search-field input{ height: 25px !important;}
</style>
<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/user_add_title.png' ?>" />&nbsp;<span>  Promotion Log Detail</span></h1>
    <?php if ($sf_user->hasFlash('error_user')): ?>
        <div class="alert alert-success">
            <h2><?php echo $sf_user->getFlash('error_user') ?></h2>
        </div>
    <?php endif; ?>

 
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
                 <?php echo $promotion->getPromotionTitle();  ?> 
                </div>
            </div> 
            <div class="lblleft">Start Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getStartDate();  ?> 
                </div>
            </div> 
            <br clear="all" />
             <div class="lblleft">Promotion Type:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   
                      <?php if($promotion->getPromotionType()==1){ ?> Percentage(%) <?php }   ?> 
                      <?php if($promotion->getPromotionType()==2){ ?>Value<?php }   ?> 
                    
                </div>
            </div>
            <div class="lblleft">End Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getEndDate();  ?> 
                </div> 
            </div>



            

            <br clear="all" />

            <div class="lblleft">Promotion Value:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getPromotionValue();  ?> 
                </div>
            </div> 


            <div class="lblleft">On All Item :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php if($promotion->getOnAllItem()==1){ ?>  Yes <?php }else{ ?>  No  <?php } ?>
               
                </div>
            </div> 
        
            <br clear="all" />

           
            <div class="lblleft">Description 1:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   <?php echo $promotion->getDescription1();  ?> 
                </div>
            </div> 
             <div class="lblleft">Color:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getColor();  ?> 
                </div>
            </div>
              <br clear="all" />
            <div class="lblleft">Description 2 :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   <?php echo $promotion->getDescription2();  ?> 
                </div>
            </div> 
            <div class="lblleft">Size:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   <?php echo $promotion->getSize();  ?> 
                </div>
            </div> 
              <br clear="all" />
            <div class="lblleft">Description 3:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                  <?php echo $promotion->getDescription3();  ?> 
                </div>
            </div> 
           

          
           
 <div class="lblleft">On Item Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    
                       <?php if($promotion->getItemIdType()==1){ ?>Not Define<?php }?> 
                     <?php if($promotion->getItemIdType()==2){ ?>Single Item<?php }?> 
                      <?php if($promotion->getItemIdType()==3){ ?>Item Range<?php }?> 
                  
                </div>
            </div>
 <br clear="all" />
                     <div class="lblleft">On Group Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    
                       <?php if($promotion->getGroupType()==1){ ?>Not Define <?php }?> 
                         <?php if($promotion->getGroupType()==2){ ?>Single Group<?php }?> 
                        <?php if($promotion->getGroupType()==3){ ?>Group Range<?php }?> 
                    
                </div>
            </div>
                   <div class="lblleft">Item ID:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                      <?php echo $promotion->getItemId();  ?> 
                    </div>
                </div>    
                    <br clear="all" />    
                     <div class="lblleft">Group ID:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $promotion->getGroupName();  ?> 
                    </div>
                </div> 
               
            
              
                
                <div class="lblleft">Item ID To:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $promotion->getItemIdTo();  ?> 
                    </div>
                </div> 
                 <br clear="all" />
                
                   <div class="lblleft">Group ID  From:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $promotion->getGroupFrom();  ?> 
                    </div>
                </div>   
                
                
                
                <div class="lblleft">Item ID From :&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                   <?php echo $promotion->getItemIdFrom();  ?> 
                    </div>
                </div> 
          
          <br clear="all" />
                <div class="lblleft">Group ID To :&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                       <?php echo $promotion->getGroupTo();  ?> 
                    </div>
                </div>
               
            <div class="lblleft">On Price Type :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   
                       <?php if($promotion->getPriceType()==1){ ?> Not Define <?php }?> 
                        <?php if($promotion->getPriceType()==2){ ?> Price Less Then <?php }?> 
                     <?php if($promotion->getPriceType()==3){ ?> Price Greater Then<?php }?> 
                        <?php if($promotion->getPriceType()==4){ ?> Price Range <?php }?> 
                  
                </div>
            </div>
             <br clear="all" />
             
             <div class="lblleft">Supplier Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getSupplierNumber();  ?> 
                </div>
            </div>   
            <div class="lblleft">Price Less Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                  <?php echo $promotion->getPriceLess();  ?> 
                </div>
            </div> 
             <br clear="all" />
             <div class="lblleft">Supplier Item Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   <?php echo $promotion->getSupplierItemNumber();  ?> 
                </div>
            </div>  

            <div class="lblleft">Price Greater Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getPriceGreater();  ?> 
                </div>
            </div>
           
           
            <br clear="all" />
            
                         <div class="lblleft">Promotion Status :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
               
                   <?php if($promotion->getPromotionStatus()==3){ ?>  Active<?php }?> 
                        <?php if($promotion->getPromotionStatus()==2){ ?> Deactive <?php }?> 
 
                    
                
                </div>
            </div> 
            <div class="lblleft">Price To:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $promotion->getPriceTo();  ?> 
                </div>
            </div>  
            <br clear="all" /> 
             <div class="lblleft">On All Branches :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                <?php if($promotion->getOnAllBranch()==1){ ?>  Yes <?php }else{ ?>  No  <?php } ?>
                
                </div>
            </div> 
            <div class="lblleft">Price From:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                   <?php echo $promotion->getPriceFrom();  ?> 
                </div>
            </div>  
             
          
            
            
             <br clear="all" /> 
            
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

        




















            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>
</div>
</div>
