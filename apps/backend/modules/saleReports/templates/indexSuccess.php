<h1>SaleReports List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Report date from</th>
      <th>Report date to</th>
      <th>Status</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Data xml</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sale_reports_list as $sale_reports): ?>
    <tr>
      <td><a href="<?php echo url_for('saleReports/edit?id='.$sale_reports->getId()) ?>"><?php echo $sale_reports->getId() ?></a></td>
      <td><?php echo $sale_reports->getReportDateFrom() ?></td>
      <td><?php echo $sale_reports->getReportDateTo() ?></td>
      <td><?php echo $sale_reports->getStatusId() ?></td>
      <td><?php echo $sale_reports->getCreatedAt() ?></td>
      <td><?php echo $sale_reports->getUpdatedAt() ?></td>
      <td><?php echo $sale_reports->getDataXml() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('saleReports/new') ?>">New</a>
