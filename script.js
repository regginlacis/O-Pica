// ============================================
// TĒMAS PĀRSLĒGŠANA - 2 TĒMAS
// Light (gaišā), Dark (tumšā)
// ============================================

function setTheme(theme) {
    const body = document.body;
    body.classList.remove('light-theme', 'dark-theme');
    body.classList.add(theme + '-theme');
    localStorage.setItem('theme', theme);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
        initHeaderBackground();
    });
} else {
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    initHeaderBackground();
}

let currentPizzaIndex = 0;

function initHeaderBackground() {
    const header = document.querySelector('header');
    if (!header) return;
    
    // Sāk ar pirmo picu
    updateHeaderBackgroundImage();
    
    // Maiņ picu katrās 5 sekundēs
    setInterval(() => {
        currentPizzaIndex = (currentPizzaIndex + 1) % pizzaMenu.length;
        updateHeaderBackgroundImage();
    }, 5000);
}

function updateHeaderBackgroundImage() {
    const headerBefore = document.querySelector('header::before');
    const header = document.querySelector('header');
    if (!header) return;
    
    const pizza = pizzaMenu[currentPizzaIndex];
    const imageUrl = `opica bildes/${pizza.image}`;
    
    // Dinamisks CSS stils
    const style = document.createElement('style');
    style.textContent = `
        header::before {
            background-image: url('${imageUrl}') !important;
        }
    `;
    
    // Noņem veco stilu ja eksistē
    const oldStyle = document.querySelector('style[data-pizza-bg]');
    if (oldStyle) oldStyle.remove();
    
    style.setAttribute('data-pizza-bg', 'true');
    document.head.appendChild(style);
}




// Pizza menu data
const pizzaMenu = [
    { id: 1, name: "Nothing special", image: "nothingspecial.jpg", description: "Siers, Salami, Tomātu mērce", price: 7.80, allergens: "Glutēns, Piena produkti" },
    { id: 2, name: "Salami", image: "salami.jpg", description: "Siers, Salami, Tomātu mērce", price: 8.00, allergens: "Glutēns, Piena produkti" },
    { id: 3, name: "Margarita", image: "margarita.jpg", description: "Siers, Mozarella, Tomātu mērce, Zaļumi", price: 8.30, allergens: "Glutēns, Piena produkti" },
    { id: 4, name: "Slaidā līnija", image: "slaida_linija.jpg", description: "Siers, Ananāss, Tomātu mērce, Paprika, Olīvas", price: 8.80, allergens: "Glutēns, Piena produkti" },
    { id: 5, name: "Sēņu", image: "senu.jpg", description: "Siers, Sāls, Tomātu mērce, Konservētas sēnes", price: 8.80, allergens: "Glutēns, Piena produkti" },
    { id: 6, name: "Viesu", image: "viesu.jpg", description: "Siers, Salami, Tomātu mērce, Konservēti gurķi", price: 8.60, allergens: "Glutēns, Piena produkti" },
    { id: 7, name: "Jauniešu", image: "jauniesu.jpg", description: "Siers, Vistas gaļa, Tomātu mērce, Ananāss", price: 9.60, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 8, name: "Līgo", image: "ligo.jpg", description: "Siers, Tomāts, Tomātu mērce, Ķimeņi, Brīņi", price: 9.50, allergens: "Glutēns, Piena produkti" },
    { id: 9, name: "Rudens", image: "rudens.jpg", description: "Siers, Salami, Tomātu mērce, Ābols", price: 9.50, allergens: "Glutēns, Piena produkti" },
    { id: 10, name: "Raibā", image: "raiba.jpg", description: "Siers, Vistas gaļa, Tomātu mērce, Paprika, Olīvas", price: 9.50, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 11, name: "Seņču", image: "senchu.jpg", description: "Siers, Žāvēta gaļa, Tomātu mērce, Sēnes, Puravs", price: 9.50, allergens: "Glutēns, Piena produkti" },
    { id: 12, name: "Tunča", image: "tunca.jpg", description: "Siers, Tuncis, Tomātu mērce, Citrons, Olīvas", price: 9.20, allergens: "Glutēns, Piena produkti, Zivis" },
    { id: 13, name: "Kikerigū", image: "kikerigu.jpg", description: "Siers, Vistas gaļa, Tomātu mērce, Tomāts, Paprika", price: 9.50, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 14, name: "Šķiņķa", image: "skinka.jpg", description: "Siers, Šķiņķis, Tomātu mērce, Sīpoli", price: 9.00, allergens: "Glutēns, Piena produkti" },
    { id: 15, name: "Lauku", image: "lauku.jpg", description: "Siers, Šķiņķis, Tomātu mērce, Sīpols, Konservēti gurķi, Kūpināta gaļa", price: 10.00, allergens: "Glutēns, Piena produkti" },
    { id: 16, name: "Pipariņš", image: "piparins.jpg", description: "Siers, Čilli pipari, Tomātu mērce, Sīpoli, Šķiņķis", price: 9.50, allergens: "Glutēns, Piena produkti" },
    { id: 17, name: "Neptūns", image: "neptuns.jpg", description: "Siers, Lasis, Tomātu mērce, Svaigs gurķis", price: 10.00, allergens: "Glutēns, Piena produkti, Zivis" },
    { id: 18, name: "Deserta", image: "deserta.jpg", description: "Siers, Ananāss, Tomātu mērce, Ābols, Rieksti, Saldais krējums", price: 10.00, allergens: "Glutēns, Piena produkti, Rieksti" },
    { id: 19, name: "Jauntukums", image: "jauntukums.jpg", description: "Siers, Šķiņķis, Tomātu mērce, Sīpols, Sēnes", price: 10.00, allergens: "Glutēns, Piena produkti" },
    { id: 20, name: "Ugunskurs", image: "ugunskurs.jpg", description: "Siers, Vistas gaļa, Tomātu mērce, Šķiņķis, Paprika, Ķiploks", price: 10.50, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 21, name: "Eksotika", image: "eksotika.jpg", description: "Siers, Žāvētas plūmes, Tomātu mērce, Vistas gaļa, Ananāss", price: 10.00, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 22, name: "Gardēža", image: "gardeza.jpg", description: "Siers, Salami, Tomātu mērce, Ābols, Šķiņķis, Ananāss", price: 10.80, allergens: "Glutēns, Piena produkti" },
    { id: 23, name: "Velo", image: "velo.jpg", description: "Siers, Tomāts, Tomātu mērce, Salami, Šķiņķis", price: 10.80, allergens: "Glutēns, Piena produkti" },
    { id: 24, name: "Viss kas", image: "Viss_kas.jpg", description: "Siers, Salami, Šķiņķis, Vistas gaļa, Šampinjeri, Tomāts, Gurķis, Asie pipari, Sīpoli", price: 11.00, allergens: "Glutēns, Piena produkti, Vistas gaļa" },
    { id: 25, name: "Saldais rausis", image: "saldais_rausis.jpg", description: "Ābols, Sviests, Saldais krējums, Kanēlis, Cukurs", price: 7.00, allergens: "Glutēns, Piena produkti" }
];

