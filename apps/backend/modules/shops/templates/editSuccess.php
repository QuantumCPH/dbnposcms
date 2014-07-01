
<script type="text/javascript">
    $(document).ready(function() {
        $("#Editshop").validate({
            rules: {
                branch_number: {
                    required: true,
                    digits: true,
                    remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>shops/validateEditBranch",
                        type: "get",
                        dataType: 'json',
                        data: {
                            id: function() {
                                return $('#shopid').val();
                            }
                        }
                    }
                }
            }, messages: {
                branch_number: {
                    remote: "<?php echo __("Branch Number already exist."); ?>"
                }
            }

        });
        $(".languages").chosen({no_results_text: "Oops, nothing found!"});
        $("#sale_receipt").focus(function() {
            if (!$("#sale_receipt_msg label").hasClass("error")) {
                $("#sale_receipt_msg").append('<label for="sale_receipt" class="error">If you change Receipt No. Please confirm with the POS the actual Receipt No otherwise there will be sync Problem !</label>');
            }
            if ($("#sale_receipt_msg label.error").css("display", "none")) {
                $("#sale_receipt_msg label.error").css("display", "");
            }
        });
        $("#sale_receipt").blur(function() {
            if ($("#sale_receipt_msg label.error").css("display", "")) {
                $("#sale_receipt_msg label.error").css("display", "none");
            }
        });
        $("#return_receipt").focus(function() {
            if (!$("#sale_receipt_msg label").hasClass("error")) {
                $("#return_receipt_msg").append('<label for="return_receipt" class="error">If you change Receipt No. Please confirm with the POS the actual Receipt No otherwise there will be sync Problem !</label>');
            }
            if ($("#return_receipt_msg label.error").css("display", "none")) {
                $("#return_receipt_msg label.error").css("display", "");
            }
        });
        $("#return_receipt").blur(function() {
            if ($("#return_receipt_msg label.error").css("display", "")) {
                $("#return_receipt_msg label.error").css("display", "none");
            }
        });
    });
