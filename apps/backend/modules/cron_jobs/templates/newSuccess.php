<script type="text/javascript">
    jQuery(function(){
       $('#cronJobGenerator').submit(function(){
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
            if(jobname==""){
                 alert('Please enter job name.');
                    return false;
            }
            
            
            var email = $("#email").val();
            if(email ==""){
                 alert('Please enter Email Address');
                    return false;
            }
            
            var defination_file_path = $("#defination_file_path").val();
            if(defination_file_path ==""){
                 alert('Please enter Defination File Path.');
                    return false;
            }
            
            var data_file_path = $("#data_file_path").val();
            if(data_file_path ==""){
                 alert('Please enter Data File Path');
                    return false;
            }
            
            if(data_file_path==defination_file_path){
                 alert('You entered same Data File Path and Defination File path. Please choose different!');
                    return false;
            }
            
            
                    
        });  
    });
    function handleCronJobGenerator(){
        $('select[name="selectMinutes[]"], select[name="selectHours[]"], select[name="selectDays[]"], select[name="selectMonths[]"], select[name="selectWeekdays[]"]').on('click', function(){
            $(this).parent().parent().find('input:radio').prop('checked', true);
        });
        
           
    }
</script>

<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/cron_title.png' ?>" align="left" />&nbsp;<span>New Schedule Jobs</span></h1>
</div>
<form method="POST" action="<?php echo url_for('cron_jobs/create')?>" name="" id="cronJobGenerator">
<div  class="backimgDiv">
      <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>cron_jobs/index';" value="Back" class="btn btn-cancel"/>
      <input type="submit" class="btn btn-primary" name="Generate" value="Save" />  
