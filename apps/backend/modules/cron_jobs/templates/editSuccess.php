 
<script type="text/javascript">
    jQuery(function() {
        $('#cronJobGenerator').submit(function() {
            var minutes = $("input:radio[name='minutes']:checked").val();
            if (minutes == 'select') {
                var selectMinutes = $('select[name="selectMinutes[]"]').val();
                if (!selectMinutes) {
                    alert('Please choose at least one entry in minute field.');
                    return false;
                }
            }
            var hours = $("input:radio[name='hours']:checked").val();
            if (hours == 'select') {
                var selectHours = $('select[name="selectHours[]"]').val();
                if (!selectHours) {
                    alert('Please choose at least one entry in hour field.');
                    return false;
                }
            }
            var days = $("input:radio[name='days']:checked").val();
            if (days == 'select') {
                var selectDays = $('select[name="selectDays[]"]').val();
                if (!selectDays) {
                    alert('Please choose at least one entry in day field.');
                    return false;
                }
            }
            var months = $("input:radio[name='months']:checked").val();
            if (months == 'select') {
                var selectMonths = $('select[name="selectMonths[]"]').val();
                if (!selectMonths) {
                    alert('Please choose at least one entry in month field.');
                    return false;
                }
            }
            var weekdays = $("input:radio[name='weekdays']:checked").val();
            if (weekdays == 'select') {
                var selectWeekdays = $('select[name="selectWeekdays[]"]').val();
                if (!selectWeekdays) {
                    alert('Please choose at least one entry in weekday field.');
                    return false;
                }
            }
            var jobname = $("#job_name").val();
            if (jobname == "") {
                alert('Please enter job name.');
                return false;
            }


            var email = $("#email").val();
            if (email == "") {
                alert('Please enter Email Address');
                return false;
            }

            var defination_file_path = $("#defination_file_path").val();
            if (defination_file_path == "") {
                alert('Please enter Defination File Path.');
                return false;
            }

            var data_file_path = $("#data_file_path").val();
            if (data_file_path == "") {
                alert('Please enter Data File Path');
                return false;
            }
            
            if(data_file_path==defination_file_path){
                 alert('You entered same Data File Path and Defination File path. Please choose different!');
                    return false;
            }


        });
    });
    function handleCronJobGenerator() {
        $('select[name="selectMinutes[]"], select[name="selectHours[]"], select[name="selectDays[]"], select[name="selectMonths[]"], select[name="selectWeekdays[]"]').on('click', function() {
            $(this).parent().parent().find('input:radio').prop('checked', true);
        });


    }
