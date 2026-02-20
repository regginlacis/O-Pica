// ============================================
// TĒMAS PĀRSLĒGŠANA - 3 TĒMAS
// Light (gaišā), White (balta), Dark (tumšā)
// ============================================

function setTheme(theme) {
    const body = document.body;
    
    // Noņem visas tēmas
    body.classList.remove('light-theme', 'white-theme', 'dark-theme');
    
    // Pievieno jauno tēmu
    body.classList.add(theme + '-theme');
    
    // Saglabā localStorage
    localStorage.setItem('theme', theme);
    console.log('Mainīta tema uz: ' + theme);
}

// Ielādē tēmu sākumā
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
});




// Picas izvēlnes dati
const pizzaMenu = [
    { id: 1, name: "Margarita", emoji: "🍕", description: "Svaiga mozarella, tomāti, baziliks", price: 12.99 },
    { id: 2, name: "Peperoni", emoji: "🍕", description: "Peperoni, siers, tomātu mērce", price: 13.99 },
    { id: 3, name: "Veģetāriešu", emoji: "🥦", description: "Pipari, sīpoli, šampinjeri, olīvas", price: 12.99 },
    { id: 4, name: "Gaļas Cienītāju", emoji: "🍖", description: "Peperoni, desiņa, bekons, šķiņķis", price: 15.99 },
    { id: 5, name: "BBQ Vistas", emoji: "🍗", description: "BBQ mērce, vistas gaļa, sīpoli, koriandrs", price: 14.99 },
    { id: 6, name: "Havajiešu", emoji: "🍍", description: "Ananāss, šķiņķis, siers", price: 13.99 }
];

let cart = [];
let orders = [];

// Admin piekļuve caur konsoli
function goAdmin() {
    window.location.href = 'admin.php';
}

console.log('%c🍕 O! Pica Admin Panel', 'color: #ff4757; font-size: 20px; font-weight: bold;');
console.log('%cAdmin piekļuves komanda:', 'color: #ff4757; font-weight: bold;');
console.log('%cgoAdmin()', 'color: #27ae60; font-size: 14px; font-weight: bold; background: #f0f0f0; padding: 5px;');

function init() {
    displayPizzas();
    updateCartDisplay();
    loadOrdersFromStorage();
}

