<h1>POS User View</h1>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>User ID</th>
            <th>PIN</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>User type</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td><a href="<?php echo url_for('users/edit?id=' . $users->getId()) ?>"><?php echo $users->getId() ?></a></td>
            <td><?php echo $users->getLogin() ?></td>
            <td><?php echo $users->getPassword() ?></td>
            <td><?php echo $users->getFirstName() ?></td>
            <td><?php echo $users->getLastName() ?></td>
            <td><?php echo $users->getEmail() ?></td>
            <td><?php echo $users->getMobile() ?></td>
            <td><?php echo $users->getUserTypeId() ?></td>

        </tr>

    </tbody>
</table>

<a href="<?php echo url_for('users/edit?id=' . $users->getId()) ?>">Edit</a>
