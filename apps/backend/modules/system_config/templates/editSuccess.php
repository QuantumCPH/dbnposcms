<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/settings_title.png'?>" />&nbsp;<span>Edit Global Setting</span></h1>
    <?php if ($sf_user->hasFlash('error_user')): ?>
    <div class="alert alert-success">
        <h2><?php echo $sf_user->getFlash('error_user') ?></h2>
    </div>
    <?php endif;?>
<script type="text/javascript">
   $(document).ready(function() {
      $("#frmSetting").validate();
  });
</script>
<form id="frmSetting" action="<?php echo url_for('system_config/update')?>" method="post">
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
    <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <input type="hidden" value="<?php echo $setting->getId();?>" name="id" />
    <div class="lblleft">Checks:&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
            <input type="text" value="<?php echo $setting->getKeys();?>" name="keys" class="form-control" required="required" readonly="readonly" />
        </div>
    </div>    
    <div class="lblleft">Values:&nbsp;</div>
    <div class="lblright">
        <div class="col-lg-6">
          <?php
          if(strtolower($setting->getKeys())=="language"){
              $languages = LanguagesPeer::doSelect(new Criteria);
          ?>                   
             <select name="values" class="form-control" required="required">
         <?php    
              foreach($languages as $language){
         ?>  
                <option value="<?php echo $language->getId(); ?>" <?php echo $language->getId()==$setting->getValues()?"selected='selected'":''?>><?php echo $language->getTitle();?></option>
          <?php
              }
         ?>      
             </select>  
         <?php
          }elseif(strtolower($setting->getKeys())=="currency"){
              $currenies = CurrenciesPeer::doSelect(new Criteria);
          ?>                   
             <select name="values" class="form-control" required="required">
         <?php    
              foreach($currenies as $curreny){
         ?>  
                <option value="<?php echo $curreny->getId(); ?>" <?php echo $curreny->getId()==$setting->getValues()?"selected='selected'":''?>><?php echo $curreny->getCurrencyTitle();?></option>
          <?php
              }
         ?>      
             </select>  
         <?php
          }elseif(strtolower($setting->getKeys())=="vat percentage"){ 
              ?>
            <input type="text" value="<?php echo $setting->getValues();?>" name="values" class="form-control required number"   />  
            <?php
          }else{
        ?>
             <select name="values" class="form-control" required="required">
                <option value="Yes" <?php echo strtolower($setting->getValues())=="yes"?"selected='selected'":''?>>Yes</option>
                <option value="No" <?php echo strtolower($setting->getValues())=="no"?"selected='selected'":''?>>No</option>
            </select>
        <?php      
          }
        ?>  
            
        </div>
    </div>   
</div>
</form>

</div>
