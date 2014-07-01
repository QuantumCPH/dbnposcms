<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div style="display: block;" id="role-block" class="regForm">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Denomination</th>
              
                
                  <?php
                $c5 = new Criteria();
                $c5->add(DayStartsAttemptsPeer::DAY_START_ID, $dayStart->getId());
                $c5->addDescendingOrderByColumn(DayStartsAttemptsPeer::ID);
                $count = DayStartsAttemptsPeer::doCount($c5);
                for ($i = 1; $i <= $count; $i++) {
                    ?>

                    <th>Day Start</th>

                <?php } ?>
                    
                <?php
                $c2 = new Criteria();
                $c2->add(DayEndsPeer::DAY_START_ID, $dayStart->getId());
                $c2->addDescendingOrderByColumn(DayEndsPeer::DAY_ENDED_AT);
                $count = DayEndsPeer::doCount($c2);
                for ($i = 1; $i <= $count; $i++) {
                    ?>

                    <th>Day End</th>

                <?php } ?>

            </tr>
        </thead>
        <tbody>
            <?php
            $i=0;
            foreach ($denominations as $deomination) {
                ?>
                <tr class="<?php echo ($i%2==0)?'even':'odd' ?>">
                    <td><?php echo $deomination->getTitle(); ?></td>
<!--                    <td>
                        <?php
//                        $c = new Criteria();
//                        $c->add(DayStartDenominationsPeer::DAY_START_ID, $dayStart->getId());
//                        $c->add(DayStartDenominationsPeer::DENOMINATION_ID, $deomination->getId());
//                        $ss = DayStartDenominationsPeer::doSelectOne($c);
//                        echo $ss->getCount();
                        ?>
                    </td>-->
                    
                      <?php
                    if (DayStartsAttemptsPeer::doCount($c5) > 0) {
                        $daystarts = DayStartsAttemptsPeer::doSelect($c5);
                        foreach ($daystarts as $daystart) {
                            $ced = new Criteria();
                            $ced->add(DayStartDenominationsPeer::DAY_ATTEMPT_ID, $daystart->getId());
                            $ced->add(DayStartDenominationsPeer::DENOMINATION_ID, $deomination->getId());
                            echo "<td>";
                            if (DayStartDenominationsPeer::doCount($ced) > 0) {
                                $dstartdenomination = DayStartDenominationsPeer::doSelectOne($ced);
                                echo $dstartdenomination->getCount();
                            }

                            echo "</td>";
                            ?>


                    <?php } } ?>
                    
                    
                    <?php
                    if (DayEndsPeer::doCount($c2) > 0) {
                        $dayends = DayEndsPeer::doSelect($c2);
                        foreach ($dayends as $dayend) {
                            $ced = new Criteria();
                            $ced->add(DayEndDenominationsPeer::DAY_END_ID, $dayend->getId());
                            $ced->add(DayEndDenominationsPeer::DENOMINATION_ID, $deomination->getId());
                            echo "<td>";
                            if (DayEndDenominationsPeer::doCount($ced) > 0) {
                                $denddenomination = DayEndDenominationsPeer::doSelectOne($ced);
                                echo $denddenomination->getCount();
                            }

                            echo "</td>";
                            ?>


                        <?php } ?>
                    <?php } ?>





                </tr>
                <?php
                $i++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Total:</th>
                <?php
                  $dayStartAtteptsObj = DayStartsAttemptsPeer::doSelect($c5);
                foreach ($dayStartAtteptsObj as $dayStartAtteptObj) {
                    ?>


                    <th><?php echo $dayStartAtteptObj->getTotalAmount(); ?></th>

                <?php } ?>

                <?php
                $dayendsObj = DayEndsPeer::doSelect($c2);

                foreach ($dayendsObj as $dayendObj) {
                    ?>


                    <th><?php echo $dayendObj->getTotalAmount(); ?></th>

                <?php } ?>

            </tr>
            <tr>
                <th colspan="1">Expected Total:</th>
                  <?php
                $daystartatemptObj = DayStartsAttemptsPeer::doSelect($c5);

                foreach ($daystartatemptObj as $daystartatemobj) {
                    ?>


                    <th><?php echo $daystartatemobj->getExpectedAmount(); ?></th>

                <?php } ?>
                <?php
                $dayendsObj = DayEndsPeer::doSelect($c2);

                foreach ($dayendsObj as $dayendObj) {
                    ?>


                    <th><?php echo $dayendObj->getExpectedAmount(); ?></th>

                <?php } ?>

            </tr>
        </tfoot>
    </table>

</div>