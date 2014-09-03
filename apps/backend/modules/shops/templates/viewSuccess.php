<script type="text/javascript">
    $(document).ready(function() {

        $.datepicker.regional[""].dateFormat = 'yy-mm-dd 00:00:00';
        $.datepicker.setDefaults($.datepicker.regional['']);
        var oTable = $('#myTablesale').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_byshop_sales.php",
            "sPaginationType": "full_numbers",
            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
            "fnServerParams": function(aoData) {
                aoData.push({"name": "shop_id", "value": <?php echo $shops->getId(); ?>});
            }

        }).columnFilter({aoColumns: [null, null,{type: "text"}, null,null, {type: "text"},{type: "text"}, {type: "text"}, {type: "text"}, {type: "text"},
                {type: "date-range"},
                null

            ]

        });
        oTable.fnSort([[10, 'desc']]);
        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });

        jQuery("#myTablesale_filter").hide();

        $("#sales-head").click(function() {
            var toggle_switch = $("#headingarrow2 img");
            $("#sales-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });


        $("#item-head").click(function() {
            var toggle_switch = $("#headingarrow3 img");
            $("#role-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });
 $("#stock-head").click(function() {
            var toggle_switch = $("#headingarrow2 img");
            $("#stock-block").toggle(function() {
                if ($(this).css('display') == 'none') {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png"));
                } else {
                    toggle_switch.attr("src", toggle_switch.attr("src").replace("<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png", "<?php echo sfConfig::get("app_web_url") ?>images/arrow-down.png"));
                }
            });
        });





    });



</script>


<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;Branch Detail

    </h1>

    <div class="backimgDiv">
        <?php if (gf::getPreviousId($shops->getId())) { ?>
            <a href="<?php echo url_for("shops/view?id=" . gf::getPreviousId($shops->getId())) ?>" class="btn btn-primary viewPrev">Previous </a>
        <?php } ?>  
        <?php if (gf::getNextId($shops->getId())) { ?>
            <a href="<?php echo url_for("shops/view?id=" . gf::getNextId($shops->getId())) ?>" class="btn btn-primary viewNext">Next </a>
        <?php } ?>

        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/index';" value="Back" class="btn btn-cancel"/>
        <input type="button" onclick="document.location.href = '<?php echo url_for(sfConfig::get("app_admin_url") . 'shops/edit?id=' . $shops->getId()); ?>';" value="Edit" class="btn btn-primary"/>
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
        <div class="lblleft">Name :&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getName() ?></div>
        </div> 
        <div class="lblleft">Branch number:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getBranchNumber() ?></div>
        </div> 
        <br clear="all" />        
        <div class="lblleft">Company number:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getCompanyNumber() ?></div>
        </div> 
                <div class="lblleft">Password:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getPassword() ?></div>
        </div> 
        
        <br clear="all" />         
        <div class="lblleft">Created by:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php
                if ($shops->getCreatedBy() != "") {
                    $uc = new Criteria();
                    $uc->add(UserPeer::ID, $shops->getUpdatedBy());
                    $user = UserPeer::doSelectOne($uc);
                    if ($user) {
                        echo $user->getName();
                    }
                }
                ?></div>
            
        </div>
        <div class="lblleft">Configured at:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getConfiguredAt(); ?></div>
        </div> 
        <br clear="all" />
<div class="lblleft">Is configured:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php
                if ($shops->getIsConfigured() == 1) {
                    echo "yes";
                } else {
                    echo "No";
                }
                ?></div>
        </div>

        <div class="lblleft">Status:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getStatusId() ?></div>
        </div>
        <br clear="all" /> 
        <div class="lblleft">Address:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getAddress() ?></div>
        </div>        
        <div class="lblleft">Zip:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getZip() ?></div>
        </div>
        <br clear="all" /> 
        <div class="lblleft">Place:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getPlace() ?></div>
        </div> 

        <div class="lblleft">Country:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getCountry() ?></div>
        </div>
        <br clear="all" /> 
        <div class="lblleft">Tel:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getTel() ?></div>
        </div> 
        <div class="lblleft">Fax:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getFax() ?></div>
        </div>
        <br clear="all" />

        <div class="lblleft">Created at:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getCreatedAt() ?></div>
        </div>   
        <div class="lblleft">Negative Sale:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getNegativeSale() ? "Yes" : "No" ?></div>
        </div>
        <br clear="all" />

        <div class="lblleft">Language:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo LanguagesPeer::retrieveByPK($shops->getLanguageId())->getTitle() ?></div>
        </div> 
        <div class="lblleft">Time out:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getTimeOut() ?></div>
        </div>
        <br clear="all" />

        <div class="lblleft">Start Value Sale Receipt:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getStartValueSaleReceipt() ?></div>
        </div> 
        <div class="lblleft">Start Value Bookout:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getStartValueBookout() ?></div>
        </div> 
        <br clear="all" />

        <div class="lblleft">Sale Receipt Format:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php if ($shops->getSaleReceiptFormatId()) echo ReceiptFormatsPeer::retrieveByPK($shops->getSaleReceiptFormatId())->getTitle(); ?></div>
        </div>  
  <div class="lblleft">Bookout number Format:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php if ($shops->getBookoutFormatId()) echo ReceiptFormatsPeer::retrieveByPK($shops->getBookoutFormatId())->getTitle() ?></div>
        </div>
        
        
        <br clear="all" />
        <div class="lblleft">Status:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo ($shops->getStatusId() == "" || $shops->getStatusId() == 3) ? "Active" : "Inactive"; ?></div>
        </div>
        <div class="lblleft">Employee Discount Type:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php if ($shops->getDiscountTypeId()) echo DiscountTypesPeer::retrieveByPK($shops->getDiscountTypeId())->getName() ?></div>
        </div>
        <br clear="all" />

        <div class="lblleft">Employee Discount Value:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getDiscountValue(); ?></div>
        </div>
        <div class="lblleft">Max Day End Attempts:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getMaxDayEndAttempts(); ?></div>
        </div>    
        <br clear="all" />
        <div class="lblleft">Receipt Header Position:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getReceiptHeaderPosition(); ?></div>
        </div> 
        <div class="lblleft">Receipt footer line 1:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getReceiptTaxStatmentOne(); ?></div>
        </div> 
        <br clear="all" />
        <div class="lblleft">Receipt footer line 2:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getReceiptTaxStatmentTwo(); ?></div>
        </div> 
        <div class="lblleft">Receipt footer line 3:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getReceiptTaxStatmentThree(); ?></div>
        </div> 
        <br clear="all" />
        <div class="lblleft">Receipt Auto Print:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $shops->getReceiptAutoPrint() ? "Yes" : "No" ?></div>
        </div>
    </div>
</div>
<br clear="all" /><br />
<h1 >Branch Users
    <?php if ($shops->getStatusId() == 3) { ?> 
        <div style="float:right;" >
            <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/new?redirect_shop_id=<?php echo $shops->getId() ?>';" value="Add New User" class="btn btn-primary" style="margin-right: 50px;"/>

            <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/assignExistingUser?shop_id=<?php echo $shops->getId() ?>';" value="Assign Existing Users" class="btn btn-primary"/>
        </div>
    <?php } ?>
</h1>
<div class="regForm">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Pin</th>
                <th>Password</th>
                <th>POS Role</th>
                <th>POS Admin</th>
           <!--    <th>Edit</th>-->
                <th>Action</th>
            </tr>

        </thead>
        <tbody>
            <?php $incrment = 1; ?>
            <?php foreach ($user_list as $user): ?>
                <?php
                if ($user->getUser()->getPinStatus() != 3) {
                    $class = 'clsRed ';
                } else {
                    $class = "";
                }
                if ($incrment % 2 == 0) {
                    $class.= 'even';
                } else {
                    $class.= 'odd';
                }
                ?>
                <tr class='<?php echo $class; ?>'>
                    <td><?php echo $user->getUser()->getName(); ?></td>
                    <td><?php echo $user->getUser()->getPin(); ?></td>
                    <td>******</td>

                    <td><?php
                        if ($user->getPosRoleId()) {
                            $posrole = PosRolePeer::retrieveByPK($user->getPosRoleId());
                            if ($posrole)
                                echo $posrole->getName();
                        }
                        ?></td>

                    <td><?php echo ($user->getPosSuperUser()) ? "Yes" : "No"; ?></td>
                <!--    <td></td>-->
                    <td>
                        <a href="<?php echo url_for('user/edit') ?>?id=<?php echo $user->getUserId() ?>&redirect_shop_id=<?php echo $shops->getId(); ?>"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/edit_icon.png" /></a>
                        <a href="<?php echo sfConfig::get("app_admin_url") . 'shops/shopUserDelete'; ?>?userid=<?php echo $user->getUserId() ?>&shopid=<?php echo $user->getShopId(); ?>" onclick="return confirm('Are you sure to delete user from this shop?');"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/delete_icon.png" /></a></td>
                </tr>
                <?php $incrment++; ?>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>
<br clear="all" />
<h1 class="items-head" id="sales-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Sale
    <span id="headingarrow2" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
</h1>  
<div class="regForm" id="sales-block" style="display: block;">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTablesale" >
        <thead>
            <tr>
               <th>Branch</th>
                <th>Sold Price</th>
                <th>Invoice no</th>
                <th>Qty</th>
                <th>Sell. Price</th>
                <th>User</th>
                <th>Item</th>
                 <th>Desc</th>
                <th>Status</th>
                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>


            </tr>
        </thead>
        <tbody>


        </tbody>
        <tfoot>
            <tr>
                 <th>Branch</th>
                <th>Sold Price</th>
                <th>Invoice no</th>
                <th>Qty</th>
                <th>Sell. Price</th>
                <th>User</th>
                <th>Item</th>
                 <th>Desc</th>
                <th>Status</th>
                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>


            </tr>
        </tfoot>
    </table>
</div> 
<br clear="all" />
<br clear="all" />
<h1 class="items-head" id="item-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/role_title.png' ?>" />&nbsp;POS Roles
    <?php if ($shops->getStatusId() == 3) { ?>
        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>pos_role/createRole?redirect_shop_id=<?php echo $shops->getId() ?>';" value="Add Roles" class="btn btn-primary" style="margin-right: 50px;"/>
    <?php } ?>

    <span id="headingarrow3" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
</h1>  
<div class="regForm" id="role-block" style="display: block;">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTablesale" >
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($posRoles as $posRole) {
                ?>
                <tr>    
                    <td><?php echo $posRole->getName(); ?></td>
                    <td><a href="<?php echo url_for('pos_role/editRole') ?>?id=<?php echo $posRole->getId(); ?>&redirect_shop_id=<?php echo $shops->getId(); ?>"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/edit_icon.png" /></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div> 
<br clear="all" />

<h1 class="cash-head" id="cash-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/role_title.png' ?>" />&nbsp;Daily Cash Log


    <span id="headingarrow4" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
</h1>  
<div>Please Select a DAY START date.<div id="test"></div></div>
<br clear="all" />
<div id="daily-cash-list"></div>

<br clear="all" />
<br clear="all" />
 
<h1 class="items-head" id="stock-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Stock Detail
    <span id="headingarrow2" class="headingarrow"><img src="<?php echo sfConfig::get("app_web_url") ?>images/arrow-right.png" /></span>
</h1>
<div class="regForm" id="stock-block" style="display: block;">
 <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTablesale" >
        <thead>
            <tr>
                <th>Stock Id</th>
                 <th>Stock Type</th>
                <th>Created At</th>
                  <th>Adjust Stock</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($shopStocks as $shopStock) {
                ?>
                <tr>    
                    <td><a target="_blank" href="<?php echo url_for('transactions/stockReport') ?>?id=<?php echo $shopStock->getId(); ?>"><?php echo $shopStock->getStockId(); ?></a></td>
                        <td><?php echo $shopStock->getStockType(); ?></td> 
                    <td><?php echo $shopStock->getCreatedAt('Y-m-d '); ?></td>
                         <td><a target="_blank" href="<?php echo url_for('transactions/stockAdjust') ?>?id=<?php echo $shopStock->getId(); ?>"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/edit_icon.png" /></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<br clear="all" />
<br clear="all" />

<script>
    jQuery(function() {
        jQuery("#test").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            onSelect: function(date, obj) {
                var request = jQuery.ajax({
                    url: "<?php echo sfConfig::get('app_web_url') ?>/backend.php/shops/getDailyCashList",
                    type: "POST",
                    data: {date: date, shop_id:<?php echo $shops->getId(); ?>},
                    dataType: "html"
                });
                request.done(function(msg) {
                    jQuery("#daily-cash-list").html(msg);
                });
                request.fail(function(jqXHR, textStatus) {
                    console.log("Request failed: " + textStatus);
                });
            }
        });



        $('.ui-datepicker-current-day').click();



    });
</script>