<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User</title>
</head>

<body>

    <h1>Add User</h1>

    <form method="post" action="<?php echo site_url('user/create'); ?>">

        <p>
            <input type="text" name="name" placeholder="Name" required>
        </p>
        <p>
            <input type="email" name="email" placeholder="Email" required>
        </p>
        <p>
            <button type="submit">Add User</button>
        </p>
    </form>

    <a href="<?php echo site_url('user'); ?>">Back to Users List</a>

</body>

</html>