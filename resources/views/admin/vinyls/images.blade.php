@extends('layouts.admin')

@section('title', 'Gerenciar Imagens')

@section('content')
<div x-data="imageManager" class="container mx-auto px-4 py-8">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-6">
                Gerenciar imagens de: {{ $vinylMaster->title }}
            </h2>

            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.vinyl.images.store', $vinylMaster->id) }}" method="POST" enctype="multipart/form-data" class="mb-8">
                @csrf
                <div class="form-control w-full max-w-xs">
                    <label class="label" for="images">
                        <span class="label-text">Upload de Novas Imagens (Serão cortadas em quadrado)</span>
                    </label>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="file-input file-input-bordered w-full max-w-xs" />
                    @error('images')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                    @error('images.*')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-4">
                    Upload e Cortar Imagens
                </button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($images as $image)
                    <div class="card bg-base-100 shadow-xl">
                        <figure>
                            <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $image->file_name }}" class="w-full h-48 object-cover" />
                            </a>
                        </figure>
                        <div class="card-body p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold truncate">{{ $image->file_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $image->created_at->format('d/m/Y') }}</p>
                                </div>
                                <form action="{{ route('admin.vinyl.images.destroy', [$vinylMaster->id, $image->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-square btn-sm btn-error" @click.prevent="confirmDelete($event, '{{ $image->file_name }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-actions justify-end p-4">
            <a href="{{ route('admin.vinyls.index') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                Finalizar e voltar
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageManager', () => ({
        confirmDelete(event, imageName) {
            if (confirm(`Tem certeza que deseja excluir a imagem "${imageName}"?`)) {
                event.target.closest('form').submit();
            }
        }
    }));
});
</script>
@endpush
