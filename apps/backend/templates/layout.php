<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <?php include_title() ?>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <META HTTP-EQUIV="Expires" CONTENT="-1">
        <!--  
              <link rel="shortcut icon" href="<?php echo sfConfig::get('app_web_url'); ?>images/favicon.ico" type="image/x-icon" /> 
        -->
        <style type="text/css" title="currentStyle">
            @import "<?php echo sfConfig::get("app_web_url")?>/media/css/demo_page.css";
            @import "<?php echo sfConfig::get("app_web_url")?>media/css/demo_table.css";
        </style>  
    </head>
    <body>
 <?php
    
        $modulName = $sf_context->getModuleName();

        $actionName = $sf_context->getActionName();
//     echo $modulName;
//     echo '<br />';
//     echo $actionName;
?>
  	<div id="wrap">
  	
      <?php if($sf_user->isAuthenticated()): 
            $sf_user->setCulture('en'); 
           $permissions = $sf_user->getAttribute('user_permissions', '', 'backendsession');
//           var_dump($permissions);die;
           $user_id  = $sf_user->getAttribute('user_id', '', 'backendsession');
           $loggedin_user = UserPeer::retrieveByPK($user_id);
           if($loggedin_user->getIsSuperUser()){
               $user_role =  "Admin";
           }else{
               $user_role = $loggedin_user->getRole();
           }
      ?>
            
              <script>
            $(document).ready(function() {    
              $('#leftSideNav').slimscroll({
                    height: 'auto'
                });
              $( "#setting_menu_list" ).hide();  
              $("#setting_menu").click(function() {
                $( "#setting_menu_list" ).toggle('slow');
             });  
            });
            </script>
            
       <div id="header">  
            <div class="logo">
  		<?php //echo image_tag(sfConfig::get("app_web_url").'images/logo-inner.png') ?>
            </div> 
           <div class="logout"><span class="usr_name"><?php echo $loggedin_user->getName(); ?><br/><span class="usr_role"><?php echo $user_role;?></span></span><a href="<?php echo sfConfig::get("app_admin_url"); ?>user/logout" class="usericon"></a></div>
           <div class="clr"></div>
  	</div> 
       <div class="clr"></div>
        <div class="content">
            <div class="leftSide">
                   <div id="leftSideNav"> 
                <div class="mainhead">Main Menu</div>
                <ul class="nav nav-pills nav-stacked">
