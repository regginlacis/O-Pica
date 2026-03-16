# 🍕 O! PICA - PILNĪGA DATU BĀZES SISTĒMA

## 📖 PĀRSKATS

**O! Pica** ir pilnīga pasūtījuma vadības sistēma picu restorānam **Tukumā, Latvija**. Sistēma nodrošina:

✅ **Klientiem:**
- Picu katalogs ar detaļām un alergēniem
- Groza sistēma
- Vairākas maksāšanas iespējas
- Pasūtījuma izsekošana
- Atsauksmju sniegšana

✅ **Administrātoram:**
- Pasūtījumus vadības panelis
- Estatūna apskatīšana un atjaunināšana
- Statistika par pasūtījumiem
- Atsauksmes moderācija

---

## � IETEIKTĀ LASĪŠANAS SECĪBA

1. 📊 [**ER Diagramma**](ER_DIAGRAM.md) - Skatīt datu bāzes struktūru
2. 📖 [**Tehniskā Dokumentācija**](DOKUMENTACIJA.md) - Detalizēts API un DB apraksts
3. ✅ [**Testēšanas Protokols**](TESTING_PROTOCOL.md) - Testēšanas rezultāti4. 🔌 [**API Dokumentācija**](API_REFERENCE.md) - API endpoints un piemēri

## ✨ JAUNĀKIE UZLABOJUMI (v1.2)

### Lietotāju Profila Sistēma 👤
- **Profila Apskatīšana** - `profile.php` - Lietotāja informācija, pasūtījumi, atsauksmes
- **Profila Rediģēšana** - `edit_profile.php` - Lietotājvārds, e-pasts, tēma
- **Paroles Maiņa** - Draudzīga forma ar validāciju
- **Iziet funkcija** - `logout.php` - Beigt sesiju
- **Navigācijas Atjauninājumi** - index.php rāda pierakstītā lietotāja stāvokli

### Admin Terminālā-Tikai Pieeja 🔐
- **Terminal Login Script** - `admin-login.php` - Ieiet caur termināli
- **Drošības Paziņojums** - Admin panelis ar terminālā-tikai pieprasījumu
- **Sesijas Aizsardzība** - Admin var piekļūt tikai caur CLI metodi

### Dashboard & Statistics 📊
- **Statistikas Widgeti** - Pasūtījuma skaits, ieņēmumi, vidējā vērtība, reitings
- **Interaktīvie Grafiki** - Chart.js ar real-time datiem
- **Top 5 Picas** - Populārākās picas tabelā
- **Perioda Izvēle** - Šodien, šonedēļ, šomēnesi
- **CSV Eksports** - Visus pasūtījumus uz Excel

### Lietotāju Autentifikācija 

### API Uzlabojumi 🔌
- **Input Validācija** - Validator klase ar 10+ validācijas tipa
- **Rate Limiting** - Aizsardzība no pārāk daudziem pieprasījumiem
- **CSRF Aizsardzība** - Iebūvēts Security klases metodēs
- **Error Logging** - Detalizēts logging ar Security/Error/API notikumiem
- **CSV Eksports** - Automātiski lejupielādējami CSV faili

### Drošības Uzlabojumi 🔒
- **Šifrētas Paroles** - bcrypt ar cost=12
- **SQL Injection Aizsardzība** - Prepared statements
- **XSS Aizsardzība** - htmlspecialchars()
- **IP Bloķēšana** - Automatiska bloķēšana pēc pārkāpumiem
- **2FA Atbalsts** - Token ģenerēšana (turpmāk)
---

## �🚀 ĀTRI UZSĀKT

### 1. INSTALĀCIJA
```bash
1. Nokopēt failus uz c:\xampp\htdocs\opica\
2. Atvērt MySQL (phpMyAdmin)
3. Atvērt pārlūkā: http://localhost/opica/install.php
4. Automātiski izveidos datubāzi
5. Dzēst install.php failu!
```

### 2. PALAIŠANA
```
http://localhost/opica/
```

### 3. ADMIN PANELIS
```
Atver: http://localhost/opica/admin.php
Pieeja: Terminal-Tikai!

Iestata sesiju caur termināli:
$ cd c:\xampp\htdocs\opica
$ php admin-login.php

Tad atvērt http://localhost/opica/admin.php pārlūkā
```

---

## 🗄️ DATU BĀZES STRUKTŪRA

9 galvenās tabulas ar pilnīgiem foreign keys:

| Tabula | Žanrs |
|--------|-------|
| `users` | Lietotāji |
| `pizzas` | Picu katalogs |
| `ingredients` | Sastāvdaļas |
| `pizza_ingredients` | Picas sastāvs |
| `orders` | Pasūtījumi |
| `order_items` | Pasūtījuma pozīcijas |
| `payment_methods` | Maksāšana |
| `delivery_methods` | Piegāde |
| `reviews` | Atsauksmes |
| `support_requests` | Atbalsts |

Skatīt `setup_database.sql` pilnai diagrammai.

---

## 📡 API ENDPOINTS

### Picas
```
GET /api_mysql.php?action=get_pizzas
```

### Pasūtījumi
```
POST /api_mysql.php?action=create_order
GET /api_mysql.php?action=get_orders
POST /api_mysql.php?action=update_order_status
POST /api_mysql.php?action=mark_delivered
POST /api_mysql.php?action=delete_order
```

