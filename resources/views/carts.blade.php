<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tez-Chop</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link rel="icon" href="/favicon.svg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        .nav {
            flex-wrap: nowrap;
            overflow-x: auto
        }

        .nav-link {
            width: 100px;
        }

        ul.nav.nav-pills::-webkit-scrollbar {
            display: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-30px);
            }

            60% {
                transform: translateY(-15px);
            }
        }

        .bounce {
            animation: bounce 1s;
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            window.Telegram.WebApp.ready();
            const initDataUnsafe = window.Telegram.WebApp.initDataUnsafe;
            const user = initDataUnsafe.user;

            if (user) {
                const userInfo = `
                    User ID: ${user.id} <br>
                    Username: ${user.username} <br>
                    First Name: ${user.first_name} <br>
                    Last Name: ${user.last_name}
                `;

                // Display user info as an alert
                alert(userInfo);

                // Alternatively, insert the user info into the HTML
                document.getElementById('user-info').innerHTML = userInfo;
            } else {
                alert('No user data available.');
            }
        });
    </script>
</head>

<body data-spy="scroll" data-target="#navbar-example" data-offset="50">

    <nav id="navbar-example" class=" navbar navbar-light bg-light fixed-top">
        <div class="container">
            <ul class="nav nav-pills">
                <a class="navbar-brand" href=""><img src="{{ asset('/TezChop-removebg-preview.png') }} "
                        alt="" width="120px"></a>
                @foreach ($menu as $items)
                    <li class="nav-item">
                        <a class="nav-link" href="#section-{{ Str::slug($items->name) }}">{{ $items->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    <div data-spy="scroll" data-target="#navbar-example" data-offset="0">
        <br>
        <br>
        <br>
        <div class="container">
            @foreach ($foods->groupBy('menu.name') as $menuName => $menuItems)
                <section id="section-{{ Str::slug($menuName) }}" class="text-dark mb-5">
                    <h1>{{ $menuName }}</h1>

                    <div class="row">
                        @foreach ($menuItems as $food)
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <img src="{{ $food->image_url }}" class="card-img-top" alt="{{ $food->name }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $food->name }}</h5>
                                        <p class="card-text">{{ $food->description }}</p>
                                        <p class="card-text"><strong>Price:</strong>
                                            ${{ number_format($food->price / 100, 2) }}</p>
                                        <a href="#" class="btn btn-primary add-to-cart-btn"
                                            data-id="{{ $food->id }}" data-name="{{ $food->name }}"
                                            data-price="{{ $food->price }}"
                                            data-description="{{ $food->description }}">
                                            Add To Cart
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>



    </div>
    <div id="user-info"></div>

    <!-- Cart Icon -->
    <button id="cart-icon" class="btn btn-primary" style="display: none; position: fixed; bottom: 20px; right: 20px;">
        <img src="cart-icon.png" alt="Cart" width="50">
        <span class="badge bg-secondary">0</span>
    </button>
    <!-- Quantity Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityModalLabel">Enter Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quantity-form">
                        <div class="mb-3 d-flex align-items-center">
                            <button type="button" id="decrement" class="btn btn-secondary">-</button>
                            <input type="number" class="form-control mx-2" id="quantity-input" min="1"
                                value="1" required readonly>
                            <button type="button" id="increment" class="btn btn-secondary">+</button>
                        </div>
                        <input type="hidden" id="food-id">
                        <input type="hidden" id="food-name">
                        <input type="hidden" id="food-price">
                        <input type="hidden" id="food-description">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items"></div>
                    <hr>
                    <p id="total-amount">Total: $0.00</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="make-order-btn" class="btn btn-primary">Make an Order</button>
                </div>
            </div>
        </div>
    </div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];

            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const cartIcon = document.getElementById('cart-icon');
            const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
            const quantityModal = new bootstrap.Modal(document.getElementById('quantityModal'));

            function updateCartIcon() {
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartIcon.innerHTML =
                    `<img src="cart-icon.png" alt="Cart" width="50"> <span class="badge bg-secondary">${totalItems}</span>`;
                if (totalItems > 0) {
                    cartIcon.style.display = 'block';
                    cartIcon.classList.add('bounce');
                    setTimeout(() => cartIcon.classList.remove('bounce'), 1000);
                } else {
                    cartIcon.style.display = 'none';
                }
            }

            function updateCartModal() {
                const cartItemsContainer = document.getElementById('cart-items');
                const totalAmount = document.getElementById('total-amount');
                cartItemsContainer.innerHTML = '';
                let total = 0;

                cart.forEach((item, index) => {
                    total += item.price * item.quantity;
                    cartItemsContainer.innerHTML += `
                <div class="cart-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p><strong>${item.name}</strong> (x${item.quantity})</p>
                        <p>${item.description}</p>
                        <p>Price: $${(item.price / 100).toFixed(2)}</p>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn" data-index="${index}">Remove</button>
                </div>
            `;
                });

                totalAmount.textContent = `Total: $${(total / 100).toFixed(2)}`;
            }

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const foodId = this.getAttribute('data-id');
                    const foodName = this.getAttribute('data-name');
                    const foodPrice = this.getAttribute('data-price');
                    const foodDescription = this.getAttribute('data-description');

                    // Set values in quantity modal
                    document.getElementById('food-id').value = foodId;
                    document.getElementById('food-name').value = foodName;
                    document.getElementById('food-price').value = foodPrice;
                    document.getElementById('food-description').value = foodDescription;

                    // Show the quantity modal
                    quantityModal.show();
                });
            });

            document.getElementById('quantity-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const foodId = document.getElementById('food-id').value;
                const foodName = document.getElementById('food-name').value;
                const foodPrice = document.getElementById('food-price').value;
                const foodDescription = document.getElementById('food-description').value;
                const quantity = parseInt(document.getElementById('quantity-input').value, 10);

                if (quantity > 0) {
                    const existingItem = cart.find(item => item.food_id === foodId);

                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        cart.push({
                            food_id: foodId,
                            name: foodName,
                            price: foodPrice,
                            description: foodDescription,
                            quantity: quantity
                        });
                    }

                    updateCartIcon();
                    quantityModal.hide();
                } else {
                    alert('Invalid quantity. Please enter a positive number.');
                }
            });

            document.getElementById('decrement').addEventListener('click', function() {
                const quantityInput = document.getElementById('quantity-input');
                let quantity = parseInt(quantityInput.value, 10);
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                }
            });

            document.getElementById('increment').addEventListener('click', function() {
                const quantityInput = document.getElementById('quantity-input');
                let quantity = parseInt(quantityInput.value, 10);
                quantityInput.value = quantity + 1;
            });

            cartIcon.addEventListener('click', function() {
                updateCartModal();
                cartModal.show();
            });

            document.getElementById('make-order-btn').addEventListener('click', function() {
                const orderData = {
                    items: cart,
                    telegram_id: 1468522815
                };

                fetch('/order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(orderData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Order placed successfully!');
                        cart = []; // Clear the cart
                        updateCartIcon();
                        cartModal.hide();
                    })
                    .catch(error => {
                        alert('There was an error placing the order.');
                        console.error(error);
                    });
            });

            // Handle item removal
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-item-btn')) {
                    const index = event.target.getAttribute('data-index');
                    cart.splice(index, 1);
                    updateCartModal();
                    updateCartIcon();
                }
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
