@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-sky-600 border-b border-gray-200">
            <h1 class="text-xl font-bold text-white">Minhas Notificações</h1>
        </div>

        <div class="p-6">
            @if ($notifications->count() > 0)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('site.notifications.mark-all-read') }}" class="text-sm text-sky-600 hover:text-sky-800">
                        Marcar todas como lidas
                    </a>
                </div>

                <div class="space-y-4">
                    @foreach ($notifications as $notification)
                        <div class="border rounded-lg p-4 {{ $notification->read_at ? 'bg-white' : 'bg-yellow-50' }}">
                            <div class="flex items-start">
                                @if(isset($notification->data['cover_image']))
                                    <div class="flex-shrink-0 mr-4">
                                        <img src="{{ asset('storage/' . $notification->data['cover_image']) }}" 
                                             alt="Capa do disco" 
                                             class="w-16 h-16 object-cover rounded">
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $notification->data['message'] ?? 'Nova notificação' }}
                                        </p>
                                        <span class="text-xs text-slate-500">
                                            {{ $notification->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center justify-between">
                                        <div>
                                            @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" 
                                                   class="text-sm text-sky-600 hover:text-sky-800">
                                                    Ver detalhes
                                                </a>
                                            @endif
                                        </div>
                                        
                                        @unless($notification->read_at)
                                            <a href="{{ route('site.notifications.mark-as-read', $notification->id) }}" 
                                               class="text-sm text-sky-600 hover:text-sky-800">
                                                Marcar como lida
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-500">
                                                Lida em {{ $notification->read_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endunless
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-5xl text-gray-300 mb-4">
                        <i class="fa-regular fa-bell"></i>
                    </div>
                    <p class="text-gray-600">Você não tem notificações.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
