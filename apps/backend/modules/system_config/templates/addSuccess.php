<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/settings_title.png'?>" />&nbsp;<span>New Global Setting</span></h1>
    <?php if ($sf_user->hasFlash('error_user')): ?>
    <div class="alert alert-success">
        <h2><?php echo $sf_user->getFlash('error_user') ?></h2>
    </div>
    <?php endif;?>

<script type="text/javascript">
   $(document).ready(function() {
      $("#frmSetting").validate({
          rules: {
             "keys":{
                required: true,
                remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>system_config/validateKeys" ,
                    type: "get",
                    dataType: 'json'                   
                } 
            }
         },messages: {
           "keys":{
                remote: "<?php echo __("Check already exist."); ?>"
            }
         }
      });
  });
</script>
<form id="frmSetting" action="<?php echo url_for('system_config/addNew')?>" method="post">
<div  class="backimgDiv">
    <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>system_config/index';" value="Cancel" class="btn btn-cancel"/>
    <input type="submit" value="Save" class="btn btn-primary" />
</div>
<div class="regForm listviewpadding"><br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <div class="lblleft">Checks:&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
            <input type="text" value="" name="keys" class="form-control" required="required" />
        </div>
    </div>    
    <div class="lblleft">Values:&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
            <select name="values" class="form-control" required="required">
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
    </div>   
</div>
</form>

</div>