<div id="sf_admin_container"><h1>Vendors List</h1>

<br />
  <div id="sf_admin_container"><a href="<?php echo url_for('venders/new') ?>" class="external_link">Create New</a></div>
  <br />
<table width="75%" cellspacing="0" cellpadding="2" class="tblAlign">
    <thead>
        <tr class="headings">
      <th>Id</th>
      <th>Title</th>
      <th>Logo</th>
      <th>Status</th>
      <th>Created at</th>
    </tr>
  </thead>
  <tbody>
    <?php   $incrment=1;    ?>
    <?php foreach ($vendors_list as $vendors): ?>
    <?php
        if($incrment%2==0){
         $class= 'class="even"';
        }else{
         $class= 'class="odd"';
        }
        ?>
        <tr <?php echo $class;?>>
      <td><a href="<?php echo url_for('vendors/edit?id='.$vendors->getId()) ?>"><?php echo $vendors->getId() ?></a></td>
      <td><?php echo $vendors->getTitle() ?></td>
      <td><?php echo $vendors->getLogo() ?></td>
      <td><?php echo $vendors->getStatus() ?></td>
      <td><?php echo $vendors->getCreatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <div id="sf_admin_container"><a href="<?php echo url_for('venders/new') ?>" class="external_link">Create New</a></div>
