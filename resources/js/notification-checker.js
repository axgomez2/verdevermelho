/**
 * Verifica novas notificações do usuário e atualiza a interface
 */
document.addEventListener('DOMContentLoaded', function() {
    // Só executa em páginas onde o usuário está logado
    if (document.querySelector('[data-notifications-count]')) {
        // Verificar notificações a cada 60 segundos
        setInterval(checkNotifications, 60000);
        
        // Verificar imediatamente
        checkNotifications();
    }
});

/**
 * Busca notificações não lidas do servidor
 */
function checkNotifications() {
    fetch('/notificacoes/verificar')
        .then(response => response.json())
        .then(data => {
            updateNotificationsBadge(data.unread_count);
            updateNotificationsDropdown(data.notifications);
        })
        .catch(error => console.error('Erro ao verificar notificações:', error));
}

/**
 * Atualiza o contador de notificações não lidas
 */
function updateNotificationsBadge(count) {
    const badge = document.querySelector('[data-notifications-count]');
    
    if (!badge) return;
    
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

/**
 * Atualiza o conteúdo do dropdown de notificações
 */
function updateNotificationsDropdown(notifications) {
    const container = document.querySelector('[data-notifications-container]');
    
    if (!container) return;
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="px-4 py-3 text-sm text-slate-600">
                <p>Você não tem notificações.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    notifications.forEach(notification => {
        let readClass = notification.read ? 'bg-white' : 'bg-yellow-50';
        
        html += `
            <div class="px-4 py-2 border-b border-gray-100 ${readClass}">
                <div class="flex items-center">
                    ${notification.image ? `
                        <div class="flex-shrink-0 mr-3">
                            <img src="${notification.image}" alt="Capa do disco" class="w-10 h-10 object-cover rounded">
                        </div>
                    ` : ''}
                    <div class="flex-1">
                        <p class="text-sm text-slate-700">${notification.message}</p>
                        <p class="text-xs text-slate-500 mt-1">${notification.time}</p>
                    </div>
                    ${!notification.read ? `
                        <a href="/notificacoes/${notification.id}/marcar-como-lida" class="ml-2 text-xs text-sky-600 hover:text-sky-800">
                            <i class="fa-regular fa-circle-check"></i>
                        </a>
                    ` : ''}
                </div>
                ${notification.url ? `
                    <div class="mt-2">
                        <a href="${notification.url}" class="text-xs text-sky-600 hover:text-sky-800">Ver detalhes</a>
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}
