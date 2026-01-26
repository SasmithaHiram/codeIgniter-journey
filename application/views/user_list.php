<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
</head>

<body>
    <button id="openModalBtn">Create New User</button>
    <div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; border-radius:8px; min-width:300px; position:relative;">
            <span id="closeModalBtn" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
            <h2>Create User</h2>
            <form action="<?php echo site_url('user/user/create'); ?>" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    <h1>Users List</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user->id; ?></td>
                <td><?php echo $user->name; ?></td>
                <td><?php echo $user->email; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
        const openModalBtn = document.getElementById('openModalBtn');
        const userModal = document.getElementById('userModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        openModalBtn.onclick = function() {
            userModal.style.display = 'flex';
        };
        closeModalBtn.onclick = function() {
            userModal.style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target === userModal) {
                userModal.style.display = 'none';
            }
        };
    </script>

</body>

</html>