function displayPizzas() {
    const pizzaList = document.getElementById("pizza-list");
    pizzaList.innerHTML = "";
    pizzaMenu.forEach(pizza => {
        const pizzaCard = document.createElement("div");
        pizzaCard.className = "pizza-card";
        pizzaCard.innerHTML = `
            <div class="pizza-image">${pizza.emoji}</div>
            <div class="pizza-info">
                <div class="pizza-name">${pizza.name}</div>
                <div class="pizza-description">${pizza.description}</div>
                <div class="pizza-footer">
                    <div class="pizza-price">€${pizza.price.toFixed(2)}</div>
                    <button class="btn-add" onclick="addToCart(${pizza.id})">Pievienot</button>
                </div>
            </div>
        `;
        pizzaList.appendChild(pizzaCard);


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
                    <div class="cart-item-name">${item.emoji} ${item.name}</div>
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

// Rāda izvēlnes sekciju
function showMenu() {
    document.getElementById("menu-section").classList.add("active");
    document.getElementById("cart-section").classList.remove("active");
    document.getElementById("orders-section").classList.remove("active");
}

// Rāda groza sekciju
function showCart() {
    document.getElementById("menu-section").classList.remove("active");
    document.getElementById("cart-section").classList.add("active");
    document.getElementById("orders-section").classList.remove("active");
}

// Rāda pasūtījumu sekciju
function showOrders() {
    document.getElementById("menu-section").classList.remove("active");
    document.getElementById("cart-section").classList.remove("active");
    document.getElementById("orders-section").classList.add("active");
    loadOrdersFromStorage(); // Ielādē jaunākos datus pirms parādīšanas
    displayUserOrders();
}

// Pasūtījuma noslēgšana - saglabā datubāzē caur API
function checkout() {
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
        timestamp: new Date().toLocaleString('lv-LV'),
        status: 'pending'
    };
    console.log('Saglabāts pasūtījums localStorage:', newOrder);
    saveOrderToStorage(newOrder);
    
    // Sagatavo datus API izsaukumam
    const orderData = {
        orderId: orderId,
        items: cart,
        total: total
    };
    
    // Izsauc API, lai izveidotu pasūtījumu datubāzē
    fetch('api.php?action=create_order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Pasūtījums iesniegts! ID: ${orderId}\nKopā: €${total.toFixed(2)}\n\nJūsu pica tiks piegādāta 30-45 minūtēs. Paldies par pasūtījumu! 🍕`);
            
            // Iztukšo grozu
            cart = [];
            updateCartDisplay();
            loadOrdersFromStorage();
            showMenu();
        } else {
            alert('Kļūda pasūtījuma veidošanā: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kļūda:', error);
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
    
    let html = '';
    
    for (let i = 0; i < orders.length; i++) {
        const order = orders[i];
        if (!order) continue;
        
        const items = order.items || [];
        const status = order.status || 'pending';
        const statusText = status === 'pending' ? '⏳ Gaida' : '✓ Piegādāts';
        const statusClass = status === 'pending' ? 'status-pending' : 'status-delivered';
        
        let itemsHtml = '';
        for (let j = 0; j < items.length; j++) {
            const item = items[j];
            if (!item) continue;
            const itemPrice = parseFloat(item.price) || 0;
            const qty = parseInt(item.quantity) || 1;
            const itemTotal = itemPrice * qty;
            const itemTotalStr = itemTotal.toString().split('.')[0] + '.' + (itemTotal.toString().split('.')[1] || '00').substring(0, 2);
            itemsHtml += '<div class="order-item">' + (item.emoji || '🍕') + ' ' + item.name + ' x' + qty + ' - €' + itemTotalStr + '</div>';
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
        const orderTime = new Date(order.timestamp);
        const nowTime = new Date();
        const minutesDiff = (nowTime - orderTime) / (1000 * 60);
        const canCancel = minutesDiff < 10;
        
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
        if (canCancel && status === 'pending') {
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
    
    // Noņem no localStorage
    let storedOrders = JSON.parse(localStorage.getItem('userOrders')) || [];
    storedOrders = storedOrders.filter(order => order.id !== orderId);
    localStorage.setItem('userOrders', JSON.stringify(storedOrders));
    
    // Atjaunina pasūtījumu sarakstu
    loadOrdersFromStorage();
    displayUserOrders();
    
    showNotification('Pasūtījums atcelts! ❌');
}

// Atzīmē pasūtījumu kā piegādātu caur API
function markAsDelivered(orderId) {
    fetch('api.php?action=mark_delivered', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ orderId: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Noņem pasūtījumu no localStorage
            let storedOrders = JSON.parse(localStorage.getItem('userOrders')) || [];
            storedOrders = storedOrders.filter(order => order.id !== orderId);
            localStorage.setItem('userOrders', JSON.stringify(storedOrders));
            
            // Atjaunina pasūtījumu sarakstu
            loadOrdersFromStorage();
            
            showNotification("Pasūtijums #" + orderId + " piegadats! ✓");
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
        fetch('api.php?action=clear_all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orders = [];
                showNotification('Visi pasūtījumi dzēsti.');
            } else {
                alert('Kļūda: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Kļūda:', error);
            alert('Kļūda dzēšot pasūtījumus');
        });
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
        const existing = localStorage.getItem('userOrders');
        console.log('Esošais localStorage:', existing);
        
        if (existing) {
            try {
                storedOrders = JSON.parse(existing);
            } catch (e) {
                console.error('Kļūda parsējot localStorage:', e);
                storedOrders = [];
            }
        }
        
        console.log('Pirms pievienošanas:', storedOrders);
        storedOrders.unshift(order);
        console.log('Pēc pievienošanas:', storedOrders);
        
        localStorage.setItem('userOrders', JSON.stringify(storedOrders));
        console.log('Saglabāts localStorage!');
    } catch (error) {
        console.error('KRITISKA KĻŪDA saglabājot pasūtījumu:', error);
        alert('Nevarēja saglabāt pasūtījumu lokāli!');
    }
}

// Ielādē pasūtījumus no localStorage
function loadOrdersFromStorage() {
    try {
        const storedOrders = JSON.parse(localStorage.getItem('userOrders')) || [];
        orders = storedOrders;
        console.log('Ielādēti pasūtījumi no localStorage:', orders);
        console.log('Orders skaits:', orders.length);
        
        // Sinhronizē localStorage ar datubāzi (noņem dzēstus pasūtījumus)
        syncOrdersWithDatabase();
    } catch (error) {
        console.error('Kļūda ielādējot pasūtījumus:', error);
        orders = [];
    }
}

// Sinhronizē localStorage ar datubāzes stāvokli
function syncOrdersWithDatabase() {
    if (orders.length === 0) return;
    
    const orderIds = orders.map(o => o.id);
    
    fetch('api.php?action=get_all_orders')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const dbOrderIds = data.orders.map(o => o.id);
                
                // Atrod pasūtījumus, kas ir localStorage, bet nē datubāzē (admin tos dzēsa)
                const deletedOrderIds = orderIds.filter(id => !dbOrderIds.includes(id));
                
                if (deletedOrderIds.length > 0) {
                    console.log('Atrod dzēstus pasūtījumus:', deletedOrderIds);
                    
                    // Noņem dzēstus pasūtījumus no localStorage
                    let storedOrders = JSON.parse(localStorage.getItem('userOrders')) || [];
                    storedOrders = storedOrders.filter(order => !deletedOrderIds.includes(order.id));
                    localStorage.setItem('userOrders', JSON.stringify(storedOrders));
                    
                    // Atjaunina orders masīvu
                    orders = storedOrders;
                    console.log('localStorage sinhronizēts ar datubāzi');
                }
            }
        })
        .catch(error => console.log('Sinhronizācijas kļūda (normāli, ja datubāze nav pieejama):', error));
}

// Ielādē pasūtījumus no API
function loadOrders() {
    fetch('api.php?action=get_user_orders')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                orders = data.orders;
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

// Inializē lapas ielādes laikā
window.addEventListener("DOMContentLoaded", init);