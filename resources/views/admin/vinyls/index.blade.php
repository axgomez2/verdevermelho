@extends('layouts.admin')

@section('title', 'Vinyls')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['title' => 'Todos os discos', 'url' => route('admin.vinyls.index')]
    ]" />
@endsection

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6">
                <h2 class="card-title text-2xl">Todos os Discos</h2>
                <a href="{{ route('admin.vinyls.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Adicionar novo disco
                </a>
            </div>

            @if($vinyls->isEmpty())
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum disco cadastrado</h3>
                    <p class="text-gray-600">Vamos começar?</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Informações do Disco</th>
                                <th>Preço de venda</th>
                                <th>Preço promocional</th>
                                <th>Ano</th>
                                <th>Estoque</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vinyls as $vinyl)
                                <x-admin.vinyl-row :vinyl="$vinyl" />
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $vinyls->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toggleSwitch', (vinylId, field, initialState) => ({
        checked: initialState,
        async toggle() {
            try {
                const response = await fetch('{{ route('admin.vinyls.updateField') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: vinylId, field: field, value: this.checked ? 1 : 0 })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                if (data.success) {
                    console.log('Update successful');
                    // Optionally show a success message
                    alert('Atualizado com sucesso!');
                } else {
                    throw new Error('Update failed');
                }
            } catch (error) {
                console.error('Error:', error);
                this.checked = !this.checked;
                alert('Ocorreu um erro. Por favor, tente novamente.');
            }
        }
    }));
});
</script>
@endpush

