/**
 * Wishlist Item Management
 * Controla a interação com os itens da wishlist, incluindo validação de estoque,
 * controle de quantidade e ações de adicionar ao carrinho e remover da wishlist.
 */
document.addEventListener('DOMContentLoaded', function() {
    checkItemsInCart();
    initQuantityControls();
    initAddToCartButtons();
    initRemoveFromWishlistButtons();
    initSaveForLaterButtons();
});

/**
 * Verifica quais produtos já estão no carrinho e atualiza os botões
 */
function checkItemsInCart() {
    fetch('/carrinho/check-items', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.inCart && data.inCart.length > 0) {
            // Para cada botão de adicionar ao carrinho
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                const productId = button.dataset.productId;
                
                // Verifica se o produto já está no carrinho
                if (data.inCart.includes(parseInt(productId))) {
                    // Atualiza o botão para estilo "já adicionado"
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                    button.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white');
                    button.querySelector('.add-to-cart-text').textContent = 'Adicionado ao Carrinho';
                    button.dataset.inCart = 'true';
                } else {
                    // Garantir que o botão tenha o estilo padrão
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-500', 'text-white');
                    button.dataset.inCart = 'false';
                }
            });
        } else {
            // Garantir que todos os botões tenham o estilo padrão
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-blue-600', 'hover:bg-blue-500', 'text-white');
                button.dataset.inCart = 'false';
            });
        }
    })
    .catch(error => {
        console.error('Erro ao verificar itens no carrinho:', error);
    });
}

/**
 * Inicializa os controles de quantidade para cada item
 */
function initQuantityControls() {
    // Botões de diminuir quantidade
    document.querySelectorAll('.qty-btn-minus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const inputElement = document.querySelector(`#quantity-${productId}`);
            let currentValue = parseInt(inputElement.value);
            
            if (currentValue > 1) {
                inputElement.value = currentValue - 1;
            }
        });
    });
    
    // Botões de aumentar quantidade
    document.querySelectorAll('.qty-btn-plus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const maxQuantity = parseInt(this.dataset.maxQty);
            const inputElement = document.querySelector(`#quantity-${productId}`);
            let currentValue = parseInt(inputElement.value);
            
            if (currentValue < maxQuantity) {
                inputElement.value = currentValue + 1;
            } else {
                // Alerta de estoque máximo atingido
                showFlowbiteAlert(`Quantidade máxima disponível: ${maxQuantity}`, 'warning');
            }
        });
    });
    
    // Validação direta nos inputs de quantidade
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const maxQuantity = parseInt(this.dataset.maxQty);
            let value = parseInt(this.value);
            
            // Valida se é um número
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxQuantity) {
                // Limita ao estoque disponível
                this.value = maxQuantity;
                showFlowbiteAlert(`Quantidade máxima disponível: ${maxQuantity}`, 'warning');
            }
        });
    });
}
    
/**
 * Inicializa os botões de adicionar ao carrinho
 */
function initAddToCartButtons() {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`#quantity-${productId}`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            addItemToCart(productId, quantity, this);
        });
    });
}

/**
 * Inicializa os botões de remover da wishlist
 */
function initRemoveFromWishlistButtons() {
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            
            toggleFavorite(productId, productType, this);
        });
    });
}

/**
 * Inicializa os botões de salvar para depois
 */
function initSaveForLaterButtons() {
    document.querySelectorAll('.save-for-later').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            
            saveForLater(productId, productType, this);
        });
    });
}
    
/**
 * Adiciona um item ao carrinho
 */
