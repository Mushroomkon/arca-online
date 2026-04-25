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

$items = get_all_items($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<header>
    <div class="left">
        <div class="logo"><img src="../assets/img/arca.png" alt="logo"></div>
        <nav>
            <a href="dashboard.php active">Dashboard</a>
            <a href="inventory.php">Inventory</a>
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
    <form action="../../server/inventory/add.php" autocomplete="off" method="post">
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
        <input type="number" name="quantity" placeholder="Item quantity" min="0" required>
        <input type="number" name="cost"     placeholder="Item cost"     min="0" step="0.01" required>
        <input type="number" name="price"    placeholder="Item price"    min="0" step="0.01" required>
        <button type="submit" name="add">Add</button>
    </form>
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
        <tbody>
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

    <div id="overlay"></div>

    <!-- Delete popup -->
    <div class="popup">
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
    <div class="update">
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
</main>

<script>
let highlightedRow = null;

function clearHighlight() {
    if (highlightedRow) {
        highlightedRow.classList.remove('highlighted');
        highlightedRow = null;
    }
}

document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        clearHighlight();
        const row = btn.closest('tr');
        row.classList.add('highlighted');
        highlightedRow = row;

        document.getElementById('deleteId').value = btn.dataset.id;
        document.querySelector('.popup').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
});

document.getElementById('cancelBtn').addEventListener('click', () => {
    document.querySelector('.popup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    clearHighlight();
});

document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        clearHighlight();
        const row = btn.closest('tr');
        row.classList.add('highlighted');
        highlightedRow = row;

        const updatePop = document.querySelector('.update');
        document.getElementById('idupdate').value = btn.dataset.idupdate;
        updatePop.querySelector('input[name="item"]').value         = row.children[0].textContent.trim();
        updatePop.querySelector('select[name="category"]').value    = row.children[1].textContent.trim();
        updatePop.querySelector('input[name="quantity"]').value     = row.children[2].textContent.trim();
        updatePop.querySelector('input[name="cost"]').value         = row.children[3].textContent.trim();
        updatePop.querySelector('input[name="price"]').value        = row.children[4].textContent.trim();

        updatePop.style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
});

document.getElementById('cancel').addEventListener('click', () => {
    document.querySelector('.update').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    clearHighlight();
});
</script>

</body>
</html>