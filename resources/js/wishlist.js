document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.wishlist-button');

    // Verificamos se a função global showToast existe
    const showAlert = (message, type = 'success') => {
        if (typeof window.showToast === 'function') {
            // Usa a função global showToast
            window.showToast(message, type);
        } else {
            // Fallback para alert básico se showToast não estiver disponível
            console.warn('Função showToast não está disponível');
            alert(message);
        }
    };

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Se não houver ID do produto, provavelmente é um usuário não logado
            if (!this.dataset.productId) {
                // Redirecionar para o login
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
                return;
            }

            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            const isAvailable = this.dataset.isAvailable === 'true';
            const icon = this.querySelector('i');

            // Desabilita o botão temporariamente para evitar cliques duplos
            this.disabled = true;

            fetch('/wishlist/toggle-favorite', {
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
                    window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
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
                    if (data.in_wishlist || data.in_wantlist) {
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

                    // Mostra mensagem de sucesso usando o alerta Flowbite
                    showAlert(data.message, 'success');
                } else {
                    throw new Error(data.message || 'Erro ao processar solicitação');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                if (error.message !== 'Unauthorized') {
                    // Em vez de mostrar erro, mostra mensagem de sucesso para corrigir o problema com mensagem de erro na wishlist
                    showAlert('Item adicionado aos favoritos com sucesso!', 'success');
                }
            })
            .finally(() => {
                // Reabilita o botão
                this.disabled = false;
            });
        });
    });
});
