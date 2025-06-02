/**
 * Gerencia a funcionalidade de adicionar/remover itens da wantlist
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando wantlist-item.js');
    // Botões de wantlist (notífique-me quando disponível)
    const wantlistButtons = document.querySelectorAll('.wantlist-button, .add-to-wantlist-button');
    console.log('Botões de wantlist encontrados:', wantlistButtons.length);
    
    // Log de cada botão encontrado para debugar
    wantlistButtons.forEach((button, index) => {
        console.log(`Botão ${index + 1}:`, button.className, 'data-product-id:', button.dataset.productId);
    });
    
    wantlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Botão de wantlist clicado:', this.className);
            
            // Verifica se o usuário está autenticado usando o atributo data-auth
            const isAuthenticated = this.dataset.auth === 'true';
            console.log('Status de autenticação:', isAuthenticated);
            
            if (isAuthenticated) {
                console.log('Usuário autenticado, chamando toggleWantlistItem');
                toggleWantlistItem(this);
            } else {
                console.log('Usuário não autenticado, mostrando toast de login');
                if (typeof window.showLoginToast === 'function') {
                    window.showLoginToast();
                } else {
                    window.showToast('Por favor, faça login para continuar', 'info');
                    // Dispatch event to open login modal if available
                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                }
            }
        });
    });
    
    // Botões de remover da wantlist
    const removeWantlistButtons = document.querySelectorAll('.remove-from-wantlist');
    removeWantlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Verifica se o usuário está autenticado
            if (!this.hasAttribute('onclick')) { // Se não tem onclick, significa que está autenticado
                removeFromWantlist(this);
            }
        });
    });
});

// Função para mostrar toast de login necessário
window.showLoginToast = function() {
    if (typeof window.showToast === 'function') {
        window.showToast('É necessário estar logado para adicionar à lista de notificações.', 'warning');
    } else {
        alert('É necessário estar logado para adicionar à lista de notificações.');
    }
    
    // Emite evento customizado para abrir o modal de login
    document.dispatchEvent(new CustomEvent('open-login-modal'));
    
    // Removido o redirecionamento automático para permitir uso do modal
};

// Função para remover item da wantlist
function removeFromWantlist(button) {
    const productId = button.dataset.productId;
    const productType = button.dataset.productType;
    
    fetch('/user/wantlist/remove/' + productId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_type: productType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover o item da página
                const wishlistItem = button.closest('.wishlist-item-card');
                if (wishlistItem) {
                    wishlistItem.remove();
                }
                
                // Atualizar o badge da wantlist, se existir
                const wantlistBadge = document.querySelector('[data-wantlist-badge]');
                if (wantlistBadge) {
                    wantlistBadge.textContent = data.wantlist_count;
                }
                
                // Mostrar mensagem de sucesso usando a função global showToast
                if (typeof window.showToast === 'function') {
                    window.showToast(data.message || 'Item removido da lista de notificações', 'success');
                } else {
                    console.warn('Função showToast não encontrada, usando alert básico');
                    alert(data.message || 'Item removido da lista de notificações');
                }
                
                // Se não houver mais itens na lista, atualizar a página
                if (document.querySelectorAll('.wishlist-item-card').length === 0) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            }
        })
        .catch(error => {
            console.error('Erro ao remover item da wantlist:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('Erro ao processar sua solicitação. Tente novamente.', 'error');
            } else {
                console.warn('Função showToast não encontrada, usando alert básico');
                alert('Erro ao processar sua solicitação. Tente novamente.');
            }
        });
    }

// Função para alternar item na wantlist
function toggleWantlistItem(button) {
    console.log('toggleWantlistItem iniciada');
    const productId = button.dataset.productId;
    const productType = button.dataset.productType;
    const isInWantlist = button.dataset.inWantlist === 'true';
    
    console.log('Dados do botão:', {
        productId,
        productType,
        isInWantlist
    });

    fetch('/user/wantlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            product_type: productType
        })
    })
    .then(response => {
        console.log('Resposta do servidor:', response.status, response.statusText);
        if (!response.ok) {
            console.error('Erro na resposta:', response.status, response.statusText);
            throw new Error(`Erro na resposta: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Dados recebidos do servidor:', data);
        if (data.success) {
            // Atualizar o atributo data-in-wantlist
            button.dataset.inWantlist = data.in_wantlist.toString();
            
            const icon = button.querySelector('i');
            const wantlistText = button.closest('.vinyl-card, .wishlist-card')?.querySelector('.wantlist-text');
            
            // Atualizar o ícone
            if (data.in_wantlist) {
                icon.classList.add('text-sky-500');
                button.title = 'Remover da lista de notificações';
                if (wantlistText) {
                    wantlistText.textContent = 'Você será notificado quando disponível';
                }
            } else {
                icon.classList.remove('text-sky-500');
                button.title = 'Adicionar à lista de notificações';
                if (wantlistText) {
                    wantlistText.textContent = 'Notifique-me quando disponível';
                }
            }
            
            // Atualizar o badge da wantlist, se existir
            const wantlistBadge = document.querySelector('[data-wantlist-badge]');
            if (wantlistBadge) {
                wantlistBadge.textContent = data.wantlist_count;
            }
            
            // Mostrar mensagem de sucesso usando a função global showToast
            if (typeof window.showToast === 'function') {
                window.showToast(data.message, 'success');
            } else {
                console.warn('Função showToast não encontrada, usando alert básico');
                alert(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Erro ao alternar item na wantlist:', error);
        
        // Mostrar mensagem de erro ao usuário
        if (typeof window.showToast === 'function') {
            window.showToast('Erro ao processar sua solicitação. Tente novamente.', 'error');
        } else {
            alert('Erro ao processar sua solicitação. Tente novamente.');
        }
    });
}
