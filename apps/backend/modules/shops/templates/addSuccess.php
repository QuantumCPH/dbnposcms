<script type="text/javascript">
    $(document).ready(function() {
        $("#addshop").validate({
            rules: {
                branch_number: {
                    required: true,
                    digits: true,
                    remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>shops/validateBranch",
                        type: "get",
                        dataType: 'json'
                    }
                }
            }, messages: {
                branch_number: {
                    remote: "<?php echo __("Branch Number already exist."); ?>"
                }
            }

        });
        $(".languages").chosen({no_results_text: "Oops, nothing found!"});
    });
</script>
<form action="<?php echo sfConfig::get("app_admin_url"); ?>shops/addSubmit"  id="addshop" method="post"  enctype="multipart/form-data" >
    <div class="itemslist">
        <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;<span>Add Branch</span>

        </h1>
        <div  class="backimgDiv">
            <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/index';" value="Cancel" class="btn btn-cancel"/>
            <input type="submit" value="Add" class="btn btn-primary" />
        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>

            <div class="lblleft">Name :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="name" class="form-control required"></div>
            </div> 
            <div class="lblleft">Branch number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="branch_number"  class="form-control required"></div>
            </div> 
            <br clear="all" />        
            <div class="lblleft">Company number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="company_number"  class="form-control required"></div>
            </div> 
            <div class="lblleft">Password:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="password"  class="form-control required"></div>
            </div>
            <br clear="all" />         
            <div class="lblleft addedit">Address:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="address" class="form-control"></div>
            </div>
            <div class="lblleft addedit">Zip:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="zip" class="form-control"></div>
            </div> 
            <br clear="all" />
            <div class="lblleft addedit">Place:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="place" class="form-control"></div>
            </div>
            <div class="lblleft">Country:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="country"  class="form-control required"></div>
            </div> 
            <br clear="all" />        
            <div class="lblleft addedit">Tel:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="tel" class="form-control"></div>
            </div>
            <div class="lblleft addedit">Fax:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="fax" class="form-control"></div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Negative Sale:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="checkbox" name="negative_sale" class=""></div>
            </div> 
            <div class="lblleft addedit">Language:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6 selectLanguage">
                    <select name="languages" class="form-control languages">
                        <?php
                        $lanugages = LanguagesPeer::doSelect(new Criteria());
                        foreach ($lanugages as $lanugage) {
                            ?>
                            <option value="<?php echo $lanugage->getId() ?>"><?php echo $lanugage->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Time out:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="time_out" value="" class="form-control"></div>
            </div> 
            <div class="lblleft addedit">Start Value Sale Receipt:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6" id="sale_receipt_msg">
                    <input type="text" name="sale_receipt" id="sale_receipt" value="" class="form-control">
                </div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Start Value Return Receipt:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6" id="return_receipt_msg"><input type="text" name="return_receipt" id="return_receipt" value="" class="form-control"></div>
            </div> 
            <div class="lblleft addedit">Sale Receipt Format:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="saleFormat" class="form-control">
                        <?php
                        $receiptformats = ReceiptFormatsPeer::doSelect(new Criteria());
                        foreach ($receiptformats as $receiptformat) {
                            ?>
                            <option value="<?php echo $receiptformat->getId() ?>"><?php echo $receiptformat->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div> 
            <br clear="all" /> 
            <div class="lblleft addedit">Return Receipt Format:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="returnFormat" class="form-control">
                        <?php
                        $receiptformats = ReceiptFormatsPeer::doSelect(new Criteria());
                        foreach ($receiptformats as $receiptformat) {
                            ?>
                            <option value="<?php echo $receiptformat->getId() ?>"><?php echo $receiptformat->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div>
            <div class="lblleft addedit">Max Day End Attempts:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="max_day_end_attempts" class="form-control required">
                </div>
            </div>
            
            <div class="lblleft addedit">Employee Discount Type:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="discount_type_id" class="form-control">
                        <?php
                        $discountTypes = DiscountTypesPeer::doSelect(new Criteria());
                        foreach ($discountTypes as $discountType) {
                            ?>
                            <option value="<?php echo $discountType->getId() ?>"><?php echo $discountType->getName(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div>

            <div class="lblleft addedit">Employee Discount Value:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" value="0" name="discount_value" class="form-control"/>
                </div>
            </div>
            <br clear="all" /> 
            <div class="lblleft addedit">Receipt Header Position:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="receipt_header_position" class="form-control"> 
                        <option value="Center">Center</option>
                        <option value="Left">Left</option>
                        <option valu="Right">Right</option>
                    </select>
                   <!-- <input type="text" value="center" name="receipt_header_position" class="form-control"/> -->
                </div>
            </div>
            <div class="lblleft addedit">Receipt footer line 1:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="receipt_tax_statement_one" class="form-control"/>
                </div>
            </div>
            <br clear="all" />
            <div class="lblleft addedit">Receipt footer line 2:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="receipt_tax_statement_two" class="form-control"/>
                </div>
            </div>
            <div class="lblleft addedit">Receipt footer line 3:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="receipt_tax_statement_three" class="form-control"/>
                </div>
            </div>
            <br clear="all" />  
            <div class="lblleft addedit">Receipt Auto Print:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="checkbox" name="receipt_auto_print" class=""></div>
            </div> 
        </div>
    </div></form>