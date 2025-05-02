@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Relatórios</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total de visualizações -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium text-gray-900">Total de visualizações</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalViews, 0, ',', '.') }}</p>
        </div>
        
        <!-- Discos únicos visualizados -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium text-gray-900">Discos visualizados</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($uniqueVinyls, 0, ',', '.') }}</p>
        </div>
        
        <!-- Usuários únicos -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium text-gray-900">Usuários</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($uniqueUsers, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Links de relatórios detalhados -->
    <div class="mb-6">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Relatórios disponíveis</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.most-viewed') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                    <span class="flex-1 ml-3 whitespace-nowrap">Discos mais vistos</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-gray-500 bg-gray-200 rounded">Detalhado</span>
                </a>
                <a href="{{ route('admin.newsletter.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                    <span class="flex-1 ml-3 whitespace-nowrap">Mailing / Newsletter</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-blue-500 bg-blue-100 rounded">Gerenciamento</span>
                </a>
                <a href="{{ route('admin.newsletter.compose') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
                    <span class="flex-1 ml-3 whitespace-nowrap">Envio de E-mails em Massa</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 ml-3 text-xs font-medium text-green-500 bg-green-100 rounded">Marketing</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Top 10 discos mais vistos -->
    <div class="mb-6">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Top 10 discos mais vistos</h2>
                <a href="{{ route('admin.reports.most-viewed') }}" class="text-sm text-blue-600 hover:underline">Ver todos</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Disco</th>
                            <th scope="col" class="px-6 py-3">Visualizações</th>
                            <th scope="col" class="px-6 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topVinyls as $vinyl)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 flex items-center">
                                @if($vinyl->cover_image)
                                <div class="flex-shrink-0 h-10 w-10 mr-3">
                                    <img class="h-10 w-10 rounded-sm object-cover" src="{{ Storage::url($vinyl->cover_image) }}" alt="{{ $vinyl->title }}">
                                </div>
                                @endif
                                <span>{{ $vinyl->title }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-semibold">{{ number_format($vinyl->view_count, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.reports.vinyl-details', $vinyl->id) }}" class="font-medium text-blue-600 hover:underline">Detalhes</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico de visualizações por dia -->
    <div class="mb-6">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Visualizações nos últimos 30 dias</h2>
            <div style="height: 300px;">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('viewsChart').getContext('2d');
    
    const dateLabels = @json(array_keys($dateRange));
    const viewData = @json(array_values($dateRange));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'Visualizações',
                data: viewData,
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
