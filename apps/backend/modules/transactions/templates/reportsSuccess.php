 

<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Reports</h1>
</div>

<div class="itemslist listviewpadding "><br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
        <div class="alert alert-warning">
            <?php echo $sf_user->getFlash('access_error') ?>
        </div>
    <?php endif; ?>
    <h1>Products</h1>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="productNameReport">
                <div class="thumbnail">

                    <div class="caption">
                        <h3 class="text-center">Name</h3>
                        <p class="text-center">Grass Sale</p>

                    </div>
                </div>
            </a>
        </div>
    </div>
    <h1>Orders</h1>    
    <div class="row">
        <div class="col-sm-6 col-md-3">
              <a href="monthlySale">
            <div class="thumbnail">
                <div class="caption">
                    <h3 class="text-center">Month</h3>
                    <p class="text-center">Total Sale</p>

                </div>
            </div>
              </a>
            
        </div>
<!--        <div class="col-sm-6 col-md-3">
            <div class="thumbnail">

                <div class="caption">
                    <h3>Name</h3>
                    <p>...</p>

                </div>
            </div>
        </div>-->
        <div class="col-sm-6 col-md-3">
               <a href="staffSale">
            <div class="thumbnail">
                <div class="caption">
                    <h3 class="text-center">Staff</h3>
                    <p class="text-center">Total Sale</p>

                </div>
            </div>
               </a>
        </div>
        <div class="col-sm-6 col-md-3">
             <a href="staffDailySale">
            <div class="thumbnail">
                <div class="caption">
                    <h3 class="text-center">Staff(Daily)</h3>
                    <p class="text-center">Total Sale</p>
                </div>
            </div>
                </a>  
        </div>
    </div>  
    <h1>Payments</h1>        
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="paymentMethod">
                <div class="thumbnail">

                    <div class="caption">
                        <h3  class="text-center">Payment Method</h3>
                        <p  class="text-center">Total Payments</p>

                    </div>
                </div>
            </a>
        </div>
    </div>
    <h1>Taxes</h1>        
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="taxRate">
                <div class="thumbnail">
                    <div class="caption">
                        <h3 class="text-center">Tax Rate</h3>
                        <p class="text-center">Amount On Paid Orders</p>

                    </div>
                </div>
            </a>
        </div>
    </div>

</div>
