<script type="text/javascript">
    $(document).ready(function() {
        $("#addshop").validate({
        });
    });
</script>

<h1> Add Pos User</h1>
<form action="<?php echo sfConfig::get("app_admin_url"); ?>users/addSubmit"  id="addshop" method="post"  enctype="multipart/form-data" >


    <table>

        <tbody>

            <tr>
                <th>User ID</th>
                <td>
                    <input type="text" name="login" class="required digits" minlength="4">
                </td>
            </tr>
            <tr>
                <th>Pin</th>
                <td>
                    <input type="text" name="password"  class="required digits" minlength="4">
                </td>
            </tr>
            <tr>
                <th>first name</th>
                <td>
                    <input type="text" name="first_name"  class="required">
                </td>
            </tr>




            <tr>
                <th>last name</th>
                <td>
                    <input type="text" name="last_name"  class="required">
                </td>
            </tr>

            <tr>
                <th>Email</th>
                <td>
                    <input type="text" name="email"  class="required email">
                </td>
            </tr>
            <tr>
                <th>mobile </th>
                <td>
                    <input type="text" name="mobile"  class="required digit">
                </td>
            </tr>
            <tr>
                <th>User type</th>
                <td>
                    <input type="text" name="user_type_id">
                </td>
            </tr>

            <tr>

                <td colspan="2">
                    <input type="submit" name="add User" value="Add User" class="btn">

                </td>
            </tr>

        </tbody>
    </table>
</form>
<a href="<?php echo url_for('users/index') ?>">View POS User</a>