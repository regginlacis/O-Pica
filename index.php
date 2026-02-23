<?php
// Datubāzes savienojums (vēlāk)
// require_once 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍕 O! Pica - Pasūtiet savu mīļoto picu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🍕 O! Pica</h1>
            <p>Garšīgas picas piegādātas jūsu durvīs</p>
        </div>
    </header>

    <nav class="navbar">
        <div class="container">
            <button class="nav-btn" onclick="showMenu()">Izvēlne</button>
            <button class="nav-btn" onclick="showCart()">🛒 Grozs (<span id="cart-count">0</span>)</button>
            <button class="nav-btn" onclick="showOrders()">📦 Pasūtījumi</button>
        </div>
    </nav>

    <main class="container">
        <!-- Izvēlnes sekcija -->
        <section id="menu-section" class="section active">
            <h2>Mūsu Picas</h2>
            <div class="pizza-grid" id="pizza-list"></div>
        </section>

        <!-- Grozs sekcija -->
        <section id="cart-section" class="section">
            <h2>Jūsu Grozs</h2>
            <div id="cart-items"></div>
            <div class="cart-summary">
                <h3>Kopā: €<span id="total-price">0.00</span></h3>
                <button class="btn-checkout" onclick="checkout()">Pasūtīt</button>
                <button class="btn-continue" onclick="showMenu()">Turpināt Iepirkšanos</button>
            </div>
        </section>

        <!-- Pasūtījumu sekcija -->
        <section id="orders-section" class="section">
            <h2>Jūsu Pasūtījumi</h2>
            <div id="orders-list"></div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas. 🍕</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
