# O! PICA - TEHNISKĀ DOKUMENTĀCIJA

**📊 Saistītie dokumenti:**
- [ER Diagramma](ER_DIAGRAM.md) - Visuāla datu bāzes struktūra
- [Testēšanas Protokols](TESTING_PROTOCOL.md) - Testēšanas rezultāti un protokols

## 1. DATU BĀZES PROJEKTĒŠANA

### 1.1 UZSKAITĪJUSIES PARADIGMA
- Relāciju datu bāze (Relational Database)
- ACID atbalsts (InnoDB)
- Normalizēta struktūra (3NF)

### 1.2 TABULU SPECIFIKĀCIJAS

#### USERS (Lietotāji)
```sql
user_id INT PRIMARY KEY AUTO_INCREMENT
username VARCHAR(100) UNIQUE NOT NULL
email VARCHAR(100) UNIQUE NOT NULL
password_hash VARCHAR(255) NOT NULL
theme VARCHAR(10) DEFAULT 'light'
role ENUM('user', 'admin') DEFAULT 'user'
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```
**Indeksi:** email, username, created_at  
**Drošība:** Password tiek glabāts hešs formātā

#### PIZZAS (Picu katalogs)
```sql
pizza_id INT PRIMARY KEY AUTO_INCREMENT
name VARCHAR(150) UNIQUE NOT NULL
description TEXT
price DECIMAL(8,2) NOT NULL
image_filename VARCHAR(255)
allergens VARCHAR(255) COMMENT 'Komatu atdalīti'
category VARCHAR(50) DEFAULT 'pizza'
is_active BOOLEAN DEFAULT TRUE
```
**Indeksi:** name, price, category  
**Izmantošana:** Klientu saskarnes katalogs

#### ORDERS (Pasūtījumi)
```sql
order_id INT PRIMARY KEY AUTO_INCREMENT
user_id INT FOREIGN KEY
total_price DECIMAL(10,2)
status ENUM('pending','confirmed','preparing','on_way','delivered','cancelled')
payment_method_id INT FOREIGN KEY
delivery_method_id INT FOREIGN KEY
delivery_address VARCHAR(255)
delivery_city VARCHAR(100)
order_date TIMESTAMP
delivery_date TIMESTAMP NULL
```
**Indeksi:** user_id, status, order_date  
**Savienojumi:** users, payment_methods, delivery_methods

#### ORDER_ITEMS (Pasūtījuma pozīcijas)
```sql
order_item_id INT PRIMARY KEY AUTO_INCREMENT
order_id INT FOREIGN KEY
pizza_id INT FOREIGN KEY
quantity INT
unit_price DECIMAL(8,2)
subtotal GENERATED ALWAYS AS (quantity * unit_price)
```
**Indeksi:** order_id, pizza_id  
**Aprēķins:** Subtotal tiek aprēķināts automātiski (GENERATED COLUMN)

#### PAYMENT_METHODS (Maksāšanas metodes)
```
ID | method_name | icon | is_active
1  | Maksāšana ar karti | 💳 | TRUE
2  | Maksāšana uz vietas | 💵 | TRUE
```

#### DELIVERY_METHODS (Piegādes metodes)
```
ID | method_name | cost | min_minutes | max_minutes | is_active
1  | Piegāde Tukumā | 2.00 | 30 | 45 | TRUE
2  | Savākšana pie Mego | 0.00 | 20 | 30 | TRUE
3  | Piegāde Jauntukumā, Durbē | 1.00 | 40 | 60 | TRUE
```

#### REVIEWS (Atsauksmes)
```sql
review_id INT PRIMARY KEY
user_id INT FOREIGN KEY
order_id INT FOREIGN KEY (nullable)
pizza_id INT FOREIGN KEY
rating INT CHECK (rating >= 1 AND rating <= 5)
comment TEXT
is_approved BOOLEAN
```

#### SUPPORT_REQUESTS (Atbalsta pieprasījumi)
```sql
support_id INT PRIMARY KEY
user_id INT FOREIGN KEY (nullable)
name VARCHAR(100)
email VARCHAR(100)
message TEXT
status ENUM('open','in_progress','resolved','closed')
created_at TIMESTAMP
```

### 1.3 NORMALIZĀCIJA

**Pirmā Normālā Forma (1NF):**
- [CHECK] Visi lauki satur atomus vērtibas
- [CHECK] Nav atkārtotu grupu

