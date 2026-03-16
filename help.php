<?php
require_once 'theme_init.php';
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="lv" data-theme="<?php echo isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O! Pica - Palīdzība</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .help-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .help-section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #E8360F;
            border-radius: 5px;
        }
        
        .help-section h3 {
            color: #E8360F;
            margin-top: 0;
        }
        
        .step {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        
        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #E8360F;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .link-button {
            display: inline-block;
            padding: 10px 20px;
            background: #E8360F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px 10px 0;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .link-button:hover {
            background: #ff6b4a;
            transform: translateY(-2px);
        }
        
        .status-box {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .status-green {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            color: #2e7d32;
        }
        
        .status-orange {
            background: #fff3e0;
            border: 1px solid #ff9800;
            color: #e65100;
        }
        
        .status-red {
            background: #ffebee;
            border: 1px solid #f44336;
            color: #c62828;
        }
    </style>
</head>
<body class="light-theme" style="background: #f5f5f5;">
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>O! Pica</h1>
                <p>Palīdzība un Norādes</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="theme-toggle" onclick="setTheme('light')" title="Gaisma tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">☀️</button>
                <button class="theme-toggle" onclick="setTheme('dark')" title="Tumšā tema" style="background: none; border: none; font-size: 1.5em; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🌙</button>
            </div>
        </div>
    </header>
    
    <div class="help-container">
        <?php if ($is_logged_in): ?>
            <div class="status-box status-green">
                OK Jūs esat pierakstīts sistēmā kā <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </div>
        <?php else: ?>
            <div class="status-box status-orange">
                WARNING Jūs neesat pierakstīts sistēmā. <a href="login.php" style="color: inherit; font-weight: bold;">Pierakstieties šeit</a>
            </div>
        <?php endif; ?>
        
        <h2>TOOLS Klūdu Novēršana</h2>
        
        <div class="help-section">
            <h3>ERROR Problēma: Nevar atvertuProfilel</h3>
            <p><strong>Iemesls:</strong> Datubāze nav sava konjunkcija vai MySQL serviss nav startēts.</p>
            
            <div class="step">
                <span class="step-number">1</span>
                <strong>Pārbaudiet, vai MySQL ir aktīvs</strong>
                <p>Atveriet XAMPP kontroles paneli un pārliecinieties, ka MySQL ir startēts (sarkans poga kļūst zaļa)</p>
            </div>
            
            <div class="step">
                <span class="step-number">2</span>
                <strong>Inicializējiet datubāzi</strong>
                <p>Apmeklējiet šo lapu, lai inicializētu datubāzi ar nepieciešamajiem tabulām:</p>
                <a href="setup_db.php" class="link-button">Inicializēt Database</a>
            </div>
            
            <div class="step">
                <span class="step-number">3</span>
                <strong>Pārbaudiet sistēmas stāvokli</strong>
                <p>Apmeklējiet statusanī lapu, lai apskatītu datubāzes savienojuma stāvokli:</p>
                <a href="status.php" class="link-button">Rādīt Stāvokli</a>
            </div>
            
            <div class="step">
                <span class="step-number">4</span>
                <strong>Atversiet Profilu</strong>
                <p>Pēc datu bāzes inicializēšanas, apmeklējiet savu profilu:</p>
                <a href="profile.php" class="link-button">Atvērt Profilu</a>
            </div>
        </div>
        
        <div class="help-section">
            <h3>LIST Pieejamās Vietas</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f5f5f5; border: 1px solid #ddd;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Vieta</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Apraksts</th>
                </tr>
                <tr style="border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="login.php" style="color: #E8360F;">Login</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Pierakstīties sistēmā</td>
                </tr>
                <tr style="background: #f9f9f9; border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="register.php" style="color: #E8360F;">Register</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Izveidot jaunu kontu</td>
                </tr>
                <tr style="border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="profile.php" style="color: #E8360F;">Profile</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Skaities profila informāciju (jāpierakstās)</td>
                </tr>
                <tr style="background: #f9f9f9; border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="setup_db.php" style="color: #E8360F;">Setup DB</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Inicializēt datu bāzi un tabulas</td>
                </tr>
                <tr style="border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="status.php" style="color: #E8360F;">Status</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Skatīt sistēmas stāvokli</td>
                </tr>
                <tr style="background: #f9f9f9; border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="index.php" style="color: #E8360F;">Home</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Galvenais demostrācijas lapā</td>
                </tr>
                <tr style="border: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><a href="test_complete.php" style="color: #E8360F;">Test Complete</a></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">Veikt pilnu sistēmas testu</td>
                </tr>
            </table>
        </div>
        
        <div class="help-section">
            <h3>ROCKET Ātrs Sākums</h3>
            
            <p>Izpildiet šīs darbības, lai sāktu darbu:</p>
            
            <div class="step">
                <span class="step-number">1</span>
                <strong>Inicializējiet datu bāzi</strong>
                <p>Pirmo reizi apmeklējiet <a href="setup_db.php">DB Setup lapu</a> visas nepieciešamās tabulas izveido</p>
            </div>
            
            <div class="step">
                <span class="step-number">2</span>
                <strong>Reģistrējieties</strong>
                <p>Apmeklējiet <a href="register.php">reģistrācijas lapu</a> un izveidojiet savu kontu</p>
            </div>
            
            <div class="step">
                <span class="step-number">3</span>
                <strong>Pierakstieties</strong>
                <p>Atgriezieties uz <a href="login.php">pierakstīšanās lapu</a> un pierakstieties ar savas akreditācijas</p>
            </div>
            
            <div class="step">
                <span class="step-number">4</span>
                <strong>Skatieties Profilu</strong>
                <p>Pēc pierakstīšanās, jūs varēsit <a href="profile.php">apskatīt savu profilu</a></p>
            </div>
        </div>
        
        <div class="help-section">
            <h3>HELP Biežāk Uzdotie Jautājumi</h3>
            
            <h4>P: Kādu paroli man jāizmanto?</h4>
            <p>A: Varat escolher jebkuru paroli ar vismaz 6 rakstzīmēm. Iesakām izmantot stipru paroli ar burtiem, cipāriem un speciālajiem symboliem.</p>
            
            <h4>P: Kur es varu atrast mūsu konta datus?</h4>
            <p>A: Apmeklējiet savu <a href="profile.php">profila lapu</a>, lai apskatītu jūsu informāciju.</p>
            
            <h4>P: Kā es varu izlogošanās?</h4>
            <p>A: Apmeklējiet savu <a href="profile.php">profila lapu</a> un noklikšķiniet uz "Izlogošanās" pogas.</p>
            
            <h4>P: Vai es varu mainīt mūsu e-pastu?</h4>
            <p>A: Jā, apmeklējiet savu profilu un noklikšķiniet uz "Rediģēt Profilu" pogas.</p>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" class="link-button"><- Atgriezties uz Galveno Lapu</a>
        </div>
    </div>
    
    <footer style="margin-top: 50px; text-align: center; color: #999; padding: 20px;">
        <p>&copy; 2026 O! Pica. Visas tiesības rezervētas.</p>
    </footer>
    
    <script>
        function setTheme(theme) {
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(theme + '-theme');
            localStorage.setItem('theme', theme);
            fetch('set_theme.php?theme=' + theme).catch(e => {});
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });
        
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
