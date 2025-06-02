/**
 * Wishlist Cart Integration
 * Permite adicionar itens da wishlist ao carrinho
 */
document.addEventListener('DOMContentLoaded', function() {
    // Botão para adicionar todos os itens ao carrinho
    const addAllToCartBtn = document.getElementById('add-all-to-cart');
    
    if (addAllToCartBtn) {
        addAllToCartBtn.addEventListener('click', function() {
            // Encontrar todos os botões de adicionar ao carrinho visíveis e disponíveis
            const addButtons = document.querySelectorAll('.add-to-cart-btn');
            
            if (addButtons.length === 0) {
                showFlowbiteAlert('Não há itens disponíveis para adicionar ao carrinho', 'error');
                return;
            }
            
            // Contador para acompanhar progresso
            let addedCount = 0;
            let totalToAdd = addButtons.length;
            
            // Desabilita o botão principal durante o processo
            addAllToCartBtn.disabled = true;
            addAllToCartBtn.classList.add('opacity-75');
            addAllToCartBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adicionando...';
            
            // Adiciona cada produto ao carrinho sequencialmente
            addButtons.forEach(button => {
                const productId = button.dataset.productId;
                
                // Usa a mesma função usada pelo botão individual
                addToCart(productId, 1, null, false).then(() => {
                    addedCount++;
                    
                    // Quando todos estiverem adicionados
                    if (addedCount === totalToAdd) {
                        // Restaura o botão
                        addAllToCartBtn.disabled = false;
                        addAllToCartBtn.classList.remove('opacity-75');
                        addAllToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Adicionar Todos ao Carrinho';
                        
                        // Exibe mensagem de sucesso
                        showFlowbiteAlert(`${totalToAdd} itens adicionados ao carrinho com sucesso!`, 'success');
                        
                        // Atualiza o contador do carrinho na interface
                        updateCartCount(totalToAdd);
                    }
                }).catch(error => {
                    console.error('Erro ao adicionar produto:', error);
                    addedCount++;
                    
                    // Mesmo com erros, continuamos até o final
                    if (addedCount === totalToAdd) {
                        addAllToCartBtn.disabled = false;
                        addAllToCartBtn.classList.remove('opacity-75');
                        addAllToCartBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg> Adicionar Todos ao Carrinho';
                    }
                });
            });
        });
    }
    
    // Função para mostrar alertas - Wrapper para a função global showToast
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
    
    // Função auxiliar para atualizar o contador do carrinho
    function updateCartCount(added) {
        const cartCounters = document.querySelectorAll('.cart-count');
        cartCounters.forEach(counter => {
            const currentCount = parseInt(counter.textContent || '0');
            counter.textContent = currentCount + added;
        });
    }
});

// Versão promisificada da função addToCart para uso pelo botão "Adicionar Todos"
// Esta função será utilizada pelo botão de adicionar todos, mantendo compatibilidade
// com a função existente mas retornando uma Promise
function addToCart(productId, quantity = 1, button = null, showMessage = true) {
    return new Promise((resolve, reject) => {
        // Se um botão foi fornecido, desabilita-o
        if (button) {
            button.disabled = true;
            const originalText = button.querySelector('.add-to-cart-text').textContent.trim();
            button.querySelector('.add-to-cart-text').innerHTML = 'Adicionando...';
        }
        
        // Faz a requisição para adicionar ao carrinho
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao adicionar produto ao carrinho');
            }
            return response.json();
        })
        .then(data => {
            // Se um botão foi fornecido, atualiza sua aparência
            if (button) {
                button.disabled = false;
                button.querySelector('.add-to-cart-text').textContent = 'Adicionado';
                
                setTimeout(() => {
                    button.querySelector('.add-to-cart-text').textContent = 'Adicionar';
                }, 2000);
            }
            
            // Atualiza o contador do carrinho
            const cartCounters = document.querySelectorAll('.cart-count');
            cartCounters.forEach(counter => {
                counter.textContent = data.cartCount;
            });
            
            // Mostra mensagem de sucesso se solicitado
            if (showMessage) {
                // Usa a função global de alerta
                if (typeof showFlowbiteAlert === 'function') {
                    showFlowbiteAlert('Produto adicionado ao carrinho!', 'success');
                }
            }
            
            resolve(data);
        })
        .catch(error => {
            console.error('Erro:', error);
            
            // Restaura o botão se fornecido
            if (button) {
                button.disabled = false;
                button.querySelector('.add-to-cart-text').textContent = 'Adicionar';
            }
            
            // Mostra mensagem de erro se solicitado
            if (showMessage) {
                if (typeof showFlowbiteAlert === 'function') {
                    showFlowbiteAlert('Erro ao adicionar produto ao carrinho', 'error');
                }
            }
            
            reject(error);
        });
    });
}
