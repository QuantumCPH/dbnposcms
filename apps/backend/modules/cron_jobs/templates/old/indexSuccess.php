<h1>Cron jobs List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Custom minutes</th>
      <th>Minutes</th>
      <th>Hours</th>
      <th>Custom hours</th>
      <th>Days</th>
      <th>Custom days</th>
      <th>Months</th>
      <th>Custom months</th>
      <th>Weekdays</th>
      <th>Custom weekdays</th>
      <th>Cron type</th>
      <th>Job</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($cron_jobs_list as $cron_jobs): ?>
    <tr>
      <td><a href="<?php echo url_for('cron_jobs/edit?id='.$cron_jobs->getId()) ?>"><?php echo $cron_jobs->getId() ?></a></td>
      <td><?php echo $cron_jobs->getCustomMinutes() ?></td>
      <td><?php echo $cron_jobs->getMinutes() ?></td>
      <td><?php echo $cron_jobs->getHours() ?></td>
      <td><?php echo $cron_jobs->getCustomHours() ?></td>
      <td><?php echo $cron_jobs->getDays() ?></td>
      <td><?php echo $cron_jobs->getCustomDays() ?></td>
      <td><?php echo $cron_jobs->getMonths() ?></td>
      <td><?php echo $cron_jobs->getCustomMonths() ?></td>
      <td><?php echo $cron_jobs->getWeekdays() ?></td>
      <td><?php echo $cron_jobs->getCustomWeekdays() ?></td>
      <td><?php echo $cron_jobs->getCronTypeId() ?></td>
      <td><?php echo $cron_jobs->getJob() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('cron_jobs/new') ?>">New</a>
