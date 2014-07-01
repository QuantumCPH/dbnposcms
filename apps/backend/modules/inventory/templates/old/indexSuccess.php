<h1>Inventory List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Branch</th>
      <th>CMS item</th>
      <th>Total</th>
      <th>Sold</th>
      <th>Book out</th>
      <th>Returned</th>
      <th>Available</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Item</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($inventory_list as $inventory): ?>
    <tr>
      <td><a href="<?php echo url_for('inventory/edit?id='.$inventory->getId()) ?>"><?php echo $inventory->getId() ?></a></td>
      <td><?php echo $inventory->getShopId() ?></td>
      <td><?php echo $inventory->getCmsItemId() ?></td>
      <td><?php echo $inventory->getTotal() ?></td>
      <td><?php echo $inventory->getSold() ?></td>
      <td><?php echo $inventory->getBookOut() ?></td>
      <td><?php echo $inventory->getReturned() ?></td>
      <td><?php echo $inventory->getAvailable() ?></td>
      <td><?php echo $inventory->getCreatedAt() ?></td>
      <td><?php echo $inventory->getUpdatedAt() ?></td>
      <td><?php echo $inventory->getItemId() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('inventory/new') ?>">New</a>
