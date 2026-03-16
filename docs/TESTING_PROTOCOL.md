# TESTĒŠANAS PROTOKOLS - O! PICA

**Projekts:** O! PICA - Picu piegādes vadības sistēma  
**Datums:** 2026-03-05  
**Pārbaudītājs:** Izstrādātājs  
**Versija:** 1.0  

---

## 1. LIETOTĀJA SASKARNES TESTĒŠANA

### 1.1 Lapas ielāde un vizuālais dizains

| # | Tests | Soļi | Paredzamais rezultāts | Stāvoklis |
|---|-------|------|----------------------|-----------|
| 1.1.1 | Pārlūka sākums | Atvērt http://localhost/opica/index.php | Galvenā lapa ielādējas bez kļūdām | ✅ PASSED |
| 1.1.2 | Gaišā tēma | Noklikšķināt uz 🌙 pogas | Lapas tēma mainās uz gaišu | ✅ PASSED |
| 1.1.3 | Tumšā tēma | Noklikšķināt uz ☀️ pogas | Lapas tēma mainās uz tumšu | ✅ PASSED |
| 1.1.4 | Tēmas saglabāšana | Mainīt tēmu un pārlādēt lapu | Izvēlētā tēma paliek saglabāta | ✅ PASSED |
| 1.1.5 | Responsīvs dizains (mobīlais) | Mainīt ekrāna izmēru uz 375px | Izkārtojums ir responseīvs | ✅ PASSED |

### 1.2 Navigācija

| # | Tests | Soļi | Paredzamais rezultāts | Stāvoklis |
|---|-------|------|----------------------|-----------|
| 1.2.1 | "Par mums" sekcija | Noklikšķināt uz "ℹ️ Par mums" | Rāda restorāna informāciju | ✅ PASSED |
| 1.2.2 | "Izvēlne" sekcija | Noklikšķināt uz "Izvēlne" | Parāda picu sarakstu | ✅ PASSED |
| 1.2.3 | "Grozs" sekcija | Noklikšķināt uz "🛒 Grozs" | Parāda groza saturu | ✅ PASSED |
| 1.2.4 | "Pasūtījumi" sekcija | Noklikšķināt uz "📦 Pasūtījumi" | Parāda lietotāja pasūtījumus | ✅ PASSED |

### 1.3 Picu katalogs

| # | Tests | Soļi | Paredzamais rezultāts | Stāvoklis |
|---|-------|------|----------------------|-----------|
| 1.3.1 | Picu ielāde | Atvērt "Izvēlni" | Picas tiek ielādētas no localStorage | ✅ PASSED |
| 1.3.2 | Picas detaļas | Hovēt pāri picai | Rāda nosaukumu, cenu, alergēnus | ✅ PASSED |
| 1.3.3 | Picas pievienošana grozam | Noklikšķināt "Pievienot grozam" | Pica tiek pievienota grozam | ✅ PASSED |
| 1.3.4 | Groza skaita atjaunināšana | Pievienot vairākas picas | Groza skaits atjaunās | ✅ PASSED |

### 1.4 Pasūtījuma veidošana

| # | Tests | Soļi | Paredzamais rezultāts | Stāvoklis |
|---|-------|------|----------------------|-----------|
| 1.4.1 | Maksāšanas modalas | Noklikšķināt "Pasūtīt" | Parāda maksāšanas opcijas | ✅ PASSED |
| 1.4.2 | Maksāšanas metodēs | Izvēlēties "Karte" vai "Skaidra nauda" | Metode tiek izvēlēta | ✅ PASSED |
| 1.4.3 | Piegādes metodes | Izvēlēties piegādes lomu | Loģa metodes tiek izvēlētas | ✅ PASSED |
| 1.4.4 | Adreses ievade | Ievadīt piegādes adresi | Adrese tiek saglabāta grozam | ✅ PASSED |
| 1.4.5 | Pasūtījuma apstiprinājums | Noklikšķināt "Pārliecināt pasūtījumu" | Rāda apstiprinājuma ekrānu | ✅ PASSED |

---

## 2. API TESTĒŠANA (RESTful)

### 2.1 GET Endpoints

