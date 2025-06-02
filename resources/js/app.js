import './bootstrap';
import './playlist';
import './wishlist';
import Alpine from 'alpinejs';

// Implementação global da função showToast usando Flowbite
window.showToast = function(message, type = 'success') {
    // Cria um elemento div para o toast
    const toast = document.createElement('div');
    toast.id = 'toast-' + Math.random().toString(36).substr(2, 9);
    toast.className = 'fixed flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow right-5 top-5 z-50 dark:bg-gray-800';
    toast.role = 'alert';
    
    // Define cor do ícone baseado no tipo
    let iconClass = '';
    let bgClass = '';
    
    if (type === 'success') {
        iconClass = 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200';
        bgClass = 'border-l-4 border-green-500';
    } else if (type === 'error') {
        iconClass = 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200';
        bgClass = 'border-l-4 border-red-500';
    } else if (type === 'info') {
        iconClass = 'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200';
        bgClass = 'border-l-4 border-blue-500';
    } else if (type === 'warning') {
        iconClass = 'text-yellow-500 bg-yellow-100 dark:bg-yellow-800 dark:text-yellow-200';
        bgClass = 'border-l-4 border-yellow-500';
    }
    
    // Adiciona as classes da borda separadamente
    if (bgClass) {
        bgClass.split(' ').forEach(cls => {
            if (cls) toast.classList.add(cls);
        });
    }
    
    // Conteúdo do toast
    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconClass}">
            ${type === 'success' ? '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>' : ''}
            ${type === 'error' ? '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>' : ''}
            ${type === 'warning' ? '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>' : ''}
            ${type === 'info' ? '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/></svg>' : ''}
        </div>
        <div class="ml-3 text-sm font-normal">${message}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#${toast.id}" aria-label="Close">
            <span class="sr-only">Fechar</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    `;
    
    // Adiciona o toast ao DOM
    document.body.appendChild(toast);
    
    // Remove o toast após 3 segundos
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
};

// Implementação de um componente Alpine para modal de login
document.addEventListener('DOMContentLoaded', () => {
    // Registra um event listener para abrir o modal de login
    window.addEventListener('open-login-modal', () => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login-modal' }));
    });
});

// Initialize Alpine.js
document.addEventListener('alpine:init', () => {
    // Global modal component
    Alpine.data('modal', (id = null) => ({
        show: false,
        id: id,

        init() {
            this.$watch('show', value => {
                const body = document.querySelector('body');
                if (value) {
                    body.style.overflow = 'hidden';
                } else {
                    body.style.overflow = '';
                }
            });

            // Listen for modal events
            window.addEventListener('open-modal', (event) => {
                if (!this.id || event.detail === this.id) {
                    this.show = true;
                }
            });

            window.addEventListener('close-modal', () => {
                this.show = false;
            });
        }
    }));

    // Vinyl manager component
    Alpine.data('vinylManager', () => ({
        loading: false,
        saveLoading: false,
        modalTitle: '',
        modalMessage: '',
        modalStatus: '',
        savedVinylId: null,

        init() {
            // Initialize any required setup
        },

        startSearch() {
            this.loading = true;
            // The form will handle the actual submission
            return true;
        },

        get completeVinylUrl() {
            if (!this.savedVinylId) return '#';
            const baseUrl = document.querySelector('meta[name="complete-vinyl-url"]')?.content;
            return baseUrl?.replace(':id', this.savedVinylId) || '#';
        },

        async saveVinyl(releaseId) {
            this.saveLoading = true;
            try {
                const storeUrl = document.querySelector('meta[name="store-vinyl-url"]')?.content;
                if (!storeUrl) throw new Error('Store URL not found');

                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ release_id: releaseId })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Network response was not ok');
                }

                const data = await response.json();
                this.savedVinylId = data.vinyl_id;
                this.modalMessage = data.message;
                this.modalStatus = data.status;
                this.modalTitle = this.modalStatus === 'exists' ? 'Disco já cadastrado' :
                                 this.modalStatus === 'success' ? 'Disco salvo com sucesso!' :
                                 'Erro';
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'result-modal' }));
            } catch (error) {
                console.error('Erro:', error);
                this.modalMessage = error.message || 'Ocorreu um erro ao processar o disco. Por favor, tente novamente.';
                this.modalStatus = 'error';
                this.modalTitle = 'Erro';
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'result-modal' }));
            } finally {
                this.saveLoading = false;
            }
        },

        closeModalAndRedirect() {
            const indexUrl = document.querySelector('meta[name="vinyl-index-url"]')?.content;
            if (indexUrl) {
                window.dispatchEvent(new CustomEvent('close-modal'));
                setTimeout(() => {
                    window.location.href = indexUrl;
                }, 150);
            }
        }
    }));
});

// Make Alpine available globally
window.Alpine = Alpine;

// Configurar x-cloak para funcionar corretamente antes da inicialização
document.addEventListener('DOMContentLoaded', () => {
    // Adicionar o atributo x-cloak aos elementos que precisam ficar escondidos inicialmente
    document.querySelectorAll('[x-show]').forEach(el => {
        if (!el.hasAttribute('x-cloak')) {
            el.setAttribute('x-cloak', '');
        }
    });

    // Garantir que modais não sejam abertos automaticamente
    document.querySelectorAll('[x-data]').forEach(el => {
        const dataStr = el.getAttribute('x-data');
        if (dataStr && dataStr.includes('open:') && dataStr.includes('true')) {
            // Substitui qualquer inicialização que tenha open: true por open: false
            const newDataStr = dataStr.replace(/open:\s*true/g, 'open: false');
            el.setAttribute('x-data', newDataStr);
        }
    });

    // Start Alpine
    Alpine.start();
});

// Adicionar listener para o evento page-fully-loaded
window.addEventListener('page-fully-loaded', function() {
    // Caso necessário, podemos inicializar componentes específicos após a página estar totalmente carregada
    console.log('Página totalmente carregada, componentes Alpine inicializados');

    // Atualizamos componentes Alpine que podem precisar de reavaliação
    if (typeof Alpine !== 'undefined' && Alpine.isInitialized) {
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el._x_dataStack) {
                // Força uma atualização se necessário
                if (typeof el._x_effects !== 'undefined') {
                    el._x_effects.forEach(effect => {
                        if (typeof effect === 'function') {
                            effect();
                        }
                    });
                }
            }
        });
    }
});
