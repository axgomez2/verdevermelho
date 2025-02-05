@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detalhes do Disco: {{ $vinyl->title }}</h1>
        <a href="{{ route('admin.vinyls.index') }}" class="btn btn-secondary">Voltar para Lista</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Informações Básicas</h2>
                <p><strong>Título:</strong> {{ $vinyl->title }}</p>
                <p><strong>Artistas:</strong>
                    @foreach($vinyl->artists as $artist)
                        {{ $artist->name }}@if(!$loop->last), @endif
                    @endforeach
                </p>
                <p><strong>Gêneros:</strong>
                    @foreach($vinyl->genres as $genre)
                        {{ $genre->name }}@if(!$loop->last), @endif
                    @endforeach
                </p>
                <p><strong>Estilos:</strong>
                    @foreach($vinyl->styles as $style)
                        {{ $style->name }}@if(!$loop->last), @endif
                    @endforeach
                </p>
                <p><strong>Gravadora:</strong> {{ $vinyl->recordLabel->name ?? 'N/A' }}</p>
                <p><strong>Ano de Lançamento:</strong> {{ $vinyl->release_year }}</p>
                <p><strong>Descrição:</strong> {{ $vinyl->description }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Detalhes do Produto</h2>
                <p><strong>Peso:</strong> {{ $vinyl->vinylSec->weight->name ?? 'N/A' }}</p>
                <p><strong>Dimensão:</strong> {{ $vinyl->vinylSec->dimension->name ?? 'N/A' }}</p>
                <p><strong>Quantidade em Estoque:</strong> {{ $vinyl->vinylSec->quantity ?? 0 }}</p>
                <p><strong>Preço:</strong> R$ {{ number_format($vinyl->vinylSec->price ?? 0, 2, ',', '.') }}</p>
                <p><strong>Preço de Compra:</strong> R$ {{ number_format($vinyl->vinylSec->buy_price ?? 0, 2, ',', '.') }}</p>
                <p><strong>Preço Promocional:</strong> R$ {{ number_format($vinyl->vinylSec->promotional_price ?? 0, 2, ',', '.') }}</p>
                <p><strong>Em Promoção:</strong> {{ $vinyl->vinylSec->is_promotional ? 'Sim' : 'Não' }}</p>
                <p><strong>Em Estoque:</strong> {{ $vinyl->vinylSec->in_stock ? 'Sim' : 'Não' }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Estatísticas</h2>
                <p><strong>Cliques no Card:</strong> {{ $cardClicks }}</p>
                <p><strong>Na Wishlist de:</strong> {{ $wishlistCount }} pessoa(s)</p>
                @if(!$vinyl->vinylSec->in_stock)
                    <p><strong>Na Want List de:</strong> {{ $wantListCount }} pessoa(s)</p>
                @endif
                <p><strong>Em Carrinhos Incompletos:</strong> {{ $incompleteCartsCount }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Faixas</h2>
                <ol class="list-decimal list-inside">
                    @foreach($vinyl->tracks as $track)
                        <li>{{ $track->title }} - {{ $track->duration }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end space-x-4">
        <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}" class="btn btn-primary">Editar Disco</a>
        <form action="{{ route('admin.vinyls.destroy', $vinyl->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este disco?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-error">Excluir Disco</button>
        </form>
    </div>
</div>
@endsection