</script>
<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/cron_title.png' ?>" align="left" />&nbsp;<span>Edit Schedule Job</span></h1>
</div>
<form method="POST" action="<?php echo url_for('cron_jobs/update') ?>" name="" id="cronJobGenerator">
    <div  class="backimgDiv">
        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>cron_jobs/index';" value="Back" class="btn btn-cancel"/>
        <input type="submit" class="btn btn-primary" name="Generate" value="Update" /> 
    </div>
    <div class="itemslist listviewpadding">
        <?php if ($sf_user->hasFlash('access_error')): ?>
            <div class="alert alert-warning">
                <?php echo $sf_user->getFlash('access_error') ?>
            </div>
        <?php endif; ?>
        <?php if ($sf_user->hasFlash('message')): ?>
            <div class="alert alert-success">
                <?php echo $sf_user->getFlash('message') ?>
            </div>
        <?php endif; ?>
        <?php if ($sf_user->hasFlash('edit_error')): ?>
            <div class="alert alert-danger">
                <?php echo $sf_user->getFlash('edit_error') ?>
            </div>
        <?php endif; ?>
        <input type="hidden" name="cron_job_id" id="cron_job_id"  class="input required" value="<?php echo $cron_jobs->getId(); ?>">
        <div class="crontype-head">  
           
            <strong>Schedule type:</strong><br/>&nbsp;<select name="cron_type_id" class="cron_type form-control">
                <?php
                foreach ($types as $type) {
                    ?>
                    <option value="<?php echo $type->getId(); ?>" <?php if ($type->getId() == $cron_jobs->getCronTypeId()) { ?> selected="selected"   <?php } ?>><?php echo $type->getTitle(); ?></option>
                    <?php
                }
                ?>

            </select></div>
        <div class="crontype-head">  
            <input type="text" name="job_name" id="job_name" placeholder="Job Name" class="input required" value="<?php echo $cron_jobs->getJobName(); ?>">
        </div>
        <div class="crontype-head">  
            <input type="text" name="email" id="email" placeholder="email"   class="input required" value="<?php echo $cron_jobs->getEmail(); ?>">
            can enter semicolon separated email address.
        </div>
        <div class="crontype-head">  
            <input type="text" name="defination_file_path" id="defination_file_path" value="<?php echo $cron_jobs->getDefinationFilePath(); ?>" placeholder="Defination file path" class="input required">
            for example: /items/xml or items/xml
        </div>
        <div class="crontype-head">  
            <input type="text" name="data_file_path" id="data_file_path" value="<?php echo $cron_jobs->getDataFilePath(); ?>"  placeholder="Data file path"   class="input required">
            for example: /items/csv or items/csv
        </div>

        <table class="generator">
            <tbody>
                <tr>
                    <td width="339"><table class="generatorBlock">
                            <tbody>
                                <tr>
                                    <th colspan="2">Months</th>
                                </tr>
                                <tr>
                                    <td width="203" valign="top" class="cron-label"><label for="everyMonth" class="radio">
                                            <input type="radio" name="months" value="*"  <?php if ($cron_jobs->getMonths() == "*") { ?> checked="checked"   <?php } ?>  id="everyMonth">
                                            Every Month</label>
                                        <label for="everyEvenMonths" class="radio">
                                            <input type="radio" name="months" value="*/2"   <?php if ($cron_jobs->getMonths() == "*/2") { ?> checked="checked"   <?php } ?>  id="everyEvenMonths">
                                            Even Months</label>
                                        <label for="everyOddMonths" class="radio">
                                            <input type="radio" name="months" value="1-11/2"   <?php if ($cron_jobs->getMonths() == "1-11/2") { ?> checked="checked"   <?php } ?>  id="everyOddMonths">
                                            Odd Months</label>
                                        <label for="every4Months" class="radio">
                                            <input type="radio" name="months" value="*/4"   <?php if ($cron_jobs->getMonths() == "*/4") { ?> checked="checked"   <?php } ?>  id="every4Months">
                                            Every 4 Months</label>
                                        <label for="every6Months" class="radio">
                                            <input type="radio" name="months" value="*/6"   <?php if ($cron_jobs->getMonths() == "*/6") { ?> checked="checked"   <?php } ?>  id="every6Months">
                                            Every Half Year</label></td>
                                    <td width="113" style="padding-right:10px;"><table class="multipleEntries">
                                            <tbody>
                                                <tr>
                                                    <td width="28" style="padding:0 5px;"><input type="radio" name="months" value="select"   <?php if ($cron_jobs->getMonths() == "select") { ?> checked="checked"   <?php } ?>  ></td>
                                                    <td width="75"><select class="cron" name="selectMonths[]" size="10" multiple="">
                                                            <?php
                                                            $arCustomMonth = explode(',', $cron_jobs->getCustomMonths());

                                                            for ($xmon = 1; $xmon <= 12; $xmon++) {
                                                                ?>
                                                                <option value="<?php echo $xmon; ?>"  <?php if (in_array($xmon, $arCustomMonth)) { ?> selected="Selected" <?php } ?>    >
                                                                    <?php
                                                                    if ($xmon == 1) {
                                                                        echo "Jan";
                                                                    } elseif ($xmon == 2) {
                                                                        echo "Feb";
                                                                    } elseif ($xmon == 3) {
                                                                        echo "Mar";
                                                                    } elseif ($xmon == 4) {
                                                                        echo "Apr";
                                                                    } elseif ($xmon == 5) {
                                                                        echo "May";
                                                                    } elseif ($xmon == 6) {
                                                                        echo "Jun";
                                                                    } elseif ($xmon == 7) {
                                                                        echo "Jul";
                                                                    } elseif ($xmon == 8) {
                                                                        echo "Aug";
                                                                    } elseif ($xmon == 9) {
                                                                        echo "Sep";
                                                                    } elseif ($xmon == 10) {
                                                                        echo "Oct";
                                                                    } elseif ($xmon == 11) {
                                                                        echo "Nov";
                                                                    } else {
                                                                        echo "Dec";
                                                                    }
                                                                    ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table></td>
                                </tr>
                            </tbody>
                        </table></td>
                    <td width="339"><table class="generatorBlock">
                            <tbody>
                                <tr>
                                    <th colspan="2">Weekday</th>
                                </tr>
                                <tr>
                                    <td width="203" valign="top" class="cron-label"><label for="everyWeekday" class="radio">
                                            <input type="radio"   value="*"    <?php if ($cron_jobs->getWeekdays() == "*") { ?> checked="checked"   <?php } ?>    name="weekdays" id="everyWeekday">
                                            Every Weekday</label>
                                        <label for="everyNonWeekenDays" class="radio">
                                            <input type="radio" value="1-5" name="weekdays"   <?php if ($cron_jobs->getWeekdays() == "1-5") { ?> checked="checked"   <?php } ?> id="everyNonWeekenDays">
                                            Monday-Friday</label>
                                        <label for="everyWeekenDays" class="radio">
                                            <input type="radio" value="0,6"   <?php if ($cron_jobs->getWeekdays() == "0,6") { ?> checked="checked"   <?php } ?>  name="weekdays" id="everyWeekenDays">
                                            Weekend Days</label></td>
                                    <td width="113" style="padding-right:10px;"><table class="multipleEntries">
                                            <tbody>
                                                <tr>
                                                    <td style="padding:0 5px;"><input type="radio" name="weekdays" value="select"   <?php if ($cron_jobs->getWeekdays() == "select") { ?> checked="checked"   <?php } ?> ></td>
                                                    <td><select name="selectWeekdays[]" size="10" multiple="">
                                                            <?php
                                                            $arCustomWeeks = explode(',', $cron_jobs->getCustomWeekdays());

                                                            for ($xweek = 0; $xweek <= 6; $xweek++) {
                                                                ?>
                                                                <option value="<?php echo $xweek; ?>"  <?php if (in_array($xweek, $arCustomWeeks)) { ?> selected="Selected" <?php } ?>    >
                                                                    <?php
                                                                    if ($xweek == 0) {
                                                                        echo "Sun";
                                                                    } elseif ($xweek == 1) {
                                                                        echo "Mon";
                                                                    } elseif ($xweek == 2) {
                                                                        echo "Tue";
                                                                    } elseif ($xweek == 3) {
                                                                        echo "Wed";
                                                                    } elseif ($xweek == 4) {
                                                                        echo "Thu";
                                                                    } elseif ($xweek == 5) {
                                                                        echo "Fri";
                                                                    } else {
                                                                        echo "Sat";
                                                                    }
                                                                    ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table></td>
                                </tr>
                            </tbody>
                        </table></td>
                    <td valign="top" width="339"><table class="generatorBlock">
                            <tbody>
                                <tr>
                                    <th colspan="2">Days</th>
                                </tr>
                                <tr>
                                    <td width="203" valign="top" class="cron-label"><label for="everyday" class="radio">
                                            <input type="radio" name="days" value="*"  <?php if ($cron_jobs->getDays() == "*") { ?> checked="checked"   <?php } ?> id="everyday">
                                            Every Day</label>
                                        <label for="everyEvenDay" class="radio">
                                            <input type="radio" name="days" value="*/2"  <?php if ($cron_jobs->getDays() == "*/2") { ?> checked="checked"   <?php } ?>  id="everyEvenDay">
                                            Even Days</label>
                                        <label for="everyOddDay" class="radio">
                                            <input type="radio" name="days" value="1-31/2"  <?php if ($cron_jobs->getDays() == "select") { ?> checked="1-31/2"   <?php } ?>  id="everyOddDay">
                                            Odd Days</label>
                                        <label for="every5Days" class="radio">
                                            <input type="radio" name="days" value="*/5"  <?php if ($cron_jobs->getDays() == "*/5") { ?> checked="checked"   <?php } ?>  id="every5Days">
                                            Every 5 Days</label>
                                        <label for="every5Days" class="radio">
                                            <input type="radio" name="days" value="*/10"  <?php if ($cron_jobs->getDays() == "*/10") { ?> checked="checked"   <?php } ?>  id="every5Days">
                                            Every 10 Days</label>
                                        <label for="every15Days" class="radio">
                                            <input type="radio" name="days" value="*/15"   <?php if ($cron_jobs->getDays() == "*/15") { ?> checked="checked"   <?php } ?>  id="every15Days">
                                            Every Half Month</label></td>
                                    <td valign="top" width="113" style="padding-right:10px;"><table class="multipleEntries">
                                            <tbody>
                                                <tr>
                                                    <td style="padding:0 5px;"><input type="radio" name="days" value="select"  <?php if ($cron_jobs->getDays() == "select") { ?> checked="checked"   <?php } ?> ></td>
                                                    <td><select name="selectDays[]" size="10" multiple="">
                                                            <?php
                                                            $arCustomDay = explode(',', $cron_jobs->getCustomDays());
                                                            for ($xday = 1; $xday <= 31; $xday++) {
                                                                ?>
                                                                <option value="<?php echo $xday; ?>"  <?php if (in_array($xday, $arCustomDay)) { ?> selected="Selected" <?php } ?> ><?php echo $xday; ?></option>
                                                            <?php }
                                                            ?>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table></td>
                                </tr>
                            </tbody>
                        </table></td>
                </tr>
                <tr>
                    <td valign="top" width="339"><table class="generatorBlock">
                            <tbody>
                                <tr>
                                    <th colspan="2">Hours</th>
                                </tr>
                                <tr>
                                    <td width="203" valign="top" class="cron-label"><label for="everyHour" class="radio">
                                            <input type="radio" name="hours" value="*"  <?php if ($cron_jobs->getHours() == "*") { ?> checked="checked"   <?php } ?> id="everyHour">
                                            Every Hour</label>
                                        <label for="everyEvenHour" class="radio">
                                            <input type="radio" name="hours" value="*/2"   <?php if ($cron_jobs->getHours() == "*/2") { ?> checked="checked"   <?php } ?> id="everyEvenHour">
                                            Even Hours</label>
                                        <label for="everyOddHour" class="radio">
                                            <input type="radio" name="hours" value="1-23/2"   <?php if ($cron_jobs->getHours() == "1-23/2") { ?> checked="checked"   <?php } ?> id="everyOddHour">
                                            Odd Hours</label>
                                        <label for="every6Hours" class="radio">
                                            <input type="radio" name="hours" value="*/6"   <?php if ($cron_jobs->getHours() == "*/6") { ?> checked="checked"   <?php } ?>id="every6Hours">
                                            Every 6 Hours</label>
                                        <label for="every12Hours" class="radio">
                                            <input type="radio" name="hours" value="*/12"   <?php if ($cron_jobs->getHours() == "*/12") { ?> checked="checked"   <?php } ?> id="every12Hours">
                                            Every 12 Hours</label></td>
                                    <td width="113" style="padding-right:10px;"><table class="multipleEntries">
                                            <tbody>
                                                <tr>
                                                    <td style="padding:0 5px;"><input type="radio" name="hours" value="select"   <?php if ($cron_jobs->getHours() == "select") { ?> checked="checked"   <?php } ?>></td>
                                                    <td><select name="selectHours[]" size="10" multiple="">
                                                            <?php
                                                            $arCustomHours = explode(',', $cron_jobs->getCustomHours());

                                                            for ($xhours = 0; $xhours <= 23; $xhours++) {
                                                                ?>
                                                                <option value="<?php echo $xhours; ?>"  <?php if (in_array($xhours, $arCustomHours)) { ?> selected="Selected" <?php } ?>    >
                                                                    <?php
                                                                    if ($xhours == 0) {
                                                                        echo "Midnight";
                                                                    } elseif ($xhours <= 11) {
                                                                        echo $xhours . "am";
                                                                    } elseif ($xhours == 12) {
                                                                        echo "noon";
                                                                    } else {
                                                                        echo $xhours . "pm";
                                                                    }
                                                                    ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table></td>
                                </tr>
                            </tbody>
                        </table></td>
                    <td valign="top" width="339"><table class="generatorBlock">
                            <tbody>
                                <tr>
                                    <th colspan="2">Minutes</th>
                                </tr>
                                <tr>
                                    <td width="203" valign="top" class="cron-label"><label for="everyMinute" class="radio">
                                            <input type="radio" name="minutes" value="*"  <?php if ($cron_jobs->getMinutes() == "*") { ?> checked="checked"   <?php } ?>  id="everyMinute">
                                            Every Minute</label>
                                        <label for="everyEvenMinute" class="radio">
                                            <input type="radio" name="minutes" value="*/2"   <?php if ($cron_jobs->getMinutes() == "*/2") { ?> checked="checked"   <?php } ?>   id="everyEvenMinute">
                                            Even Minutes</label>
                                        <label for="everyOddMinute" class="radio">
                                            <input type="radio" name="minutes" value="1-59/2"  <?php if ($cron_jobs->getMinutes() == "1-59/2") { ?> checked="checked"   <?php } ?>  id="everyOddMinute">
                                            Odd Minutes</label>
                                        <label for="every5Minute" class="radio">
                                            <input type="radio" name="minutes" value="*/5"   <?php if ($cron_jobs->getMinutes() == "*/5") { ?> checked="checked"   <?php } ?>   id="every5Minute">
                                            Every 5 Minutes</label>
                                        <label for="every15Minute" class="radio">
                                            <input type="radio" name="minutes" value="*/15"   <?php if ($cron_jobs->getMinutes() == "*/15") { ?> checked="checked"   <?php } ?>  id="every15Minute">
                                            Every 15 Minutes</label>
                                        <label for="every30Minute" class="radio">
                                            <input type="radio" name="minutes" value="*/30"   <?php if ($cron_jobs->getMinutes() == "*/30") { ?> checked="checked"   <?php } ?>   id="every30Minute">
                                            Every 30 Minutes</label></td>
                                    <td width="113"><table class="multipleEntries">
                                            <tbody>
                                                <tr>
                                                    <td style="padding:0 5px;"><input type="radio" name="minutes" value="select"   <?php if ($cron_jobs->getMinutes() == "select") { ?> checked="checked"   <?php } ?>  ></td>
                                                    <td width="75"><select name="selectMinutes[]" size="10" multiple="">
                                                            <?php
                                                            $arCustomMinute = explode(',', $cron_jobs->getCustomMinutes());

                                                            for ($xmin = 0; $xmin <= 59; $xmin++) {
                                                                ?>
                                                                <option value="<?php echo $xmin; ?>"  <?php if (in_array($xmin, $arCustomMinute)) { ?> selected="Selected" <?php } ?>    ><?php echo $xmin; ?></option>
                                                            <?php }
                                                            ?>
                                                        </select></td>
                                                </tr>
                                            </tbody>
                                        </table></td>
                                </tr>
                            </tbody>
                        </table></td>

                </tr>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</form>