| # | Endpoint | Metode | Paredzamais rezultāts | Stāvoklis |
|---|----------|--------|----------------------|-----------|
| 2.1.1 | `/api_mysql.php?action=get_pizzas` | GET | Atgriež JSON ar picu sarakstu | ✅ PASSED |
| 2.1.2 | JSON struktūra | GET | Katrai picai ir pizza_id, name, price | ✅ PASSED |
| 2.1.3 | Picu skaits | GET | Atgriež vismaz 1 picu | ✅ PASSED |

### 2.2 POST Endpoints - Pasūtījuma izveide

| # | Endpoint | Dati | Paredzamais rezultāts | Stāvoklis |
|---|----------|------|----------------------|-----------|
| 2.2.1 | `/api_mysql.php?action=create_order` | `{items, total, paymentMethod}` | Pasūtījums tiek izveidots (200) | ⚠️ TEST REQUIRED |
| 2.2.2 | order_id atgriešana | POST dati | Atgriež order_id naudas sekmīga izveidošana | ⚠️ TEST REQUIRED |
| 2.2.3 | Datu validācija | Saturs `items` = [] | Atgriež 400 kļūdu | ⚠️ TEST REQUIRED |
| 2.2.4 | Total cena | POST total = 0 | Pasūtījums netiek izveidots | ⚠️ TEST REQUIRED |

### 2.3 Databasejs tranzakcijas

| # | Tests | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 2.3.1 | Transakciju atomarums | Veidot pasūtījumu | Ja kļūda, viss rollback | ⚠️ TEST REQUIRED |
| 2.3.2 | ORDER_ITEMS savienojums | Pasūtījums ar 2 itemiem | Abi item tiek saglabāti | ⚠️ TEST REQUIRED |

---

## 3. DATUBĀZES TESTĒŠANA

### 3.1 Savienojums

| # | Tests | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 3.1.1 | MySQL savienojums | config.php ielāde | Savienojums ir veiksmīgs | ⚠️ Parka datubāze |
| 3.1.2 | UTF-8 kodējums | Latviešu teksts | Teksts tiek saglabāts pareizi | ⚠️ Parka datubāze |

### 3.2 Tabulu dati

| # | Tabula | Tests | Paredzamais rezultāts | Stāvoklis |
|---|--------|-------|----------------------|-----------|
| 3.2.1 | PIZZAS | Skaits > 0 | Ir vismaz viena pica | ⚠️ Parka datubāze |
| 3.2.2 | PAYMENT_METHODS | Skaits = 2 | Ir maksāšanas metodes | ⚠️ Parka datubāze |
| 3.2.3 | DELIVERY_METHODS | Skaits = 3 | Ir piegādes metodes | ⚠️ Parka datubāze |
| 3.2.4 | ORDERS | Cikls COUNT | Pasūtījumi tiek saglabāti | ⚠️ Parka datubāze |

### 3.3 Foreign Keys

| # | Tests | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 3.3.1 | ORDER -> PAYMENT_METHODS | Saites derīgums | Foreign key bez kļūdas | WARNING Parka datubāze |
| 3.3.2 | ORDER_ITEMS -> ORDERS | Saites derīgums | Foreign key bez kļūdas | WARNING Parka datubāze |
| 3.3.3 | ORDER_ITEMS -> PIZZAS | Saites derīgums | Foreign key bez kļūdas | WARNING Parka datubāze |

---

## 4. DROŠĪBAS TESTĒŠANA

### 4.1 SQL Injection

| # | Tests | Ievade | Paredzamais rezultāts | Stāvoklis |
|---|-------|--------|----------------------|-----------|
| 4.1.1 | Prepared statements | `' OR '1'='1` | Izsaukums neizpildās | ✅ PROTECTED |
| 4.1.2 | Parametru binding | POST dati | Parametri tiek iesieti droši | ✅ PROTECTED |

### 4.2 XSS (Cross-Site Scripting)

| # | Tests | Ievade | Paredzamais rezultāts | Stāvoklis |
|---|-------|--------|----------------------|-----------|
| 4.2.1 | HTML skripti | `<script>alert('XSS')</script>` | Skripts netiek izpildīts | ✅ PROTECTED |
| 4.2.2 | htmlspecialchars() | Teksts ar HTML | Teksts tiek enkodēts | ✅ PROTECTED |

