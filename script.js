document.addEventListener('DOMContentLoaded', () => {
    const products = [
        { name: 'Nintendo Switch', price: '$299.99', image: 'https://example.com/switch.jpg', category: 'consoles' },
        { name: 'The Legend of Zelda: Breath of the Wild', price: '$59.99', image: 'https://example.com/zelda.jpg', category: 'games' },
        { name: 'Mario Amiibo', price: '$12.99', image: 'https://example.com/mario-amiibo.jpg', category: 'amiibo' },
        { name: 'Nintendo T-Shirt', price: '$19.99', image: 'https://example.com/tshirt.jpg', category: 'merch' },
        { name: 'Nintendo Switch Lite', price: '$199.99', image: 'https://example.com/switch-lite.jpg', category: 'consoles' },
        { name: 'Super Mario Odyssey', price: '$59.99', image: 'https://example.com/odyssey.jpg', category: 'games' },
        { name: 'Luigi Amiibo', price: '$12.99', image: 'https://example.com/luigi-amiibo.jpg', category: 'amiibo' },
        { name: 'Nintendo Hat', price: '$24.99', image: 'https://example.com/hat.jpg', category: 'merch' },
        { name: 'Animal Crossing: New Horizons', price: '$59.99', image: 'https://example.com/animal-crossing.jpg', category: 'games' },
        { name: 'Pikachu Amiibo', price: '$12.99', image: 'https://example.com/pikachu-amiibo.jpg', category: 'amiibo' },
        { name: 'Nintendo Hoodie', price: '$49.99', image: 'https://example.com/hoodie.jpg', category: 'merch' },
        // Add more products here to reach around 60 products
        // ...
    ];

    const featuredProducts = [
        { name: 'Nintendo Switch Lite', price: '$199.99', image: 'https://example.com/switch-lite.jpg', category: 'consoles' },
        { name: 'Pikachu Amiibo', price: '$12.99', image: 'https://example.com/pikachu-amiibo.jpg', category: 'amiibo' },
        { name: 'Nintendo Hat', price: '$24.99', image: 'https://example.com/hat.jpg', category: 'merch' }
    ];

    const productsContainer = document.getElementById('products');
    const featuredContainer = document.getElementById('featured-container');
    const cartModal = document.getElementById('cart-modal');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartIcon = document.getElementById('cart-icon');
    const cartCount = document.getElementById('cart-count');
    const closeCartButton = document.getElementById('close-cart');
    const checkoutButton = document.getElementById('checkout-button');
    const searchInput = document.getElementById('search');
    const categoryFilter = document.getElementById('category-filter');

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function updateCart() {
        cartItemsContainer.innerHTML = '';
        cart.forEach((item, index) => {
            const cartItem = document.createElement('li');
            cartItem.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <span>${item.name}</span>
                <span>${item.price}</span>
                <button onclick="removeFromCart(${index})">Remove</button>
            `;
            cartItemsContainer.appendChild(cartItem);
        });
        cartCount.textContent = cart.length;
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function addToCart(product) {
        cart.push(product);
        updateCart();
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCart();
    }

    cartIcon.addEventListener('click', () => {
        cartModal.style.display = 'block';
    });

    closeCartButton.addEventListener('click', () => {
        cartModal.style.display = 'none';
    });

    checkoutButton.addEventListener('click', () => {
        window.location.href = 'checkout.html';
    });

    function renderProducts(productsToRender, container) {
        container.innerHTML = '';
        productsToRender.forEach(product => {
            const productElement = document.createElement('div');
            productElement.classList.add('product');
            
            productElement.innerHTML = `
                <img src="${product.image}" alt="${product.name}">
                <h2>${product.name}</h2>
                <p>${product.price}</p>
                <button data-product='${JSON.stringify(product)}' class='add-to-cart-button'>Add to Cart</button>
            `;

            container.appendChild(productElement);
        });

        document.querySelectorAll('.add-to-cart-button').forEach(button => {
            button.addEventListener('click', (event) => {
                const product = JSON.parse(event.target.getAttribute('data-product'));
                addToCart(product);
            });
        });
    }

    searchInput?.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredProducts = products.filter(product =>
            product.name.toLowerCase().includes(searchTerm)
        );
        renderProducts(filteredProducts, productsContainer);
    });

    categoryFilter?.addEventListener('change', () => {
        const selectedCategory = categoryFilter.value;
        const filteredProducts = selectedCategory === 'all'
            ? products
            : products.filter(product => product.category === selectedCategory);
        renderProducts(filteredProducts, productsContainer);
    });

    // Initial render
    renderProducts(products, productsContainer);
    renderProducts(featuredProducts, featuredContainer);

    // Load the cart from localStorage when the page is loaded
    updateCart();
});