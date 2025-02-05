@props(['vinyl'])

<tr class="border border-slate-700 rounded-lg overflow-hidden {{ !$vinyl->vinylSec ? 'bg-red-100' : '' }}">
    <td>
        <div class="avatar">
            <div class="w-16 h-16 rounded">
                <img src="{{ $vinyl->cover_image ? asset('storage/' . $vinyl->cover_image) : asset('assets/images/placeholder.jpg') }}" alt="Cover" />
            </div>
        </div>
    </td>
    <td class="border border-slate-700 rounded-lg overflow-hidden">
        <div class="font-bold">{{ $vinyl->artists->pluck('name')->join(', ') }}</div>
        <div class="text-sm ">{{ $vinyl->title }}</div>
        @php
            $totalTracks = $vinyl->tracks->count();
            $tracksWithYoutube = $vinyl->tracks->whereNotNull('youtube_url')->count();
            $allTracksHaveYoutube = $totalTracks > 0 && $totalTracks === $tracksWithYoutube;
        @endphp

        <div class="badge {{ $allTracksHaveYoutube ? 'badge-success' : 'badge-error' }} mt-2">
            Faixas: {{ $totalTracks }} ({{ $tracksWithYoutube }} com YouTube)
        </div>
    </td>
    <td class="border border-slate-700 rounded-lg overflow-hidden">R$ {{ $vinyl->vinylSec->price ?? '--' }}</td>
    <td>R$ {{ $vinyl->vinylSec->promotional_price ?? '--' }}</td>
    <td class="border border-slate-700 rounded-lg overflow-hidden">{{ $vinyl->release_year }}</td>
    <td>{{ $vinyl->vinylSec->quantity ?? '0' }}</td>
    <td class="border border-slate-700 rounded-lg overflow-hidden">
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
            <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <a href="{{ route('admin.vinyls.edit-tracks', $vinyl->id) }}" class="btn btn-sm btn-accent">Faixas</a>
            @if($vinyl->vinylSec)
                <a href="{{ route('admin.vinyl.images', $vinyl->id) }}" class="btn btn-sm btn-success">Imagens</a>
            @else
                <a href="{{ route('admin.vinyls.complete', $vinyl->id) }}" class="btn btn-sm btn-warning">Completar</a>
            @endif
            @if($vinyl->vinylSec)
            <a href="{{ route('admin.vinyls.show', $vinyl->id) }}" class="btn btn-sm btn-info">Ver</a>
            @else
            <a href="#" class="btn btn-sm btn-info">N/A</a>
            @endif
            <form action="{{ route('admin.vinyls.destroy', $vinyl->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esse disco?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-error">Excluir</button>
            </form>
        </div>
    </td>
</tr>


