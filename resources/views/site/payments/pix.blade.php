@extends('layouts.site')

@section('title', 'Pagamento PIX')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2 class="h4 mb-0">Pagamento por PIX</h2>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h4>Instruções</h4>
                        <p>Escaneie o QR Code abaixo com o aplicativo do seu banco para realizar o pagamento via PIX.</p>
                        <ul>
                            <li>O pagamento será confirmado em poucos minutos.</li>
                            <li>O seu pedido será processado após a confirmação do pagamento.</li>
                            <li>O QR Code tem validade de 30 minutos.</li>
                        </ul>
                    </div>

                    <div class="text-center mb-4">
                        <div class="qrcode-container p-3 d-inline-block border rounded mx-auto">
                            <img src="{{ $pixQrCode }}" alt="QR Code PIX" class="img-fluid" style="max-width: 250px;">
                        </div>

                        <p class="mt-3 text-muted">Escaneie este QR Code para pagar</p>
                        
                        <div class="mt-4">
                            <h5>Código PIX para copiar e colar</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="pixCode" value="{{ $pixCode }}" readonly>
                                <button class="btn btn-outline-success" type="button" onclick="copyPixCode()">Copiar</button>
                            </div>
                            <small class="text-muted">Cole este código no aplicativo do seu banco</small>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h4>Resumo do Pedido</h4>
                        <p><strong>Número do Pedido:</strong> #{{ $order->id }}</p>
                        <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Total:</strong> R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                        <p><strong>Status:</strong> {{ $order->status }}</p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('site.orders.show', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i> Ver Detalhes do Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyPixCode() {
        const pixCodeInput = document.getElementById('pixCode');
        pixCodeInput.select();
        document.execCommand('copy');
        
        // Mostrar feedback ao usuário
        const copyButton = pixCodeInput.nextElementSibling;
        const originalText = copyButton.textContent;
        copyButton.textContent = 'Copiado!';
        copyButton.classList.remove('btn-outline-success');
        copyButton.classList.add('btn-success');
        
        setTimeout(() => {
            copyButton.textContent = originalText;
            copyButton.classList.remove('btn-success');
            copyButton.classList.add('btn-outline-success');
        }, 2000);
    }
    
    // Verificar status do pagamento periodicamente
    function checkPaymentStatus() {
        const orderId = {{ $order->id }};
        const pixId = '{{ $pixId }}';
        
        if (!pixId) return;
        
        fetch(`/pagamentos/check-pix-status/${orderId}/${pixId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'paid') {
                    // Redirecionar para a página do pedido
                    window.location.href = `/minha-conta/pedidos/${orderId}?payment_success=true`;
                }
            })
            .catch(error => console.error('Erro ao verificar status:', error));
    }
    
    // Verificar a cada 30 segundos
    setInterval(checkPaymentStatus, 30000);
    
    // Verificar na carga da página
    document.addEventListener('DOMContentLoaded', checkPaymentStatus);
</script>
@endpush

@endsection
