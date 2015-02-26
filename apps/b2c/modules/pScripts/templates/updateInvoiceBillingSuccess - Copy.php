<?php
use_helper('Number');
ob_start();
$totalcost = 0.00;
$totalSubFee = 0.00;
$totalPayments = 0.00;
$totalEventFee = 0.00;
$netbalance;
?>
<html>
    <head>
        <title><?php echo date('d-m-y') . $invoice_meta->getInvoiceNumber(); ?>- <?php echo $company_meta->getName() ?></title>
        <style>
            body{
                font-family:Verdana, Arial, Helvetica, sans-serif;
                font-size:11px;
            }
            h2{
                font-size:17px;
                color:#000!important;
            }
            fieldset {
                -moz-border-radius:10px;
                border-radius: 10px;
                -webkit-border-radius: 10px;
                border: 2px solid #000;
            }
            .border{
                border-bottom: 1px solid #000 !important;
                border-top:1px solid #000 !important;
            }
            .borderleft{
                border-left: 1px solid #000 !important;
            }
            .borderright{
                border-right:1px solid #000 !important;
            }
            .padding{
                padding-top:10px!important;
                padding-bottom:10px!important;
                padding-left:5px!important;
            }
            .padbot{
                padding-bottom:10px;
            }
            .trbg{
                font-weight:bold;
                background-color:#CCCCCC;
            }
            .table{
                padding-top:30px;
            }
            .table td{
                padding-left:5px;
                padding-top:5px;
            }
            table td{
                border:none!important;
            }
        </style>
    </head>
    <body>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:30px 0 30px  0;margin:0 auto;">
            <tr>
                <td colspan="2">
                    <fieldset>
                        <table width="100%">
                            <tr>
                                <td> <?php echo image_tag(sfConfig::get('app_web_url') . 'images/logo.jpg'); ?></td>
                                <td>

                                </td>
                                <td align="right" style="padding-right:10px;">
                                       <b>M-easypay Aps</b><br />
                                    Smedeholm 13 B-2730<br />
                                    Herlev TLF 7070 2126<br />
                                    CVR 35029974 
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <td width="61%">
                    <table width="100%" border="0" style="padding-top:30px">
                        <tr>
                            <td  width="33%">Invoice Number:</td>
                            <td  width="33%"><?php echo $invoice_meta->getInvoiceNumber(); ?></td>
                            <td  width="34%"></td>
                        </tr>
                        <tr>
                            <td>Invoice Period:</td>
                            <td><?php echo $invoice_meta->getBillingStartingDate('d M.') . ' - ' . $invoice_meta->getBillingEndingDate('d M.') ?></td>
                         <td></td>
                        </tr>
                        <tr>
                            <td>Invoice Date:</td>
                            <td><?php echo $invoice_meta->getCreatedAt('d M. Y') ?></td>
                               <td></td>
                        </tr>
                        <tr>
                            <td>Due date:</td>
                            <td><?php echo $invoice_meta->getDueDate('d M. Y') ?></td>
                               <td></td>
                        </tr>


                    </table>
                </td>
                <td width="39%" align="right">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr  >
                            <td width="30%"></td>
                            <td colspan="2"  class="border borderleft borderright" style="background:#CCCCCC;padding: 5px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;">
                                <?php echo $company_meta->getName() ?><br />
                                <?php echo $company_meta->getAddress() ?><br />
                                <?php echo $company_meta->getPostCode() ?> 
                                <?php //echo $company_meta->getCity() ?> 
                                <?php //echo 	Att: $company_meta->getContactName() ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

 <?php  //////////////////////////////////////////////////////////////////////// ?>       
            
       <?php
         $finalPricetotal=0;
            $conn = Propel::getConnection();
            $query = 'SELECT p.id,p.name
