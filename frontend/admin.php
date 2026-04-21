<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
switch($sort){
    case 'name': $order_by = "user_name ASC"; break;
    case 'date': $order_by = "user_date ASC"; break;
    case 'sales': $order_by = "total_sales DESC"; break;
    default: $order_by = "user_id ASC"; break;
}

$sql = "
SELECT u.user_id, u.user_name, u.user_password, u.user_date, 
       IFNULL(SUM(s.total_sales),0) AS total_sales
FROM users u
LEFT JOIN sales s ON u.user_id = s.user_id_fk
WHERE u.user_name != 'admin'
GROUP BY u.user_id
ORDER BY $order_by
";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arca Admin</title>
<link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #0A0A16;
    color: #fff;
    min-height: 100vh;
}

header {
    width: 100%;
    height: 60px;
    background: #1c1c29;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
}

header .logo img {
    width: 80px;
    height: auto;
}

header nav {
    display: flex;
    gap: 20px;
    justify-content: flex-start;
    align-items: center;
}


header nav a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

header nav a:hover {
    color: #00bcd4;
}

header .logout button {
    background: #e74c3c;
    border: none;
    color: #fff;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

header .logout button:hover {
    background: #c0392b;
}

main {
    padding: 20px;
    width: 84%;
    margin: 0 auto;
}

h1 {
    margin-bottom: 20px;
}

.sort-buttons {
    margin-bottom: 15px;
}

.sort-buttons a {
    text-decoration: none;
    padding: 6px 12px;
    margin-right: 5px;
    background: #00bcd4;
    color: #fff;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.3s;
}

.sort-buttons a:hover {
    background: #0097a7;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
    min-width: 600px;
}

th {
    text-align: left;
    font-weight: normal;
    padding: 10px 20px;
    color: #fff;
}

td {
    padding: 10px 20px;
    background: #1f1f2e;
    color: #fff;
}

td:first-child {
    border-top-left-radius: 50px;
    border-bottom-left-radius: 50px;
}

td:last-child {
    border-top-right-radius: 50px;
    border-bottom-right-radius: 50px;
}

tbody tr:hover td {
    background: #333454;
}

.delete-button {
    background: #e74c3c;
    border: none;
    color: #fff;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.delete-button:hover {
    background: #c0392b;
}

.delete-confirm {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    background: #1c1c29;
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    text-align: center;
    z-index: 1000;
    display: none;
}

.delete-confirm p {
    margin-bottom: 20px;
    font-size: 16px;
    color: #fff;
}

.delete-confirm button {
    padding: 8px 16px;
    margin: 0 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.delete-confirm .confirm-yes {
    background: #e74c3c;
    color: #fff;
}

.delete-confirm .confirm-yes:hover {
    background: #c0392b;
}

.delete-confirm .confirm-no {
    background: #95a5a6;
    color: #fff;
}

.delete-confirm .confirm-no:hover {
    background: #7f8c8d;
}

@media(max-width:700px) {
    th, td {padding: 8px 12px;font-size:14px;}
    .sort-buttons a {font-size:12px;padding:4px 8px;}
}

@media(max-width:500px) {
    th, td {padding: 6px 8px;font-size:12px;}
}
</style>
</head>
<body>
<header>
    <div class="logo"><img src="../img/arca.png" alt="Arca Logo"></div>
    <nav>
        <a href="admin.php">Admin</a>
        <a href="main.php">Main</a>
        <a href="inventory.php">Inventory</a>
        <a href="counter.php">Counter</a>
        <a href="sales.php">Sales Report</a>
    </nav>
    <div class="logout">
        <form action="../backend/logout.php" method="post"><button type="submit">Logout</button></form>
    </div>
</header>

<main class="table-wrapper">
<h1>Welcome Admin</h1>

<div class="sort-buttons">
    <a href="admin.php?sort=name">Sort by Name</a>
    <a href="admin.php?sort=date">Sort by Date</a>
    <a href="admin.php?sort=sales">Sort by Sales</a>
</div>

<table>
    <thead>
        <tr>
            <th>User Name</th>
            <th>Password</th>
            <th>Created Date</th>
            <th>Total Sales</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if(mysqli_num_rows($result) > 0){
        while($user = mysqli_fetch_assoc($result)){
            echo "<tr>
                    <td>".$user['user_name']."</td>
                    <td>".$user['user_password']."</td>
                    <td>".$user['user_date']."</td>
                    <td>".$user['total_sales']."</td>
                    <td>
                        <form class='delete-form' action='../backend/control.php' method='POST' style='display:inline-block;'>
                            <input type='hidden' name='user_id' value='".$user['user_id']."'>
                            <button class='delete-button' type='button'>Delete</button>
                        </form>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No users found.</td></tr>";
    }
    ?>
    </tbody>
</table>
</main>

<div class="delete-confirm" id="deleteConfirm">
    <p>Do you want to delete this user?</p>
    <button class="confirm-yes" id="confirmYes">Yes</button>
    <button class="confirm-no" id="confirmNo">No</button>
</div>

<script>
let selectedForm = null;
document.querySelectorAll('.delete-button').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedForm = this.closest('.delete-form');
        document.getElementById('deleteConfirm').style.display = 'block';
    });
});

document.getElementById('confirmYes').addEventListener('click', function() {
    if (selectedForm) selectedForm.submit();
});

document.getElementById('confirmNo').addEventListener('click', function() {
    document.getElementById('deleteConfirm').style.display = 'none';
    selectedForm = null;
});
</script>
</body>
</html>
