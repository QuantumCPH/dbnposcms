<?php use_helper('I18N') ?><?php //if($request->getMethod() != 'post') $is_postback = true; ?>

<div style="">
<form id="form1" action="<?php echo url_for('user/login') ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>  

    <?php echo $form->renderGlobalErrors() ?>
    <div class="login-area-div" >
    
        <div class="login-logo"> 
            
           <img src="<?php echo sfConfig::get('app_web_url');?>images/logo_login.png">
        </div>
            
        
        
        <div class="admin-login-container">
        
            <div>
                
                    <?php
	      if(($sf_request->getMethod()=='POST')){
            ?>
            <span class="fieldError">    
             <?php   echo $form['pin']->renderError();   ?>
            </span>
            <?php
              }
	     ?> 
                <?php echo $form['pin']->render(array('placeholder'=>Pin, 'class' => 'form-control') )  ?>   
            </div>
        
        
         <div>
           <?php   if(($sf_request->getMethod()=='POST')){ ?>
            <span class="fieldError"> 
            	<?php echo $form['password']->renderError() ?>                
            </span>  
             <?php } ?>    
                
                  <?php echo $form['password']->render(array('placeholder'=>Password, 'class' => 'password-input') )  ?> <a href="<?php echo sfConfig::get("app_customer_url");?>pScripts/forgot" class="forgot">Forgot your Password?</a>
            </div>
        
        <br clear="all" /><br clear="all" />              
            <div class="submitButton">
                <button  type="submit" class="btn btn-primary"><?php echo __('Login') ?></button>
            </div> 
        </div>
 
         
        </div>
            <div class="right"></div>
      
</form>
</div>
<div class="clear"></div>