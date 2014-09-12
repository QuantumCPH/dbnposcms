

<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;Invoice Detail     Invoice Number # <?php echo $invoice_number; ?>  And  Branch no # <?php echo $branch_number;   ?>

    </h1>
     <div class="backimgDiv">
       

        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>transactions/index';" value="Back" class="btn btn-cancel"/>
          </div>
</div>
<br/><br/><br/>
<div class="regForm">
    <table class="table table-striped">
        <thead>
            <tr>
              
                <th>Sold Price</th>
               
                <th>Qty</th>
                <th>Sell. Price</th>
                <th>User</th>
                <th>Item</th>
                <th>Desc</th>

                <th>Type</th>
                <th>Date</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php $incrment = 1;
            $totalsoldprice=0;
              $totalquantity=0;
             $totalsellingprice=0;
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
                   
                    <td><?php echo $transaction->getSoldPrice();  $totalsoldprice=$totalsoldprice+$transaction->getSoldPrice();    ?></td>
                    
                    <td><?php echo $transaction->getQuantity(); $totalquantity=$totalquantity+$transaction->getQuantity(); ?></td>
                    <td><?php echo $transaction->getSellingPrice(); $totalsellingprice=$totalsellingprice+$transaction->getSellingPrice(); ?></td>
                    <td><?php $user = UserPeer::retrieveByPK($transaction->getUserId());
            echo $user->getName(); ?></td>
                    <td><?php echo $transaction->getItemId(); ?></td>
                    <td><?php echo $transaction->getDescription1(); ?></td>
                    <td><?php $trtype = TransactionTypesPeer::retrieveByPK($transaction->getTransactionTypeId());
            echo $trtype->getTitle(); ?></td>
                    <td><?php echo $transaction->getCreatedAt(); ?></td>
                    <td><?php
                        $abc = "";
                        $sho = new Criteria();

                        $sho->addJoin(PaymentTypesPeer::ID, OrderPaymentsPeer::PAYMENT_TYPE_ID, Criteria::LEFT_JOIN);
                        $sho->add(OrderPaymentsPeer::ORDER_ID, $transaction->getOrderId());
                        $paymentTypes = PaymentTypesPeer::doSelect($sho);
                        foreach ($paymentTypes as $paymentType) {
                            if ($abc == "") {
                                $abc = $paymentType->getTitle();
                            } else {
                                $abc = $abc . " , " . $paymentType->getTitle();
                            }
                        }
                        echo $abc;
                        ?></td>
                </tr>
    <?php $incrment++; ?>
<?php } ?>
                
                
                
                
                
                
                
                
        </tbody>
 <tr>
               
                <th><b><?php echo number_format($totalsoldprice,2); ?></b></th>
                
                <th><b><?php  echo $totalquantity; ?></b></th>
                <th><b><?php echo number_format($totalsellingprice,2); ?></b></th>
                <th><b>Total</b></th>
                <th> </th>
                <th> </th>

                <th> </th>
                <th> </th>
                <th> </th>
            </tr>
    </table>
</div>