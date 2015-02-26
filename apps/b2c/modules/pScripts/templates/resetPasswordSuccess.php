<?php use_helper('I18N') ?>
<link rel="stylesheet" href="<?php echo sfConfig::get("app_web_url")?>css/poscms.css" />
<script type="text/javascript">
   $(document).ready(function() {
      $("#form1").validate({
          rules: {
             new_password:{
                    required: true
                },
                confirm_password:{
                    required: true,
                    equalTo: "#new_password"
                }
         },messages: {      
                confirm_password:{
                    equalTo: "<?php echo __("The passwords don’t match."); ?>"
                }
            }
      })
  });
</script>
<center>
<?php use_helper('I18N') ?><?php //if($request->getMethod() != 'post') $is_postback = true; ?>
                    
<div style="">
<form id="form1" action="<?php //echo url_for('user/forgot') ?>" method="post">  
    <div class="login-area-div" >    
        <div class="login-logo">             
           <img src="<?php echo sfConfig::get('app_web_url');?>images/logo_login.png">
        </div>        
        <div class="admin-login-container reset">
            <div>   
                <input type="password" name="new_password" class="form-control" placeholder="New Password" id="new_password" />
                   
            </div>   
            <div>   
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" />
                   
            </div> 
            <div class="submitButton">
                <button  type="submit" class="btn btn-primary"><?php echo __('Reset Password') ?></button>
            </div> 
        </div>
 
         
        </div>
            <div class="right"></div>
      
</form>
</div>
<div class="clear"></div>
                
               

</center>