document.addEventListener('DOMContentLoaded', function() {
    // Adiciona eventos aos botões de adicionar ao carrinho
    document.querySelectorAll('.add-to-cart-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Se o botão estiver desabilitado, não faz nada
            if (this.disabled || this.classList.contains('cursor-not-allowed')) {
                return;
            }

            // Obtém os dados do produto
            const productId = this.dataset.productId;
            const quantity = parseInt(this.dataset.quantity) || 1;
            
            // Desabilita o botão temporariamente para evitar cliques duplos
            this.disabled = true;
            
            // Usa a URL base da aplicação
            const baseUrl = window.location.origin;
            
            // Faz a requisição para adicionar ao carrinho
            fetch(`${baseUrl}/cart/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // Usuário não está logado, mas vamos permitir adicionar ao carrinho
                    // como convidado, então apenas retorna a resposta
                    return response.json();
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Atualiza o contador de itens no carrinho (se existir)
                    const cartCounter = document.querySelector('.cart-count');
                    if (cartCounter) {
                        cartCounter.textContent = data.cartCount;
                    }

                    // Mostra mensagem de sucesso usando SweetAlert2
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Produto adicionado ao carrinho'
                    });
                } else {
                    throw new Error(data.message || 'Erro ao adicionar ao carrinho');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Ops!',
                    text: error.message || 'Erro ao adicionar ao carrinho',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Reabilita o botão
                this.disabled = false;
            });
        });
    });

    // Escuta evento personalizado para adicionar ao carrinho (usado em Alpine.js)
    window.addEventListener('add-to-cart', event => {
        // Obtém os dados do produto do evento
        const { id, quantity } = event.detail;
        
        // Usa a URL base da aplicação
        const baseUrl = window.location.origin;
        
        // Faz a requisição para adicionar ao carrinho
        fetch(`${baseUrl}/cart/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: id,
                quantity: quantity || 1
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Atualiza o contador de itens no carrinho (se existir)
                const cartCounter = document.querySelector('.cart-count');
                if (cartCounter) {
                    cartCounter.textContent = data.cartCount;
                }

                // Mostra mensagem de sucesso usando SweetAlert2
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: data.message || 'Produto adicionado ao carrinho'
                });
            } else {
                throw new Error(data.message || 'Erro ao adicionar ao carrinho');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Ops!',
                text: error.message || 'Erro ao adicionar ao carrinho',
                confirmButtonText: 'OK'
            });
        });
    });
});
