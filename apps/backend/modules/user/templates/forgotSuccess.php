<?php use_helper('I18N') ?>
<script type="text/javascript">
   $(document).ready(function() {
      $("#form1").validate({
          rules: {
             "user[email]":{
                required: true,
                email: true,
                remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>user/validateEmailOnForgot" ,
                    type: "get",
                    dataType: 'json'                   
                } 
            }
         },messages: {
           "user[email]":{
                remote: "<?php echo __("Email not exist."); ?>"
            }
         }
      })
  });
</script>
<center>
<?php use_helper('I18N') ?><?php //if($request->getMethod() != 'post') $is_postback = true; ?>
                    <?php if ($sf_user->hasFlash('reset_message')): ?>
                    <div class="alert alert-warning" style="width:50%;">
                        <?php echo $sf_user->getFlash('reset_message') ?>
                    </div>
                    <?php endif;?>
<div style="">
<form id="form1" action="<?php echo url_for('user/forgot') ?>" method="post">  
    <div class="login-area-div" >    
        <div class="login-logo">             
           <img src="<?php echo sfConfig::get('app_web_url');?>images/logo_login.png">
        </div>        
        <div class="admin-login-container">
            <div>   
                <input type="email" name="user[email]" class="form-control" placeholder="Email" />
                   
            </div>                   
            <div class="submitButton">
                <button  type="button" class="btn btn-cancel" onclick="<?php echo sfConfig::get("app_admin_url");?>"><?php echo __('Back') ?></button> &nbsp; <button  type="submit" class="btn btn-primary"><?php echo __('Reset Password') ?></button>
            </div> 
        </div>
 
         
        </div>
            <div class="right"></div>
      
</form>
</div>
<div class="clear"></div>
                
               

</center>