<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/user_edit_title.png' ?>" />&nbsp;<span>Edit User</span></h1>

    <?php // include_partial('form', array('form' => $form,'user_role',$user_role)) ?>

    <?php include_stylesheets_for_form($form) ?>
    <?php include_javascripts_for_form($form) ?>
    <script>
        $().ready(function() {
            $("#frmUser").validate({
                rules: {
                    "user[email]": {
                        required: true,
                        email: true
                                /*,
                                 remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>user/validateEditEmail?userid=<?php echo $user->getId(); ?>",
                                 type: "get",
                                 dataType: 'json'
                                 }*/
                    },
                    "user[pin]": {
                        required: true,
                        number: true,
                        remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>user/validateEditPin?userid=<?php echo $user->getId(); ?>",
                            type: "get",
                            dataType: 'json'
                        }
                    },
                    "pos_user_role_id": {
                        required: function() {
                            return (!(jQuery("#branch-select option:selected").val() == ''));
                        }
                    }
                }, messages: {
                    "user[email]": {
                        remote: "<?php echo __("Email already exist."); ?>"
                    },
                    "user[pin]": {
                        remote: "<?php echo __("Pin already exist."); ?>"
                    }
                }
            });

            $("#branch-select").on('change', function() {
                selectedValue = $(this).val();
                if (selectedValue == "") {
                    jQuery("#pos_roles").html("<option value=''>Please select a branch first</option>");
                    console.log("no value selected");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo sfConfig::get("app_admin_url"); ?>user/getPosRoles",
                        data: {shop_id: selectedValue},
                        cache: false
                    }).done(function(data) {
                        jQuery("#pos_roles").html(data);
                    });

                }

            });
            
            
               $('#user_is_super_user').click(function(){
    if (this.checked) {
        $('.emailNotificationContainer').show();
    }else{
      $('.emailNotificationContainer').hide();
     
    }
       });  
            
            
        });
    </script>
    <form action="<?php echo url_for('user/edit?id=' . $form->getObject()->getId()) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?> id="frmUser">
        <div  class="backimgDiv">
            <?php
            //var_dump($form->getErrorSchema()->getErrors());
