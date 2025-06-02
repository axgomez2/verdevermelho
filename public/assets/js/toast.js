/**
 * Arquivo toast.js - Wrapper de compatibilidade
 * 
 * ATENÇÃO: Este arquivo foi mantido apenas para compatibilidade.
 * A implementação real da função showToast agora está em resources/js/app.js
 * e é carregada via Vite. Em uma futura atualização, este arquivo será removido.
 * 
 * Todos os novos desenvolvimentos devem usar diretamente window.showToast,
 * que é fornecido pelo app.js.
 */

// Este arquivo não faz nada, pois a função showToast já está definida no app.js
// Esta é apenas uma camada de compatibilidade para scripts antigos que ainda importam este arquivo.

// Se por algum motivo a função showToast não estiver disponível, definimos uma versão básica
if (typeof window.showToast !== 'function') {
    console.warn('A função showToast não foi encontrada no escopo global. Usando fallback básico.');
    
    window.showToast = function(message, type = 'info') {
        console.log(`Toast (${type}): ${message}`);
        alert(message);
    };
}
          opacity: 1;
      }
      @keyframes slideInRight {
          from { transform: translateX(100%); }
          to { transform: translateX(0); }
      }
      @keyframes fadeOut {
          from { opacity: 1; }
          to { opacity: 0; }
      }
  `
  document.head.appendChild(style)