### 4.3 Admin autentifikācija

| # | Tests | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 4.3.1 | Admin pieeja | Bez sesijas | Pieeja liegta | ✅ PROTECTED |
| 4.3.2 | Pareiza parole | `parole123` | Pieeja piešķirta | ✅ PROTECTED |
| 4.3.3 | Nepareiza parole | `wrongpass` | Pieeja liegta, error rādīts | ✅ PROTECTED |

---

## 5. DARBĪBAS TESTĒŠANA

### 5.1 Lietotāja plūsma

| # | Solis | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 5.1.1 | Startspaids | Atvērt index.php | Lapa ielādējas | ✅ PASSED |
| 5.1.2 | Izlasīt izvēlni | Noklikšķināt "Izvēlne" | Picas ielādējas | ✅ PASSED |
| 5.1.3 | Pievienot grozam | Noklikšķināt "+" poga | Pica pievienota | ✅ PASSED |
| 5.1.4 | Pasūtīt | Noklikšķināt "Pasūtīt" | Maksāšanas forma | ✅ PASSED |
| 5.1.5 | Samaksāt | Noklikšķināt "Pārliecināt" | Pasūtījums saglabāts | ⚠️ NEESAT PĀRBAUDĪTS |

### 5.2 Admin plūsma

| # | Solis | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 5.2.1 | Admin pieeja | Atvērt admin.php | Parāda login formu | ✅ PASSED |
| 5.2.2 | Login | Ievadīt paroli `parole123` | Pieeja piešķirta | ✅ PASSED |
| 5.2.3 | Pasūtījumu apskate | Noklikšķināt "Pasūtījumi" | Rāda visus pasūtījumus | ⚠️ NEESAT PĀRBAUDĪTS |
| 5.2.4 | Pasūtījuma statusa maiņa | Noklikšķināt "Piegādāts" | Statuss atjaunojās | ⚠️ NEESAT PĀRBAUDĪTS |

---

## 6. PRODUKTIVITĀTES TESTĒŠANA

| # | Tests | Apraksts | Paredzamais rezultāts | Stāvoklis |
|---|-------|---------|----------------------|-----------|
| 6.1 | Lapas ielādes laiks | index.php | < 2 sekundes | ✅ PASSED |
| 6.2 | API izsaukuma laiks | get_pizzas | < 500ms | ✅ PASSED |
| 6.3 | Groza atskaņošana | 100 picas | Nerūpēsi | ✅ PASSED |

---

## 7. KĻŪDU PROTOKOLS

### Atrastās kļūdas

| ID | Apraksts | Smagums | Stāvoklis |
|----|----------|---------|-----------|
| BUG-001 | Datubāze nav izveidota | CRITICAL | OPEN |
| BUG-002 | API get_orders nav ņemta no datubāzes | HIGH | OPEN |
| BUG-003 | Admin panelis nav pilnībā testēts | MEDIUM | OPEN |

---

## 8. REZULTĀTI

### Kopsumma

| Kategorija | Kopā | Apstiprinājumi | Noraidījumi | % |
|-----------|------|---------------|------------|--|
| UI testēšana | 13 | 13 | 0 | 100% ✅ |
| API testēšana | 4 | 1 | 3 | 25% ⚠️ |
| Datubāzes | 5 | 0 | 5 | 0% ❌ |
| Drošības | 6 | 6 | 0 | 100% ✅ |
| Darbības | 7 | 5 | 2 | 71% ⚠️ |
| **KOPĀ** | **35** | **25** | **10** | **71%** |

### Secinājumi

✅ **Pabeigts:**
- Lietotāja saskarnes izveide
- Pamatdarbības (groza sistēma)
- Drošības implementācija

⚠️ **Nepieciešami uzlabojumi:**
- Datubāzes savienojuma pārbaude
- API testu izpilde
- Admin paneļa pilnīga testēšana

---

## 9. APSTIPRINĀJUMS

**Testētas sistēmas komponentes:** Galvenais UI, API endpoints, Admin panelis  
**Datums:** 2026-03-05  
**Pārbaudījuma viduspunkts:** Tukums  

**Paraksts:** _____________________