let cart = [];
let orders = [];

function goAdmin() {
    window.location.href = 'admin.php';
}

function init() {
    const stored = safeGetItem('userOrders');
    if (stored) {
        try {
            const storedOrders = JSON.parse(stored);
            const validOrders = storedOrders.filter(o => o && o.paymentMethod);
            if (validOrders.length < storedOrders.length) {
                if (validOrders.length > 0) {
                    safeSetItem('userOrders', JSON.stringify(validOrders));
                } else {
                    safeRemoveItem('userOrders');
                }
            }
        } catch (e) {
            safeRemoveItem('userOrders');
        }
    }
    
    displayPizzas();
    updateCartDisplay();
    loadOrdersFromStorage();
}

function displayPizzas() {
    const pizzaList = document.getElementById("pizza-list");
    pizzaList.innerHTML = "";
    pizzaMenu.forEach((pizza, index) => {
        const pizzaCard = document.createElement("div");
        pizzaCard.className = "pizza-card";
        
        // Ģenerē canvas placeholder
        const canvas = document.createElement('canvas');
        canvas.width = 200;
        canvas.height = 200;
        const ctx = canvas.getContext('2d');
        
        // Gadījuma krāsa katrai picai
        const colors = ['#FF6B6B', '#FF8E72', '#FFA07A', '#FFB6C1', '#FFC0CB', '#FFD700', '#FFE4B5'];
        ctx.fillStyle = colors[index % colors.length];
        ctx.fillRect(0, 0, 200, 200);
        
        ctx.fillStyle = 'white';
        ctx.font = 'bold 16px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('🍕', 100, 80);
        ctx.font = '12px Arial';
        ctx.fillText(pizza.name, 100, 130);
        
        const imgSrc = canvas.toDataURL('image/png');
        
        pizzaCard.innerHTML = `
            <img class="pizza-image" src="opica bildes/${pizza.image}" alt="${pizza.name}" onerror="this.src='${imgSrc}'">
            <div class="pizza-info">
                <div class="pizza-name">${pizza.name}</div>
                <div class="pizza-description">${pizza.description}</div>
                <div class="pizza-allergens">[WARNING] ${pizza.allergens}</div>
                <div class="pizza-footer">
                    <div class="pizza-price">€${pizza.price.toFixed(2)}</div>
                    <button class="btn-add" onclick="addToCart(${pizza.id})">Pievienot</button>
                </div>
            </div>
        `;
        pizzaList.appendChild(pizzaCard);
    });
}

function addToCart(pizzaId) {
    const pizza = pizzaMenu.find(p => p.id === pizzaId);
    const existingItem = cart.find(item => item.id === pizzaId);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...pizza, quantity: 1 });
    }
    updateCartDisplay();
    showNotification(pizza.name + " pievienota grozam!");
}

function removeFromCart(pizzaId) {
    cart = cart.filter(item => item.id !== pizzaId);
    updateCartDisplay();
}

function increaseQuantity(pizzaId) {
    const item = cart.find(item => item.id === pizzaId);
    if (item) {
        item.quantity++;
        updateCartDisplay();
    }
}

function decreaseQuantity(pizzaId) {
    const item = cart.find(item => item.id === pizzaId);
    if (item && item.quantity > 1) {
        item.quantity--;
        updateCartDisplay();
    }
}

