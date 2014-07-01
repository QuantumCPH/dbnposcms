<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type="text/javascript">
    jQuery(function() {

        jQuery('.loglist').click(function(e) {

            e.preventDefault();
            var request = jQuery.ajax({
                url: jQuery(this).attr("href"),
                type: "GET",
                // data: {date: date, shop_id:<?php // echo  $shops->getId();  ?>},
                dataType: "html"
            });
            request.done(function(msg) {
                jQuery("#daily-cash-log").html(msg);
            });
            request.fail(function(jqXHR, textStatus) {
                console.log("Request failed: " + textStatus);
            });
        });


    });


</script>
<div style="display: block;" id="role-block" class="regForm">

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th> Sr. </th>
                <th> ID </th>
                <th> Date </th>

            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($dayStarts as $dayStart) {
                ?>
                <tr class="<?php echo ($i%2==0)?'even':'odd' ?>">
                    <td><?php echo $i; ?></td>
                    <td><a class="loglist" href="<?php echo sfConfig::get("app_admin_url"); ?>shops/getDailyCashLog?id=<?php echo $dayStart->getId(); ?>" ><?php echo $dayStart->getId(); ?></a></td>
                    <td><?php echo $dayStart->getDayStartedAt(); ?></td>
                </tr>
<?php $i++; } ?>
        </tbody>

    </table>

</div>   

<br clear="all" />
<div id="daily-cash-log"></div>