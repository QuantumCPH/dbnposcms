
<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/active-item2.png' ?>" />&nbsp;Item No. <?php echo $item->getId() ?></h1>

    <table class="table table-striped">
        <thead>
            <tr><td colspan="14"><h3>Item Detail</h3></td> </tr>
            <tr>

                <th>Item No.</th>
                <th>Item Pic</th>
                <th>Des.1</th>
                <th>Des.2</th>
                <th>Des.3</th>
                <th>Supplier No.</th>
                <th>Supplier item No.</th>
                <th>Group</th>
                <th>Color</th>
                <th>Size</th>
                <th>Buying Price</th>
                <th>Selling price</th>
                <th>Taxation Code</th>

                <th>Updated At</th>
            </tr>

        </thead>
        <tbody>

            <?php
            $items = $item;
            $itemsCount = 0;
            ?>

            <tr>

                <td> <?php echo $items->getId() ?></td>
                <td><?php echo $items->getSmallPic() == "" ? "<img src='" . sfConfig::get("app_web_url") . "images/no-image.png' />" : "" ?></td>
                <td><?php echo $items->getDescription1() ?></td>
                <td><?php echo $items->getDescription2() ?></td>
                <td><?php echo $items->getDescription3() ?></td>
                <td><?php echo $items->getSupplierNumber() ?></td>
                <td><?php echo $items->getSupplierItemNumber() ?></td>
                <td><?php echo $items->getGroup() ?></td>
                <td><?php echo $items->getColor() ?></td>
                <td><?php echo $items->getSize() ?></td>
                <td><?php echo $items->getBuyingPrice() ?></td>
                <td><?php echo $items->getSellingPrice() ?></td>
                <td><?php echo $items->getTaxationCode() ?></td>
                <td><?php echo date("Y-m-d", strtotime($items->getUpdatedAt())); ?></td>

            </tr>

            <tr><td colspan="14"><h3>Item History</h3></td> </tr>
<?php foreach ($log_items as $items): ?>
                <tr>

                    <td> <?php echo $items->getItemId() ?></td>
                    <td><?php echo $items->getSmallPic() == "" ? "<img src='" . sfConfig::get("app_web_url") . "images/no-image.png' />" : "" ?></td>
                    <td><?php echo $items->getDescription1() ?></td>
                    <td><?php echo $items->getDescription2() ?></td>
                    <td><?php echo $items->getDescription3() ?></td>
                    <td><?php echo $items->getSupplierNumber() ?></td>
                    <td><?php echo $items->getSupplierItemNumber() ?></td>
                    <td><?php echo $items->getGroup() ?></td>
                    <td><?php echo $items->getColor() ?></td>
                    <td><?php echo $items->getSize() ?></td>
                    <td><?php echo $items->getBuyingPrice() ?></td>
                    <td><?php echo $items->getSellingPrice() ?></td>
                    <td><?php echo $items->getTaxationCode() ?></td>
                    <td><?php echo date("Y-m-d", strtotime($items->getUpdatedAt())); ?></td>

                </tr>
                <?php $itemsCount++; ?>
<?php endforeach; ?>
            <tr><td colspan="14" align="left"><strong>Total Items. <?php echo $itemsCount; ?></strong></td></tr>
        </tbody>

    </table>


</div>