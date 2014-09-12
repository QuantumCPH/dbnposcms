

<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;Invoice Detail     Invoice Number # <?php echo $invoice_number; ?>    

    </h1>
     <div class="backimgDiv">
       

        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>transactions/index';" value="Back" class="btn btn-cancel"/>
          </div>
</div>
<br/><br/><br/>





<div class="regForm">
    <br/>
    
<table class="table table-striped">
    <thead><tr><th>Invoice Detail</th><th></th></tr></thead>
    <tbody>
    <tr class="even"><td>Date </td><td><?php echo $transactionOne->getCreatedAt();   ?></td></tr>
     <tr  class="odd"><td>Branch </td><td><?php echo $branch_number;   ?></td></tr>
     <tr  class="even"><td>User </td><td><?php $user = UserPeer::retrieveByPK($transactionOne->getUserId());
            echo $user->getName(); ?></td></tr>
    </tbody>
</table>
     <br/>
    <table class="table table-striped">
        <thead>
            <tr>
               <th>Item</th>
                <th>Desc</th>
                 <th>Sell. Price</th>
                   <th>Qty</th>
                    <th>Discount</th>
                <th>Sold Price</th>
               
              
               
             
               
            </tr>
        </thead>
        <tbody>
            <?php $incrment = 1;
            $totalsoldprice=0;
              $totalquantity=0;
             $totalsellingprice=0;
             $totaldiscount=0;
            ?>
            <?php foreach ($transactions as $transaction) { ?>
                <?php
                if ($incrment % 2 == 0) {
                    $class= 'even';
                } else {
                    $class= 'odd';
                }
                ?>
                <tr class='<?php echo $class; ?>'>
                      <td><?php echo $transaction->getItemId(); ?></td>
                    <td><?php echo $transaction->getDescription1(); ?></td>
                       <td><?php echo $transaction->getSellingPrice(); $totalsellingprice=$totalsellingprice+$transaction->getSellingPrice(); ?></td>
                         <td><?php echo $transaction->getQuantity(); $totalquantity=$totalquantity+$transaction->getQuantity(); ?></td>
                  <td><?php echo $transaction->getDiscountValue(); $totaldiscount=$totaldiscount+$transaction->getDiscountValue();  ?> </td>
                    <td><?php echo $transaction->getSoldPrice();  $totalsoldprice=$totalsoldprice+$transaction->getSoldPrice();    ?></td>
                    
                  
                   
                 
                    
                    
                   
                </tr>
    <?php $incrment++; ?>
<?php } ?>
                
                
                
                
                
                
                
                
        </tbody>
 <tr>
                 <th><b>Total</b></th>
                <th> </th>
                    <th><b><?php echo number_format($totalsellingprice,2); ?></b></th>
                      <th> </th>
                <th><b><?php echo number_format($totaldiscount,2); ?></b></th>
                
              
            
                 <th><b><?php echo number_format($totalsoldprice,2); ?></b></th>
              

                
             
            </tr>
    </table>
     <br/>
     
      <table class="table table-striped">
        <thead>
            <tr>
               
                <th>Payment Type</th>
                <th>Amount</th>
             
              
            </tr>
        </thead>
        <tbody>
        <?php    
            $sho = new Criteria();

                      
                        $sho->add(OrderPaymentsPeer::ORDER_ID, $transactionOne->getOrderId());
                        $orderpayments = OrderPaymentsPeer::doSelect($sho);
                        foreach ($orderpayments as $orderpayment) {  ?>
                           <tr>
               
                               <th> <?php $paymentType=PaymentTypesPeer::retrieveByPK($orderpayment->getPaymentTypeId());  echo $paymentType->getTitle();  ?></th>
                <th><?php echo number_format($orderpayment->getAmount(),2);  ?></th>
             
              
            </tr>
                    <?php    }    ?>
            
            
            
            
        </tbody></table>
     
    <?php     
$order=OrdersPeer::retrieveByPK($transactionOne->getOrderId());
    



    ?>
   
      <br/>
    
<table class="table table-striped">
    <thead><tr><th>Order Detail</th><th></th></tr></thead>
    <tbody>
    <tr class="even"><td>Total Amount  </td><td><?php echo number_format($order->getTotalAmount(),2);  ?></td></tr>
    <tr  class="odd"><td>Discount </td><td><?php echo number_format($order->getDiscountValue(),2);   ?></td></tr>
     <tr  class="even"><td>Total Invoice Amount </td><td><?php    echo number_format($order->getTotalSoldAmount(),2); ?></td></tr>
    </tbody>
</table>
     
     
     
</div>
 