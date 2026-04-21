<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Admin</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="stylesheet" href="../frontend/main.css">
</head>
<body>

<header>
    <div class="left">
        <div class="logo"><img src="../img/arca.png" alt="logo"></div>
        <nav>
            
            <a href="main.php" active >Main</a>
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
    <form action="../backend/add.php" autocomplete="off" method="post"> 
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
        </select>
        <input type="number" name="quantity" placeholder="Item quantity" required>
        <input type="number" name="cost" placeholder="Item cost" required>
        <input type="number" name="price" placeholder="Item price" required>
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
            <?php
            $sql = "SELECT * FROM items WHERE user_id_fk = $user_id";
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
            }
            ?>
        </tbody>
    </table>

    <div id="overlay"></div>

    <div class="popup">
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
let deleteButtons = document.querySelectorAll('.deleteBtn');
let deletePop = document.querySelector('.popup');
let deleteId = document.getElementById('deleteId');
let highlightedRows = null;

deleteButtons.forEach(btn => {
    btn.addEventListener('click', () => {

        
        if (highlightedRows) {
            highlightedRows.classList.remove('highlighted');
        }

        const rowd = btn.closest('tr');
        rowd.classList.add('highlighted');
        highlightedRows = rowd;

        
        deleteId.value = btn.dataset.deleteId || btn.dataset.id;
        deletePop.style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
});


document.getElementById('cancelBtn').addEventListener('click', (event) => {
    event.preventDefault();
    deletePop.style.display = 'none';
    document.getElementById('overlay').style.display = 'none';

    if (highlightedRows) {
        highlightedRows.classList.remove('highlighted');
        highlightedRows = null;
    }
});


let editButtons = document.querySelectorAll('.editBtn');
let updatePop = document.querySelector('.update');
let updateId = document.getElementById('idupdate');
let highlightedRow = null;

editButtons.forEach(btn => {
    btn.addEventListener('click', () => {

        
        if (highlightedRow) {
            highlightedRow.classList.remove('highlighted');
        }

        
        const row = btn.closest('tr');
        row.classList.add('highlighted');
        highlightedRow = row;

        
        updateId.value = btn.dataset.idupdate;

        
        updatePop.querySelector('input[name="item"]').value = row.children[0].textContent;
        updatePop.querySelector('select[name="category"]').value = row.children[1].textContent;
        updatePop.querySelector('input[name="quantity"]').value = row.children[2].textContent;
        updatePop.querySelector('input[name="cost"]').value = row.children[3].textContent;
        updatePop.querySelector('input[name="price"]').value = row.children[4].textContent;

        
        updatePop.style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
});


document.getElementById('cancel').addEventListener('click', (event) => {
    event.preventDefault();
    updatePop.style.display = 'none';
    document.getElementById('overlay').style.display = 'none';

    if (highlightedRow) {
        highlightedRow.classList.remove('highlighted');
        highlightedRow = null;
    }
});

    </script>    
</main>

</body>
</html>
