 

<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/user_add_title.png' ?>" />&nbsp;<span>New Promotion</span></h1>
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

        });

    </script>
    <form action="<?php echo url_for('promotion/create') ?>" method="post" id="frmUser">
        <div  class="backimgDiv">


            <input type="submit" value="Save" class="btn btn-primary" />

        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>
            <input type="hidden" name="user[updated_by]" value="<?php //echo $user_id;  ?>" />
            <div class="lblleft">Promotion Title :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="promotion_title" value="" class="form-control" required="required" />
                </div>
            </div> 
            <div class="lblleft">Start Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="start_date" id="start_date" value="" class="form-control" required="required" />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">End Date:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="end_date" id="end_date" value="" class="form-control" required="required" /> 
                </div> 
            </div>



            <div class="lblleft">Promotion Type:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="promotion_type"  class="form-control" required="required" >
                        <option value="1">Percentage(%)</option>
                        <option value="2">Value</option>
                    </select>
                </div>
            </div> 

            <br clear="all" />

            <div class="lblleft">Promotion Value:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="promotion_value" value="" class="form-control" required="required" />
                </div>
            </div> 


            <div class="lblleft">On All Item :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="checkbox" name="on_all_item"  checked="checked"  value="1"/>
                </div>
            </div> 

            <br clear="all" />

            <div class="lblleft">On Item Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="item_id_type" class="form-control" >
                        <option value="1">Not Define</option>
                        <option  value="2">Single Item</option>
                        <option  value="3">Item Range</option>
                    </select>
                </div>
            </div>
            <div class="lblleft">Item ID:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="item_id"  class="form-control" />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Item ID To:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="item_id_to"  class="form-control"  />
                </div>
            </div> 
            <div class="lblleft">Item ID From :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="item_id_from"   class="form-control"  />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Description 1:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description1"  class="form-control"  />
                </div>
            </div> 
            <div class="lblleft">Description 2 :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description2"   class="form-control"  />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Description 3:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="description3"  class="form-control"  />
                </div>
            </div> 
            <div class="lblleft">Size:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="size"   class="form-control"  />
                </div>
            </div> 

            <br clear="all" />
            <div class="lblleft">Color:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="color"   class="form-control"  />
                </div>
            </div>

            <div class="lblleft">On Group Id :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="group_type" class="form-control" >
                        <option value="1">Not Define</option>
                        <option  value="2">Single Group</option>
                        <option  value="3">Group Range</option>
                    </select>
                </div>
            </div>
            <br clear="all" />
            <div class="lblleft">Group ID:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="group_name"  class="form-control" />
                </div>
            </div> 

            <div class="lblleft">Group To ID:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="group_to"  class="form-control"  />
                </div>
            </div>
            <br clear="all" />
            <div class="lblleft">Group From ID:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="group_from"   class="form-control"  />
                </div>
            </div>   

  <div class="lblleft">On Price Type :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select  name="price_type" class="form-control" >
                        <option value="1">Not Define</option>
                        <option  value="2">Price Less Then</option>
                         <option  value="3">Price Greater Then</option>
                        <option  value="4">Price Range</option>
                    </select>
                </div>
            </div>
            <br clear="all" />
            <div class="lblleft">Price Less Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_less"  class="form-control" />
                </div>
            </div> 

            <div class="lblleft">Price Greater Then:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_greater"  class="form-control"  />
                </div>
            </div>
            <br clear="all" />
            <div class="lblleft">Price To:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_to"   class="form-control"  />
                </div>
            </div>   
 <div class="lblleft">Price From:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="price_from"   class="form-control"  />
                </div>
            </div>  
 <br clear="all" />
            <div class="lblleft">Supplier Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="supplier_number"   class="form-control"  />
                </div>
            </div>   
 <div class="lblleft">Supplier Item Number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="supplier_item_number"   class="form-control"  />
                </div>
            </div>  

  <div class="lblleft">On All Branches :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="checkbox" name="on_all_branch"  checked="checked"  value="1"/>
                </div>
            </div> 
  
<div class="lblleft">Selected branches :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="branch_id"    class="form-control"  />
                </div>
            </div> 






















            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>
</div>
</div>
