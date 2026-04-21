<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['ajax_search'])){
    $search = mysqli_real_escape_string($conn, $_POST['ajax_search']);
    $sql = "SELECT * FROM items 
            WHERE user_id_fk = $user_id 
            AND product_name LIKE '%$search%' 
            ORDER BY product_name ASC";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        while($item = mysqli_fetch_assoc($result)){
            echo "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['product_category']}</td>
                <td>{$item['product_quantity']}</td>
                <td>{$item['product_cost']}</td>
                <td>{$item['product_price']}</td>
                <td>
                    <div class='action'>
                        <button type='button' class='editBtn' data-idupdate='{$item['product_id']}'>Edit</button>
                        <button type='button' class='deleteBtn' data-id='{$item['product_id']}'>Delete</button>
                    </div>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No items found</td></tr>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arca Inventory</title>
<link rel="icon" type="image/png" sizes="32x32" href="/img/logo.png">
<link rel="stylesheet" href="../frontend/inventory.css">
</head>
<body>

<header>
    <div class="left">
        <div class="logo"><img src="../img/arca.png" alt="Arca Logo"></div>
        <nav>
            
            <a href="main.php">Main</a>
            <a href="inventory.php">Inventory</a>
            <a href="counter.php">Counter</a>
            <a href="sales.php">Sales Report</a>
        </nav>
    </div>
    <div class="logout">
        <form action="../backend/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>

<div class="container">
    <input type="text" id="itemSearch" placeholder="Search item" onkeyup="searchItem()">
</div>

<main class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Cost</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php
            $sql = "SELECT * FROM items WHERE user_id_fk = $user_id ORDER BY product_name ASC";
            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) > 0){
                while($item = mysqli_fetch_assoc($result)){
                    echo "
                    <tr>
                        <td>{$item['product_name']}</td>
                        <td>{$item['product_category']}</td>
                        <td>{$item['product_quantity']}</td>
                        <td>{$item['product_cost']}</td>
                        <td>{$item['product_price']}</td>
                        <td>
                            <div class='action'>
                                <button type='button' class='editBtn' data-idupdate='{$item['product_id']}'>Edit</button>
                                <button type='button' class='deleteBtn' data-id='{$item['product_id']}'>Delete</button>
                            </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No items found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<div id="overlay" style="display:none;"></div>

<div class="popup" style="display:none;">
    <p>Are you sure you want to delete this item?</p>
    <form action="../backend/delete.php" method="post">
        <input type="hidden" name="id" id="deleteId">
        <div class="button-container">
            <button type="submit" class="red">Delete</button>
            <button id="cancelBtn" class="blue" type="button">Cancel</button>
        </div>
    </form>
</div>

<div class="update">
    <form action="../backend/update.php" id="update" autocomplete="off" method="post">
        <input type="hidden" name="updateId" id="idupdate">
        <input type="text" name="item" placeholder="Item name" required>
            <select name="category" required>
                <option value="" disabled selected hidden>Select category</option>
                <option value="food">Food</option>
                <option value="drinks">Drinks</option>
                <option value="canned">Canned</option>
                <option value="noodles">Noodles</option>
                <option value="snacks">Snacks</option>
                <option value="cleaning">Cleaning</option>
                <option value="others">Others</option>
            </select>
        <input type="number" name="quantity" placeholder="Item quantity" required>
        <input type="number" name="cost" placeholder="Item cost" required>
        <input type="number" name="price" placeholder="Item price" required>
        <div class="button-container">
            <button type="submit" class="blue" name="edit">Update</button>
            <button type="button" class="red" id="cancel">Cancel</button>
        </div>
    </form>
</div>

<script>
    const updatePopup = document.querySelector('.update');
const editIdInput = document.getElementById('idupdate');
const overlay = document.getElementById('overlay');


function attachRowEvents() {
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');

            editIdInput.value = this.dataset.idupdate;
            updatePopup.querySelector('input[name="item"]').value = row.children[0].textContent.trim();
            updatePopup.querySelector('select[name="category"]').value = row.children[1].textContent.trim();
            updatePopup.querySelector('input[name="quantity"]').value = row.children[2].textContent.trim();
            updatePopup.querySelector('input[name="cost"]').value = row.children[3].textContent.trim();
            updatePopup.querySelector('input[name="price"]').value = row.children[4].textContent.trim();

            updatePopup.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteId').value = this.dataset.id;
            document.querySelector('.popup').style.display = 'block';
            overlay.style.display = 'block';
        });
    });
}


document.getElementById('cancel').addEventListener('click', () => {
    updatePopup.style.display = 'none';
    overlay.style.display = 'none';
});


document.getElementById('cancelBtn').addEventListener('click', () => {
    document.querySelector('.popup').style.display = 'none';
    overlay.style.display = 'none';
});


function searchItem() {
    let search = document.getElementById("itemSearch").value;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "inventory.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById("table-body").innerHTML = this.responseText;

            
            attachRowEvents();
        }
    };

    xhr.send("ajax_search=" + encodeURIComponent(search));
}


attachRowEvents();

</script>

</body>
</html>
