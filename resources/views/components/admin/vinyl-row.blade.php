@props(['vinyl'])

<tr class="{{ !$vinyl->vinylSec ? 'bg-red-50' : '' }}">
    <td>
        <div class="avatar">
            <div class="w-16 h-16 rounded">
                <img src="{{ $vinyl->cover_image ? asset('storage/' . $vinyl->cover_image) : asset('assets/images/placeholder.jpg') }}" alt="Cover" />
            </div>
        </div>
    </td>
    <td>
        <div class="font-bold">{{ $vinyl->artists->pluck('name')->join(', ') }}</div>
        <div class="text-sm opacity-50">{{ $vinyl->title }}</div>
        <div class="text-xs opacity-50">Faixas: {{ $vinyl->tracks->count() }} ({{ $vinyl->tracks->whereNotNull('youtube_url')->count() }} com YouTube)</div>
    </td>
    <td>R$ {{ $vinyl->vinylSec->price ?? '--' }}</td>
    <td>R$ {{ $vinyl->vinylSec->promotional_price ?? '--' }}</td>
    <td>{{ $vinyl->release_year }}</td>
    <td>{{ $vinyl->vinylSec->quantity ?? '0' }}</td>
    <td>
        <div class="flex flex-col space-y-2">
            <div class="form-control" x-data="toggleSwitch({{ $vinyl->id }}, 'is_promotional', {{ $vinyl->vinylSec && $vinyl->vinylSec->is_promotional ? 'true' : 'false' }})">
                <label class="cursor-pointer label">
                    <span class="label-text mr-2">Em promoção</span>
                    <input type="checkbox" class="toggle toggle-primary" x-model="checked" @change="toggle" />
                </label>
            </div>
            <div class="form-control" x-data="toggleSwitch({{ $vinyl->id }}, 'in_stock', {{ $vinyl->vinylSec && $vinyl->vinylSec->in_stock ? 'true' : 'false' }})">
                <label class="cursor-pointer label">
                    <span class="label-text mr-2">Em estoque</span>
                    <input type="checkbox" class="toggle toggle-primary" x-model="checked" @change="toggle" />
                </label>
            </div>
        </div>
    </td>
    <td>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}" class="btn btn-xs btn-primary">Editar</a>
            <a href="{{ route('admin.vinyls.edit-tracks', $vinyl->id) }}" class="btn btn-xs btn-secondary">Faixas</a>
            @if($vinyl->vinylSec)
                <a href="{{ route('admin.vinyl.images', $vinyl->id) }}" class="btn btn-xs btn-success">Imagens</a>
            @else
                <a href="{{ route('admin.vinyls.complete', $vinyl->id) }}" class="btn btn-xs btn-warning">Completar</a>
            @endif
            <a href="{{ route('admin.vinyls.show', $vinyl->id) }}" class="btn btn-xs btn-info">Ver</a>
            <form action="{{ route('admin.vinyls.destroy', $vinyl->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esse disco?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-error">Excluir</button>
            </form>
        </div>
    </td>
</tr>


