<h1>Transactions List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Transaction type</th>
      <th>Quantity</th>
      <th>Item</th>
      <th>Branch receipt number</th>
      <th>Branch order number</th>
      <th>Status</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Parent type</th>
      <th>Parent type</th>
      <th>Sold price</th>
      <th>Discount value</th>
      <th>Discount type</th>
      <th>Branch transaction</th>
      <th>Branch</th>
      <th>Description1</th>
      <th>Description2</th>
      <th>Description3</th>
      <th>Supplier number</th>
      <th>Supplier item number</th>
      <th>Ean</th>
      <th>Group</th>
      <th>Color</th>
      <th>Size</th>
      <th>Buying price</th>
      <th>Selling price</th>
      <th>Taxation code</th>
      <th>Down sync</th>
      <th>User</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($transactions_list as $transactions): ?>
    <tr>
      <td><a href="<?php echo url_for('transactions/edit?id='.$transactions->getId()) ?>"><?php echo $transactions->getId() ?></a></td>
      <td><?php echo $transactions->getTransactionTypeId() ?></td>
      <td><?php echo $transactions->getQuantity() ?></td>
      <td><?php echo $transactions->getItemId() ?></td>
      <td><?php echo $transactions->getShopReceiptNumberId() ?></td>
      <td><?php echo $transactions->getShopOrderNumberId() ?></td>
      <td><?php echo $transactions->getStatusId() ?></td>
      <td><?php echo $transactions->getCreatedAt() ?></td>
      <td><?php echo $transactions->getUpdatedAt() ?></td>
      <td><?php echo $transactions->getParentType() ?></td>
      <td><?php echo $transactions->getParentTypeId() ?></td>
      <td><?php echo $transactions->getSoldPrice() ?></td>
      <td><?php echo $transactions->getDiscountValue() ?></td>
      <td><?php echo $transactions->getDiscountTypeId() ?></td>
      <td><?php echo $transactions->getShopTransactionId() ?></td>
      <td><?php echo $transactions->getShopId() ?></td>
      <td><?php echo $transactions->getDescription1() ?></td>
      <td><?php echo $transactions->getDescription2() ?></td>
      <td><?php echo $transactions->getDescription3() ?></td>
      <td><?php echo $transactions->getSupplierNumber() ?></td>
      <td><?php echo $transactions->getSupplierItemNumber() ?></td>
      <td><?php echo $transactions->getEan() ?></td>
      <td><?php echo $transactions->getGroup() ?></td>
      <td><?php echo $transactions->getColor() ?></td>
      <td><?php echo $transactions->getSize() ?></td>
      <td><?php echo $transactions->getBuyingPrice() ?></td>
      <td><?php echo $transactions->getSellingPrice() ?></td>
      <td><?php echo $transactions->getTaxationCode() ?></td>
      <td><?php echo $transactions->getDownSync() ?></td>
      <td><?php echo $transactions->getUserId() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('transactions/new') ?>">New</a>
