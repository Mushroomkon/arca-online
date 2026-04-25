<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../server/utils/sanitize.php';
require_once __DIR__ . '/../../server/queries/inventory_queries.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.html");
    exit();
}

$user_id = sanitize_int($_SESSION['user_id']);
if ($user_id === false) {
    session_destroy();
    header("Location: ../auth/login.html");
    exit();
}

// AJAX search handler — returns table rows only, then exits
if (isset($_POST['ajax_search'])) {
    $search = sanitize_text($_POST['ajax_search'] ?? '');
    $items  = search_items($conn, $user_id, $search);

    if ($items) {
        foreach ($items as $item) {
            $name     = htmlspecialchars($item['product_name'],     ENT_QUOTES, 'UTF-8');
            $category = htmlspecialchars($item['product_category'], ENT_QUOTES, 'UTF-8');
            $qty      = (int)$item['product_quantity'];
            $cost     = number_format($item['product_cost'],  2);
            $price    = number_format($item['product_price'], 2);
            $id       = (int)$item['product_id'];

            echo "
            <tr>
                <td>{$name}</td>
                <td>{$category}</td>
                <td>{$qty}</td>
                <td>{$cost}</td>
                <td>{$price}</td>
                <td>
                    <div class='action'>
                        <button type='button' class='editBtn'   data-idupdate='{$id}'>Edit</button>
                        <button type='button' class='deleteBtn' data-id='{$id}'>Delete</button>
                    </div>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;'>No items found</td></tr>";
    }
    exit;
}

$items = get_all_items($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Inventory</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/inventory.css">
</head>
<body>

<header>
    <div class="left">
        <div class="logo"><img src="../assets/img/arca.png" alt="logo"></div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php" class="active">Inventory</a>
            <a href="counter.php">Counter</a>
            <a href="sales.php">Sales Report</a>
        </nav>
    </div>
    <div class="logout">
        <form action="../../server/auth/logout.php" method="post">
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
            <?php if ($items): ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name'],     ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['product_category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$item['product_quantity'] ?></td>
                    <td><?= number_format($item['product_cost'],  2) ?></td>
                    <td><?= number_format($item['product_price'], 2) ?></td>
                    <td>
                        <div class="action">
                            <button type="button" class="editBtn"   data-idupdate="<?= (int)$item['product_id'] ?>">Edit</button>
                            <button type="button" class="deleteBtn" data-id="<?= (int)$item['product_id'] ?>">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No items found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<div id="overlay" style="display:none;"></div>

<!-- Delete popup -->
<div class="popup" style="display:none;">
    <p>Are you sure you want to delete this item?</p>
    <form action="../../server/inventory/delete.php" method="post">
        <input type="hidden" name="id" id="deleteId">
        <div class="button-container">
            <button type="submit" class="red">Delete</button>
            <button id="cancelBtn" class="blue" type="button">Cancel</button>
        </div>
    </form>
</div>

<!-- Edit popup -->
<div class="update" style="display:none;">
    <form action="../../server/inventory/update.php" id="update" autocomplete="off" method="post">
        <input type="hidden" name="updateId" id="idupdate">
        <input type="text"   name="item"     placeholder="Item name"     required>
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
        <input type="number" name="quantity" placeholder="Item quantity" min="0" required>
        <input type="number" name="cost"     placeholder="Item cost"     min="0" step="0.01" required>
        <input type="number" name="price"    placeholder="Item price"    min="0" step="0.01" required>
        <div class="button-container">
            <button type="submit" class="blue" name="edit">Update</button>
            <button type="button" class="red"  id="cancel">Cancel</button>
        </div>
    </form>
</div>

<script>
function attachRowEvents() {
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const updatePop = document.querySelector('.update');

            document.getElementById('idupdate').value = this.dataset.idupdate;
            updatePop.querySelector('input[name="item"]').value      = row.children[0].textContent.trim();
            updatePop.querySelector('select[name="category"]').value = row.children[1].textContent.trim();
            updatePop.querySelector('input[name="quantity"]').value  = row.children[2].textContent.trim();
            updatePop.querySelector('input[name="cost"]').value      = row.children[3].textContent.trim();
            updatePop.querySelector('input[name="price"]').value     = row.children[4].textContent.trim();

            updatePop.style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        });
    });

    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('deleteId').value = this.dataset.id;
            document.querySelector('.popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        });
    });
}

document.getElementById('cancel').addEventListener('click', () => {
    document.querySelector('.update').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
});

document.getElementById('cancelBtn').addEventListener('click', () => {
    document.querySelector('.popup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
});

function searchItem() {
    const search = document.getElementById("itemSearch").value;
    const xhr = new XMLHttpRequest();
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