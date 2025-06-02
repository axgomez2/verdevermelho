document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.wishlist-button');

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            const isAvailable = this.dataset.isAvailable === 'true';
            const icon = this.querySelector('i');

            // Desabilita o botão temporariamente para evitar cliques duplos
            this.disabled = true;

            // Usa a URL base da aplicação
            const baseUrl = window.location.origin;
            
            fetch(`${baseUrl}/wishlist/toggle-favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_type: productType
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // Usuário não está logado
                    window.showToast('Você precisa estar logado para adicionar itens aos favoritos.', 'warning');
                    
                    // Após mostrar o toast, abrir o modal de login
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                    }, 1000);
                    throw new Error('Unauthorized');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Atualiza o ícone baseado no status do item
                    if (data.in_wishlist) {
                        icon.classList.add('text-red-500');
                        if (!data.is_in_stock) {
                            icon.classList.remove('fa-heart');
                            icon.classList.add('fa-flag');
                            this.title = 'Item na Wantlist';
                        } else {
                            icon.classList.remove('fa-flag');
                            icon.classList.add('fa-heart');
                            this.title = 'Remover dos favoritos';
                        }
                    } else {
                        icon.classList.remove('text-red-500', 'fa-flag');
                        icon.classList.add('fa-heart');
                        this.title = 'Adicionar aos favoritos';
                    }

                    // Atualiza o contador de wishlist no header (se existir)
                    const wishlistCounter = document.querySelector('.wishlist-count');
                    if (wishlistCounter) {
                        wishlistCounter.textContent = data.wishlistCount;
                    }

                    // Mostra mensagem de sucesso usando a função showToast
                    window.showToast(data.message, 'success');
                } else {
                    throw new Error(data.message || 'Erro ao processar solicitação');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                if (error.message !== 'Unauthorized') {
                    window.showToast('Erro ao processar sua solicitação', 'error');
                }
            })
            .finally(() => {
                // Reabilita o botão
                this.disabled = false;
            });
        });
    });
});
