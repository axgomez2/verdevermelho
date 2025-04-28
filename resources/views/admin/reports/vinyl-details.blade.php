@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Detalhes de Visualizações: {{ $vinyl->title }}</h1>
        <a href="{{ route('admin.reports.most-viewed') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
            Voltar
        </a>
    </div>

    <!-- Informações do disco -->
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row">
            @if($vinyl->cover_image)
            <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-6">
                <img class="h-48 w-48 rounded-md object-cover" src="{{ Storage::url($vinyl->cover_image) }}" alt="{{ $vinyl->title }}">
            </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $vinyl->title }}</h2>
                <p class="text-lg text-gray-700 mb-2">{{ $vinyl->artists->pluck('name')->implode(', ') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Total de visualizações</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalViews, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Usuários logados</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($userViewsCount, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Visitantes</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($guestViewsCount, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex space-x-3">
                    <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Editar Disco
                    </a>
                    <a href="{{ url('/'.$vinyl->artists->first()->slug.'/'.$vinyl->slug) }}" target="_blank" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                        Ver no Site
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de visualizações dos últimos 30 dias -->
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Visualizações nos últimos 30 dias</h2>
        <div style="height: 300px;">
            <canvas id="dailyViewsChart"></canvas>
        </div>
    </div>

    <!-- Usuários que mais visualizaram -->
    @if($userViews->count() > 0)
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Usuários que mais visualizaram</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Usuário</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Visualizações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userViews as $user)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-semibold">{{ number_format($user->view_count, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dailyViewsChart').getContext('2d');
    
    // Preparar dados para o gráfico
    const dates = [];
    const counts = [];
    
    @foreach($dailyViews as $view)
        dates.push('{{ $view->date }}');
        counts.push({{ $view->count }});
    @endforeach
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Visualizações',
                data: counts,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} visualizações`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
