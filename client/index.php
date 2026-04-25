
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Landing Page</title>
    <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png">
    <link rel="stylesheet" href="/client/assets/css/index.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="img/arca.png" alt="Arca Logo">
    </div>

   
    
</header>

<main>
    <section class="hero">
        <h1>Welcome to Arca</h1>
        <p>Your classic and efficient inventory & sales dashboard</p>

        <a href="auth/login.html" class="btn">
            Get Started
        </a>
    </section>

    <section class="features">
        <div class="feature">
            <h2>Manage Inventory</h2>
            <p>Easily add, edit, and remove items in your inventory.</p>
        </div>
        <div class="feature">
            <h2>Counter</h2>
            <p>Quickly punch items and handle transactions efficiently.</p>
        </div>
        <div class="feature">
            <h2>Sales Reports</h2>
            <p>View overall sales, top items, and track your profits.</p>
        </div>
    </section>

    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>
</main>

<footer>
    <p>© <?= date('Y') ?> Arca. All rights reserved.</p>
</footer>

</body>
</html>
