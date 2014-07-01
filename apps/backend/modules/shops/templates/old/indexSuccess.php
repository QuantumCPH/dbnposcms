<h1>Branches List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Branch number</th>
      <th>Company number</th>
      <th>Is configured</th>
      <th>Created by</th>
      <th>Created at</th>
      <th>Configured at</th>
      <th>First login</th>
      <th>Password</th>
      <th>Status</th>
      <th>Address</th>
      <th>Zip</th>
      <th>Place</th>
      <th>Country</th>
      <th>Tel</th>
      <th>Fax</th>
      <th>Item sync requested at</th>
      <th>Item sync synced at</th>
      <th>Pic sync requested at</th>
      <th>Pic sync synced at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($shops_list as $shops): ?>
    <tr>
      <td><a href="<?php echo url_for('shops/edit?id='.$shops->getId()) ?>"><?php echo $shops->getId() ?></a></td>
      <td><?php echo $shops->getName() ?></td>
      <td><?php echo $shops->getBranchNumber() ?></td>
      <td><?php echo $shops->getCompanyNumber() ?></td>
      <td><?php echo $shops->getIsConfigured() ?></td>
      <td><?php echo $shops->getCreatedBy() ?></td>
      <td><?php echo $shops->getCreatedAt() ?></td>
      <td><?php echo $shops->getConfiguredAt() ?></td>
      <td><?php echo $shops->getFirstLogin() ?></td>
      <td><?php echo $shops->getPassword() ?></td>
      <td><?php echo $shops->getStatusId() ?></td>
      <td><?php echo $shops->getAddress() ?></td>
      <td><?php echo $shops->getZip() ?></td>
      <td><?php echo $shops->getPlace() ?></td>
      <td><?php echo $shops->getCountry() ?></td>
      <td><?php echo $shops->getTel() ?></td>
      <td><?php echo $shops->getFax() ?></td>
      <td><?php echo $shops->getItemSyncRequestedAt() ?></td>
      <td><?php echo $shops->getItemSyncSyncedAt() ?></td>
      <td><?php echo $shops->getPicSyncRequestedAt() ?></td>
      <td><?php echo $shops->getPicSyncSyncedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>


