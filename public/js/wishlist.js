document.addEventListener('DOMContentLoaded', function() {
    const wishlistButtons = document.querySelectorAll('.wishlist-button');
    
    // Função para verificar se o usuário está logado
    function isUserLoggedIn() {
        // Na estrutura do site, há vários elementos que só aparecem para usuários logados
        // Verificamos a presença de qualquer um desses elementos
        return (
            // Botão de perfil que mostra o nome do usuário
            document.querySelector('a[href="/user/profile"]') !== null ||
            // Link para edição de perfil
            document.querySelector('a[href="/profile/edit"]') !== null ||
            // Botão de logout
            document.querySelector('form[action="/logout"]') !== null ||
            // Meta tag que contém o ID do usuário
            document.querySelector('meta[name="user-id"]') !== null ||
            // Botão de favoritos autenticado
            document.querySelector('a[href="/favoritos"]') !== null
        );
    }

    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const productType = this.dataset.productType;
            const isAvailable = this.dataset.isAvailable === 'true';
            const icon = this.querySelector('i');

            // Verifica se o usuário está logado
            if (!isUserLoggedIn()) {
                // Exibe o toast com botão de login
                window.showToast(
                    `Você precisa estar logado para adicionar itens ${isAvailable ? 'aos favoritos' : 'à wantlist'}.`, 
                    'error', 
                    {
                        actionButton: {
                            text: 'Fazer Login',
                            onClick: function() {
                                window.dispatchEvent(new CustomEvent('open-login-modal'));
                            }
                        },
                        duration: 8000 // Aumenta o tempo para o usuário ver o botão
                    }
                );
                return;
            }

            // Desabilita o botão temporariamente para evitar cliques duplos
            this.disabled = true;

            // Usa a URL base da aplicação
            const baseUrl = window.location.origin;
            
            // Determina qual endpoint usar baseado na disponibilidade do produto
            const endpoint = isAvailable === true ? 'wishlist/toggle-favorite' : 'wantlist/toggle-favorite';
            
            // Log para debug
            console.log('Toggling with endpoint:', endpoint, 'Product Available:', isAvailable);
            
            fetch(`${baseUrl}/${endpoint}`, {
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
                    // Usuário não está logado - exibe toast com botão de login
                    window.showToast(
                        'Você precisa estar logado para adicionar itens aos favoritos.', 
                        'error', 
                        {
                            actionButton: {
                                text: 'Fazer Login',
                                onClick: function() {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                }
                            },
                            duration: 8000 // Aumenta o tempo para o usuário ver o botão
                        }
                    );
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

                    // Mostra mensagem de sucesso usando o sistema de toast padronizado
                    window.showToast(data.message, 'success');
                } else {
                    throw new Error(data.message || 'Erro ao processar solicitação');
                }
            })
            .catch(error => {
                console.error('Erro ao processar wishlist:', error);
                window.showToast('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.', 'error');
            })
            .finally(() => {
                // Reabilita o botão
                this.disabled = false;
            });
        });
    });
});