<!--                    <li class="<?php //echo ($modulName=="user" && $actionName=="dashboard")?'active active_dashboard':'dashboard'?>">
                          <a href="<?php //echo sfConfig::get("app_admin_url"); ?>user/dashboard">Dashboard</a>
                    </li>-->
                    <?php 
                    if(in_array("1_index",$permissions)){ //|| in_array("1_view",$permissions)
                    ?> 
                       <li class="<?php echo (($modulName=="items"&&$actionName=="index") || ($modulName=="items"&&$actionName=="view")|| ($modulName=="items"&&$actionName=="edit") || ($modulName=="items"&&$actionName=="add"))?'active active_items':'items'?>">
                          <a href="<?php echo sfConfig::get("app_admin_url"); ?>items/index">Items</a>
                       </li>
                    <?php         
                    }
                    if(in_array("4_index",$permissions)){
                    ?>
                    <li class="<?php echo (($modulName == "delivery_notes" && $actionName == "index") || ($modulName == "delivery_notes" && $actionName == "view")|| ($modulName == "delivery_notes" && $actionName == "edit")|| ($modulName == "delivery_notes" && $actionName == "add")) ? 'active active_delivery' : 'delivery' ?>">
                       <a href="<?php echo sfConfig::get("app_admin_url"); ?>delivery_notes/index"> Delivery Notes</a>
                    </li>
                    <?php
                    }
                     if(in_array("4_index",$permissions)){
                    ?>
                    <li class="<?php echo (($modulName == "bookoutNotes" && $actionName == "index") || ($modulName == "bookoutNotes" && $actionName == "view")|| ($modulName == "bookoutNotes" && $actionName == "edit")|| ($modulName == "bookoutNotes" && $actionName == "add")) ? 'active active_delivery' : 'delivery' ?>">
                       <a href="<?php echo sfConfig::get("app_admin_url"); ?>bookoutNotes/index"> Bookout Notes</a>
                    </li>
                    <?php
                    }
                    if(in_array("7_index",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="inventory"&&$actionName=="index") || ($modulName=="inventory"&&$actionName=="inventorySoldDetail") || ($modulName=="inventory"&&$actionName=="deliveries"))?'active active_inventory':'inventory'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>inventory/index">Inventory</a>
                        </li>
                    <?php
                    }
                    if(in_array("8_index",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="transactions"&&$actionName=="index"))?'active active_sale':'sale'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>transactions/index">Sales</a>
                        </li>
                      <li class="<?php echo (($modulName=="transactions"&&$actionName=="transaction"))?'active active_sale':'sale'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>transactions/transaction">Transactions</a>
                        </li>
                     <li class="<?php echo (($modulName=="transactions"&&$actionName=="reports"))?'active active_sale':'sale'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>transactions/reports">Reports</a>
                        </li>
                    <?php
                    }
                    if(in_array("10_addItems",$permissions)){
                    ?>
                       <li class="<?php echo ($modulName=="items"&&$actionName=="addItems")?'active active_imports':'imports'?>">
                          <a href="<?php echo sfConfig::get("app_admin_url"); ?>items/addItems">Import</a>
                      </li>
                    <?php 
                    }
                    if(in_array("11_itemExport",$permissions)){
                    ?>
                      <li class="<?php echo ($modulName=="items"&&$actionName=="itemExport")?'active active_imports':'imports'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>items/itemExport"> Export</a>
                      </li>
                    
                    <?php 
                    }
                     if(in_array("12_index",$permissions) || in_array("12_new",$permissions) || in_array("12_edit",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="promotion"&&$actionName=="index") || ($modulName=="promotion"&&$actionName=="new") || ($modulName=="promotion"&&$actionName=="edit"))?'active active_users':'user-mng'?>">
                           <a href="<?php echo sfConfig::get("app_admin_url"); ?>promotion/index">Promotions</a>
                        </li>
                    <?php
                    }
                   
                    ?>
                    <li class="<?php echo ($modulName=="voucher")?'active active_imports':'imports'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>voucher/index"> Gift Vouchers</a>
                      </li>
                </ul>
                <div class="mainhead">Settings</div>
                <ul class="nav nav-pills nav-stacked">
                    <?php
                    if(in_array("5_index",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="shops"&&$actionName=="index") || ($modulName=="shops"&&$actionName=="view") || ($modulName=="shops"&&$actionName=="edit") || ($modulName=="shops"&&$actionName=="add"))?'active active_shops':'shops'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>shops/index">Branches</a>
                        </li>
                    <?php
                    }
                     if(in_array("2_index",$permissions) || in_array("2_new",$permissions) || in_array("2_edit",$permissions) || in_array("2_delete",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="user"&&$actionName=="index") || ($modulName=="user"&&$actionName=="new") || ($modulName=="user"&&$actionName=="edit"))?'active active_users':'user-mng'?>">
                           <a href="<?php echo sfConfig::get("app_admin_url"); ?>user/index">User Management</a>
                        </li>
                    <?php
                    }
                    if(in_array("3_index",$permissions) || in_array("3_createRole",$permissions) || in_array("3_editRole",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="role"&&$actionName=="index") || ($modulName=="role"&&$actionName=="createRole") || ($modulName=="role"&&$actionName=="editRole"))?'active active_roles':'roles'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>role/index">Roles</a>
                        </li>
                    <?php
                    } if(in_array("3_index",$permissions) || in_array("3_createRole",$permissions) || in_array("3_editRole",$permissions)){
                    ?>
                        <li class="<?php echo (($modulName=="pos_role"&&$actionName=="index") || ($modulName=="pos_role"&&$actionName=="createRole") || ($modulName=="pos_role"&&$actionName=="editRole"))?'active active_roles':'roles'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>pos_role/index">POS Roles</a>
                        </li>
                    <?php
                    }
                    if(in_array("9_index",$permissions) || in_array("9_view",$permissions) || in_array("9_new",$permissions) || in_array("9_edit",$permissions)){
                    ?>
                        <li class="<?php echo ($modulName=="cron_jobs")?'active active_cron':'cron'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>cron_jobs/index">Schedule Jobs</a>
                        </li>
                    <?php
                    }
                    if($loggedin_user->getIsSuperUser()){
                    ?>
                        <li class="<?php echo (($modulName=="system_config"&&$actionName=="index") || ($modulName=="system_config"&&$actionName=="edit") || ($modulName=="system_config"&&$actionName=="add"))?'active active_system':'system'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>system_config/index">Global Settings</a>
                        </li>
                    <?php     
                    }
                    if($loggedin_user->getIsSuperUser()){
                    ?>
                        <li class="<?php echo (($modulName=="gcmRequest"&&$actionName=="index") || ($modulName=="gcmRequest"&&$actionName=="edit") || ($modulName=="gcmRequest"&&$actionName=="add"))?'active active_system':'system'?>">
                         <a href="<?php echo sfConfig::get("app_admin_url"); ?>gcmRequest/index">Gcm Request</a>
                        </li>
                    <?php     
                    }
                    ?>
                   
                </ul>
                <br/> <br/> <br/> 
                </div>
              </div>
            <div class="rightSide">
                <?php echo $sf_content ?>
            </div>
            <div class="clr"></div>
        </div>
         <div class="clr"></div>
           <?php if($sf_user->isAuthenticated()): ?>
        <div id="footer_container"> 
           <div class="footer">
               <span style="float:left">Scarletbigoux &copy copyright <?php echo date("Y")?></span>
               <span class="version">Version 0.2.4</span>
           </div>
        </div>   
           <?php
             endif;
      ?>     
      <?php else:?>
        <div class="content">   <?php echo $sf_content ?> </div>
      <?php endif; ?> 
      <div class="clr"></div>   
    </div> <!--  end wrapper -->
    </body>
</html>
<?php
// Turn off all error reporting
error_reporting(0);
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// Report all errors except E_NOTICE
// This is the default value set in php.ini
error_reporting(E_ALL ^ E_NOTICE);

// For PHP >=5.3 use: E_ALL & ~E_NOTICE

// Report all PHP errors (see changelog)
error_reporting(E_ALL);

// Report all PHP errors
error_reporting(-1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
?>