//            echo "<hr/>";
//            $errors = $form->getErrorSchema()->getErrors();
//            if (count($errors) > 0){
//                echo 'List of Errors:' . '<br>'; 
//                foreach ($errors as $name => $error) {
//                    echo $name . ': ' . $error . '<BR>';
//                }
//            }
            ?>
            <?php if ($redirect_shop_id != "" && $redirect_shop_id > 0) { ?>

                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/view?id=<?php echo $redirect_shop_id; ?>'" value="Cancel" class="btn btn-cancel" />
            <?php } else {
                ?>
                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/index';" value="Close" class="btn btn-cancel"/>

            <?php } ?>

            <input type="submit" value="Save" class="btn btn-primary" />
            <input type="hidden" value="<?php echo $redirect_shop_id; ?>" name="redirect_shop_id" />
        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('message')): ?>
                <div class="alert alert-success">
                    <?php echo $sf_user->getFlash('message') ?>
                </div>
            <?php endif; ?>
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>

            <?php if (!$form->getObject()->isNew()): ?>
                <input type="hidden" name="sf_method" value="put" />
            <?php endif; ?>
            <input type="hidden" name="user[updated_by]" value="<?php echo $user_id; ?>" />
            <?php echo $form->renderGlobalErrors() ?>
            <div class="lblleft"><?php echo $form['name']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['name']->renderError() ?>
                    <?php echo $form['name']->render(array('class' => "form-control", 'required' => 'required')) ?></div>
            </div> 
            <div class="lblleft"><?php echo $form['sur_name']->renderLabel() ?>:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['sur_name']->renderError() ?>
                    <?php echo $form['sur_name']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft"><?php echo $form['address']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['address']->renderError() ?>
                    <?php echo $form['address']->render(array('class' => "form-control", 'required' => 'required')) ?></div>
            </div> 
            <div class="lblleft"><?php echo $form['zip']->renderLabel() ?>:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['zip']->renderError() ?>
                    <?php echo $form['zip']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 

            <div class="lblleft"><?php echo $form['city']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['city']->renderError() ?>
                    <?php echo $form['city']->render(array('class' => "form-control", 'required' => 'required')) ?></div>
            </div> 
            <br clear="all" />
            <div class="lblleft"><?php echo $form['country']->renderLabel() ?>:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['country']->renderError() ?>
                    <?php echo $form['country']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 

            <div class="lblleft"><?php echo $form['tel']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['tel']->renderError() ?>
                    <?php echo $form['tel']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft"><?php echo $form['mobile']->renderLabel() ?>:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['mobile']->renderError() ?>
                    <?php echo $form['mobile']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 

            <div class="lblleft"><?php echo $form['pin']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['pin']->renderError() ?>
                    <?php echo $form['pin']->render(array('class' => "form-control digits", 'required' => 'required', 'minlength' => "4")) ?>
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft"><?php echo $form['password']->renderLabel() ?>:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['password']->renderError() ?>
                    <?php echo $form['password']->render(array('class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 

            <div class="lblleft"><?php echo $form['email']->renderLabel() ?> :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <?php echo $form['email']->renderError() ?>
                    <?php echo $form['email']->render(array('type' => "email", 'class' => "form-control", 'required' => 'required')) ?>
                </div>
            </div> 
            <br clear="all" />
            <?php
            if ($user_role == 1) {
                ?> 
                <div class="lblleft"><?php echo $form['role_id']->renderLabel("User Web Role") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['role_id']->renderError() ?>
                        <?php echo $form['role_id']->render(array('class' => "form-control")) ?>
                    </div>
                </div> 

                <div class="lblleft"><?php echo $form['pos_user_role_id']->renderLabel("User POS Role") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['pos_user_role_id']->renderError() ?>
                        <!--    <?php echo $form['pos_user_role_id']->render(array('class' => "form-control", 'id' => 'pos_roles')) ?> -->
                        <select name="pos_user_role_id" class="form-control" id="pos_roles">
                            <option value="">Please select a Branch first</option>
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?php echo $role->getId(); ?>" 

                                        <?php
                                        if ($shopsSelectedUser->getPosRoleId() == $role->getId()) {
                                            echo "selected=selected";
                                        }
                                        ?> 

                                        ><?php echo $role->getName(); ?></option>
                                    <?php } ?>
                        </select>
                    </div>
                </div> 
                <br clear="all" />
                <div class="lblleft addedit">User Branch:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <div class="branchchoice">

                            <select data-placeholder="Choose a Branch..."   class="form-control" name="branches"  id="branch-select">
                                <option value="">Please Select a branch</option>

                                <?php if ($shopsSelectedUser) { ?>

                                    <?php foreach ($shops as $shop) { ?>


                                        <?php if ($shop->getId() == $shopsSelectedUser->getShopId()) { ?>

                                            <option value="<?php echo $shop->getId(); ?>"  selected><?php echo $shop->getBranchNumber(); ?></option> 
                                        <?php } else { ?>

                                            <option value="<?php echo $shop->getId(); ?>"   ><?php echo $shop->getBranchNumber(); ?></option> 

                                            <?php
                                        }
                                    }
                                } else {
                                    ?>

                                    <?php foreach ($shops as $shop) { ?>

                                        <option value="<?php echo $shop->getId(); ?>"   ><?php echo $shop->getBranchNumber(); ?></option> 

                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div> 
            <?php } else {
                ?>
                <input type="hidden" name="user[role_id]" value="<?php echo $user->getRoleId(); ?>" />
                <input type="hidden" name="user[pos_user_role_id]" value="<?php echo $user->getPosUserRoleId(); ?>" />
            <?php }
            ?>  

            <?php if ($curr_user->getIsSuperUser()): ?>
                <div class="lblleft addedit"><?php echo $form['pos_super_user']->renderLabel("POS Administrator") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php if ($shopsSelectedUser) { ?>
                            <input type="checkbox"    name="pos_super_user" <?php if ($shopsSelectedUser->getPosSuperUser() == 1) echo "checked=checked"; ?> />
                        <?php }else { ?>
                            <input type="checkbox"    name="pos_super_user" />
                        <?php } ?>
                    </div>
                </div>
                <br clear="all" />
                <div class="lblleft addedit"><?php echo $form['is_super_user']->renderLabel("Administrator") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['is_super_user']->renderError() ?>
                        <?php echo $form['is_super_user']->render(array('class' => "")) ?>
                    </div>
                </div> 
                
 
            <?php endif; ?>
            <br clear="all" />
            
            <div class="emailNotificationContainer" <?php if($user->getIsSuperUser()){ ?>style="display: block;"<?php }else{ ?> style="display: none;"  <?php } ?>     >
  <br clear="all" />  <br clear="all" />
            <h2>Email Notification</h2>
               <div class="lblleft addedit"><?php echo $form['deliverynote_ok_email']->renderLabel("DeliveryNote Ok") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['deliverynote_ok_email']->renderError() ?>
                        <?php echo $form['deliverynote_ok_email']->render(array('class' => "")) ?>
                           
                    </div>
                </div>
                <div class="lblleft addedit"><?php echo $form['bookout_ok_email']->renderLabel("BookOut Ok") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['bookout_ok_email']->renderError() ?>
                        <?php echo $form['bookout_ok_email']->render(array('class' => "")) ?>
                           
                    </div>
                </div>
                
             <br clear="all" />
              <div class="lblleft addedit"><?php echo $form['deliverynote_change_email']->renderLabel("DeliveryNote Change") ?>:</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['deliverynote_change_email']->renderError() ?>
                        <?php echo $form['deliverynote_change_email']->render(array('class' => "")) ?>
                    </div>
                </div> 
               
                <div class="lblleft addedit"><?php echo $form['bookout_change_email']->renderLabel("BookOut Change") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['bookout_change_email']->renderError() ?>
                        <?php echo $form['bookout_change_email']->render(array('class' => "")) ?>
                    </div>
                </div> 
              <br clear="all" />
               <div class="lblleft addedit"><?php echo $form['sale_email']->renderLabel("Sale") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['sale_email']->renderError() ?>
                        <?php echo $form['sale_email']->render(array('class' => "")) ?>
                           
                    </div>
                </div>
                 <div class="lblleft addedit"><?php echo $form['bookout_sync_email']->renderLabel("BookOut Sync") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['bookout_sync_email']->renderError() ?>
                        <?php echo $form['bookout_sync_email']->render(array('class' => "")) ?>
                    </div>
                </div> 
               
              <br clear="all" />
                <div class="lblleft addedit"><?php echo $form['daystart_email']->renderLabel("DayStart") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['daystart_email']->renderError() ?>
                        <?php echo $form['daystart_email']->render(array('class' => "")) ?>
                    </div>
                </div> 
               <div class="lblleft addedit"><?php echo $form['setting_email']->renderLabel("Setting Change ") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['setting_email']->renderError() ?>
                        <?php echo $form['setting_email']->render(array('class' => "")) ?>
                    </div>
                </div> 
             
                <br clear="all" />
               <div class="lblleft addedit"><?php echo $form['dayend_email']->renderLabel("DayEnd") ?>:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <?php echo $form['dayend_email']->renderError() ?>
                        <?php echo $form['dayend_email']->render(array('class' => "")) ?>
                           
                    </div>
                </div>
                 </div>
               
            <?php echo $form->renderHiddenFields() ?>          
            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>
</div>