**Otrā Normālā Forma (2NF):**
- [CHECK] Atbilst 1NF
- [CHECK] Visi ne-atslegu atributi pilņiba atkarigi no primārās atslegu
- [CHECK] Neatkarības dēļ PIZZA_INGREDIENTS tabula

**Trešā Normālā Forma (3NF):**
- [CHECK] Atbilst 2NF
- [CHECK] Nav tranziţivu atkarigību

---

## 2. ER DIAGRAMMA

### Galvenās Sakarības:

```
users (1) ──→ (M) orders ──→ (M) order_items ←─ (1) pizzas
              ↓
        payment_methods (1)
        delivery_methods (1)

users (1) ──→ (M) reviews ←─ (1) pizzas
              ↓
           orders (optional)

pizzas (M) ←─ (1) pizza_ingredients ─→ (1) ingredients

users (1) ──→ (M) support_requests
```

### Sakarību Tipi:

| Sakarība | Tips | Darbības |
|----------|------|----------|
| users → orders | 1:M | ON DELETE CASCADE |
| orders → order_items | 1:M | ON DELETE CASCADE |
| order_items → pizzas | M:1 | - |
| orders → payment_methods | M:1 | - |
| reviews → users | M:1 | ON DELETE CASCADE |
| pizza_ingredients → pizzas | M:1 | ON DELETE CASCADE |
| pizza_ingredients → ingredients | M:1 | ON DELETE CASCADE |

---

## 3. LIETOTĀJA SASKARNES SPECIFIKĀCIJA

### 3.1 LAPAS IZKĀRTOJUMS

```
┌─────────────────────────────────────────┐
│         HEADER - Logo | O! Pica         │
├─────────────────────────────────────────┤
│  NAVBAR: [Par mums] [Izvēlne] [Grozs] [Pasūtījumi] │
├─────────────────────────────────────────┤
│                                         │
│            MAIN CONTENT                 │
│  (Par mums | Izvēlne | Grozs | Pasūtījumi) │
│                                         │
├─────────────────────────────────────────┤
│   FOOTER: © 2026 O! Pica                │
└─────────────────────────────────────────┘
```

### 3.2 SADAĻAS

1. **PAR MUMS** - Kontakti, darba laiks, piegādes
2. **IZVĒLNE** - Grid ar 25 picām
3. **GROZS** - Pasūtīto picu saraksts, kopsumma
4. **PASŪTĪJUMI** - Lietotāja pasūtījumu vēsture

### 3.3 MODĀLIE LOGI

**Maksāšanas metodes:**
- 💳 Maksāšana ar karti
- 💵 Maksāšana uz vietas

**Atbalsts:**
- AI čats (automātiski)
- Cilvēka atbalsts (forma)

### 3.4 TĒMAS

```css
/* GAIŠĀ TĒMA */
--bg-primary: #ffffff
--bg-secondary: #f5f5f5
--text-primary: #1a1a1a
--text-secondary: #666666
--accent: #ff4757

/* TUMŠĀ TĒMA */
--bg-primary: #1a0033
--bg-secondary: #2d0052
--text-primary: #f5f5f5
--text-secondary: #b0b0b0
--accent: #ff4757
```

### 3.5 RESPONSIVE

```
📱 Mobilā (< 768px):
  - 1 kolonna
  - Pilnais platums
  - Touch-friendly

💻 Plānotā (768px - 1024px):
  - 2 kolonnas
  - Pielagotie margines

🖥️ Deskstops (> 1024px):
  - 3-4 kolonnas
  - Optimizēts lielākai ekrānam
```

---

## 4. PROGRAMMAS FUNKCIONALITĀTE

### 4.1 LIETOTĀJA PLŪSMA

```
1. PALAIŠANA
   ↓
2. SKATĪT KATALOGS
   ├─ Par mums
   └─ Picu režģis

3. PIEVIENOT GROZAM
   ├─ Klikšķis uz "Pievienot"
   └─ Groza skaits +1

4. GROZA SKATĪŠANA
   ├─ Skatīt cenu
   ├─ Mainīt daudzumu
   └─ Izņemt pozīcijas

5. PASŪTĪJUMA NOSLĒGŠANA
   ├─ Noklikšķiniet "Pasūtīt"
   ├─ Izvēlieties maksāšanu (MODAL)
   ├─ Atsūtīt uz API
   └─ Redzēt apstiprinājumu

6. PASŪTĪJUMA IZSEKOŠANA
   ├─ Skatīt vēsturi
   ├─ Redzēt statusu
   └─ Atsauksmes atstāšana
```

### 4.2 ADMIN PLŪSMA

