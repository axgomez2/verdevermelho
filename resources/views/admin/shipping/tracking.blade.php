@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rastreamento do Pedido #{{ $order->id }}</h3>
                    <a href="{{ route('admin.shipping.index') }}" class="btn btn-sm btn-outline-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($trackingInfo['error']))
                        <div class="alert alert-danger">
                            {{ $trackingInfo['error'] }}
                        </div>
                    @else
                        <div class="tracking-info">
                            <div class="mb-4">
                                <h5>Informações do Pedido</h5>
                                <p><strong>Cliente:</strong> {{ $order->user->name }}</p>
                                <p><strong>Código de Rastreamento:</strong> {{ $order->tracking_code }}</p>
                                <p><strong>Status Atual:</strong> {{ $trackingInfo['status'] ?? 'Não disponível' }}</p>
                            </div>

                            @if(isset($trackingInfo['tracking']))
                                <div class="timeline">
                                    @foreach($trackingInfo['tracking'] as $event)
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">{{ $event['status'] }}</h6>
                                                <p class="timeline-date">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y H:i') }}</p>
                                                <p>{{ $event['location'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Ainda não há informações de rastreamento disponíveis.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background-color: #3490dc;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #3490dc;
}

.timeline-content {
    padding: 10px;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.timeline-title {
    margin: 0;
    color: #2d3748;
}

.timeline-date {
    color: #718096;
    font-size: 0.875rem;
    margin: 5px 0;
}
</style>
@endsection
