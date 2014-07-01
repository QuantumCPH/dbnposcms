<h1>Edit POS User</h1>

<script type="text/javascript">
    $(document).ready(function() {
        $("#EditPosUser").validate({
        });
    });
</script>

<form action="<?php echo sfConfig::get("app_admin_url"); ?>users/editSubmit"  id="EditPosUser" method="post"  enctype="multipart/form-data" >

    <input type="hidden" name="id" class="required" value="<?php echo $user->getId() ?>">
    <table>

        <tbody>

            <tr>
                <th>User ID</th>
                <td>
                    <input type="text" name="login" class="required digits" minlength="4" value="<?php echo $user->getLogin() ?>">
                </td>
            </tr>
            <tr>
                <th>PIN</th>
                <td>
                    <input type="text" name="password"  class="required digits" minlength="4"  value="<?php echo $user->getPassword() ?>">
                </td>
            </tr>
            <tr>
                <th>First Name</th>
                <td>
                    <input type="text" name="first_name"  class="required" value="<?php echo $user->getFirstName() ?>">
                </td>
            </tr>




            <tr>
                <th>Last Name</th>
                <td>
                    <input type="text" name="last_name"  class="required"  value="<?php echo $user->getLastName() ?>">
                </td>
            </tr>
 <tr>
                <th>Email</th>
                <td>
                    <input type="text" name="email" class="required email"   value="<?php echo $user->getEmail() ?>">
                </td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>
                    <input type="text" name="mobile"  class="required"  value="<?php echo $user->getMobile() ?>">
                </td>
            </tr>
            <tr>
                <th>User Type </th>
                <td>
                    <input type="text" name="user_type_id"  value="<?php echo $user->getUserTypeId() ?>">
                </td>
            </tr>

            <tr>

                <td colspan="2">
                    <input type="submit" name="Update" value="Update" class="btn">

                </td>
            </tr>

        </tbody>
    </table>
</form>
<a href="<?php echo url_for('users/index') ?>">View POS Users</a>