<?php
include("admin_check.php");
include("../db.php");

// DELETE USER (safe)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // secure

    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>

<style>
body {
    font-family: 'Segoe UI';
    background: #f4f6f9;
    margin: 0;
}

.container {
    padding: 20px;
}

h2 {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

th, td {
    padding: 12px;
    text-align: center;
}

th {
    background: #667eea;
    color: white;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

a.delete {
    color: red;
    text-decoration: none;
    font-weight: bold;
}

a.delete:hover {
    text-decoration: underline;
}
</style>

</head>
<body>

<div class="container">

<h2>👥 Manage Users</h2>

<table>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['username']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <a class="delete" href="?delete=<?php echo $row['id']; ?>" 
           onclick="return confirm('Delete this user?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>