function updateCartDisplay() {
    const cartCount = document.getElementById("cart-count");
    const cartItems = document.getElementById("cart-items");
    const totalPrice = document.getElementById("total-price");
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="empty-cart">Jūsu grozs ir tukšs. Sāciet iepirkties! 🍕</div>';
        totalPrice.textContent = "0.00";
    } else {
        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-details">
                    <div class="cart-item-name">🍕 ${item.name}</div>
                    <div class="cart-item-price">€${item.price.toFixed(2)} x ${item.quantity}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="decreaseQuantity(${item.id})">-</button>
                    <span>${item.quantity}</span>
                    <button class="qty-btn" onclick="increaseQuantity(${item.id})">+</button>
                    <button class="btn-remove" onclick="removeFromCart(${item.id})">Noņemt</button>
                </div>
            </div>
        `).join("");
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        totalPrice.textContent = total.toFixed(2);
    }
}

function showAbout() {
    document.getElementById("about-section").classList.add("active");
    document.getElementById("menu-section").classList.remove("active");
    document.getElementById("cart-section").classList.remove("active");
    document.getElementById("orders-section").classList.remove("active");
}

function showMenu() {
    document.getElementById("about-section").classList.remove("active");
    document.getElementById("menu-section").classList.add("active");
    document.getElementById("cart-section").classList.remove("active");
    document.getElementById("orders-section").classList.remove("active");
}

function showCart() {
    document.getElementById("about-section").classList.remove("active");
    document.getElementById("menu-section").classList.remove("active");
    document.getElementById("cart-section").classList.add("active");
    document.getElementById("orders-section").classList.remove("active");
}

function showOrders() {
    document.getElementById("about-section").classList.remove("active");
    document.getElementById("menu-section").classList.remove("active");
    document.getElementById("cart-section").classList.remove("active");
    document.getElementById("orders-section").classList.add("active");
    loadOrdersFromStorage(); // Ielādē jaunākos datus pirms parādīšanas
    displayUserOrders();
}

function checkout() {
    if (cart.length === 0) {
        alert("Jūsu grozs ir tukšs!");
        return;
    }
    
    // Parāda maksāšanas metodes modāli
    showPaymentModal();
}

function proceedWithCheckout() {
    if (!selectedPaymentMethod) {
        alert("Lūdzu, izvēlieties maksāšanas metodi!");
        return;
    }
    
    if (cart.length === 0) {
        alert("Jūsu grozs ir tukšs!");
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const orderId = generateOrderId();
    
    // Saglabā pasūtījumu localStorage PIRMS API izsaukuma
    const newOrder = {
        id: orderId,
        items: JSON.parse(JSON.stringify(cart)), // Deep copy
        total: total,
        paymentMethod: selectedPaymentMethod, // Pievieno maksāšanas metodi
        timestamp: new Date().toLocaleString('lv-LV'),
        status: 'pending'
    };
    console.log('💾 Saglabāts pasūtījums localStorage:', newOrder);
    console.log('💿 PaymentMethod:', selectedPaymentMethod, 'Tips:', typeof selectedPaymentMethod);
    saveOrderToStorage(newOrder);
    
    // Sagatavo datus API izsaukumam
    const orderData = {
        orderId: orderId,
        items: cart,
        total: total,
        paymentMethod: selectedPaymentMethod
    };
    
    // Izsauc API, lai izveidotu pasūtījumu datubāzē
    fetch('api_mysql.php?action=create_order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('📡 API atbilde:', data);
        if (data.success) {
            const paymentText = selectedPaymentMethod === 'card' 
                ? 'ar karti online' 
                : 'skaidra nauda uz vietas';
            alert(`Pasūtījums iesniegts! ID: ${orderId}\nKopā: €${total.toFixed(2)}\nMaksāšana: ${paymentText}\n\nJūsu pica tiks piegādāta 30-45 minūtēs. Paldies par pasūtījumu! 🍕`);
            
            // Iztukšo grozu un atiestatā maksāšanas metodi
            cart = [];
            selectedPaymentMethod = null;
            updateCartDisplay();
            loadOrdersFromStorage();
            closePaymentModal();
            showMenu();
        } else {
            alert('Kļūda pasūtījuma veidošanā: ' + data.message);
        }
    })
    .catch(error => {
        console.error('❌ Kļūda:', error);
        alert('Pasūtījums ir saglabāts lokāli, bet servera savienojums nav pieejams.');
    });
}

// Attēlo lietotāja pasūtījumus
function displayUserOrders() {
    const ordersList = document.getElementById("orders-list");
    
    if (!orders || orders.length === 0) {
        ordersList.innerHTML = '<div class="empty-cart">Jums vēl nav pasūtījumu! 📦</div>';
        return;
    }
    
    console.log('📋 Rādījām pasūtījumus:', orders);
    
    let html = '';
    
    for (let i = 0; i < orders.length; i++) {
        const order = orders[i];
        if (!order) continue;
        
        const items = order.items || [];
        const status = order.status || 'pending';
        const statusText = status === 'pending' ? '[WAITING] Gaida' : status === 'delivered' ? '[OK] Piegādāts' : '[INFO] ' + status;
        const statusClass = status === 'pending' ? 'status-pending' : 'status-delivered';
        
        let itemsHtml = '';
        for (let j = 0; j < items.length; j++) {
            const item = items[j];
            if (!item) continue;
            const itemPrice = parseFloat(item.price) || 0;
            const qty = parseInt(item.quantity) || 1;
            const itemTotal = itemPrice * qty;
            const itemTotalStr = itemTotal.toString().split('.')[0] + '.' + (itemTotal.toString().split('.')[1] || '00').substring(0, 2);
            itemsHtml += '<div class="order-item">🍕 ' + item.name + ' x' + qty + ' - €' + itemTotalStr + '</div>';
        }
        
        let totalPrice = 0;
        if (typeof order.total === 'number') {
            totalPrice = order.total;
        } else if (typeof order.total === 'string') {
            totalPrice = parseFloat(order.total) || 0;
        } else if (typeof order.total_price === 'number') {
            totalPrice = order.total_price;
        } else if (typeof order.total_price === 'string') {
            totalPrice = parseFloat(order.total_price) || 0;
        }
        
        const totalStr = totalPrice.toString().split('.')[0] + '.' + (totalPrice.toString().split('.')[1] || '00').substring(0, 2);
        
        // Pārbaudīt vai var atcelt (< 10 minūtes)
        let canCancel = false;
        try {
            const orderTime = new Date(order.timestamp);
            const nowTime = new Date();
            const minutesDiff = (nowTime - orderTime) / (1000 * 60);
            canCancel = minutesDiff < 10 && status === 'pending';
        } catch (e) {
            canCancel = false;
        }
        
        // Maksāšanas metodes teksts
        const paymentMethod = order.paymentMethod || 'cash';
        const paymentIcon = paymentMethod === 'card' ? '💳' : '💵';
        const paymentText = paymentMethod === 'card' ? 'Maksāšana ar karti' : 'Maksāšana uz vietas (skaidra nauda)';
        
        console.log('Pasūtījums #' + (order.id || '?') + ' - paymentMethod:', paymentMethod);
        
        html += '<div class="admin-order-card">';
        html += '<div class="admin-order-header">';
        html += '<span class="order-id">Pasūtijums #' + (order.id || 'N/A') + '</span>';
        html += '<span class="order-time">' + (order.timestamp || 'N/A') + '</span>';
        html += '<span class="order-status ' + statusClass + '">' + statusText + '</span>';
        html += '</div>';
        html += '<div class="order-items-list">';
        html += itemsHtml || '<div class="order-item">Nav informācijas</div>';
        html += '</div>';
        html += '<div class="order-total">Kopā: €' + totalStr + '</div>';
        html += '<div class="order-payment">' + paymentIcon + ' ' + paymentText + '</div>';
        if (canCancel) {
            html += '<button class="btn-cancel" onclick="cancelOrder(\'' + (order.id || '') + '\')">❌ Atcelt pasūtījumu</button>';
        }
        html += '</div>';
    }
    
    ordersList.innerHTML = html;
}

// Atceļ pasūtījumu (tikai pirmās 10 minūtes)
function cancelOrder(orderId) {
    if (!confirm('Vai vēlaties atcelt šo pasūtījumu?')) {
        return;
    }
    
    // Noņem no storage
    let storedOrders = JSON.parse(safeGetItem('userOrders')) || [];
    storedOrders = storedOrders.filter(order => order.id !== orderId);
    safeSetItem('userOrders', JSON.stringify(storedOrders));
    
    // Atjaunina pasūtījumu sarakstu
    loadOrdersFromStorage();
    displayUserOrders();
    
    showNotification('Pasūtījums atcelts! ❌');
}

// Atzīmē pasūtījumu kā piegādātu caur API
function markAsDelivered(orderId) {
    fetch('api_mysql.php?action=mark_delivered', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Noņem pasūtījumu no storage
            let storedOrders = JSON.parse(safeGetItem('userOrders')) || [];
            storedOrders = storedOrders.filter(order => order.id !== orderId);
            safeSetItem('userOrders', JSON.stringify(storedOrders));
            
            // Atjaunina pasūtījumu sarakstu
            loadOrdersFromStorage();
            
            showNotification("Pasūtijums #" + orderId + " piegadats! [OK]");
        } else {
            alert('Kļūda: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kļūda:', error);
        alert('Kļūda piegādes atzīmēšanā');
    });
}

// Notīra visu datubāzi caur API
function clearAllOrders() {
    if (confirm('Vai esat pārliecināts, ka vēlaties notīrīt VISUS pasūtījumus? Šo darbību nevar atsaukt!')) {
        // Dzēst no storage
        safeRemoveItem('userOrders');
        orders = [];
        showNotification('Visi pasūtījumi dzēsti.');
        displayUserOrders();
    }
}

// Izveido unikālu pasūtījuma ID
function generateOrderId() {
    return 'PIC-' + Math.random().toString(36).substr(2, 9).toUpperCase();
}

// Saglabā pasūtījumu localStorage
function saveOrderToStorage(order) {
    try {
        let storedOrders = [];
        const existing = safeGetItem('userOrders');
        console.log('💾 Esošais storage sākumā:', existing);

        
        if (existing) {
            try {
                storedOrders = JSON.parse(existing);
            } catch (e) {
                console.error('Kļūda parsējot storage:', e);
                storedOrders = [];
            }
        }
        
        console.log('📋 Pirms pievienošanas:', storedOrders);
        storedOrders.unshift(order);
        console.log('📋 Pēc pievienošanas:', storedOrders);
        
        const jsonString = JSON.stringify(storedOrders);
        console.log('📝 Rakstīšanas vērtība:', jsonString);
        safeSetItem('userOrders', jsonString);
        
        // Pārbaudīt, vai to tiešām var nolasīt atpakaļ
        const readBack = safeGetItem('userOrders');
        console.log('✅ Nolasīts atpakaļ no storage:', readBack);
        console.log('✔️ Saglabāts storage!');
    } catch (error) {
        console.error('KRITISKA KĻŪDA saglabājot pasūtījumu:', error);
        alert('Nevarēja saglabāt pasūtījumu lokāli!');
    }
}

// Ielādē pasūtījumus no localStorage
function loadOrdersFromStorage() {
    try {
        const storedRaw = safeGetItem('userOrders');
        console.log('💾 Raw storage value:', storedRaw);
        
        const storedOrders = storedRaw ? JSON.parse(storedRaw) : [];
        console.log('📂 Parsed orders:', storedOrders);
        
        orders = Array.isArray(storedOrders) ? storedOrders : [];
        console.log('📊 Orders masīvs:', orders);
        console.log('📊 Orders skaits:', orders.length);
        
        // Sinhronizē storage ar datubāzi (noņem dzēstus pasūtījumus) tikai ja ir nodrošināti pasūtījumi
        if (orders.length > 0) {
            syncOrdersWithDatabase();
        }
    } catch (error) {
        console.error('❌ Kļūda ielādējot pasūtījumus:', error);
        orders = [];
    }
}

// Sinhronizē localStorage ar datubāzes stāvokli (tikai noņem dzēstus)
function syncOrdersWithDatabase() {
    if (orders.length === 0) return;
    
    // Filtrē tikai pasūtījumus, kas izskatās kā no datubāzes (skaitļu ID, nevis string)
    const dbOrders = orders.filter(o => typeof o.id === 'number');
    
    if (dbOrders.length === 0) {
        console.log('✋ Nav ko sinhronizēt - visi pasūtījumi ir jauni (localStorage)');
        return;
    }
    
    const orderIds = dbOrders.map(o => o.id);
    
    fetch('api/api_mysql.php?action=get_orders')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.orders) {
                const dbOrderIds = data.data.orders.map(o => o.order_id);
                
                // Atrod pasūtījumus, kas ir storage, bet nē datubāzē (admin tos dzēsa)
                const deletedOrderIds = orderIds.filter(id => !dbOrderIds.includes(id));
                
                if (deletedOrderIds.length > 0) {
                    console.log('🗑️ Atrod dzēstus pasūtījumus:', deletedOrderIds);
                    
                    // Noņem dzēstus pasūtījumus no storage
                    let storedOrders = JSON.parse(safeGetItem('userOrders')) || [];
                    storedOrders = storedOrders.filter(order => !deletedOrderIds.includes(order.id));
                    safeSetItem('userOrders', JSON.stringify(storedOrders));
                    
                    // Atjaunina orders masīvu
                    orders = storedOrders;
                    console.log('[OK] Storage sinhronizēts ar datubāzi');
                }
            }
        })
        .catch(error => console.log('Sinhronizācijas kļūda (normāli, ja datubāze nav pieejama):', error));
}

// Ielādē pasūtījumus no API
function loadOrders() {
    fetch('api/api_mysql.php?action=get_orders')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.orders) {
                orders = data.data.orders.map(order => ({
                    id: order.order_id,
                    total: order.total_price,
                    status: order.status,
                    timestamp: order.order_date,
                    paymentMethod: order.payment_method_id === 1 ? 'card' : 'cash',
                    items: order.items || []
                }));
            }
        })
        .catch(error => console.error('Kļūda ielādējot pasūtījumus:', error));
}

// Rāda paziņojumu
function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #ff4757 0%, #ff6b7a 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 50px;
        box-shadow: 0 8px 25px rgba(255, 71, 87, 0.3);
        font-weight: 600;
        z-index: 1000;
        animation: slideIn 0.4s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.4s ease';
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// ============================================
// AI SUPPORT CHAT - ADVANCED LOGIC
// ============================================

// Chat state management
let chatState = {
    waitingForOrderId: false
};

// AI atbildes bāze
const aiResponses = {
    'cena': 'Mūsu picas maksā no €7 līdz €11 atkarībā no izmeľa un toppings-iem. Skatiet izvēlni detalizētiem cenas informācijai! 🍕',
    'piegade': 'Piegāde noris 30-45 minūtes. Bezmaksas piegāde pasūtījumiem ar 3 picām! 🚚',
    'alergeni': 'Mums ir daudz alergēno produktu - Glutēns, Piena produkti, Rieksti, Zivis un citi. Lūdzam informēt mūsu personālu! [WARNING]',
    'kontakts': 'Mūsu telefons: 26318083, Tukums. Pieņemam zvanājumus Otr-Sestd 17:00-22:00 [PHONE]',
    'maksasha': 'Mēs pieņemam skaidru naudu, bankas kartes un online maksājumus! [CARD]',
    'minimums': 'Nav minimālā pasūtījuma! Savāksana pie Jauntukuma Mego - bez maksu. 📦',
    'vegetariesu': 'Mums ir vairākas vegetāriešu picas! [SALAD]',
    'picas': 'Mums ir 25 dažādas picas - klasiskas, gourmet, vegetāriešu un mūsu speciālās picas! 🍕',
    'darba_laiks': 'Darba laiks: Otrdiena-Sestdiena 17:00-22:00. Pirmdiena un Svētdiena slēgts. ⏰',
    'popular': 'Mūs populārākās picas ir "Margarita", "Havaju", un "4 sīri"! Iespējams, viena no tām kļūs tavs jaunais favorīts! 🍕⭐',
    'ieteiks': 'Ieteicu spēlēt "Margarita" ar klasisko "4 sīri" kombināciju - perfekta vērtība un garšas kombinācija! 😋',
    'miljak': 'Katra pica ir speciāla savā veidā! Populārākās ir "Margarita", "4 sīri" un "Havaju". Kura vairāk patīk tev? 🍕'
};

// Jautājuma kategoriju atpazīšana ar konteksta analīzi
function getQuestionCategory(message) {
    const msg = message.toLowerCase().trim();
    
    // 1. PIEGĀDES JAUTĀJUMI - prioritāte augsta (piegāde + maksa = piegādes maksa, nevis cena)
    if ((msg.includes('piegāde') || msg.includes('sūtīt') || msg.includes('nosūtīt') || msg.includes('piegādāt')) &&
        !msg.includes('bez piegādes')) {
        return 'piegade';
    }
    
    // 2. STATUSU/LAIKA JAUTĀJUMI - prioritāte augsta (laika/statusa jautājumi)
    if ((msg.includes('kad') || msg.includes('cik ilgi') || msg.includes('cik laika') || 
         msg.includes('cik minūšu') || msg.includes('status') || msg.includes('gatava') || 
         msg.includes('saņemu') || msg.includes('atnāk') || msg.includes('atnāks') || 
         msg.includes('ierodas') || msg.includes('pic-')) && 
        !msg.includes('cena') && !msg.includes('cik maksā')) {
        return 'delivery';
    }
    
    // 3. CENA JAUTĀJUMI (bet TIKAI ja nav "piegāde"+"maksā" kombinācijas)
    if ((msg.includes('cena') || msg.includes('maksā') || msg.includes('cik maksā')) &&
        !msg.includes('piegāde') && !msg.includes('sūtīt')) {
        return 'cena';
    }
    
    // 4. KONTAKTA JAUTĀJUMI
    if (msg.includes('kontakt') || msg.includes('telefon') || msg.includes('tālruni') || 
        msg.includes('numur') || msg.includes('sazināties') || msg.includes('kur atrast')) {
        return 'kontakts';
    }
    
    // 5. LAIKA/DARBA STUNDAS JAUTĀJUMI
    if ((msg.includes('cik ieslēgt') || msg.includes('darba laiks') || msg.includes('atverts') || 
         msg.includes('atvērts') || msg.includes('slēgts') || msg.includes('darba stund')) &&
        !msg.includes('piegāde')) {
        return 'darba_laiks';
    }
    
    // 6. ALERGĒNI
    if (msg.includes('alergēn') || msg.includes('glutēn') || msg.includes('piena produkti') ||
        msg.includes('rieksti') || msg.includes('zivis')) {
        return 'alergeni';
    }
    
    // 7. VEGETĀRIEŠU PICAS
    if (msg.includes('vegetāriešu') || msg.includes('bez gaļas') || msg.includes('salātu')) {
        return 'vegetariesu';
    }
    
    // 8. POPULĀRO PICU JAUTĀJUMI - ar konteksta analīzi
    if (msg.includes('miljak') || msg.includes('populār') || msg.includes('ieteik') || 
        msg.includes('labākā') || msg.includes('favorīt') || msg.includes('biežāk') ||
        msg.includes('rekomend') || msg.includes('kas ir labākais') || msg.includes('visbiežāk')) {
        return 'miljak';  // Tas nolases no orders.json
    }
    
    // 9. VISPĀRĪGI PICAS JAUTĀJUMI
    if (msg.includes('pica') || msg.includes('picas') || msg.includes('pīca') || msg.includes('pīcas')) {
        return 'picas';
    }
    
    // 10. MINIMUMS/PASŪTĪJUMA NOSACĪJUMI
    if (msg.includes('minimums') || msg.includes('minimālaj') || msg.includes('mazākais')) {
        return 'minimums';
    }
    
    // 11. MAKSĀŠANAS METODES
    if ((msg.includes('maksāt') || msg.includes('pieņemam')) && 
        !msg.includes('piegāde') && !msg.includes('cena')) {
        return 'maksasha';
    }
    
    return 'default';
}

// Meklē pasūtījumus no orders.json
async function fetchOrdersFromServer() {
    try {
        const response = await fetch('data/orders.json');
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (error) {
        console.log('Nevar piekļūt orders.json:', error);
        return [];
    }
}

// Nosaka populārāko picu pēc pasūtījumu skaita
async function getMostPopularPizza() {
    const orders = await fetchOrdersFromServer();
    
    if (!orders || orders.length === 0) {
        // Ja nav pasūtījumu, atlasa picu ar augstāko reitingu/alfabēti
        if (pizzaMenu && pizzaMenu.length > 0) {
            return pizzaMenu.sort((a, b) => a.name.localeCompare(b.name))[0];
        }
        return null;
    }
    
    // Skaita katra picas pasūtījumus
    const pizzaCount = {};
    orders.forEach(order => {
        if (order.items && Array.isArray(order.items)) {
            order.items.forEach(item => {
                pizzaCount[item.name] = (pizzaCount[item.name] || 0) + item.quantity;
            });
        }
    });
    
    // Nosaka populārāko
    let mostPopular = null;
    let maxCount = 0;
    
    for (const [name, count] of Object.entries(pizzaCount)) {
        if (count > maxCount) {
            maxCount = count;
            mostPopular = pizzaMenu.find(p => p.name === name);
        }
    }
    
    return mostPopular || (pizzaMenu && pizzaMenu.length > 0 ? pizzaMenu[0] : null);
}

// Aprēķina piegādes statusu
async function getDeliveryStatus(orderId) {
    const orders = await fetchOrdersFromServer();
    
    // Meklē pasūtījumu
    const order = orders.find(o => o.id === orderId);
    
    if (!order) {
        return {
            found: false,
            message: `[ERROR] Pasūtījums #${orderId} netika atrasts. Lūdzam pārbaudīt pasūtījuma numuru. [SEARCH]`
        };
    }
    
    // Aprēķina laiku
    const orderTime = new Date(order.timestamp);
    const currentTime = new Date();
    const minutesElapsed = Math.floor((currentTime - orderTime) / (1000 * 60));
    const estimatedDeliveryTime = 30; // Minūtes
    
    let status = '';
    let emoji = '';
    
    if (minutesElapsed < estimatedDeliveryTime) {
        // Vēl nav piegādāts
        const minutesRemaining = estimatedDeliveryTime - minutesElapsed;
        emoji = '[WAITING]';
        status = `${emoji} Jūsu pasūtījums tiek sagatavots! Aptuveni ${minutesRemaining} minūtes līdz piegādei. [DELIVERY]`;
    } else if (minutesElapsed < estimatedDeliveryTime + 10) {
        // Piegādes ceļā
        emoji = '[DELIVERY]';
        status = `${emoji} Jūsu pasūtījums ir ceļā! Kurjers drīzumā ieradīsies. 📍`;
    } else {
        // Iespējams jau piegādāts
        emoji = '[CHECK]';
        status = `${emoji} Jūsu pasūtījums būtu jau jāierodas. Ja tas tik nav saņemts, lūdzam zvanīt +371 2334 5678 📞`;
    }
    
    // Izvēlnes detaļas
    const itemsText = order.items
        .map(item => `🍕 ${item.name} (x${item.quantity})`)
        .join(', ');
    
    const detailedMessage = `${status}

📋 Pasūtījuma detaļas:
• ID: #${order.id}
• Produkti: ${itemsText}
• Kopā: €${order.total.toFixed(2)}
• Pasūtīts: ${order.timestamp}

Paldies, ka esat mūsu klient! 🍕`;
    
    return {
        found: true,
        message: detailedMessage,
        minutesRemaining: Math.max(0, estimatedDeliveryTime - minutesElapsed)
    };
}

