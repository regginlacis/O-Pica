# 🚀 O! PICA - INSTALĀCIJAS CEĻVEDIS

## SOLI PA SOLIM INSTALĀCIJA

### SOLIS 1: PĀRBAUDĪT PRIEKŠNOTEIKUMI

Pirms sākt, pārbaudiet vai jums ir:
- ✅ XAMPP/WAMP instalēts
- ✅ MySQL/MariaDB palaists
- ✅ Apache palaists
- ✅ PHP 7.4+
- ✅ Internet pārlūks

**Windows:**
```
XAMPP Control Panel -> Start Apache, MySQL
```

**Mac/Linux:**
```bash
brew services start mysql  # Mac
sudo service mysql start   # Linux
```

---

### SOLIS 2: NOKOPĒT FAILUS

1. Lejupielādēt vai klonēt O! Pica projektu
2. Nokopēt mapi `opica` uz:
   ```
   Windows: C:\xampp\htdocs\opica\
   Mac:     /Library/WebServer/Documents/opica/
   Linux:   /var/www/html/opica/
   ```

---

### SOLIS 3: KONFIGURĒT DATUBĀZI

1. Atvērt `config.php` teksta redaktorā
2. Pārbaudīt šos parametrus:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Ja nav paroles
   define('DB_NAME', 'opica_db');
   define('DB_PORT', 3306);
   ```
3. Saglabāt failu

**Problēma?** Ja MySQL ir parolē:
```php
define('DB_PASS', 'your_mysql_password');
```

---

### SOLIS 4: AUTOMĀTISKA INSTALĀCIJA (Ieteicamā)

1. Atvērt pārlūkā:
   ```
   http://localhost/opica/install.php
   ```

2. Redzēsiet zaļo skaņu ([OK]):
   ```
   [OK] CREATE DATABASE IF NOT EXISTS opica_db
   [OK] CREATE TABLE IF NOT EXISTS users
   [OK] CREATE TABLE IF NOT EXISTS pizzas
   ... utt.
   ```

3. Ja viss zaļš -> **DZĒST `install.php` failu!**
   ```
   Vienkārši izdzēst C:\xampp\htdocs\opica\install.php
   ```

---

### SOLIS 5: MANUĀLA INSTALĀCIJA (ja automātiskā nedarbojas)

1. Atvērt phpMyAdmin:
   ```
   http://localhost/phpmyadmin
   ```

2. SQL lapā (SQL tab):
   - Noklikšķiniet "New"
   - Izvēlieties "Import"
   - Augšupielādējiet `setup_database.sql`
   - Noklikšķiniet "Execute"

3. Pārbaudīt vai tabulas izvēidas:
   ```sql
   SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'opica_db';
   ```
   
   Jums jāredz 9 tabulas:
   ```
   users
   pizzas
   ingredients
   pizza_ingredients
   orders
   order_items
   payment_methods
   delivery_methods
   reviews
   support_requests
   ```

---

### SOLIS 6: PALAIST APLIKĀCIJU

1. Atvērt pārlūkā:
   ```
   http://localhost/opica/
   ```

2. Lapai jābūt ielādētai ar:
   - 🍕 Logo un nosaukums
   - 📱 Navigācijas pogi
   - 🎨 Picu katalogs
   - 💬 AI čats

3. Pārbaudīt tēmas:
   - Klikšķis uz gaismas ikonas (augšējais labais)
   - Pārslēgties starp gaišo/tumšo tēmu

---

### SOLIS 7: ADMIN PANELIS

1. Atvērt:
   ```
   http://localhost/opica/admin.php
   ```

2. Ievadīt paroli:
   ```
   parole123
   ```

3. Jums jāredz:
   - Pasūtījumu saraksts
   - Statusu pārvaldība
   - Statistika
   - "Dzēst visus" poga

---

## 🔍 PROBLĒMU RISINĀJUMS

### ❌ "Datubāzes savienojuma kļūda"

**Cēlonis:** MySQL nav palaists vai nepareizi parametri

**Risinājums:**
```bash
# Windows
XAMPP Control Panel -> Click "Start" pie MySQL

