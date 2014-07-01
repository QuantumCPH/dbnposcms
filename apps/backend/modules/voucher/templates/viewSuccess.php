
<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/delivery_notes_title.png' ?>" align="left" />&nbsp;<span>History of <?php echo $parent->getId() ?></span></h1>
</div>
<div  class="backimgDiv">
    <input type="button" onclick="document.location.href = '<?php echo sfConfig::get('app_web_url')."/backend.php/voucher/index" ?>'" value="Back" class="btn btn-primary"/>
    
</div>
<div class="itemslist listviewpadding">
    <?php if ($sf_user->hasFlash('access_error')): ?>
        <div class="alert alert-warning">
            <?php echo $sf_user->getFlash('access_error') ?>
        </div>
    <?php endif; ?>
    <?php if ($sf_user->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?php echo $sf_user->getFlash('success') ?>
        </div>
    <?php endif; ?>
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
            <tr>

                <th>Voucher</th>
                <th>Issuer Branch Number</th>
                <th>Issuer Name</th>
                <th>Amount</th>
                <th>Created At</th>
                <th>Is Used</th>
                <th>Consumer Branch Number</th>
                <th>Consumer Name</th>
                <th>Used Amount</th>
                <th>Used at</th>
                <th>Balance</th>
            </tr>

        </thead>
        <tbody>
            <?php
            //var_dump($vouchers);
            foreach ($vouchers as $voucher) {
                ?>
                <tr>
                    <td><?php echo $voucher->getId() ?></td>
                    <td><?php
                        if ($voucher->getCreatedShopId() != '') {
                            echo ShopsPeer::retrieveByPK($voucher->getCreatedShopId())->getBranchNumber();
                        }
                        ?> </td>
                    <td><?php
                        if ($voucher->getCreatedUserId() != '') {
                            echo UserPeer::retrieveByPK($voucher->getCreatedUserId())->getName();
                        }
                        ?> </td>
                    <td><?php echo $voucher->getAmount(); ?></td>
                    <td><?php echo $voucher->getShopCreatedAt(); ?> </td>
                    <td><?php echo ($voucher->getIsUsed()) ? "Yes" : "No"; ?></td>
                    <td><?php
                        if ($voucher->getUsedShopId() != '') {

                            echo ShopsPeer::retrieveByPK($voucher->getUsedShopId())->getBranchNumber();
                        }
                        ?> </td>
                    <td><?php
                        if ($voucher->getUsedUserId() != '') {

                            echo UserPeer::retrieveByPK($voucher->getUsedUserId())->getName();
                        }
                        ?> </td>
                    <td><?php echo $voucher->getUsedAmount(); ?> </td>
                    <td><?php echo $voucher->getShopUsedAt(); ?> </td>

                    <td><?php echo $voucher->getAmount()-$voucher->getUsedAmount(); ?> </td>
                </tr>

                <?php
            }
            ?>

        </tbody>

    </table>

</div>

