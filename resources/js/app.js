import './bootstrap';
import './playlist';
import Alpine from 'alpinejs';

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

// Start Alpine
Alpine.start();
