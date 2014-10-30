
<div class="itemslist">
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/dashboard_over.png'?>" />&nbsp;Dashboard</h1>
   
</div>
 
<div class="topcontainer">
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <div class="regForm">
        <div class="over-border">
            <div class="overview">
                <h2>Overview</h2>
                <div class="labels">Todays Sale</div><div class="values">$29,945.38</div>
                <br clear="all" />
                <div class="labels">Total</div><div class="values">$10,168,039.87</div>
                <br clear="all" />
                <div class="labels">Branch 1</div><div class="values"></div>
                <br clear="all" />
                <div class="labels">Branch 2</div><div class="values"></div>
                <br clear="all" />
            </div>
        </div>
        <div class="totasales">
               <h2>Top Sale</h2>
               <div class="labels">Branch 1</div><div class="values">$29,945.38</div>
                <br clear="all" />
                <div class="labels">Branch 2</div><div class="values">$10,168,039.87</div>
                <br clear="all" />
                <div class="chart">
                    <img src="<?php echo sfConfig::get("app_web_url");?>images/overview-chart.png" />
                </div>
                <br clear="all" />
                <div class="labels">Total</div><div class="values">$29,945.38</div>
                <br clear="all" />
        </div>
        <br clear="all" />
    </div>
</div>
<div class="regForm">
    <h2>Top sales product</h2>
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable">  
        <thead>
            <tr>
            <th>Item 1</th>
            <th>Qty</th>
            <th>Branch #</th>
            <th>Total</th>
            <th>Action</th>            
            </tr>
        </thead>   
        <tbody> 
             <tr class="odd">
                <td>8492</td>
                <td>2</td>
                <td>1</td>
                <td>$105.00</td>
                <td>View</td>            
            </tr>
             <tr class="even">
                <td>8492</td>
                <td>2</td>
                <td>1</td>
                <td>$105.00</td>
                <td>View</td>            
            </tr>
             <tr class="odd">
                <td>8492</td>
                <td>2</td>
                <td>1</td>
                <td>$105.00</td>
                <td>View</td>            
            </tr>
             <tr class="even">
                <td>8492</td>
                <td>2</td>
                <td>1</td>
                <td>$105.00</td>
                <td>View</td>            
            </tr>
        </tbody>
    </table>
</div>