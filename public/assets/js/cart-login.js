document.addEventListener('DOMContentLoaded', function() {
    // Formulário de login
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Anexar dados do carrinho local ao formulário
            const localCartInput = document.getElementById('local-cart-data');
            if (localCartInput) {
                const localCart = localStorage.getItem('local-cart') || '[]';
                localCartInput.value = localCart;
                
                // Depois que o formulário é enviado, limpar o carrinho local após um atraso
                // para garantir que os dados sejam enviados primeiro
                setTimeout(() => {
                    if (document.querySelector('.user-authenticated')) {
                        localStorage.removeItem('local-cart');
                        localStorage.removeItem('cart-count');
                    }
                }, 2000);
            }
        });
    }

    // Adicionar um identificador para usuários autenticados
    // Isso ajuda o JavaScript a detectar se o usuário está logado
    const userMenu = document.querySelector('form[action="/logout"]');
    if (userMenu) {
        // Se o usuário está logado (existe botão de logout), adicionar classe no body
        document.body.classList.add('user-authenticated');
    }
});