# Mac
brew services start mysql

# Linux
sudo service mysql start
```

Pārbaudīt `config.php`:
```php
define('DB_USER', 'root');
define('DB_PASS', '');  // Parole?
```

---

### ❌ "File not found"

**Cēlonis:** Faili nav pareizā mapē

**Risinājums:**
- Pārbaudīt ceļu:
  ```
  C:\xampp\htdocs\opica\index.php  ✅
  C:\xampp\htdocs\opica\install.php  ✅
  ```
- Nedrīkst būt `opica\opica\` dubulta mape

---

### ❌ "Blank page" (Balta lapa)

**Cēlonis:** PHP kļūda vai JS kļūda

**Risinājums:**
1. Nospiest `F12` (Developer Tools)
2. Skatīt Console tab uz kļūdām
3. Skatīt Network tab uz neveiksmīgiem pieprasījumiem

**Log skatīšana:**
```
C:\xampp\htdocs\opica\logs\errors.log
```

---

### ❌ "API returns error 500"

**Cēlonis:** Datubāzes kļūda API skriptā

**Pārbaudīt:**
1. Vai datubāze `opica_db` pastāv?
2. Vai tabulas izvēidas?
3. Skatīt `logs/errors.log`

---

### ❌ "Picas attēli nerāda"

**Cēlonis:** Attēli nav mapē `opica bildes/`

**Risinājums:**
1. Pārbaudīt mapē:
   ```
   C:\xampp\htdocs\opica\opica bildes\
   ```
2. Failu ir jābūt šiem:
   ```
   nothingspecial.jpg
   salami.jpg
   margarita.jpg
   ... utt.
   ```

3. Ja nav, attēli tiks ielādēti no placeholder

---

### ❌ "install.php nesāk"

**Problēma:** Dati bāze jau pastāv

**Risinājums:**
1. Dzēst datubāzi phpMyAdmin:
   ```sql
   DROP DATABASE opica_db;
   ```
2. Pēc tam draiž install.php

---

## ✅ VERIFIKĀCIJA

Ja viss darbojas, jūs redzēsiet:

```
[CHECK] http://localhost/opica/ -> Picas katalogs
[CHECK] Pievienot grozam -> Grozs skaits pieaug
[CHECK] Maksāšana -> Modālis ar divām metodēm
[CHECK] Admin panel -> Parole darbojas
[CHECK] Picas attēli -> Redzami
[CHECK] Tēma pārslēgšana -> Temne/Gaišs
```

---

## 📱 FIRST RUN CHECKLIST

- [ ] MySQL darbojas
- [ ] Faili kopēti uz pareizi
- [ ] config.php konfigurēts
- [ ] install.php palaists
- [ ] install.php dzēsts
- [ ] http://localhost/opica/ darbojas
- [ ] Admin panel: http://localhost/opica/admin.php
- [ ] Parole darbojas: "parole123"
- [ ] Datu bāze MySQL satur datus
- [ ] API darbojas: http://localhost/opica/api_mysql.php?action=get_pizzas

---

## 🎓 NĀKAMAI SOĻIEM

1. **Testēšana:** Līdz pēc faila `DOKUMENTACIJA.md`
2. **Modifikācijas:** Pielāgot ikonun, krāsas, tekstu
3. **Piegāde:** Izvietot uz web hosting
4. **Optimizācija:** Kompresēt CSS/JS, optimizēt attēlus

---

## 📞 PALĪDZĪBA

Ja jūs joprojām ir problēmas:

1. Skatīt `logs/errors.log`
2. Pārbaudīt konsoli (F12)
3. Pārbaudīt phpMyAdmin datu bāzes

---

**Versija:** 1.0  
**Data:** 2026-02-26  
🎉 Laime ar instalāciju!
