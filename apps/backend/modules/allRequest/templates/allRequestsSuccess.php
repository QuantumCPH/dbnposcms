<style>
    .odd{background-color: #EEEEFF}
    .even{background-color: #FFFFFF}
    .headings{ background-color: #CCCCFF;color: #000000;}
</style>

<div id="sf_admin_container">
    <?php if ($sf_user->hasFlash('message')): ?>
        <div class="save-ok"><h2><?php echo $sf_user->getFlash('message') ?></h2></div>
    <?php endif; ?>


    <h1><?php echo __('All Requests') ?>   </h1></div><br/>
<div id="sf_admin_container">

    <form method="get" action="">
        <fieldset>



            <!--    <div class="form-row">
                    <label for="transaction_type_id"><?php echo __('Transaction From'); ?></label>
    
                    <div class="content">
                        <select id="transaction_type_id" name="transaction_type_id">
                            <option value="0" selected="selected">All Transaction Types</option>
            <?php foreach ($transactionTypes as $transactionType) { ?>
                                    <option value="<?php echo $transactionType->getId(); ?>" <?php if ($transactionType->getId() == $transactionTypeId) { ?> selected="selected" <?php } ?> ><?php echo $transactionType->getName(); ?></option>
        
            <?php } ?>
                        </select>
                    </div>
                </div>
    
            -->
            <div class="form-row">
                <label for="agent_commission_agent_company_id">From:</label>
                <div class="content">
                    <input type="text"   name="startdate" autocomplete="off" id="stdate" style="width: 110px;" value="<?php
                    if (isset($startdate)) {
                        echo $startdate;
                    }
                    ?>" />
                </div>
            </div>



            <div class="form-row">
                <label class="datelable" style="width:35px;margin-top: 3px;">To:</label>
                <div class="content">
                    <input type="text"   name="enddate" autocomplete="off" id="endate" style="width: 110px;" value="<?php
                    if (isset($enddate)) {
                        echo $enddate;
                    }
                    ?>" />
                </div>
            </div>

            <div class="form-row">
                <label class="datelable" style="width:35px;margin-top: 3px;">Select All:</label>
                <div class="content">
                    <input type="checkbox"   name="selectall" <?php
                    if (isset($selectall) && $selectall == "on") {
                        echo 'checked="checked"';
                    }
                    ?> />
                </div>
            </div>


            <ul class="sf_admin_actions">
                <li>
                <li>
                    <input class="sf_admin_action_reset_filter" type="button" onclick="document.location.href = '/backend.php/allRequest/allRequests';" value="reset">
                </li>
                <input type="submit" name="Agent Company" class="sf_admin_action_filter" value="filter" />
                </li>
            </ul>
        </fieldset>

    </form>
    <br/>
</div>  
<div class="borderDiv">         
    <table  cellspacing="0" width="75%" class="tblAlign">
        <?php
        if ($dibsCalls) {
            ?> 
            <tr class="headings">
                <th width="5%"><?php echo __('ID') ?></th>
                <th  width="45%"><?php echo __('Call URL') ?> </th>
                <th  width="10%" style="text-align:left;"><?php echo __('Request From') ?> </th>
                <th width="10%" style="text-align:left;"><?php echo __('Created At') ?> </th>
                <th  width="15%" style="text-align:left;"><?php echo __('Agent Receipt') ?> </th>

                <th width="15%" ><?php echo __('Customer Receipt') ?></th>

            </tr>
            <?php
            $i = 0;
            $totPrice = 0;
            $totComssission = 0;
            $totResellerComssission = 0;
            foreach ($dibsCalls as $dibsCall) {
                $i++;
                ?>
                <tr <?php echo 'class="' . ($i % 2 == 0 ? 'odd' : 'even') . '"' ?>>
                    <td ><?php echo $dibsCall->getId(); ?></td>
                    <td ><?php echo $dibsCall->getCallurl(); ?></td>
                    <td><?php echo $dibsCall->getTransactionFrom()->getName(); ?></td>
                    <td><?php echo $dibsCall->getCreatedAt(); ?></td>
                    <td><?php echo $dibsCall->getAgentReceipt(); ?>  	&nbsp;</td>
                    <td><?php echo $dibsCall->getCustomerReceipt(); ?></td>

                </tr>
            <?php } ?>


            <?php
        } else {
            ?>
            <tr>
                <td> <?php echo __("No Request Found"); ?></td>
            </tr>
        <?php } ?>
    </table>
    <br/> <br/>
</div>