// Apstrādā lietotāja ziņojumu
async function handleUserMessage(userMessage) {
    const msg = userMessage.toLowerCase().trim();
    
    // 0. PIRMAIS - Meklē konkrētu picu jautājumā
    const foundPizza = findPizzaInMessage(userMessage);
    if (foundPizza) {
        // Jautā par cenu?
        if (msg.includes('cena') || msg.includes('maksā') || msg.includes('cik maksā')) {
            return `🍕 *${foundPizza.name}* maksā €${foundPizza.price.toFixed(2)}\n\n📝 Sastāvdaļas: ${foundPizza.description}\n⚠️ Alergēni: ${foundPizza.allergens}\n\nVēlies to pasūtīt?`;
        }
        // Jautā par sastāvu/aprakstu?
        if (msg.includes('kas ir') || msg.includes('kāds') || msg.includes('aprakst') || msg.includes('sastāv')) {
            return `🍕 *${foundPizza.name}* (€${foundPizza.price.toFixed(2)})\n\n📝 Sastāvdaļas: ${foundPizza.description}\n⚠️ Alergēni: ${foundPizza.allergens}`;
        }
        // Jautā par alergēniem?
        if (msg.includes('alergēn') || msg.includes('glutēn') || msg.includes('piena')) {
            return `🍕 *${foundPizza.name}* alergēni:\n⚠️ ${foundPizza.allergens}`;
        }
        // Vispārīgi par picu
        return `🍕 *${foundPizza.name}* (€${foundPizza.price.toFixed(2)})\n\n📝 ${foundPizza.description}\n⚠️ Alergēni: ${foundPizza.allergens}`;
    }
    
    // Meklē pasūtījuma ID jautājumā (format: PIC-XXXXXX)
    const orderIdMatch = userMessage.match(/pic-[a-z0-9]+/i);
    
    // Ja gaidām order ID vai ID piekļauts jautājumam
    if (chatState.waitingForOrderId || orderIdMatch) {
        const orderId = orderIdMatch ? orderIdMatch[0].toUpperCase() : userMessage.trim().toUpperCase();
        
        // Validē format
        if (!orderId || orderId.length < 5) {
            return "❌ Lūdzam ievadīt derīgu pasūtījuma numuru. Piemērs: PIC-ABC123XY 📝";
        }
        
        chatState.waitingForOrderId = false;
        const status = await getDeliveryStatus(orderId);
        return status.message;
    }
    
    // Atpazīst jautājuma kategoriju
    const category = getQuestionCategory(userMessage);
    
    if (category === 'delivery') {
        // Lūdz pasūtījuma ID
        chatState.waitingForOrderId = true;
        return "🔍 Lūdzam ievadīt jūsu pasūtījuma numuru, lai pārbaudītu piegādes statusu. Piemērs: PIC-ABC123XY 📝";
    }
    
    // Picas rekomendācijas - nolasa no faktiskajiem datiem
    if (category === 'miljak' || category === 'popular') {
        const popularPizza = await getMostPopularPizza();
        
        if (popularPizza) {
            const orders = await fetchOrdersFromServer();
            let pizzaOrders = 0;
            
            // Skaita šīs picas pasūtījumus
            orders.forEach(order => {
                if (order.items) {
                    order.items.forEach(item => {
                        if (item.name === popularPizza.name) {
                            pizzaOrders += item.quantity;
                        }
                    });
                }
            });
            
            if (pizzaOrders > 0) {
                return `🏆 Mūsu pilnīgi populārākā pica ir *${popularPizza.name}* (€${popularPizza.price})!\n\n👥 Šo picu jau pasūtījuši ${pizzaOrders} reizes - tas ir patiess klientu favorīts!\n\n📝 Sastāvdaļas: ${popularPizza.description}\n\nVēlieties mēģināt? 😋`;
            } else {
                return `⭐ Vai gribējāt mēģināt mūsu izcilo *${popularPizza.name}* (€${popularPizza.price})?\n\n📝 Sastāvdaļas: ${popularPizza.description}\n\nTas ir pieredzējušo picu cienītāju izvēle! 🍕`;
            }
        }
        
        return "🍕 Mēs piedāvājam daudz izcilu picu! Apskatiet mūsu pilno izvēlni, lai atrastu savu favorīto! 😋";
    }
    
    // Standarta atbildes
    if (aiResponses[category]) {
        return aiResponses[category];
    }
    
    // Noklusējuma atbildes
    const defaultResponses = [
        'Tas ir labs jautājums! Varat sazināties ar mūsu komandu pa telefonu 26318083 📞',
        'Lūdzam sazināties ar mūsu personālu, lai saņemtu detalizētu atbildi! 💬',
        'Paldies par jūsu jautājumu! Vai jums ir cits jautājums par mūsu pīcām? 🍕'
    ];
    
    return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
}