### Cits
```
POST /api_mysql.php?action=create_support_request
POST /api_mysql.php?action=create_review
GET /api_mysql.php?action=get_statistics
```

---

## 🎨 LIETOTĀJA SASKARNES SPECIFIKĀCIJA

- ✅ Responsive dizains (mobilā, planšete, deskstops)
- ✅ Gaišā un tumšā tēma
- ✅ Modālie logi maksāšanai
- ✅ Real-time groza atjauninājums
- ✅ AI atbalsts čats

---

## 📋 UZDEVUMU STATUSS

| # | Uzdevums | Statuss |
|---|----------|---------|
| 1️⃣ | Datu bāzes projektēšana | ✅ DONE - 9 tabulas ar ER |
| 2️⃣ | ER diagrammas izstrāde | ✅ DONE - Pilnīga Mermaid diagramma |
| 3️⃣ | Lietotāja saskarnes izveide | ✅ DONE - Responsive HTML/CSS/JS |
| 4️⃣ | Funkcionalitātes izstrāde | ✅ DONE - Pilns CRUD un vairāk |
| 5️⃣ | Kļūdu meklēšana | ✅ DONE - Error logging sistēma |
| 6️⃣ | Testēšana | ✅ DONE - 35+ testēšanas scenāriji |
| 7️⃣ | Profesionālie uzlabojumi | ✅ DONE - Validator, Security, Auth, Statistics |
| 8️⃣ | Lietotāju autentifikācija | ✅ DONE - Login, Register, Profile Management |
| 9️⃣ | Admin Panelis | ✅ DONE - Dashboard ar statistiku un CSV |
| 🔟 | Admin Terminālā-Tikai Pieeja | ✅ DONE - admin-login.php ar CLI iespēja |

---

## 🛠️ NEPIECIEŠAMĀ KONFIGURĀCIJA

Rediģējiet `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'opica_db');
```

---

## 📁 FAILU STRUKTŪRA

```
opica/
├── index.php              # Galvenā lapa (atjaunināta ar nav)
├── admin.php              # Admin panelis (terminālā-tikai)
├── admin-login.php        # Terminal admin login skripts
├── login.php              # Lietotāju pierakstīšanās
├── register.php           # Lietotāju reģistrācija
├── profile.php            # Lietotāja profila apskatīšana
├── edit_profile.php       # Profila rediģēšana
├── logout.php             # Iziet no sistēmas
├── api_mysql.php          # MySQL RESTful API (atjaunināta)
├── config.php             # Konfigurācija (ar class autoloading)
├── script.js              # JavaScript
├── style.css              # CSS (2 tēmas)
├── setup_database.sql     # SQL skripts
├── check_users_table.php  # Users tabulas pārbaude
├── logs/                  # Kļūdu un drošības žurnāls
├── src/                   # Utility klases
│   ├── Auth.php           # Autentifikācija
│   ├── Validator.php      # Input validācija
│   ├── Security.php       # CSRF, rate limiting, utt.
│   ├── Statistics.php     # Admin dashboard dati
│   └── CSVExport.php      # CSV eksports
└── opica bildes/          # Picu attēli
```

---

## 🔐 DROŠĪBA

- ✅ SQL Injection prevencija (prepared statements)
- ✅ XSS prevencija (htmlspecialchars)
- ✅ Error logging
- ✅ Input validācija

---

## 📊 SĀKOTNĒJIE DATI

- **25 picas** (ar alergēniem)
- **2 maksāšanas metodes** (karte, skaidra)
- **3 piegādes metodes** (Tukums, Jauntukums, Pickup)
- **50+ sastāvdaļas** (siers, gaļa, dārzeņi, utt.)

---

## 🧪 TESTĒŠANAS SCENĀRIJI

### Picas Palaišana
1. Atvērt http://localhost/opica/
2. Redzēt 25 picas katalogs
3. Paveikt tēmu pārslēgšanu

### Pasūtījuma Izveide
1. Pievienot 2 picas grozam
2. Atveriet Grozs
3. Noklikšķiniet "Pasūtīt"
4. Izvēlieties maksāšanu (karte/skaidra)
5. Nosūtīt un apstiprināt

### Admin Panelis
1. http://localhost/opica/admin.php
2. Parole: `parole123`
3. Redzēt visus pasūtījumus
4. Iezīmēt kā "piegādāts"

---

## 💡 PABEIGTS

- ✅ Lietotāja autentifikācija (Login, Register)
- ✅ Profila pārvaldība (View, Edit, Password Change)
- ✅ Admin panelis ar statistiku
- ✅ CSV eksports
- ✅ API validācija un rate limiting
- ✅ Drošības uzlabojumi (Bcrypt, CSRF, XSS prevencija)
- ✅ Error logging sistēma
- ✅ Dokumentācija (API, ER, Testing)

## 💡 TURPMĀKIE UZLABOJUMI (Nākotne)

- 🔄 2FA (Two-Factor Authentication)
- 📱 PWA (Progressive Web App)
- 🌐 Multi-language support (LV, EN, RU)
- 📧 E-pasts paziņojumi
- 🎨 Profila avatāri
- 📊 Paplašinātas statistika un atskaites

---

**Versija:** 1.2  
**Data:** 2026-02-27  
**Licence:** MIT

> 🎉 **Profila sistēma, terminālā-tikai admin pieeja un pilnīga lietotāju autentifikācija pabeigta!**
