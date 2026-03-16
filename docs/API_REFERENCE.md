# 🍕 O! PICA - API DOKUMENTĀCIJA

**Versija:** 1.0  
**Standarts:** RESTful  
**Autentifikācija:** Session-based (Admin) / Open (Public)  
**Format:** JSON

---

## 📋 SATURA SADAĻA

1. [Pamatinformācija](#pamatinformācija)
2. [Authentifikācija](#authentifikācija)
3. [Error Apstrāde](#error-apstrāde)
4. [Endpoints - Picas](#endpoints---picas)
5. [Endpoints - Pasūtījumi](#endpoints---pasūtījumi)
6. [Endpoints - Atsauksmes](#endpoints---atsauksmes)
7. [Endpoints - Atbalsts](#endpoints---atbalsts)
8. [Endpoints - Admin](#endpoints---admin)
9. [Rate Limiting](#rate-limiting)
10. [Kodi un Statusi](#kodi-un-statusi)

---

## PAMATINFORMĀCIJA

### Base URL
```
http://localhost/opica/
```

### Content-Type
Visi pieprasījumi un atbildes ir `application/json`

```bash
Content-Type: application/json; charset=utf-8
```

### CORS Headers
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type
```

---

## AUTHENTIFIKĀCIJA

### Session-based (Admin)
Admin panelis izmanto sesijas:
```php
POST /admin.php
{
    "password": "parole123"
}
```

### API Token (Future)
Turpmāk varētu ieviest:
```
Authorization: Bearer YOUR_API_TOKEN
```

---

## ERROR APSTRĀDE

### Standard Error Response
```json
{
    "success": false,
    "message": "Kļūda apraksts",
    "error": "error_code"
}
```

### Validācijas Kļūdas (400)
```json
{
    "success": false,
    "message": "Validācijas kļūda",
    "errors": {
        "field_name": "Lauka apraksts kļūda"
    }
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Internal Server Error",
    "error": "database_error"
}
```

---

## ENDPOINTS - PICAS

### 1. Iegūt Visas Picas
```
GET /api_mysql.php?action=get_pizzas
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Picas iegūtas veiksmīgi",
    "data": {
        "pizzas": [
            {
                "pizza_id": 1,
                "name": "Margarita",
                "description": "Klāsiskā pica ar tomātiem un sieru",
                "price": 8.50,
                "image_filename": "margarita.jpg",
                "allergens": "Piensarmatūra, Glūtens",
                "category": "pizza",
                "is_active": true
            }
        ]
    }
}
```

**Piemērs:**
```bash
curl http://localhost/opica/api_mysql.php?action=get_pizzas
```

---

## ENDPOINTS - PASŪTĪJUMI

### 1. Izveidot Pasūtījumu
```
POST /api_mysql.php?action=create_order
```

**Request Body:**
```json
{
    "items": [
        {
            "id": 1,
            "name": "Margarita",
            "quantity": 2,
            "price": 8.50
        }
    ],
    "total": 17.00,
    "paymentMethod": "card",
    "deliveryMethod": "delivery",
    "deliveryAddress": "Vēr ielas 12",
    "deliveryCity": "Tukums"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Pasūtījums izveidots veiksmīgi",
    "data": {
        "order_id": 105,
        "total_price": 17.00,
        "payment_method": "card",
        "message": "Jūsu pica tiks piegādāta 30-45 minūtēs!"
    }
}
```

**Validācijas Kļūdas (400):**
```json
{
    "success": false,
    "message": "Nepieciešami dati: items, total, paymentMethod"
}
```

**Piemērs:**
```bash
curl -X POST http://localhost/opica/api_mysql.php?action=create_order \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"id": 1, "quantity": 1, "price": 8.50}],
    "total": 8.50,
    "paymentMethod": "card"
  }'
```

---

### 2. Iegūt Pasūtījumus
```
GET /api_mysql.php?action=get_orders
```

**Query Parameters:**
- `limit` (optional): Rezultātu skaits (default: 50)
- `offset` (optional): Sākuma pozīcija (default: 0)

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Pasūtījumi iegūti",
    "data": {
        "orders": [
            {
                "order_id": 105,
                "user_id": null,
                "total_price": 17.00,
                "status": "pending",
                "payment_method_id": 1,
                "delivery_method_id": 1,
                "order_date": "2026-03-05 14:30:00"
            }
        ]
    }
}
```

**Piemērs:**
```bash
curl "http://localhost/opica/api_mysql.php?action=get_orders&limit=10"
```

---

### 3. Atjaunināt Pasūtījuma Statusu
```
POST /api_mysql.php?action=update_order_status
```

**Request Body:**
```json
{
    "order_id": 105,
    "status": "preparing"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Pasūtījuma statuss atjaunināts"
}
```

**Statusa Opcijas:**
- `pending` - Gaidīts
- `confirmed` - Apstiprinājums
- `preparing` - Pagatavošana
- `on_way` - Ceļā
- `delivered` - Piegādāts
- `cancelled` - Atcelts

---

### 4. Atzīmēt kā Piegādāts
```
POST /api_mysql.php?action=mark_delivered
```

**Request Body:**
```json
{
    "order_id": 105
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Pasūtījums atzīmēts kā piegādāts"
}
```

---

### 5. Dzēst Pasūtījumu
```
DELETE /api_mysql.php?action=delete_order
```

**Request Body:**
```json
{
    "order_id": 105
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Pasūtījums dzēsts"
}
```

---

## ENDPOINTS - ATSAUKSMES

### 1. Pievienot Atsauksmi
```
POST /api_mysql.php?action=create_review
```

**Request Body:**
```json
{
    "pizza_id": 1,
    "rating": 5,
    "comment": "Ļoti garšīga pica!",
    "user_id": null
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Atsauksme pievienota",
    "data": {
        "review_id": 42
    }
}
```

---

## ENDPOINTS - ATBALSTS

### 1. Izveidot Atbalsta Pieprasījumu
```
POST /api_mysql.php?action=create_support_request
```

**Request Body:**
```json
{
    "name": "Jānis Bērziņš",
    "email": "janis@example.lv",
    "message": "Manam pasūtījumam trūka pica"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Atbalsta pieprasījums nosūtīts",
    "data": {
        "support_id": 15
    }
}
```

---

## ENDPOINTS - ADMIN

### Admin Dashboard Statistika

**Pieejams tikai autentificētiem administratoriem!**

#### 1. Iegūt Statistiku
```
GET /api_mysql.php?action=get_statistics
```

**Query Parameters:**
- `period` (optional): 'today', 'week', 'month', 'year' (default: 'today')

**Response:**
```json
{
    "success": true,
    "data": {
        "order_count": 15,
        "total_revenue": 127.50,
        "average_order": 8.50,
        "top_pizzas": [
            {
                "pizza_id": 1,
                "name": "Margarita",
                "order_count": 45,
                "total_quantity": 52,
                "revenue": 390.00
            }
        ]
    }
}
```

#### 2. Iegūt Admin Informāciju
```
GET /admin.php
```

Parāda admin paneļa info (session rekvēts)

---

## ENDPOINTS - EKSPORTS

### 1. Eksportēt Pasūtījumus (CSV)
```
GET /api_mysql.php?action=export_orders
```

**Response:** CSV faila lejupielāde
```
Pasūtījuma ID;Kopējā Cena;Statuss;Maksāšanas Metode...
105;17.50;Gaidīts;Maksāšana ar karti...
```

### 2. Eksportēt Statistiku (CSV)
```
GET /api_mysql.php?action=export_statistics&period=month
```

---

## RATE LIMITING

### Ierobežojumi
- **Vispārējie pieprasījumi:** 100 pieprasījumi minūtē
- **Admin pieprasījumi:** 200 pieprasījumi minūtē
- **Login mēģinājumi:** 5 mēģinājumi / 15 minūtes

### 429 Too Many Requests
```json
{
    "success": false,
    "message": "Pārāk daudz pieprasījumu. Mēģiniet vēlāk."
}
```

---

## KODI UN STATUSI

### HTTP Status Kodi
| Kods | Apraksts |
|------|----------|
| 200 | OK - Veiksmīgs pieprasījums |
| 201 | Created - Resurss izveidots |
| 400 | Bad Request - Nepareizi dati |
| 401 | Unauthorized - Nav pierakstīts |
| 403 | Forbidden - Nav piekļuves |
| 404 | Not Found - Resurss nav atrasts |
| 429 | Too Many Requests - Rate limit |
| 500 | Server Error - Servera kļūda |

### Pasūtījuma Statusi
| Statuss | Apraksts |
|---------|----------|
| pending | Pasūtījums saņemts, gaidā apstiprinājumu |
| confirmed | Pasūtījums apstiprinājums |
| preparing | Pica pagatavošanā |
| on_way | Pica kosmonauta ceļā uz jums |
| delivered | Pica piegādāta |
| cancelled | Pasūtījums atcelts |

---

## DROŠĪBAS IETEIKUMI

### HTTPS
Ražošanā vienmēr izmantojiet **HTTPS**, nevis HTTP!

### CSRF Aizsardzība
Visi POST pieprasījumi pieprasa CSRF token:
```html
<input type="hidden" name="csrf_token" value="<?php echo Security::getCSRFToken(); ?>">
```

### Input Validācija
Visi input dati tiek validēti:
```php
Validator::required('field', $data);
Validator::email('email', $data);
Validator::numeric('price', $data);
```

### SQL Injection Aizsardzība
Visi SQL pieprasījumi izmanto prepared statements:
```php
$stmt = $conn->prepare("SELECT * FROM pizzas WHERE pizza_id = ?");
$stmt->bind_param('i', $pizza_id);
```

---

## PIEMĒRI

### cURL Piemēri

**Iegūt picas:**
```bash
curl -X GET "http://localhost/opica/api_mysql.php?action=get_pizzas" \
  -H "Content-Type: application/json"
```

**Izveidot pasūtījumu:**
```bash
curl -X POST "http://localhost/opica/api_mysql.php?action=create_order" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"id": 1, "name": "Margarita", "quantity": 2, "price": 8.50}
    ],
    "total": 17.00,
    "paymentMethod": "card"
  }'
```

### JavaScript Piemēri

**Iegūt picas:**
```javascript
fetch('/opica/api_mysql.php?action=get_pizzas')
    .then(response => response.json())
    .then(data => console.log(data));
```

**Izveidot pasūtījumu:**
```javascript
const order = {
    items: [{id: 1, quantity: 2, price: 8.50}],
    total: 17.00,
    paymentMethod: 'card'
};

fetch('/opica/api_mysql.php?action=create_order', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(order)
})
.then(response => response.json())
.then(data => console.log(data));
```

---

## VERSIJAS VĒSTURE

| Versija | Datums | Izmaiņas |
|---------|--------|----------|
| 1.0 | 2026-03-05 | Sākotnējā versija |

---

**Jautājumi vai problēmas?**  
Skatiet `DOKUMENTACIJA.md` vai kontaktējiet atbalstu