</div>
<div class="itemslist listviewpadding">
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('edit_error')): ?>
    <div class="alert alert-danger">
        <?php echo $sf_user->getFlash('edit_error') ?>
    </div>
    <?php endif;?>

    <div class="crontype-head">  
    <strong>Schedule type:</strong><br/>&nbsp;<select name="cron_type" class="cron_type form-control">
        <?php 
        foreach($types as $type){
            ?>
        <option value="<?php echo $type->getId(); ?>"><?php echo $type->getTitle(); ?></option>
        <?php
        }
        ?>
        
    </select></div>
     <div class="crontype-head">  
        <input type="text" name="job_name" id="job_name" placeholder="Job Name" class="input required">
    </div>
    <div class="crontype-head">  
        <input type="text" name="email" id="email" placeholder="email"   class="input required">
        can enter semicolon separated email address.
    </div>
    <div class="crontype-head">  
        <input type="text" name="defination_file_path" id="defination_file_path" placeholder="Defination file path" class="input required">
        for example: /items/xml or items/xml
    </div>
     <div class="crontype-head">  
        <input type="text" name="data_file_path" id="data_file_path" placeholder="Data file path"   class="input required">
        for example: /items/csv or items/csv
    </div>
    <i style="" class="tips">(Ctrl + Click to select multiple entries)</i>
    <table width="1013" class="generator">
        <tbody>


            <tr>
                <td width="339">
                    <table class="generatorBlock">
                        <tbody>
                            <tr>
                                <th colspan="2">Months</th>
                            </tr>
                            <tr>                        
                              <td width="203" valign="top" class="cron-label">                        
                                    <label for="everyMonth" class="radio"><input type="radio" name="months" value="*" checked="checked" id="everyMonth">    
                                        Every Month</label>
                                    <label for="everyEvenMonths" class="radio"><input type="radio" name="months" value="*/2" id="everyEvenMonths">
                                        Even Months</label>
                                    <label for="everyOddMonths" class="radio"><input type="radio" name="months" value="1-11/2" id="everyOddMonths">
                                        Odd Months</label>
                                    <label for="every4Months" class="radio"><input type="radio" name="months" value="*/4" id="every4Months">
                                        Every 4 Months</label>
                                    <label for="every6Months" class="radio"><input type="radio" name="months" value="*/6" id="every6Months">
                                        Every Half Year</label>
                                </td>
                                <td width="113" style="padding-right:10px;">
                                    <table class="multipleEntries">
                                        <tbody>
                                            <tr>                            
                                                <td width="28" style="padding:0 5px;"> 
                                                    <input type="radio" name="months" value="select">
                                                </td>
                                                <td width="75"><select class="cron" name="selectMonths[]" size="10" multiple="">
                                                  <option value="1">Jan</option>
                                                  <option value="2">Feb</option>
                                                  <option value="3">Mar</option>
                                                  <option value="4">Apr</option>
                                                  <option value="5">May</option>
                                                  <option value="6">Jun</option>
                                                  <option value="7">Jul</option>
                                                  <option value="8">Aug</option>
                                                  <option value="9">Sep</option>
                                                  <option value="10">Oct</option>
                                                  <option value="11">Nov</option>
                                                  <option value="12">Dec</option>
                                                </select></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td width="339">
                    <table class="generatorBlock">
                        <tbody>
                            <tr>
                                <th colspan="2">Weekday</th>
                            </tr>
                            <tr>                        
                                <td width="203" valign="top" class="cron-label">
                                    <label for="everyWeekday" class="radio"><input type="radio" checked="checked" value="*" name="weekdays" id="everyWeekday">
                                        Every Weekday</label>
                                    <label for="everyNonWeekenDays" class="radio"><input type="radio" value="1-5" name="weekdays" id="everyNonWeekenDays">
                                        Monday-Friday</label>                       
                                    <label for="everyWeekenDays" class="radio"><input type="radio" value="0,6" name="weekdays" id="everyWeekenDays">
                                        Weekend Days</label>
                                </td>
                                <td width="113" style="padding-right:10px;">
                                    <table class="multipleEntries">
                                        <tbody>
                                            <tr>
                                                <td style="padding:0 5px;">
                                                    <input type="radio" name="weekdays" value="select">
                                                </td>
                                                <td>
                                                    <select name="selectWeekdays[]" size="10" multiple="">
                                                        <option value="0">Sun</option>
                                                        <option value="1">Mon</option>
                                                        <option value="2">Tue</option>
                                                        <option value="3">Wed</option>
                                                        <option value="4">Thu</option>
                                                        <option value="5">Fri</option>
                                                        <option value="6">Sat</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                  </table>
                              </td>
                          </tr>
                      </tbody>
                  </table>
              </td>   
              <td width="339">
                    <table class="generatorBlock">
                        <tbody>
                            <tr>
                                <th colspan="2">Days</th>
                            </tr>
                            <tr>
                                <td width="203" valign="top" class="cron-label"> 
                                    <label for="everyday" class="radio"><input type="radio" name="days" value="*" checked="checked" id="everyday">
                                        Every Day</label>
                                    <label for="everyEvenDay" class="radio"><input type="radio" name="days" value="*/2" id="everyEvenDay">
                                        Even Days</label>
                                    <label for="everyOddDay" class="radio"><input type="radio" name="days" value="1-31/2" id="everyOddDay">
                                        Odd Days</label>
                                    <label for="every5Days" class="radio"><input type="radio" name="days" value="*/5" id="every5Days">
                                        Every 5 Days</label>
                                    <label for="every5Days" class="radio"><input type="radio" name="days" value="*/10" id="every5Days">
                                        Every 10 Days</label>                              
                                    <label for="every15Days" class="radio"><input type="radio" name="days" value="*/15" id="every15Days">
                                        Every Half Month</label>
                                </td>
                                <td width="113" valign="top" style="padding-right:10px;"> 
                                    <table class="multipleEntries">                        
                                        <tbody>
                                            <tr> 
                                                <td style="padding:0 5px;"> 
                                                    <input type="radio" name="days" value="select">
                                                </td>
                                                <td> 
                                                    <select name="selectDays[]" size="10" multiple="">
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>                        
                            </tr>
                        </tbody>
                    </table>
                </td>                
            </tr>
            <tr>
            <td valign="top" width="339">
                    <table class="generatorBlock">
                        <tbody>
                            <tr>
                                <th colspan="2">Hours</th>
                            </tr>
                            <tr>
                                <td width="203" valign="top" class="cron-label">
                                    <label for="everyHour" class="radio"><input type="radio" name="hours" value="*" checked="checked" id="everyHour">
                                        Every Hour</label>
                                    <label for="everyEvenHour" class="radio"><input type="radio" name="hours" value="*/2" id="everyEvenHour">
                                        Even Hours</label>
                                    <label for="everyOddHour" class="radio"><input type="radio" name="hours" value="1-23/2" id="everyOddHour">
                                        Odd Hours</label>
                                    <label for="every6Hours" class="radio"><input type="radio" name="hours" value="*/6" id="every6Hours">
                                        Every 6 Hours</label>
                                  <label for="every12Hours" class="radio"><input type="radio" name="hours" value="*/12" id="every12Hours">
                                      Every 12 Hours                                  </label>
                                </td>
                                <td width="113" style="padding-right:10px;">
                                    <table class="multipleEntries">
                                        <tbody>
                                            <tr> 
                                                <td style="padding:0 5px;"> 
                                                    <input type="radio" name="hours" value="select">
                                                </td>
                                                <td><span class="cron-label">
                                                  <select name="selectHours[]" size="10" multiple="">
                                                    <option value="0">Midnight</option>
                                                    <option value="1">1am</option>
                                                    <option value="2">2am</option>
                                                    <option value="3">3am</option>
                                                    <option value="4">4am</option>
                                                    <option value="5">5am</option>
                                                    <option value="6">6am</option>
                                                    <option value="7">7am</option>
                                                    <option value="8">8am</option>
                                                    <option value="9">9am</option>
                                                    <option value="10">10am</option>
                                                    <option value="11">11am</option>
                                                    <option value="12">Noon</option>
                                                    <option value="13">1pm</option>
                                                    <option value="14">2pm</option>
                                                    <option value="15">3pm</option>
                                                    <option value="16">4pm</option>
                                                    <option value="17">5pm</option>
                                                    <option value="18">6pm</option>
                                                    <option value="19">7pm</option>
                                                    <option value="20">8pm</option>
                                                    <option value="21">9pm</option>
                                                    <option value="22">10pm</option>
                                                    <option value="23">11pm</option>
                                                  </select>
                                                </span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign="top" width="339">
                    <table class="generatorBlock">
                        <tbody>
                            <tr>
                                <th colspan="2">Minutes</th>
                            </tr>
                            <tr>
                                <td width="203" valign="top" class="cron-label">
                                    <label for="everyMinute" class="radio"><input type="radio" name="minutes" value="*" checked="checked" id="everyMinute">
                                        Every Minute</label>
                                    <label for="everyEvenMinute" class="radio"><input type="radio" name="minutes" value="*/2" id="everyEvenMinute">
                                        Even Minutes</label>
                                    <label for="everyOddMinute" class="radio"><input type="radio" name="minutes" value="1-59/2" id="everyOddMinute">
                                        Odd Minutes</label>
                                    <label for="every5Minute" class="radio"><input type="radio" name="minutes" value="*/5" id="every5Minute">
                                        Every 5 Minutes</label>
                                    <label for="every15Minute" class="radio"><input type="radio" name="minutes" value="*/15" id="every15Minute">
                                        Every 15 Minutes</label>
                                    <label for="every30Minute" class="radio"><input type="radio" name="minutes" value="*/30" id="every30Minute">
                                        Every 30 Minutes</label>
                                </td>

                                <td width="113">
                                    <table class="multipleEntries">
                                        <tbody>
                                            <tr> 
                                                <td style="padding:0 5px;">
                                                    <input type="radio" name="minutes" value="select">
                                                </td>
                                                <td width="75">
                                                    <select name="selectMinutes[]" size="10" multiple="">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                        <option value="32">32</option>
                                                        <option value="33">33</option>
                                                        <option value="34">34</option>
                                                        <option value="35">35</option>
                                                        <option value="36">36</option>
                                                        <option value="37">37</option>
                                                        <option value="38">38</option>
                                                        <option value="39">39</option>
                                                        <option value="40">40</option>
                                                        <option value="41">41</option>
                                                        <option value="42">42</option>
                                                        <option value="43">43</option>
                                                        <option value="44">44</option>
                                                        <option value="45">45</option>
                                                        <option value="46">46</option>
                                                        <option value="47">47</option>
                                                        <option value="48">48</option>
                                                        <option value="49">49</option>
                                                        <option value="50">50</option>
                                                        <option value="51">51</option>
                                                        <option value="52">52</option>
                                                        <option value="53">53</option>
                                                        <option value="54">54</option>
                                                        <option value="55">55</option>
                                                        <option value="56">56</option>
                                                        <option value="57">57</option>
                                                        <option value="58">58</option>
                                                        <option value="59">59</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>        
                
                
            </tr>        
            
            <tr>
                <td colspan="3">
                   
                </td>
            </tr>

           
        </tbody>
    </table>

    


    
    
</div></form>