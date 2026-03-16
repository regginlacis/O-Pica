<?php
require_once 'theme_init.php';
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$current_theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo htmlspecialchars($current_theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Dark Mode</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .dark-mode-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .theme-showcase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .theme-demo {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
            font-size: 1.2em;
            transition: all 0.3s;
        }

        .light-demo {
            background: #f5f5f5;
            color: #1a1a1a;
            border: 2px solid #E8360F;
        }

        .dark-demo {
            background: #1a1a1a;
            color: #f5f5f5;
            border: 2px solid #E8360F;
        }

        .feature-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .feature-item {
            padding: 15px;
            border-left: 4px solid #E8360F;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .feature-item:hover {
            transform: translateX(5px);
        }

        .toggle-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .toggle-btn {
            padding: 12px 24px;
            border: 2px solid #E8360F;
            background: transparent;
            color: #E8360F;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            background: #E8360F;
            color: white;
            transform: translateY(-2px);
        }

        .toggle-btn.active {
            background: #E8360F;
            color: white;
        }

        .info-box {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .keyboard-shortcut {
            background: #f0f0f0;
            border-left: 4px solid #E8360F;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 15px 0;
        }
    </style>
</head>
<body class="light-theme">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>O! Pica</h1>
                <p>Dark Mode</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Light theme" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">Light</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Dark theme" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">Dark</button>
            </div>
        </div>
    </header>

    <div class="dark-mode-container">
        <h2 style="color: #E8360F; font-size: 2em; text-align: center;">Dark Mode</h2>
        
        <div class="info-box" style="background: #E8360F15; border-left: 4px solid #E8360F;">
            <h3>Welcome to dark mode!</h3>
            <p>You can change the theme anytime using the buttons below or the keyboard shortcut <strong>Alt + T</strong>.</p>
        </div>

        <h3>Available Themes</h3>
        <div class="theme-showcase">
            <div class="theme-demo light-demo">
                Light Theme<br>
                <small>(Classic Light)</small>
            </div>
            <div class="theme-demo dark-demo">
                Dark Theme<br>
                <small>(Night Mode)</small>
            </div>
        </div>

        <h3>Quick Commands</h3>
        <div class="toggle-buttons">
            <button class="toggle-btn" onclick="setTheme('light')">Light Theme</button>
            <button class="toggle-btn" onclick="setTheme('dark')">Dark Theme</button>
        </div>

        <div class="keyboard-shortcut">
            [KEYBOARD] <strong>Tastīšanas Kombācija:</strong> Alt + T (pārslēgt tēmu)
        </div>

        <h3>[THEME] Tēmas Funkcionalitāte</h3>
        <div class="feature-list">
            <div class="feature-item">
                [OK] <strong>Automātiska Saglabāšana</strong><br>
                Jūsu izvēlētā tēma tiek saglabāta
            </div>
            <div class="feature-item">
                [OK] <strong>Visos Lapās</strong><br>
                Tēma tiek lietota visos sistēmas lapās
            </div>
            <div class="feature-item">
                [OK] <strong>Glusa Pāreja</strong><br>
                Profesionāla animācija pēc tēmas maiņas
            </div>
            <div class="feature-item">
                [OK] <strong>Pieejamība</strong><br>
                Labāka redzamība abās tēmās
            </div>
        </div>

        <h3>[LIST] Tēmu Opcijas</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="border-bottom: 2px solid #E8360F;">
                <th style="padding: 10px; text-align: left;">Tēma</th>
                <th style="padding: 10px; text-align: left;">Apraksts</th>
                <th style="padding: 10px; text-align: left;">Lapas</th>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">LIGHT Gaišā</td>
                <td style="padding: 10px;">Klasiskā gaiša krāsu shēma</td>
                <td style="padding: 10px;">Visas lapas</td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">DARK Tumšā</td>
                <td style="padding: 10px;">Patīkama tumšā krāsu shēma</td>
                <td style="padding: 10px;">Visas lapas</td>
            </tr>
        </table>

        <h3>[SAVE] Kur Saglabājas Mana Izvēle?</h3>
        <p>Jūsu izvēlētā tēma tiek saglabāta:</p>
        <ul>
            <li><strong>localStorage</strong> - Jūsu pārlūkprogrammā (lokāli)</li>
            <li><strong>Sesija</strong> - Uz servera (kamēr esat pierakstīts)</li>
            <li><strong>Cookie</strong> - Uz 1 gadu (turpmāk atcerēsies)</li>
        </ul>

        <div class="info-box" style="background: #4CAF5015; border-left: 4px solid #4CAF50;">
            <h3>[TIPS] Padomi</h3>
            <ul>
                <li>Tumšā tēma ir labāka naktī, jo neuguņ acis par daudz</li>
                <li>Gaišā tēma ir labāka dienas laikā, ja ir spilgts apgaismojums</li>
                <li>Jūs varat mainīt tēmu jebkurā laikā!</li>
            </ul>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <a href="index.php" style="color: #E8360F; text-decoration: none; font-weight: 600; font-size: 1.1em;"><- Atgriezties uz Galveno Lapu</a>
        </div>
    </div>

    <footer style="text-align: center; margin-top: 50px; color: #999; padding: 20px;">
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
    </footer>

    <script>
        function setTheme(theme) {
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(theme + '-theme');
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            fetch('set_theme.php?theme=' + theme).catch(e => {});
            
            // Update button states
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });

        // Keyboard shortcut: Alt + T
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 't') {
                e.preventDefault();
                const current = localStorage.getItem('theme') || 'light';
                setTheme(current === 'light' ? 'dark' : 'light');
            }
        });
    </script>
</body>
</html>
