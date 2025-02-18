@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">{{ __('Create Playlist') }}</h1>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <form action="{{ route('admin.playlists.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Name') }}</label>
                        <input type="text" id="name" name="name"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Slug') }}</label>
                        <div class="flex items-center">
                            <input type="text" id="slug" name="slug" readonly
                                   class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            <button type="button" id="toggle-slug" class="ml-2 text-sm text-blue-600">
                                {{ __('Editar') }}
                            </button>
                        </div>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Bio') }}</label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        @error('bio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Social Links -->
                    <div class="space-y-4">
                        <!-- Instagram URL -->
                        <div>
                            <label for="instagram_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Instagram URL') }}</label>
                            <input type="url" id="instagram_url" name="instagram_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('instagram_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- YouTube URL -->
                        <div>
                            <label for="youtube_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('YouTube URL') }}</label>
                            <input type="url" id="youtube_url" name="youtube_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('youtube_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Facebook URL') }}</label>
                            <input type="url" id="facebook_url" name="facebook_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('facebook_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- SoundCloud URL -->
                        <div>
                            <label for="soundcloud_url" class="block mb-2 text-sm font-medium text-gray-900">{{ __('SoundCloud URL') }}</label>
                            <input type="url" id="soundcloud_url" name="soundcloud_url"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            @error('soundcloud_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Active Checkbox -->
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ __('Active') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Image Upload -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="image">{{ __('Profile Image') }}</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div id="image-preview-container" class="w-full h-full relative">
                                    <img id="image-preview" src="" class="w-full h-full object-cover rounded-lg" alt="Preview" style="display:none;">
                                    <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">{{ __('Click to upload') }}</span></p>
                                        <p class="text-xs text-gray-500">PNG, JPG ou GIF</p>
                                    </div>
                                </div>
                                <input type="file" id="image" name="image" class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>

                    <!-- Vinyls Selection -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">{{ __('Select Vinyls (max 10)') }}</label>

                        <!-- Container dos vinyls selecionados -->
                        <div id="selected-vinyls" class="mt-2 space-y-2">
                            <!-- Os vinyls adicionados serão inseridos aqui pelo JS -->
                        </div>

                        <!-- Campo de busca -->
                        <div class="mt-4 relative">
                            <input type="text" id="vinyl-search" placeholder="{{ __('Search by title or artist...') }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            <!-- Container para os resultados da busca -->
                            <div id="vinyl-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Cancelar e Enviar -->
            <div class="flex items-center justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                    {{ __('Create Playlist') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Script em JavaScript Vanilla -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos do formulário
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const toggleSlugButton = document.getElementById('toggle-slug');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');

            // Elementos da busca de vinyls
            const vinylSearchInput = document.getElementById('vinyl-search');
            const vinylResultsContainer = document.getElementById('vinyl-results');
            const selectedVinylsContainer = document.getElementById('selected-vinyls');

            const maxVinyls = 10;
            let selectedVinyls = [];
            let isSlugEditable = false;

            // Função para gerar slug a partir do nome
            function generateSlug(value) {
                if (!isSlugEditable) {
                    let slug = value.toLowerCase()
                        .replace(/[^\w\s-]/g, '')         // Remove caracteres especiais
                        .replace(/\s+/g, '-')             // Substitui espaços por hífens
                        .replace(/-+/g, '-')              // Evita hífens repetidos
                        .replace(/^-+|-+$/g, '');          // Remove hífens do início e do fim
                    slugInput.value = slug;
                }
            }

            // Atualiza o slug conforme o usuário digita o nome
            if (nameInput) {
                nameInput.addEventListener('input', function(e) {
                    generateSlug(e.target.value);
                });
            }

            // Alterna o modo de edição manual do slug
            if (toggleSlugButton) {
                toggleSlugButton.addEventListener('click', function() {
                    isSlugEditable = !isSlugEditable;
                    slugInput.readOnly = !isSlugEditable;
                    toggleSlugButton.textContent = isSlugEditable ? 'Bloquear' : 'Editar';
                    slugInput.classList.toggle('bg-gray-50', isSlugEditable);
                    slugInput.classList.toggle('bg-gray-100', !isSlugEditable);
                });
            }

            // Preview da imagem de perfil
            if (imageInput) {
                imageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file && imagePreview) {
                        imagePreview.style.display = 'block';
                        imagePreview.src = URL.createObjectURL(file);
                    }
                });
            }

            // Controle do debounce na busca
            let searchTimeout = null;

            // Busca os vinyls conforme o usuário digita
            if (vinylSearchInput) {
                vinylSearchInput.addEventListener('input', function(e) {
                    const query = e.target.value;
                    clearTimeout(searchTimeout);
                    if (query.length < 3) {
                        vinylResultsContainer.innerHTML = '';
                        return;
                    }
                    searchTimeout = setTimeout(() => {
                        fetch(`/admin/vinyls/search?q=${encodeURIComponent(query)}`)
                            .then(response => {
                                if (!response.ok) throw new Error('Erro na requisição');
                                return response.json();
                            })
                            .then(data => {
                                displayVinylResults(data);
                            })
                            .catch(err => {
                                console.error('Erro na busca de vinyls:', err);
                                vinylResultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Nenhum resultado encontrado</div>';
                            });
                    }, 300);
                });
            }

            function displayVinylResults(results) {
    if (!results || results.length === 0) {
        vinylResultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Nenhum resultado encontrado</div>';
        return;
    }
    let html = '<ul class="max-h-60 overflow-auto py-1">';
    results.forEach(result => {
        // Extrai o nome do artista (primeiro, se houver)
        let artistName = "";
        if (result.artists && result.artists.length) {
            artistName = result.artists[0].name;
        }
        // Extrai o id do vinylSec (se disponível)
        let vinylSecId = "";
        if (result.vinylSec && result.vinylSec.id) {
            vinylSecId = result.vinylSec.id;
        }
        html += `<li class="flex items-center justify-between px-4 py-2 hover:bg-gray-100">
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900">${result.title}</span>
                        <span class="text-sm text-gray-500">${artistName}</span>
                    </div>
                    <button type="button" class="add-vinyl-btn text-green-600 hover:text-green-800"
                        data-master="${result.id}"
                        data-sec="${vinylSecId}"
                        data-title="${result.title}"
                        data-artist="${artistName}">
                        Adicionar
                    </button>
                </li>`;
    });
    html += '</ul>';
    vinylResultsContainer.innerHTML = html;

    // Vincula os eventos para os botões de adicionar
    document.querySelectorAll('.add-vinyl-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const vinyl = {
                vinyl_master_id: btn.getAttribute('data-master'),
                vinyl_sec_id: btn.getAttribute('data-sec'),
                title: btn.getAttribute('data-title'),
                artist: btn.getAttribute('data-artist')
            };
            addVinyl(vinyl);
            vinylResultsContainer.innerHTML = ''; // Limpa os resultados
            vinylSearchInput.value = '';
        });
    });
}

            // Adiciona um vinyl à lista, evitando duplicatas
            function addVinyl(vinyl) {
                if (selectedVinyls.some(v => v.vinyl_master_id === vinyl.vinyl_master_id && v.vinyl_sec_id === vinyl.vinyl_sec_id)) {
                    alert('Este vinyl já foi adicionado.');
                    return;
                }
                if (selectedVinyls.length >= maxVinyls) {
                    alert('Máximo de 10 vinyls permitidos');
                    return;
                }
                selectedVinyls.push(vinyl);
                updateSelectedVinyls();
            }

            // Remove um vinyl da lista
            function removeVinyl(index) {
                selectedVinyls.splice(index, 1);
                updateSelectedVinyls();
            }

            // Atualiza a exibição dos vinyls selecionados e os inputs ocultos
            function updateSelectedVinyls() {
                let html = '';
                selectedVinyls.forEach((vinyl, index) => {
                    html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="block text-sm text-gray-900">${vinyl.title}</span>
                                    <span class="block text-xs text-gray-500">${vinyl.artist}</span>
                                </div>
                                <button type="button" class="remove-vinyl-btn text-red-600 hover:text-red-900" data-index="${index}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="vinyls[${index}][vinyl_master_id]" value="${vinyl.vinyl_master_id}">
                                <input type="hidden" name="vinyls[${index}][vinyl_sec_id]" value="${vinyl.vinyl_sec_id}">
                            </div>`;
                });
                selectedVinylsContainer.innerHTML = html;

                // Vincula os eventos aos botões "Remover"
                document.querySelectorAll('.remove-vinyl-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(btn.getAttribute('data-index'));
                        removeVinyl(index);
                    });
                });
            }
        });
    </script>
@endpush
