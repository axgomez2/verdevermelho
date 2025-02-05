{{--
<x-app-layout>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Seu Carrinho</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div id="cart-items" class="space-y-4">
                    <!-- Os itens do carrinho serão inseridos aqui via JavaScript -->
                </div>
            </div>

            <div class="md:col-span-1">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Resumo do Pedido</h2>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span>Frete:</span>
                        <span id="shipping">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span id="total">R$ 0,00</span>
                    </div>
                    <button id="checkout-button" class="btn btn-primary w-full mt-6">Finalizar Compra</button>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartItems = [
            // Exemplo de itens no carrinho. Na prática, isso viria do backend.
            { id: 1, name: "Vinyl 1", price: 50.00, quantity: 1, image: "/placeholder.svg" },
            { id: 2, name: "Vinyl 2", price: 75.00, quantity: 2, image: "/placeholder.svg" },
        ];

        const cartItemsContainer = document.getElementById('cart-items');
        const subtotalElement = document.getElementById('subtotal');
        const shippingElement = document.getElementById('shipping');
        const totalElement = document.getElementById('total');
        const checkoutButton = document.getElementById('checkout-button');

        function renderCartItems() {
            cartItemsContainer.innerHTML = '';
            let subtotal = 0;

            cartItems.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                const itemElement = document.createElement('div');
                itemElement.className = 'flex items-center bg-white p-4 shadow rounded-lg';
                itemElement.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded mr-4">
                    <div class="flex-grow">
                        <h3 class="font-semibold">${item.name}</h3>
                        <p class="text-gray-600">R$ ${item.price.toFixed(2)}</p>
                        <div class="flex items-center mt-2">
                            <button class="btn btn-sm btn-outline" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">R$ ${itemTotal.toFixed(2)}</p>
                        <button class="btn btn-sm btn-error mt-2" onclick="removeItem(${item.id})">Remover</button>
                    </div>
                `;
                cartItemsContainer.appendChild(itemElement);
            });

            const shipping = 10.00; // Valor fixo de frete para este exemplo
            const total = subtotal + shipping;

            subtotalElement.textContent = `R$ ${subtotal.toFixed(2)}`;
            shippingElement.textContent = `R$ ${shipping.toFixed(2)}`;
            totalElement.textContent = `R$ ${total.toFixed(2)}`;
        }

        function updateQuantity(itemId, newQuantity) {
            const item = cartItems.find(item => item.id === itemId);
            if (item) {
                item.quantity = Math.max(0, newQuantity);
                if (item.quantity === 0) {
                    removeItem(itemId);
                } else {
                    renderCartItems();
                }
            }
        }

        function removeItem(itemId) {
            const index = cartItems.findIndex(item => item.id === itemId);
            if (index !== -1) {
                cartItems.splice(index, 1);
                renderCartItems();
            }
        }

        checkoutButton.addEventListener('click', function() {
            // Salvar informações do carrinho no localStorage
            localStorage.setItem('cartItems', JSON.stringify(cartItems));

            // Redirecionar para a página de verificação de email
            window.location.href = '{{ route('site.checkout.email') }}';
        });

        renderCartItems();
    });
    </script>
    @endpush
</x-app-layout> --}}
