
<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/cron_title.png' ?>" align="left" />&nbsp;<span>Schedule Jobs</span></h1>
</div>
<div  class="backimgDiv">
    <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>cron_jobs/new';" value="Add" class="btn btn-primary"/>       
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
                <th>Job Name</th>
                <th>Months</th> 
                <th>Weekdays</th>  
                <th>Days</th> 
                <th>Hours</th>
                <th>Minutes</th>                
                <th>Schedule type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cron_jobs_list as $cron_jobs): ?>
                <tr>
                    <td><?php echo $cron_jobs->getJobName(); ?></td>
                    <td><?php
                        if ($cron_jobs->getMonths() == "select") {


                            
                            $monthSelect ="";
                            $arCustomMonth = explode(',', $cron_jobs->getCustomMonths());

                            for ($xmon = 1; $xmon <= 12; $xmon++) {
                                if (in_array($xmon, $arCustomMonth)) {
                                    if ($xmon == 1) {
                                        $monthSelect[]= "Jan";
                                    } elseif ($xmon == 2) {
                                        $monthSelect[] = "Feb";
                                    } elseif ($xmon == 3) {
                                        $monthSelect[] = "Mar";
                                    } elseif ($xmon == 4) {
                                        $monthSelect[] = "Apr";
                                    } elseif ($xmon == 5) {
                                        $monthSelect[] = "May";
                                    } elseif ($xmon == 6) {
                                        $monthSelect[] = "Jun";
                                    } elseif ($xmon == 7) {
                                        $monthSelect[] = "Jul";
                                    } elseif ($xmon == 8) {
                                        $monthSelect[] = "Aug";
                                    } elseif ($xmon == 9) {
                                        $monthSelect[] = "Sep";
                                    } elseif ($xmon == 10) {
                                        $monthSelect[] = "Oct";
                                    } elseif ($xmon == 11) {
                                        $monthSelect[] = "Nov";
                                    } else {
                                        $monthSelect[] = "Dec";
                                    }
                                }
                            }
                            
                            echo implode(", ", $monthSelect);
                        } else {

                            if ($cron_jobs->getMonths() == "*") {
                                echo "Every Month";
                            } elseif ($cron_jobs->getMonths() == "*/2") {
                                echo "Even Months";
                            } elseif ($cron_jobs->getMonths() == "1-11/2") {
                                echo "odd Months";
                            } elseif ($cron_jobs->getMonths() == "*/4") {
                                echo " Every 4 Months";
                            } elseif ($cron_jobs->getMonths() == "*/6") {
                                echo "   Every Half Year";
                            }
                        }
                        ?></td>
                     <td><?php
                        if ($cron_jobs->getWeekdays() == "select") {



                            $weekSelect = "";
                            $arCustomWeeks = explode(',', $cron_jobs->getCustomWeekdays());

                            for ($xweek = 0; $xweek <= 6; $xweek++) {
                                if (in_array($xweek, $arCustomWeeks)) {
                                    if ($xweek == 0) {
                                        $weekSelect[] = "Sun";
                                    } elseif ($xweek == 1) {
                                        $weekSelect[] =  "Mon";
                                    } elseif ($xweek == 2) {
                                        $weekSelect[] =  "Tue";
                                    } elseif ($xweek == 3) {
                                        $weekSelect[] =  "Wed";
                                    } elseif ($xweek == 4) {
                                        $weekSelect[] =  "Thu";
                                    } elseif ($xweek == 5) {
                                        $weekSelect[] =  "Fri";
                                    } else {
                                        $weekSelect[] =  "Sat";
                                    }
                                }
                            }
                            echo implode(", ", $weekSelect);
                        } else {

                            if ($cron_jobs->getWeekdays() == "*") {
                                echo "Every Weekday";
                            } elseif ($cron_jobs->getWeekdays() == "1-5") {
                                echo "Monday-Friday";
                            } elseif ($cron_jobs->getWeekdays() == "0,6") {
                                echo "Weekend Days";
                            }
                        }
                        ?></td> 
                        <td><?php
                        if ($cron_jobs->getDays() == "select") {


                            $daySelect = "";
                            $arCustomDay = explode(',', $cron_jobs->getCustomDays());
                            for ($xday = 1; $xday <= 31; $xday++) {
                                if (in_array($xday, $arCustomDay)) {
                                    $daySelect[]= $xday;
                                }
                            }
                            echo implode(", ", $daySelect);
                        } else {

                            if ($cron_jobs->getDays() == "*") {
                                echo "Every Day";
                            } elseif ($cron_jobs->getDays() == "*/2") {
                                echo "Even Days";
                            } elseif ($cron_jobs->getDays() == "1-31/2") {
                                echo "odd Days";
                            } elseif ($cron_jobs->getDays() == "*/5") {
                                echo " Every 5 Days";
                            } elseif ($cron_jobs->getDays() == "*/10") {
                                echo " Every 10 Days";
                            } elseif ($cron_jobs->getDays() == "*/15") {
                                echo " Every Half Month";
                            }
                        }
                        ?></td>  
                        <td><?php
                    if ($cron_jobs->getHours() == "select") {
                        $arCustomHours = explode(',', $cron_jobs->getCustomHours());
                        $hourSelect = "";
                        for ($xhours = 0; $xhours <= 23; $xhours++) {
                            if (in_array($xhours, $arCustomHours)) {

                                if ($xhours == 0) {
                                    $hourSelect[]= "Midnight";
                                } elseif ($xhours <= 11) {
                                    $hourSelect[]= $xhours . "am";
                                } elseif ($xhours == 12) {
                                    $hourSelect[]= "noon";
                                } else {
                                    $hourSelect[]= $xhours . "pm";
                                }
                            }
                        }
                        echo implode(", ",$hourSelect);
                    } else {

                        if ($cron_jobs->getHours() == "*") {
                            echo "Every Hour";
                        } elseif ($cron_jobs->getHours() == "*/2") {
                            echo "Even Hour";
                        } elseif ($cron_jobs->getHours() == "1-23/2") {
                            echo "odd Hour";
                        } elseif ($cron_jobs->getHours() == "*/6") {
                            echo " Every 6 Hour";
                        } elseif ($cron_jobs->getHours() == "*/12") {
                            echo " Every 12 Hour";
                        }
                    }
                        ?></td>
                        <td><?php
                        if ($cron_jobs->getMinutes() == "select") {


                            $arCustomMinute = explode(',', $cron_jobs->getCustomMinutes());
                            $minSelect="";
                            for ($xmin = 0; $xmin <= 59; $xmin++) {

                                if (in_array($xmin, $arCustomMinute)) {
                                    $minSelect[] =  $xmin;
                                }
                            }
                            echo implode(", ", $minSelect);
                        } else {

                            if ($cron_jobs->getMinutes() == "*") {
                                echo "Every Minute";
                            } elseif ($cron_jobs->getMinutes() == "*/2") {
                                echo "Even Minute";
                            } elseif ($cron_jobs->getMinutes() == "1-59/2") {
                                echo "odd Minute";
                            } elseif ($cron_jobs->getMinutes() == "*/5") {
                                echo " Every 5 Minutes";
                            } elseif ($cron_jobs->getMinutes() == "*/15") {
                                echo " Every 15 Minutes";
                            } elseif ($cron_jobs->getMinutes() == "*/30") {
                                echo "Every 30 Minutes";
                            }
                        }
                        ?></td>

                     
                      
                      
                      


                    <td><?php echo CronTypesPeer::retrieveByPK($cron_jobs->getCronTypeId())->getTitle(); ?></td>
                    <td align="center" style="padding-left:0">
                        <a href="<?php echo url_for('cron_jobs/edit?id=' . $cron_jobs->getId()) ?>">
                            <img src='<?php echo sfConfig::get("app_web_url"); ?>sf/sf_admin/images/edit_icon.png' />
                        </a>
                        <a href="<?php echo url_for('cron_jobs/delete?id=' . $cron_jobs->getId()) ?>" onclick="return confirm('Are you sure to delete schedule job?');">
                            <img src='<?php echo sfConfig::get("app_web_url"); ?>sf/sf_admin/images/delete.png' />
                        </a>
                        <a href="<?php echo url_for('cron_jobs/history?id=' . $cron_jobs->getId()) ?>" >
                            <img src='<?php echo sfConfig::get("app_web_url"); ?>sf/sf_admin/images/log_history.png' />
                        </a>
                    </td>
                </tr>
                    <?php endforeach; ?>
        </tbody>
    </table>
</div>

