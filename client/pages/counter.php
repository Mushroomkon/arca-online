<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../server/utils/sanitize.php';
require_once __DIR__ . '/../../server/queries/counter_queries.php';

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

$items   = get_counter_items($conn, $user_id);
$receipt = get_receipt($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Counter</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/counter.css">
</head>
<body>
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>
<header>
    <div class="logo"><img src="../assets/img/arca.png" alt="logo"></div>

    <nav id="nav-menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="counter.php" class="active">Counter</a>
        <a href="sales.php">Sales Report</a>
        <form action="../../server/auth/logout.php" method="post" class="nav-logout">
            <button type="submit">Logout</button>
        </form>
    </nav>

    <!-- Desktop logout -->
    <div class="logout">
        <form action="../../server/auth/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <!-- Hamburger on the far right -->
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
</header>

<main class="table-wrapper">
    <section class="left-box">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
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
                        <td><?= number_format($item['product_price'], 2) ?></td>
                        <td>
                            <div class="action">
                                <form action="../../server/counter/punch.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                                    <button class="blue" type="submit">Punch</button>
                                </form>
                                <form action="../../server/counter/decrease.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                                    <button class="red" type="submit">Decrease</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No items found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="right-box">
        <h1>Receipt</h1>

        <div id="printable-receipt">
            <div class="receipt-header">
                <h2>Arca Receipt</h2>
                <p id="receipt-date"></p>
            </div>

            <?php if ($receipt): ?>
                <?php
                $grand_total = 0;
                foreach ($receipt as $r) {
                    $grand_total += $r['item_price_fk'] * $r['item_quantity_fk'];
                }
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipt as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['item_name_fk'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format($r['item_price_fk'], 2) ?></td>
                            <td><?= (int)$r['item_quantity_fk'] ?></td>
                            <td><?= number_format($r['item_price_fk'] * $r['item_quantity_fk'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Grand Total</th>
                            <th><?= number_format($grand_total, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <p class="no-items">No items in receipt.</p>
            <?php endif; ?>
        </div>

        <section class="rightbottom-box">
            <form action="../../server/counter/push.php" method="POST" style="display:inline-block;">
                <button type="submit" class="blue" style="color: black;">Push All</button>
            </form>
            <form action="../../server/counter/cancel.php" method="POST" style="display:inline-block;">
                <button type="submit" class="red" style="color: black;">Cancel All</button>
            </form>
            <button type="button" class="green" onclick="printReceipt()" style="color: black;">Print</button>
        </section>
    </section>
</main>

<script>
    // ─── Date ──────────────────────────────────────────────
    document.getElementById('receipt-date').textContent =
        new Date().toLocaleString('en-PH', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

    // ─── Print ─────────────────────────────────────────────
    function printReceipt() {
        <?php if (!$receipt): ?>
            alert('No items in receipt to print.');
            return;
        <?php endif; ?>
        window.print();
    }

    // ─── Hamburger ─────────────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const navMenu   = document.getElementById('nav-menu');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        navMenu.classList.toggle('open');
    });

    // Close nav when a link is clicked
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('open');
            navMenu.classList.remove('open');
        });
    });

    // ─── Cash register sound ───────────────────────────────
    function playCashSound() {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();

        function beep(freq, startTime, duration, gain = 0.3) {
            const osc = ctx.createOscillator();
            const vol = ctx.createGain();
            osc.connect(vol);
            vol.connect(ctx.destination);
            osc.frequency.value = freq;
            osc.type = 'square';
            vol.gain.setValueAtTime(gain, startTime);
            vol.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
            osc.start(startTime);
            osc.stop(startTime + duration);
        }

        function coinClink(startTime) {
            const coinFreqs = [1200, 1800, 1500, 2000];
            coinFreqs.forEach((freq, i) => {
                beep(freq, startTime + i * 0.045, 0.12, 0.15);
            });
        }

        const now = ctx.currentTime;
        beep(180,  now,        0.08, 0.4);
        beep(220,  now + 0.09, 0.08, 0.4);
        beep(1400, now + 0.18, 0.35, 0.25);
        beep(1600, now + 0.20, 0.30, 0.15);
        coinClink(now + 0.38);
    }

    // ─── Push All intercept ────────────────────────────────
    document.querySelector('form[action="../../server/counter/push.php"]')
        .addEventListener('submit', function (e) {
            <?php if (!$receipt): ?>
                return;
            <?php endif; ?>
            e.preventDefault();
            playCashSound();
            const form = this;
            setTimeout(() => form.submit(), 700);
        });

// ─── ROBLOX "OOF" RECONSTRUCTION ────────────────────────
    function playCancelSound() {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const now = ctx.currentTime;
        
        // The Roblox oof is very short (~0.15s - 0.2s)
        const duration = 0.18; 

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        // Square wave gives it that retro, buzzy, 8-bit character
        osc.type = 'square'; 

        // THE PITCH: Starts mid-high and drops instantly
        // This mimics the "Uuh!" inflection
        osc.frequency.setValueAtTime(280, now); 
        osc.frequency.exponentialRampToValueAtTime(120, now + duration);

        // THE ENVELOPE: Punchy start, very fast fade out
        gain.gain.setValueAtTime(0.15, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + duration);

        // THE FILTER: Softens the square wave so it's not a piercing beep
        const filter = ctx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(1200, now);
        filter.frequency.exponentialRampToValueAtTime(400, now + duration);

        osc.connect(filter);
        filter.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + duration);
    }

    // ─── Cancel All intercept ──────────────────────────────
    document.querySelector('form[action="../../server/counter/cancel.php"]')
        .addEventListener('submit', function (e) {
            <?php if (!$receipt): ?>
                return;
            <?php endif; ?>
            e.preventDefault();
            playCancelSound();
            const form = this;
            // The "Oof" is super fast, so a 250ms delay is plenty
            setTimeout(() => form.submit(), 250); 
        });
</script>

</body>
</html>