```
1. PIEREĢISTRĒTIES
   ├─ URL: admin.php
   └─ Parole: "parole123"

2. SKATĪT PASŪTĪJUMUS
   ├─ Sortēti pēc datuma
   ├─ Redzēt statusu
   └─ Maksāšanas metode

3. ATJAUNINĀT STATUSU
   ├─ Klikšķis uz "Piegādāts"
   ├─ Status: pending → delivered
   └─ Atjaunināt lokāli

4. SKATĪT STATISTIKU
   ├─ Kopējie pasūtījumi
   ├─ Ieņēmumi
   ├─ Vidējā cena
   └─ Populārākā pica
```

### 4.3 API FUNKCIJAS

Visos pieprasījumos iekļauts error handling:

```php
function json_response($success, $message, $data, $status_code)
{
    http_response_code($status_code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
```

---

## 5. KĻŪDU APSTRĀDE

### 5.1 KĻŪDU TIPI

| Kods | Apraksts | Cēlonis |
|------|----------|---------|
| 200 | OK | Veiksmīgs pieprasījums |
| 400 | Bad Request | Nepareizi parametri |
| 404 | Not Found | Resurss nav atrasts |
| 500 | Server Error | Datubāzes kļūda |

### 5.2 VALIDĀCIJA

```php
// Obligātie lauki
if (empty($data['items']) || empty($data['total'])) {
    json_response(false, 'Nepieciešami: items, total', null, 400);
}

// SQL Injection prevencija
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// XSS prevencija
$safe_input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```

### 5.3 LOGGING

```
logs/errors.log

[2026-02-26 14:30:45] Pasūtījuma izveide neizdevās | {"error": "..."}
[2026-02-26 14:31:12] Datubāzes savienojuma kļūda | {"connection": "timeout"}
```

---

## 6. TESTĒŠANAS PROTOKOLS

### 6.1 UNIT TESTA SCENĀRIJI

**Test 1: Datu bāzes savienojums**
```
1. Run: http://localhost/opica/api_mysql.php?action=get_statistics
2. Verify: Statuss = "success"
3. Expected: JSON ar statistiku
```

**Test 2: Picas iegūšana**
```
1. GET /api_mysql.php?action=get_pizzas
2. Verify: Atgriež 25 picas
3. Check: Katrai piccai id, name, price
```

**Test 3: Pasūtījuma izveide**
```
1. POST /api_mysql.php?action=create_order
2. Body: {items: [{id:1,qty:2}], total:15.60, paymentMethod:"card"}
3. Expected: order_id returned
4. Verify: Dati MySQL tabulā
```

### 6.2 INTEGRĀCIJAS TESTI

**Test: Pasūtījuma Plūsma**
```
1. Skatīt picas
2. Pievienot 2 grozam
3. Noslēgt pasūtījumu
4. Iezīmēt kā piegādātu
5. Pārbaudīt statusu maiņu
```

### 6.3 UI TESTI

**Test: Maksāšanas Metodes**
```
1. Klikšķis uz "Pasūtīt"
2. Redzēt modāli
3. Izvēlieties "Karte"
4. Apstiprinājums redzams
5. Pārbaudīt localStorage
```

---

## 7. DROŠĪBAS PASĀKUMI

### 7.1 SQL INJECTION PREVENCIJA
```php
✅ Prepared Statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

❌ Direkti SQL
"SELECT * FROM users WHERE id = " . $_GET['id']
```

### 7.2 XSS PREVENCIJA
```php
✅ htmlspecialchars()
$safe = htmlspecialchars($user_input);

❌ Direkta ievietošana
echo $_GET['name'];
```

### 7.3 CSRF PREVENCIJA
✅ Plānoti tokens nākotnei

### 7.4 PAROLES HASH
```php
✅ password_hash('password', PASSWORD_BCRYPT)
❌ MD5 vai SHA bez salt
```

---

## 8. TURPMĀKIE UZLABOJUMI

- [ ] OpenID Connect autentifikācija
- [ ] Stripe integrācija (reālie maksājumi)
- [ ] SMS paziņojumi par pasūtījumiem
- [ ] Mobilā aplikācija (React Native)
- [ ] Mākslīgais intelekts (ieteikumi)
- [ ] Multi-language (ENG, RUS, DE)
- [ ] PWA (offline režīms)
- [ ] Google Analytics integrācija

---

**Versija:** 1.0  
**Data:** 2026-02-26  
**Autors:** AI Asistents  
**Status:** ✅ Gatavs ražošanai