function addItemToCart(productId, quantity, buttonElement) {
    if (buttonElement) {
        buttonElement.disabled = true;
        buttonElement.querySelector('.add-to-cart-text').textContent = 'Adicionando...';
    }
        
        if (!productId) {
            showAlert('Produto não identificado', 'error');
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.querySelector('.add-to-cart-text').textContent = 'Adicionar ao Carrinho';
            }
            return;
        }
        
        // Verifica se temos o token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token não encontrado');
            showAlert('Erro de segurança: CSRF token não encontrado', 'error');
            return;
        }

        // Log para debug
        console.log('Enviando produto para o carrinho:', {
            product_id: productId,
            quantity: quantity
        });
        
        // Faz a requisição AJAX para adicionar ao carrinho
        fetch('/carrinho/items', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(async response => {
            // Para fins de debug, vamos registrar a resposta original
            console.log('Resposta do servidor:', response.status, response.statusText);
            
            if (!response.ok) {
                // Tenta obter detalhes do erro
                const textResponse = await response.text();
                console.error('Resposta de erro recebida:', textResponse);
                throw new Error(`Erro ao adicionar produto ao carrinho: ${response.status} ${response.statusText}`);
            }
            
            // Verificar o tipo de conteúdo
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                // Se não for JSON, registrar e processar como texto
                const textResponse = await response.text();
                console.warn('Resposta não é JSON:', textResponse);
                // Retornar um objeto simulado para não quebrar o fluxo
                return { success: true, message: 'Item adicionado ao carrinho', cartCount: 1 };
            }
        })
        .then(data => {
            // Atualiza contadores de carrinho na página
            const cartCounters = document.querySelectorAll('.cart-count');
            cartCounters.forEach(counter => {
                counter.textContent = data.cartCount;
            });
            
            // Atualiza o botão para mostrar que o produto está no carrinho
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                buttonElement.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white');
                buttonElement.querySelector('.add-to-cart-text').textContent = 'Adicionado ao Carrinho';
                buttonElement.dataset.inCart = 'true';
            }
            
            // Mostra mensagem de sucesso
            showFlowbiteAlert('Produto adicionado ao carrinho!', 'success');
        })
        .catch(error => {
            console.error('Erro:', error);
            
            // Restaura o botão
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.querySelector('.add-to-cart-text').textContent = 'Adicionar ao Carrinho';
            }
            
            // Mostra mensagem de erro
            showFlowbiteAlert('Erro ao adicionar produto ao carrinho', 'error');
        });
    }
    
    /**
     * Salva um item para comprar depois (ainda não implementado no backend)
     */
    function saveForLater(productId, productType, buttonElement) {
        // Desabilita o botão durante o processo
        if (buttonElement) {
            buttonElement.disabled = true;
            const originalText = buttonElement.textContent.trim();
            buttonElement.textContent = 'Salvando...';
        }
        
        // Esta funcionalidade ainda não está implementada no backend,
        // mas a estrutura do frontend já está preparada para quando estiver
        
        // Simula operação bem-sucedida (remover quando backend for implementado)
        setTimeout(() => {
            // Restaura o botão
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = `
                    <svg class="me-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Salvar para depois
                `;
            }
            
            // Mostra mensagem (temporária)
            showFlowbiteAlert('Função de salvar para depois será implementada em breve!', 'info');
        }, 1000);
    }
    
    /**
     * Wrapper para a função global showToast
     */
    function showFlowbiteAlert(message, type = 'success') {
        // Usa a função global showToast se disponível
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }
        
        // Fallback caso a função global não esteja disponível
        console.warn('Função showToast não encontrada, usando alerta básico');
        alert(message);
    }
    
    /**
     * Toggle favorito (adicionar/remover da wishlist)
     */
    function toggleFavorite(productId, productType, buttonElement) {
        // Desabilita o botão durante o processo
        if (buttonElement) {
            buttonElement.disabled = true;
            buttonElement.innerHTML = `
                <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                Removendo...`;
        }
        
        // Faz a requisição AJAX para remover da wishlist
        fetch('/favoritos/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                product_type: productType
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao remover da wishlist');
            }
            return response.json();
        })
        .then(data => {
            // Remover o item da página
            const itemCard = buttonElement.closest('.wishlist-item-card') || 
                              buttonElement.closest('.rounded-lg');
            
            if (itemCard) {
                itemCard.classList.add('opacity-50');
                setTimeout(() => {
                    itemCard.remove();
                    
                    // Verifica se não há mais itens na wishlist
                    const remainingItems = document.querySelectorAll('.wishlist-item-card, .rounded-lg').length;
                    if (remainingItems === 0) {
                        const container = document.getElementById('wishlist-container');
                        if (container) {
                            container.innerHTML = `
                                <div class="text-center py-8">
                                    <p class="text-gray-500 mb-4">Sua lista de favoritos está vazia.</p>
                                    <a href="/shop" class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800 focus:bg-red-800 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Explorar discos
                                    </a>
                                </div>
                            `;
                        }
                    }
                }, 500);
            }
            
            // Mostra mensagem de sucesso
            showFlowbiteAlert('Item removido da lista de favoritos!', 'success');
        })
        .catch(error => {
            console.error('Erro:', error);
            
            // Restaura o botão
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = `
                    <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    Remover`;
            }
            
            // Mostra mensagem de erro
            showFlowbiteAlert('Erro ao remover da lista de favoritos', 'error');
        });
    }

// Exporta as funções para uso global
window.wishlistItem = {
    addItemToCart,
    showFlowbiteAlert,
    toggleFavorite
};