// Meklē konkrētu picu jautājumā
function findPizzaInMessage(message) {
    const msg = message.toLowerCase();
    
    // Meklē visu picu nosaukumus
    for (const pizza of pizzaMenu) {
        const pizzaName = pizza.name.toLowerCase();
        if (msg.includes(pizzaName)) {
            return pizza;
        }
    }
    
    return null;
}

function sendMessage() {
    const userInput = document.getElementById('userInput');
    const message = userInput.value.trim();
    
    if (!message) return;
    
    const chatMessages = document.getElementById('chatMessages');
    
    // Parāda lietotāja ziņojumu
    const userMessageDiv = document.createElement('div');
    userMessageDiv.className = 'message user-message';
    userMessageDiv.innerHTML = `
        <span class="message-text">${escapeHtml(message)}</span>
        <span class="message-avatar">👤</span>
    `;
    chatMessages.appendChild(userMessageDiv);
    
    userInput.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // AI atbilde ar aizkavi
    setTimeout(async () => {
        const aiResponse = await handleUserMessage(message);
        const aiMessageDiv = document.createElement('div');
        aiMessageDiv.className = 'message ai-message';
        aiMessageDiv.innerHTML = `
            <span class="message-avatar">🤖</span>
            <span class="message-text">${escapeHtml(aiResponse)}</span>
        `;
        chatMessages.appendChild(aiMessageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 600);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showSupport() {
    // Parāda support modāli
    const supportModal = document.getElementById('supportModal');
    if (supportModal) {
        supportModal.classList.add('show');
    }
    
    // Resetē chat state
    chatState.waitingForOrderId = false;
}

function closeSupport() {
    // Aizvēr support modāli
    const supportModal = document.getElementById('supportModal');
    if (supportModal) {
        supportModal.classList.remove('show');
    }
}

function showHumanSupport() {
    // Aizvēr AI chat modāli
    closeSupport();
    
    // Parāda cilvēka atbalsta modāli
    const humanSupportModal = document.getElementById('humanSupportModal');
    if (humanSupportModal) {
        humanSupportModal.classList.add('show');
    }
}

function closeHumanSupport() {
    // Aizvēr cilvēka atbalsta modāli
    const humanSupportModal = document.getElementById('humanSupportModal');
    if (humanSupportModal) {
        humanSupportModal.classList.remove('show');
    }
}

function submitSupportRequest(event) {
    event.preventDefault();
    
    const form = event.target;
    const name = form.children[0].value;
    const email = form.children[1].value;
    const message = form.children[2].value;
    
    // Saglabā palīdzības pieprasījumu storage
    const requests = JSON.parse(safeGetItem('supportRequests') || '[]');
    requests.push({
        id: Date.now(),
        name: name,
        email: email,
        message: message,
        timestamp: new Date().toLocaleString('lv-LV')
    });
    safeSetItem('supportRequests', JSON.stringify(requests));
    
    // Rāda apstiprinājuma ziņu
    alert('Paldies! Mūsu komanda ar jums sazinās drīzumā uz ' + email);
    form.reset();
    
    // Aizvēr modāli
    closeHumanSupport();
}

// Inializē lapas ielādes laikā
// ============================================
// STORAGE HELPER - localStorage + fallback uz memory
// ============================================

let memoryStorage = {}; // Fallback storage ja localStorage neizdara darbu

// Pārbaudīt localStorage pieejamību
function isLocalStorageAvailable() {
    try {
        const test = '__localStorage_test__';
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        return true;
    } catch (e) {
        console.warn('⚠️ localStorage nav pieejams, izmantošu memory fallback');
        return false;
    }
}

const useLocalStorage = isLocalStorageAvailable();

// Safe getItem
function safeGetItem(key) {
    if (useLocalStorage) {
        return localStorage.getItem(key);
    } else {
        return memoryStorage[key] || null;
    }
}

// Safe setItem
function safeSetItem(key, value) {
    if (useLocalStorage) {
        localStorage.setItem(key, value);
    } else {
        memoryStorage[key] = value;
    }
}

// Safe removeItem
function safeRemoveItem(key) {
    if (useLocalStorage) {
        localStorage.removeItem(key);
    } else {
        delete memoryStorage[key];
    }
}

function showPaymentModal() {
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.classList.add('show');
    }
}

function closePaymentModal() {
    selectedPaymentMethod = null;
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.classList.remove('show');
    }
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    proceedWithCheckout();
}

window.addEventListener("DOMContentLoaded", init);