</script>
<form action="<?php echo sfConfig::get("app_admin_url"); ?>shops/editSubmit"  id="Editshop" method="post"  enctype="multipart/form-data" >
    <div class="itemslist">
        <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;<span>Edit Branch</span>

        </h1>
        <div  class="backimgDiv">
            <input type="button" onclick="document.location.href = '<?php echo $cancel_url //sfConfig::get("app_admin_url")."shops/view/id/".$shop->getId();      ?>'" value="Cancel" class="btn btn-cancel"/>
            <input type="submit" value="Update" class="btn btn-primary" />
        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>


            <input type="hidden" name="id" class="required shopid" id="shopid" value="<?php echo $shop->getId() ?>">    
            <div class="lblleft">Name :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="name" value="<?php echo $shop->getName() ?>" class="form-control required"></div>
            </div> 
            <div class="lblleft">Branch number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="branch_number" value="<?php echo $shop->getBranchNumber() ?>" class="form-control required"></div>
            </div> 
            <br clear="all" />        
            <div class="lblleft">Company number:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="company_number" value="<?php echo $shop->getCompanyNumber() ?>" class="form-control required"></div>
            </div> 
            <div class="lblleft">Password:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="password" value="<?php echo $shop->getPassword() ?>" class="form-control required"></div>
            </div>
            <br clear="all" />         
            <div class="lblleft addedit">Address:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="address" value="<?php echo $shop->getAddress() ?>" class="form-control"></div>
            </div>
            <div class="lblleft addedit">Zip:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="zip" value="<?php echo $shop->getZip() ?>" class="form-control"></div>
            </div> 
            <br clear="all" />
            <div class="lblleft addedit">Place:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="place" value="<?php echo $shop->getPlace() ?>" class="form-control"></div>
            </div>
            <div class="lblleft">Country:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="country" value="<?php echo $shop->getCountry() ?>" class="form-control required"></div>
            </div> 
            <br clear="all" />        
            <div class="lblleft addedit">Tel:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="tel" value="<?php echo $shop->getTel() ?>" class="form-control"></div>
            </div>
            <div class="lblleft addedit">Fax:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="fax" value="<?php echo $shop->getFax() ?>" class="form-control"></div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Negative Sale:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="checkbox" name="negative_sale" <?php echo $shop->getNegativeSale() ? "checked='checked'" : ""; ?> class=""></div>
            </div> 
            <div class="lblleft addedit">Language:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6 selectLanguage">
                    <select name="languages" class="form-control languages">
                        <?php
                        $lanugages = LanguagesPeer::doSelect(new Criteria());
                        foreach ($lanugages as $lanugage) {
                            ?>
                            <option value="<?php echo $lanugage->getId() ?>" <?php echo $lanugage->getId() == $shop->getLanguageId() ? "selected='selected'" : ""; ?>><?php echo $lanugage->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Time out:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="text" name="time_out" value="<?php echo $shop->getTimeOut(); ?>" class="form-control"></div>
            </div> 
            <div class="lblleft addedit">Start Value Sale Receipt:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6" id="sale_receipt_msg">
                    <input type="text" name="sale_receipt" id="sale_receipt" value="<?php echo $shop->getStartValueSaleReceipt(); ?>" class="form-control">
                </div>
            </div> 
            <br clear="all" />  
            <div class="lblleft addedit">Start Value Return Receipt:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6" id="return_receipt_msg"><input type="text" name="return_receipt" id="return_receipt" value="<?php echo $shop->getStartValueReturnReceipt(); ?>" class="form-control"></div>
            </div> 
            <div class="lblleft addedit">Sale Receipt Format:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="saleFormat" class="form-control">
                        <?php
                        $receiptformats = ReceiptFormatsPeer::doSelect(new Criteria());
                        foreach ($receiptformats as $receiptformat) {
                            ?>
                            <option value="<?php echo $receiptformat->getId() ?>" <?php echo $receiptformat->getId() == $shop->getSaleReceiptFormatId() ? "selected='selected'" : ""; ?>><?php echo $receiptformat->getTitle(); ?></option>
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
                            <option value="<?php echo $receiptformat->getId() ?>" <?php echo $receiptformat->getId() == $shop->getReturnReceiptFormatId() ? "selected='selected'" : ""; ?>><?php echo $receiptformat->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div>
             <div class="lblleft addedit">Start Value Bookout:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6" id="return_receipt_msg"><input type="text" name="start_value_bookout" id="start_value_bookout" value="<?php echo $shop->getStartValueBookout(); ?>" class="form-control"></div>
            </div> 
           
            <br clear="all" /> 
            <div class="lblleft addedit">Status:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="status_id" class="form-control">
                        <option value="3" <?php echo ($shop->getStatusId() == "" || $shop->getStatusId() == 3) ? "selected='selected'" : "" ?>>Active</option>
                        <option value="5" <?php echo $shop->getStatusId() == 5 ? "selected='selected'" : "" ?>>Inactive</option>
                    </select>
                </div>
            </div> 
    <div class="lblleft addedit">Bookout number Format:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="bookout_format_id" class="form-control">
                        <?php
                        $receiptformats = ReceiptFormatsPeer::doSelect(new Criteria());
                        foreach ($receiptformats as $receiptformat) {
                            ?>
                            <option value="<?php echo $receiptformat->getId() ?>" <?php echo $receiptformat->getId() == $shop->getBookoutFormatId() ? "selected='selected'" : ""; ?>><?php echo $receiptformat->getTitle(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div>
            <br clear="all" /> 
            <div class="lblleft addedit">Employee Discount Type:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="discount_type_id" class="form-control">
                        <?php
                        $discountTypes = DiscountTypesPeer::doSelect(new Criteria());
                        foreach ($discountTypes as $discountType) {
                            ?>
                            <option value="<?php echo $discountType->getId() ?>" <?php echo $shop->getDiscountTypeId() == $discountType->getId() ? "selected='selected'" : "" ?> ><?php echo $discountType->getName(); ?></option>
                            <?php
                        }
                        ?> 
                    </select>
                </div>
            </div>
         
            <div class="lblleft addedit">Employee Discount Value:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" value="<?php echo $shop->getDiscountValue(); ?>" name="discount_value" class="form-control"/>
                </div>
            </div>
 <br clear="all" /> 
            
            <div class="lblleft addedit">Max Day End Attempts:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="max_day_end_attempts" class="form-control required" value="<?php echo $shop->getMaxDayEndAttempts() ?>">
                </div>
            </div>
         
            <div class="lblleft addedit">Receipt Header Position:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="receipt_header_position" class="form-control"> 
                        <option value="Center" <?php echo $shop->getReceiptHeaderPosition() == "Center" ? "selected='selected'" : "" ?>>Center</option>
                        <option value="Left" <?php echo $shop->getReceiptHeaderPosition() == "Left" ? "selected='selected'" : "" ?>>Left</option>
                        <option valu="Right" <?php echo $shop->getReceiptHeaderPosition() == "Right" ? "selected='selected'" : "" ?>>Right</option>
                    </select>
                <!--    <input type="text" value="<?php // echo $shop->getReceiptHeaderPosition();   ?>" name="receipt_header_position" class="form-control"/> -->
                </div>
            </div>   <br clear="all" /> 
            <div class="lblleft addedit">Receipt footer line 1:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" value="<?php echo $shop->getReceiptTaxStatmentOne(); ?>" name="receipt_tax_statement_one" class="form-control"/>
                </div>
            </div>
             
            <div class="lblleft addedit">Receipt footer line 2:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" value="<?php echo $shop->getReceiptTaxStatmentTwo(); ?>" name="receipt_tax_statement_two" class="form-control"/>
                </div>
            </div>   <br clear="all" /> 
            <div class="lblleft addedit">Receipt footer line 3:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" value="<?php echo $shop->getReceiptTaxStatmentThree(); ?>" name="receipt_tax_statement_three" class="form-control"/>
                </div>
            </div>
          
            <div class="lblleft addedit">Receipt Auto Print:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6"><input type="checkbox" name="receipt_auto_print" class="" <?php echo $shop->getReceiptAutoPrint() ? "checked='checked'" : ""; ?>></div>
            </div>  <br clear="all" /> 
        </div>
    </div></form>
