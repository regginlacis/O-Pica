<?php
require_once 'theme_init.php';
require_once 'config.php';
session_start();

// Pārbaudi vai lietotājs ir pierakstīts
$logged_in = Auth::isLoggedIn();
$user = null;
if ($logged_in) {
    $user = Auth::getCurrentUser();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Pasūtiet savu mīļoto picu</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="light-theme">
    <header>
        <div class="container">
            <h1>O! Pica</h1>
            <p>Garšīgas picas piegādātas jūsu durvīs</p>
        </div>
    </header>

    <nav class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 10px;">
                <button class="nav-btn" onclick="showAbout()">Par mums</button>
                <button class="nav-btn" onclick="showMenu()">Izvēlne</button>
                <button class="nav-btn" onclick="showCart()">Grozs (<span id="cart-count">0</span>)</button>
                <button class="nav-btn" onclick="showOrders()">Pasūtījumi</button>
            </div>
            
            <!-- Theme Toggle -->
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaisma tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">☀️</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🌙</button>
            </div>
            
            <!-- Auth Saites -->
            <div style="display: flex; gap: 10px; align-items: center;">
                <?php if ($logged_in): ?>
                    <span style="color: #E8360F; font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="profile.php" class="nav-btn" style="text-decoration: none; background-color: #E8360F; color: white;">Profils</a>
                    <a href="logout.php" class="nav-btn" style="text-decoration: none; color: #e74c3c;">Iziet</a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="admin.php" class="nav-btn" style="text-decoration: none; background-color: #f39c12; color: white;">Administrācija</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="nav-btn" style="text-decoration: none;">Pierakstīties</a>
                    <a href="register.php" class="nav-btn" style="text-decoration: none; background-color: #E8360F; color: white;">Reģistrēties</a>
                    <a href="admin.php" class="nav-btn" style="text-decoration: none; color: #E8360F;">Administrācija</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container">
        <!-- Par mums sekcija -->
        <section id="about-section" class="section about-section">
            <h2>Par mums</h2>
            <div class="about-content">
                <p class="about-title">Gardākā pica TUKUMĀ!</p>
                <p class="about-desc">
                    Vēlies karštu, sulīgu picu? Tu vairs nav jādodas nogurdinošās pārtikas medībās – zvani! 
                    Un jau pēc īsa brīža kūpoša pica būs pie tavām namdurvīm!
                </p>
                
                <div class="about-grid">
                    <div class="about-item">
                        <h3>Kontakti</h3>
                        <p><strong>Tālrunis:</strong> 26318083</p>
                        <p><strong>Pilsēta:</strong> Tukums, Tukuma rajons</p>
                    </div>
                    
                    <div class="about-item">
                        <h3>Darba laiks</h3>
                        <p>Pirmdiena: Slēgts</p>
                        <p>Otrdiena - Piektdiena: 17:00 - 22:00</p>
                        <p>Sestdiena: 17:00 - 22:00</p>
                        <p>Svētdiena: Slēgts</p>
                    </div>
                    
                    <div class="about-item">
                        <h3>Piegāde</h3>
                        <p>Tukumā: 2.00 €</p>
                        <p>Jauntukumā, Durbē: 1.00 €</p>
                        <p><strong>3 picas = Bezmaksas piegāde!</strong></p>
                    </div>
                    
                    <div class="about-item">
                        <h3>Savākšana</h3>
                        <p>Nāc picai pakaļ uz stāvlaukumu pie <strong>Jauntukuma Mego</strong> un nemaksā par piegādi!</p>
                    </div>
                </div>
            </div>
        </section>

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

    <!-- AI Support Modal -->
    <div id="supportModal" class="support-modal">
        <div class="support-modal-content">
            <button class="close-btn" onclick="closeSupport()">X</button>
            <h2>💬 AI Atbalsts - Tūlītēja Palīdzība</h2>
            <div class="chat-box">
                <div class="chat-messages" id="chatMessages">
                    <div class="message ai-message">
                        <span class="message-avatar">AI</span>
                        <span class="message-text">Sveiki! Es esmu O! Pica AI asistents. Kā es varu jums palīdzēt?</span>
                    </div>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="userInput" class="chat-input" placeholder="Uzdodiet savu jautājumu..." onkeypress="if(event.key==='Enter') sendMessage()">
                    <button class="chat-send-btn" onclick="sendMessage()">Sūtīt</button>
                </div>
            </div>
            <button class="support-button" onclick="showHumanSupport()">Sarunāties ar personu</button>
        </div>
    </div>

    <!-- Cilvēka atbalsts modālis -->
    <div id="humanSupportModal" class="support-modal">
        <div class="support-modal-content">
            <button class="close-btn" onclick="closeHumanSupport()">X</button>
            <h2>Cilvēka Atbalsts</h2>
            <div class="support-form">
                <h3>Mēs ar jums drīz sazināsimies!</h3>
                <form onsubmit="submitSupportRequest(event)">
                    <input type="text" placeholder="Jūsu vārds" required>
                    <input type="email" placeholder="Jūsu e-pasts" required>
                    <textarea placeholder="Aprakstiet savu problēmu..." rows="5" required></textarea>
                    <button type="submit" class="submit-btn">Nosūtīt Pieprasījumu</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
    </footer>
    <!-- Maksāšanas Metodes Modālis -->
    <div id="paymentModal" class="payment-modal">
        <div class="payment-modal-content">
            <button class="close-btn" onclick="closePaymentModal()">X</button>
            <h2>Maksāšanas Metode</h2>
            <p>Lūdzu, izvēlieties sev vēlamo maksāšanas metodi:</p>
            <div class="payment-options">
                <button class="payment-option" onclick="selectPaymentMethod('card')">
                    <span class="payment-icon">Karte</span>
                    <span class="payment-name">Maksāt ar Karti Tiešsaistē</span>
                    <span class="payment-desc">Draudzīga un draudzīga maksāšana</span>
                </button>
                <button class="payment-option" onclick="selectPaymentMethod('cash')">
                    <span class="payment-icon">Skaidra</span>
                    <span class="payment-name">Maksāt Vietā</span>
                    <span class="payment-desc">Skaidra nauda piegādes laikā</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Peldošais AI Atbalsta Poga -->
    <button class="floating-support-btn" onclick="showSupport()" title="AI Atbalsts">💬</button>
    
    <script>
        // Theme management
        function setTheme(theme) {
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(theme + '-theme');
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            fetch('set_theme.php?theme=' + theme).catch(e => {});
        }
        
        // Initialize theme on page load
        window.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });
        
        // Keyboard shortcut: Alt + T to toggle theme
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 't') {
                e.preventDefault();
                const current = localStorage.getItem('theme') || 'light';
                setTheme(current === 'light' ? 'dark' : 'light');
            }
        });
    </script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
