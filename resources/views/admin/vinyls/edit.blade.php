@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Editar Disco: {{ $vinyl->title }}</h1>
        <a href="{{ route('admin.vinyls.show', $vinyl->id) }}" class="btn btn-secondary">Voltar para Detalhes</a>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <!-- Componente de Imagem -->
            <div x-data="{ showDiscogsModal: false, showUploadModal: false }">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Imagem do Disco</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <!-- Imagem Atual -->
                            <div class="mb-4">
                                @if($vinyl->cover_image)
                                    <img src="{{ Storage::url($vinyl->cover_image) }}" alt="{{ $vinyl->title }}" class="w-48 h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                                        <span class="text-gray-500">Sem imagem</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex space-x-2">
                                <button
                                    @click="showDiscogsModal = true"
                                    class="btn btn-secondary"
                                >
                                    Buscar Imagem do Discogs
                                </button>
                                <button
                                    @click="showUploadModal = true"
                                    class="btn btn-primary"
                                >
                                    Upload Manual
                                </button>
                                @if($vinyl->cover_image)
                                    <form action="{{ route('admin.vinyls.remove-image', $vinyl->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta imagem?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error">Remover Imagem</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Discogs -->
                <div x-cloak x-show="showDiscogsModal" class="modal modal-open">
                    <div class="modal-box">
                        <h3 class="font-bold text-lg">Buscar Imagem do Discogs</h3>
                        <p class="py-4">Deseja buscar a imagem do Discogs para este disco?</p>
                        <div class="modal-action">
                            <form action="{{ route('admin.vinyls.fetch-discogs-image', $vinyl->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Buscar Imagem</button>
                            </form>
                            <button @click="showDiscogsModal = false" class="btn">Cancelar</button>
                        </div>
                    </div>
                </div>

                <!-- Modal Upload -->
                <div x-cloak x-show="showUploadModal" class="modal modal-open">
                    <div class="modal-box">
                        <h3 class="font-bold text-lg">Upload Manual de Imagem</h3>
                        <form action="{{ route('admin.vinyls.upload-image', $vinyl->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-control">
                                <label class="label" for="image">
                                    <span class="label-text">Selecione uma imagem</span>
                                </label>
                                <input type="file" name="image" id="image" accept="image/*" class="file-input file-input-bordered w-full" required>
                            </div>
                            <div class="modal-action">
                                <button type="submit" class="btn btn-primary">Upload</button>
                                <button type="button" @click="showUploadModal = false" class="btn">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Formulário principal -->
            <form action="{{ route('admin.vinyls.update', $vinyl->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label" for="description">
                            <span class="label-text">Descrição</span>
                        </label>
                        <textarea id="description" name="description" rows="4" class="textarea textarea-bordered">{{ old('description', $vinyl->description) }}</textarea>
                    </div>

                    <div class="form-control">
                        <label class="label" for="weight_id">
                            <span class="label-text">Peso</span>
                        </label>
                        <select id="weight_id" name="weight_id" class="select select-bordered" required>
                            @foreach($weights as $weight)
                                <option value="{{ $weight->id }}" {{ (old('weight_id', $vinyl->vinylSec->weight_id ?? '') == $weight->id) ? 'selected' : '' }}>
                                    {{ $weight->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label" for="cat_style_shop_id">
                            <span class="label-text">Categoria de Estilo da Loja</span>
                        </label>
                        <select name="cat_style_shop_id" id="cat_style_shop_id" class="select select-bordered w-full">
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('cat_style_shop_id', $vinyl->vinylSec->cat_style_shop_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('cat_style_shop_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label" for="dimension_id">
                            <span class="label-text">Dimensão</span>
                        </label>
                        <select id="dimension_id" name="dimension_id" class="select select-bordered" required>
                            @foreach($dimensions as $dimension)
                                <option value="{{ $dimension->id }}" {{ (old('dimension_id', $vinyl->vinylSec->dimension_id ?? '') == $dimension->id) ? 'selected' : '' }}>
                                    {{ $dimension->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label" for="quantity">
                            <span class="label-text">Quantidade</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $vinyl->vinylSec->quantity ?? 0) }}" class="input input-bordered" required min="0">
                    </div>

                    <div class="form-control">
                        <label class="label" for="price">
                            <span class="label-text">Preço</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price', $vinyl->vinylSec->price ?? 0) }}" class="input input-bordered" required min="0" step="0.01">
                    </div>

                    <div class="form-control">
                        <label class="label" for="buy_price">
                            <span class="label-text">Preço de Compra</span>
                        </label>
                        <input type="number" id="buy_price" name="buy_price" value="{{ old('buy_price', $vinyl->vinylSec->buy_price ?? '') }}" class="input input-bordered" min="0" step="0.01">
                    </div>

                    <div class="form-control">
                        <label class="label" for="promotional_price">
                            <span class="label-text">Preço Promocional</span>
                        </label>
                        <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', $vinyl->vinylSec->promotional_price ?? '') }}" class="input input-bordered" min="0" step="0.01">
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Em Promoção</span>
                            <input type="checkbox" name="is_promotional" class="toggle toggle-primary" {{ (old('is_promotional', $vinyl->vinylSec->is_promotional ?? false)) ? 'checked' : '' }} value="1">
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Em Estoque</span>
                            <input type="checkbox" name="in_stock" class="toggle toggle-primary" {{ (old('in_stock', $vinyl->vinylSec->in_stock ?? false)) ? 'checked' : '' }} value="1">
                        </label>
                    </div>
                </div>

                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Atualizar Disco</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
