<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form action="http://localhost/poscms/web/b2c.php/pScripts/syncSalesTransaction" method="post">
    shopid <input type="text" name="shop_id"/>
    <br/>
    server_json_order <textarea name="server_json_order"></textarea>
    <br/>
    server_json_trans <textarea name="server_json_trans"></textarea>
    <br/>
    
    <input type="submit" value="save"/>
</form>