// Criação de modelos de toast para reutilizar em toda aplicação
document.addEventListener('DOMContentLoaded', function() {
  // Cria o div que conterá os templates de toast
  const toastTemplates = document.createElement('div');
  toastTemplates.id = 'toast-templates';
  toastTemplates.style.display = 'none';
  toastTemplates.innerHTML = `
    <!-- Toast de sucesso -->
    <div id="toast-success-template" class="fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
      <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="ml-3 text-sm font-normal message-text"></div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
        <span class="sr-only">Fechar</span>
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Toast de erro -->
    <div id="toast-error-template" class="fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
      <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="ml-3 text-sm font-normal message-text"></div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
        <span class="sr-only">Fechar</span>
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Toast de aviso -->
    <div id="toast-warning-template" class="fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
      <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-700 dark:text-orange-200">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="ml-3 text-sm font-normal message-text"></div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
        <span class="sr-only">Fechar</span>
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Toast informativo -->
    <div id="toast-info-template" class="fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
      <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200">
        <i class="fas fa-info-circle"></i>
      </div>
      <div class="ml-3 text-sm font-normal message-text"></div>
      <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
        <span class="sr-only">Fechar</span>
        <i class="fas fa-times"></i>
      </button>
    </div>
  `;
  document.body.appendChild(toastTemplates);
});

// Implementação da função global para mostrar toasts
window.showToast = function(message, type = 'info', options = {}) {
  // Garante que o tipo seja um dos valores aceitos
  if (!['success', 'error', 'warning', 'info'].includes(type)) {
    type = 'info';
  }
  
  // Opções padrão
  const defaultOptions = {
    actionButton: null, // { text: 'Texto do botão', onClick: function() { /* ação */ } }
    duration: 5000,     // Tempo em ms antes do toast desaparecer automaticamente
  };
  
  // Mescla as opções fornecidas com as padrões
  options = { ...defaultOptions, ...options };
  
  // Verifica se os templates já estão carregados
  if (!document.getElementById('toast-templates')) {
    // Se não estiver, cria um template básico no momento
    const tempTemplate = document.createElement('div');
    tempTemplate.innerHTML = `
      <div class="fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow z-50" role="alert">
        <div class="ml-3 text-sm font-normal">${message}</div>
      </div>
    `;
    const toast = tempTemplate.firstElementChild;
    document.body.appendChild(toast);
    
    setTimeout(() => {
      if (toast.parentNode) {
        toast.remove();
      }
    }, options.duration);
    
    return;
  }
  
  // Pega o template correto baseado no tipo
  const templateId = `toast-${type}-template`;
  const template = document.getElementById(templateId);
  
  if (!template) {
    console.error(`Template de toast '${templateId}' não encontrado`);
    return;
  }
  
  // Clona o template
  const toast = template.cloneNode(true);
  toast.id = `toast-${type}-${Date.now()}`;
  toast.style.display = 'flex';
  toast.style.zIndex = '9999';
  
  // Define a mensagem
  const messageEl = toast.querySelector('.message-text');
  if (messageEl) {
    messageEl.textContent = message;
    
    // Se tiver botão de ação, adiciona-o
    if (options.actionButton) {
      // Adicionando um div para conter os botões (ação e fechar)
      messageEl.insertAdjacentHTML('afterend', `
        <div class="action-button ml-2">
          <button type="button" class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            ${options.actionButton.text}
          </button>
        </div>
      `);
      
      // Configurando evento do botão de ação
      const actionBtn = toast.querySelector('.action-button button');
      if (actionBtn && options.actionButton.onClick) {
        actionBtn.addEventListener('click', function(e) {
          e.preventDefault();
          // Remove o toast ao clicar no botão de ação
          if (toast.parentNode) {
            toast.remove();
          }
          // Executa a ação definida
          options.actionButton.onClick();
        });
      }
    }
  }
  
  // Adiciona ao corpo do documento
  document.body.appendChild(toast);
  
  // Configura o botão de fechar
  const closeButton = toast.querySelector('button[aria-label="Close"]');
  if (closeButton) {
    closeButton.addEventListener('click', function() {
      if (toast.parentNode) {
        toast.remove();
      }
    });
  }
  
  // Auto-remover após o tempo definido
  setTimeout(() => {
    if (toast.parentNode) {
      // Animação de fade out
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.5s';
      
      setTimeout(() => {
        if (toast.parentNode) {
          toast.remove();
        }
      }, 500);
    }
  }, options.duration);
};