,(SELECT count(tt.id ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.card_type_id=2 AND tt.agent_company_id="' . $company_meta->getId() . '"    AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )  AS quantity
,(SELECT sum(tt.product_registration_fee ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"     AND tt.card_type_id=2 AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )   AS price
,(SELECT sum(tt.agent_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"    AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS commission
,(SELECT sum(tt.vat_on_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"     AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS vatCommission
,(SELECT (sum(tt.product_registration_fee)-sum(tt.agent_commission ))   FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"      AND tt.card_type_id=2 AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS netprice
FROM product as p where p.id in(select product_id from topup_transactions where status=3 AND  agent_company_id="' . $company_meta->getId() . '"     AND  card_type_id=2 AND  created_at>="' . $billing_start_date . '"   AND  created_at<="' . $billing_end_date . '"  group by product_id )';

            
            
            
            $statement = $conn->prepare($query);
            $statement->execute();
              
            ?>

       <tr><td colspan="2"><br/>
       <br/></td></tr>     
        <tr  height="40px" >
            <td colspan="2" class="border borderleft borderright" style="background:#CCCCCC;padding-left: 10px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;"><strong>Overview</strong></td>
        </tr>

            <tr>
                <td colspan="2">
 
                    <table width="100%" cellpadding="0" cellspacing="0" class="table">
                        <?php   if(count($statement)>0){  ?>
                        <tr><td colspan="3" class="padbot"><h2>Paper Card</h2></td></tr>
                        <tr height="40px" class="trbg" bgcolor="#CCCCCC" style="background:#CCCCCC">
                            <td width="25%" class="border borderleft">Description</td>				  
                            <td width="10%" class="border">Quantity</td>
                            <td width="15%"  align="right"  class="border">Amount</td>
                            <td width="20%" class="border " align="right" >Commission</td>
                            <td width="30%" class="border borderright" align="right" style="padding-right:10px">Net Amount</td>
                        </tr> 
                        
               <?php  } ?>
                        <?php
                        $bilcharge = 00.00;
                        $invoiceFlag = false;
                        $count = 1;
                        $quantity = 0;
                        $price = 0;
                        $commission = 0;
                        $vatCommission=0;
                        $netprice = 0;
                        while ($rowObj = $statement->fetch(PDO::FETCH_OBJ)) {
                            ?>
                            <tr>
                                <td><?php echo $rowObj->name; ?> </td>
                                <td><?php echo $rowObj->quantity;
                        $quantity = $quantity + $rowObj->quantity;
                            ?> </td>
                                <td style="text-align: right"><?php echo number_format($rowObj->price, 2);
                                $price = $price + $rowObj->price;
                                ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <td style="text-align: right">-<?php echo number_format($rowObj->commission, 2);
                                $commission = $commission + $rowObj->commission;
                            ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <?php 
                                $vatCommission = $vatCommission + $rowObj->vatCommission;
                            ?> 
                                <td style="text-align: right"> <?php echo number_format($rowObj->netprice, 2);
                                $netprice = $netprice + $rowObj->netprice;
                            ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                            </tr>                 

<?php } ?> <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><?php echo $quantity; ?> </td>
                            <td style="text-align: right"><?php echo number_format($price, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                            <td  style="text-align: right">-<?php echo number_format($commission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                             
                            <td style="text-align: right"> <?php echo number_format($netprice, 2);  ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                        </tr>  <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                         <tr>
                            <td>Vat on Commission </td>
                            <td> </td>
                            <td style="text-align: right"> </td>
                            <td  style="text-align: right">-<?php echo number_format($vatCommission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                             
                            <td style="text-align: right"></td>
                        </tr>  
                        
                        <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                          
                        <tr>
                            <td colspan="6">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Subtotal :</strong></td>
                                        <td width="30%" align="right" ><?php echo number_format($netprice, 2);  $finalPricetotal=$finalPricetotal+$netprice; ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <hr />
                            </td>
                        </tr> 
                    </table>
                </td>
            </tr>  



            <?php
            $conn = Propel::getConnection();
            $query = 'SELECT p.id,p.name
,(SELECT count(tt.id ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.card_type_id=1 AND tt.agent_company_id="' . $company_meta->getId() . '"    AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )  AS quantity
,(SELECT sum(tt.product_registration_fee ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"      AND tt.card_type_id=1   AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )   AS price
,(SELECT sum(tt.agent_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"      AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS commission
,(SELECT sum(tt.vat_on_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"      AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS vatCommission
,(SELECT (sum(tt.product_registration_fee)-sum(tt.agent_commission ))   FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"    AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS netprice
FROM product as p where p.id in(select product_id from topup_transactions where status=3  AND  card_type_id=1  AND  created_at>="' . $billing_start_date . '"  AND  agent_company_id="' . $company_meta->getId() . '"      AND  created_at<="' . $billing_end_date . '"  group by product_id )';

            $statement = $conn->prepare($query);
            $statement->execute();
              if(count($statement)>0){ 
            ?>


            <tr>
                <td colspan="2">



                    <table width="100%" cellpadding="0" cellspacing="0" class="table">
                        <tr><td colspan="3" class="padbot"><h2>E Card</h2></td></tr>
                        <tr height="40px" class="trbg" bgcolor="#CCCCCC" style="background:#CCCCCC">
                            <td width="25%" class="border borderleft">Description</td>				  
                            <td width="10%" class="border">Quantity</td>
                            <td width="15%" class="border" align="right" >Amount</td>
                            <td width="20%" class="border " align="right" >Commission</td>
                            
                            <td width="30%" class="border borderright" align="right" style="padding-right:10px">Net Amount</td>
                        </tr>
                        
              <?php } ?>
                        <?php
                        $bilcharge = 00.00;
                        $invoiceFlag = false;
                        $count = 1;
                        $e_quantity = 0;
                        $e_price = 0;
                        $e_commission = 0;
                        $e_netprice = 0;
                         $e_vatCommission = 0;
                        while ($rowObj = $statement->fetch(PDO::FETCH_OBJ)) {
                            ?>
                            <tr>
                                <td><?php echo $rowObj->name; ?> </td>
                                <td><?php echo $rowObj->quantity;
                                $e_quantity = $e_quantity + $rowObj->quantity;
                            ?> </td>
                                <td style="text-align: right"><?php echo number_format($rowObj->price, 2);
                                $e_price = $e_price + $rowObj->price;
                                ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <td style="text-align: right">-<?php echo number_format($rowObj->commission, 2);
                                $e_commission = $e_commission + $rowObj->commission;
                                ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                                <?php 
                                $e_vatCommission = $e_vatCommission + $rowObj->vatCommission;
                                ?>
                                <td style="text-align: right"> <?php echo number_format($rowObj->netprice, 2);
                                $e_netprice = $e_netprice + $rowObj->netprice;
                                ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                            </tr>                 

<?php } ?> <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td> Total </td>
                            <td><?php echo $e_quantity; ?> </td>
                            <td style="text-align: right"><?php echo number_format($e_price, 2); ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                            <td style="text-align: right">-<?php echo number_format($e_commission, 2); ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                             
                            <td style="text-align: right"> <?php echo number_format($e_netprice, 2); ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                        </tr>     
                        <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr> <tr>
                            <td>Vat on Commission </td>
                            <td> </td>
                            <td style="text-align: right"> </td>
                            <td  style="text-align: right"> -<?php echo number_format($e_vatCommission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?>  </td>
                             
                            <td style="text-align: right"></td>
                        </tr>  
                         <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Subtotal :</strong></td>
                                        <td width="30%" align="right" > <?php echo number_format($e_netprice, 2);  $finalPricetotal=$finalPricetotal+$e_netprice;?><?php echo sfConfig::get('app_currency_code'); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <hr />
                            </td>
                        </tr> 
                        
                        <tr>
                            <td colspan="5">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Included Vat on Commission :</strong></td>
                                        <td width="30%" align="right" >-<?php echo number_format(($e_vatCommission+$vatCommission), 2); ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <hr />
                            </td>
                        </tr> 
                    </table>
                </td>
            </tr>  
       
            <?php
//    $invoice_cost = ($invoiceFlag) ? $invoice_cost : '0.00'; 
$totalcost =$finalPricetotal;
$invoice_cost = 0;
?>

            <tr>
                <td colspan="2" align="right">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="70%" align="right" class="padding" style="padding-right:10px"><strong>Total cost:</strong></td>
                            <td width="30%" align="right"  ><?php echo number_format($totalcost, 2); ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                        </tr>

                        <tr>
                            <td colspan="2" ><hr /></td>
                        </tr>
                        
                        <?php if($payCount>0){  ?>
                        <tr>
                            <td colspan="2">
                                <table width="100%" cellpadding="0" cellspacing="0" class="table">
                                    <tr><td colspan="2" class="padbot"><h2>Payment History</h2></td></tr>
                                    <tr class="trbg" height="40px">
                                        <td class="border borderleft">Date & Time</td>
                                        <td class="border">Description</td>
                                        <td  class="border borderright" align="right"  style="padding-right:10px">Amount</td>

                                    </tr>
                                            <?php
                                            $totalPayments = 0;
                                            foreach ($payments as $payment) {
                                                ?>
                                        <tr>
                                            <td><?php echo $payment->getCreatedAt(); ?></td>
                                            <td><?php $trdesc = TransactionDescriptionPeer::retrieveByPK($payment->getOrderDescription());
                                    echo $trdesc->getTitle();
                                    ?></td>
                                            <td align="right"><?php echo number_format($payment->getAmount(), 2);
                                    $totalPayments = $totalPayments + $payment->getAmount();
                                    ?> <?php echo sfConfig::get('app_currency_code'); ?>  </td>
                                        </tr>				
<?php } ?>
                                    <tr height="30px">
                                        <td colspan="3"><hr /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="right"><strong>Total:</strong></td>
                                        <td align="right"><strong><?php echo number_format($totalPayments, 2); ?><?php echo sfConfig::get('app_currency_code'); ?>
                                                &nbsp;</strong></td>


                                    </tr>
                                </table>
                            </td>
                        </tr>  
  <tr>
                            <td colspan="2" ><hr /></td>
                        </tr>


                        <?php  } ?>




                      
<?php
util::saveTotalPayment($invoice_meta->getId(), $totalcost);
$net_cost = $totalcost;
?>
                        <tr>
                            <td class="padding" align="right" style="padding-right:10px"><strong>Previous Balance:</strong></td>
                            <td align="right" style="padding-right:10px"><strong><?php echo number_format($netbalance, 2);
echo sfConfig::get('app_currency_code');
?></strong></td>
                        </tr>
                        <tr>
                            <td class="padding" align="right" style="padding-right:10px"><strong>Total Payable Balance:</strong></td>
                            <td align="right" style="padding-right:10px"><strong><?php echo number_format($net_payment = $net_cost + $netbalance, 2);
echo sfConfig::get('app_currency_code');
?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            
            
  <?php  //////////////////////////////////////////////////////////////////////// ?>              
            <?php
            
            $finalPricetotal=0;
              $conn = Propel::getConnection();
 
                         $ers = new Criteria();
                         $ers->add(AgentUserPeer::AGENT_COMPANY_ID, $company_meta->getId());
                         $agentUsers=AgentUserPeer::doSelect($ers);
                          
            foreach($agentUsers as $agentUser){
           
         
            ?>
            <tr><td colspan="2"><br/><br/></td></tr>
            <tr   height="40px"><td colspan="2" class="border borderleft borderright" style="background:#CCCCCC;padding-left: 10px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;"> <b>Agent User : <?php  echo $agentUser->getUsername();   ?></b> </td></tr>
            <?php
            $conn = Propel::getConnection();
            $query = 'SELECT p.id,p.name
,(SELECT count(tt.id ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.card_type_id=2 AND tt.agent_company_id="' . $company_meta->getId() . '"    AND tt.agent_user_id="' . $agentUser->getId() . '"    AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )  AS quantity
,(SELECT sum(tt.product_registration_fee ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"     AND tt.agent_user_id="' . $agentUser->getId() . '"     AND tt.card_type_id=2 AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )   AS price
,(SELECT sum(tt.agent_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"       AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS commission
,(SELECT sum(tt.vat_on_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"     AND tt.agent_user_id="' . $agentUser->getId() . '"   AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS vatCommission
,(SELECT (sum(tt.product_registration_fee)-sum(tt.agent_commission ))   FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"      AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=2 AND tt.card_type_id=2  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS netprice
FROM product as p where p.id in(select product_id from topup_transactions where status=3 AND  agent_company_id="' . $company_meta->getId() . '"      AND  agent_user_id="' . $agentUser->getId() . '"   AND  card_type_id=2 AND  created_at>="' . $billing_start_date . '"   AND  created_at<="' . $billing_end_date . '"  group by product_id )';

            
            
            
            $statement = $conn->prepare($query);
            $statement->execute();
            
             
            ?>

            
        

            <tr>
                <td colspan="2">



                    <table width="100%" cellpadding="0" cellspacing="0" class="table">
                        <?php   if(count($statement)>0){  ?>
                        <tr><td colspan="3" class="padbot"><h2>Paper Card</h2></td></tr>
                        <tr height="40px" class="trbg" bgcolor="#CCCCCC" style="background:#CCCCCC">
                            <td width="25%" class="border borderleft">Description</td>				  
                            <td width="10%" class="border">Quantity</td>
                            <td width="15%"  align="right"  class="border">Amount</td>
                            <td width="20%" class="border " align="right" >Commission</td>
                            <td width="30%" class="border borderright" align="right" style="padding-right:10px">Net Amount</td>
                        </tr> 
                        
               <?php  } ?>
                        <?php
                        $bilcharge = 00.00;
                        $invoiceFlag = false;
                        $count = 1;
                        $quantity = 0;
                        $price = 0;
                        $commission = 0;
                        $vatCommission=0;
                        $netprice = 0;
                        while ($rowObj = $statement->fetch(PDO::FETCH_OBJ)) {
                            ?>
                            <tr>
                                <td><?php echo $rowObj->name; ?> </td>
                                <td><?php echo $rowObj->quantity;
                        $quantity = $quantity + $rowObj->quantity;
                            ?> </td>
                                <td style="text-align: right"><?php echo number_format($rowObj->price, 2);
                                $price = $price + $rowObj->price;
                                ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <td style="text-align: right">-<?php echo number_format($rowObj->commission, 2);
                                $commission = $commission + $rowObj->commission;
                            ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <?php 
                                $vatCommission = $vatCommission + $rowObj->vatCommission;
                            ?> 
                                <td style="text-align: right"> <?php echo number_format($rowObj->netprice, 2);
                                $netprice = $netprice + $rowObj->netprice;
                            ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                            </tr>                 

<?php } ?> <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><?php echo $quantity; ?> </td>
                            <td style="text-align: right"><?php echo number_format($price, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                            <td  style="text-align: right">-<?php echo number_format($commission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                             
                            <td style="text-align: right"> <?php echo number_format($netprice, 2);  ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                        </tr>  <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                         <tr>
                            <td>Vat on Commission </td>
                            <td> </td>
                            <td style="text-align: right"> </td>
                            <td  style="text-align: right">-<?php echo number_format($vatCommission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                             
                            <td style="text-align: right"></td>
                        </tr>  
                        
                        <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                          
                        <tr>
                            <td colspan="6">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Subtotal :</strong></td>
                                        <td width="30%" align="right" ><?php echo number_format($netprice, 2);  $finalPricetotal=$finalPricetotal+$netprice; ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <hr />
                            </td>
                        </tr> 
                    </table>
                </td>
            </tr>  



            <?php
            $conn = Propel::getConnection();
            $query = 'SELECT p.id,p.name
,(SELECT count(tt.id ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.card_type_id=1 AND tt.agent_company_id="' . $company_meta->getId() . '"   AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )  AS quantity
,(SELECT sum(tt.product_registration_fee ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"   AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=1   AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" )   AS price
,(SELECT sum(tt.agent_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"   AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS commission
,(SELECT sum(tt.vat_on_commission ) FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3 AND tt.agent_company_id="' . $company_meta->getId() . '"   AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS vatCommission
,(SELECT (sum(tt.product_registration_fee)-sum(tt.agent_commission ))   FROM topup_transactions as tt  WHERE  tt.product_id=p.id  AND tt.status=3  AND tt.agent_company_id="' . $company_meta->getId() . '"   AND tt.agent_user_id="' . $agentUser->getId() . '"  AND tt.card_type_id=1  AND tt.created_at>="' . $billing_start_date . '"   AND tt.created_at<="' . $billing_end_date . '" ) AS netprice
FROM product as p where p.id in(select product_id from topup_transactions where status=3  AND  card_type_id=1  AND  created_at>="' . $billing_start_date . '"  AND  agent_company_id="' . $company_meta->getId() . '"    AND  agent_user_id="' . $agentUser->getId() . '"  AND  created_at<="' . $billing_end_date . '"  group by product_id )';

            $statement = $conn->prepare($query);
            $statement->execute();
              if(count($statement)>0){ 
            ?>


            <tr>
                <td colspan="2">



                    <table width="100%" cellpadding="0" cellspacing="0" class="table">
                        <tr><td colspan="3" class="padbot"><h2>E Card</h2></td></tr>
                        <tr height="40px" class="trbg" bgcolor="#CCCCCC" style="background:#CCCCCC">
                            <td width="25%" class="border borderleft">Description</td>				  
                            <td width="10%" class="border">Quantity</td>
                            <td width="15%" class="border" align="right" >Amount</td>
                            <td width="20%" class="border " align="right" >Commission</td>
                            
                            <td width="30%" class="border borderright" align="right" style="padding-right:10px">Net Amount</td>
                        </tr>
                        
              <?php } ?>
                        <?php
                        $bilcharge = 00.00;
                        $invoiceFlag = false;
                        $count = 1;
                        $e_quantity = 0;
                        $e_price = 0;
                        $e_commission = 0;
                        $e_netprice = 0;
                         $e_vatCommission = 0;
                        while ($rowObj = $statement->fetch(PDO::FETCH_OBJ)) {
                            ?>
                            <tr>
                                <td><?php echo $rowObj->name; ?> </td>
                                <td><?php echo $rowObj->quantity;
                                $e_quantity = $e_quantity + $rowObj->quantity;
                            ?> </td>
                                <td style="text-align: right"><?php echo number_format($rowObj->price, 2);
                                $e_price = $e_price + $rowObj->price;
                                ?><?php echo sfConfig::get('app_currency_code'); ?> </td>
                                <td style="text-align: right">-<?php echo number_format($rowObj->commission, 2);
                                $e_commission = $e_commission + $rowObj->commission;
                                ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                                <?php 
                                $e_vatCommission = $e_vatCommission + $rowObj->vatCommission;
                                ?>
                                <td style="text-align: right"> <?php echo number_format($rowObj->netprice, 2);
                                $e_netprice = $e_netprice + $rowObj->netprice;
                                ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                            </tr>                 

<?php } ?> <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td> Total </td>
                            <td><?php echo $e_quantity; ?> </td>
                            <td style="text-align: right"><?php echo number_format($e_price, 2); ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                            <td style="text-align: right">-<?php echo number_format($e_commission, 2); ?> <?php echo sfConfig::get('app_currency_code'); ?></td>
                             
                            <td style="text-align: right"> <?php echo number_format($e_netprice, 2); ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                        </tr>     
                        <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr> <tr>
                            <td>Vat on Commission </td>
                            <td> </td>
                            <td style="text-align: right"> </td>
                            <td  style="text-align: right"> -<?php echo number_format($e_vatCommission, 2); ?><?php echo sfConfig::get('app_currency_code'); ?>  </td>
                             
                            <td style="text-align: right"></td>
                        </tr>  
                         <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Subtotal :</strong></td>
                                        <td width="30%" align="right" > <?php echo number_format($e_netprice, 2);  $finalPricetotal=$finalPricetotal+$e_netprice;?><?php echo sfConfig::get('app_currency_code'); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr> 
                        
                        
                        
                           <tr>
                            <td colspan="5">
                                <table cellpadding="3" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="70%" align="right"><strong>Total  <?php  echo $agentUser->getUsername();   ?> : </strong></td>
                                        <td width="30%" align="right" > <?php  $total_netprice=$netprice+$e_netprice; echo number_format($total_netprice, 2);   ?><?php echo sfConfig::get('app_currency_code'); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                         <tr>
                            <td colspan="5">
                                <hr />
                            </td>
                        </tr> 
                    </table>
                </td>
            </tr>  

            <?php  } ?>

           
 

            <tr>
                <td colspan="2"><br />
                    <fieldset>
                        <table width="100%">
                            <tr>
                                <td  width="17%">Tel: 20717786</td>
                                <td   width="18%">Fax: XXXXXXX</td>
                                <td   width="35%"   align="right">Email: info@m-easypay.com</td>
                                <td   width="30%"  align="right">Web: m-easypay.com</td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php
$totalSubFee = 0;
$totalEventFee = 0;
$totalPayments = 1200;
$moms =$vatCommission+$e_vatCommission;


$html_content = ob_get_contents();
util::saveHtmlToInvoice($invoice_meta->getId(), $html_content);
$ci = new Criteria();
$ci->add(AgentCompanyInvoicePeer::ID, $invoice_meta->getId());
$in = AgentCompanyInvoicePeer::doSelectOne($ci);

$in->setRegistrationFee($totalEventFee);
$in->setPaymentHistoryTotal($totalPayments);
$in->setMoms($moms);
$in->setTotalusage($totalcost);
$in->setCurrentBill($net_cost);
$in->setInvoiceCost($invoice_cost);
$in->setNetPayment($net_cost);
$in->setTotalPayableBalance($net_payment); /// previous balance and current invoice payment
$in->setInvoiceStatusId(1);



$in->save();

//	$fileName = str_replace("/", "_", $in->getCompany()->getName());
//	$fileName = str_replace(" ", "_", $fileName);
//
//	$fileName = $in->getId().'-'.$fileName;
//
//	util::html2pdf($invoice_meta->getId(),$html_content,$fileName);
?>
