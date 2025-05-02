@extends('layouts.admin')

@section('styles')
<!-- TinyMCE CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.4.2/skins/ui/oxide/skin.min.css" />
@endsection

@section('content')
<div class="p-4">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Enviar E-mail para Inscritos</h1>
        <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
            Voltar para Lista
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.newsletter.send') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Assunto</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Conteúdo do E-mail</label>
                
                <div class="mb-3 flex items-center">
                    @include('admin.newsletter.product-selector')
                    <input type="hidden" id="selectedProducts" name="selectedProducts" value="{{ old('selectedProducts', '[]') }}">
                </div>
                
                <div class="rounded-md shadow-sm">
                    <textarea id="content" name="content" rows="15" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">
                        Use o editor para formatar o conteúdo e adicionar produtos do catálogo.
                    </p>
                </div>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Selecionar Destinatários</h3>
                
                <div class="border border-gray-300 rounded-md p-4 max-h-80 overflow-y-auto">
                    <div class="mb-2 flex items-center">
                        <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="select-all" class="ml-2 text-sm font-semibold text-gray-700">Selecionar Todos</label>
                    </div>
                    
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        @forelse($subscribers as $subscriber)
                            <div class="py-2 flex items-center">
                                <input type="checkbox" id="subscriber-{{ $subscriber->id }}" name="subscribers[]" 
                                    value="{{ $subscriber->id }}" class="subscriber-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array($subscriber->id, old('subscribers', [])) ? 'checked' : '' }}>
                                <label for="subscriber-{{ $subscriber->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $subscriber->email }}
                                </label>
                            </div>
                        @empty
                            <p class="py-2 text-sm text-gray-500">Nenhum inscrito ativo encontrado.</p>
                        @endforelse
                    </div>
                </div>
                
                @error('subscribers')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-8 flex items-center justify-end">
                <p class="text-sm text-gray-500 mr-4" id="selected-count">0 destinatários selecionados</p>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Enviar E-mail
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- TinyMCE Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.4.2/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#content',
            plugins: 'link image lists table code help wordcount autoresize',
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor | code',
            height: 400,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            autoresize_bottom_margin: 50,
            branding: false,
            promotion: false
        });
        
        // Gerenciar checkboxes de destinatários
        const selectAllCheckbox = document.getElementById('select-all');
        const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');
        const selectedCountElement = document.getElementById('selected-count');
        
        // Função para atualizar a contagem
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.subscriber-checkbox:checked').length;
            selectedCountElement.textContent = `${selectedCount} destinatário(s) selecionado(s)`;
        }
        
        // Inicializar a contagem
        updateSelectedCount();
        
        // Evento para o "Selecionar Todos"
        selectAllCheckbox.addEventListener('change', function() {
            subscriberCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateSelectedCount();
        });
        
        // Evento para cada checkbox individual
        subscriberCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Verificar se todos estão selecionados
                const allChecked = Array.from(subscriberCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                
                updateSelectedCount();
            });
        });
    });
</script>
@endpush
@endsection
