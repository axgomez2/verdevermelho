@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gerenciamento de Etiquetas de Envio</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Pedido #</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Endereço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                        <td>
                                            @if($order->address)
                                                {{ $order->address->street }}, {{ $order->address->number }}<br>
                                                {{ $order->address->city }}/{{ $order->address->state }} - CEP: {{ $order->address->zip_code }}
                                            @else
                                                <span class="text-danger">Endereço não encontrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->shipping_label)
                                                <span class="badge badge-success">Etiqueta Gerada</span>
                                            @else
                                                <span class="badge badge-warning">Aguardando Etiqueta</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$order->shipping_label)
                                                <form action="{{ route('admin.shipping.generate-label', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-tag"></i> Gerar Etiqueta
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.shipping.print-label', $order) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-print"></i> Imprimir Etiqueta
                                                </a>

                                                <a href="{{ route('admin.shipping.track', $order) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-truck"></i> Rastrear
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum pedido aguardando etiqueta de envio.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
