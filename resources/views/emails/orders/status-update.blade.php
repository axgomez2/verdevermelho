<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização do Pedido #{{ $data['order']->reference }}</title>
    <style>
        /* Reset CSS básico */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9fafb;
        }
        
        /* Container principal */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        
        /* Cabeçalho */
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .header img {
            max-width: 180px;
            height: auto;
        }
        
        /* Status do pedido */
        .order-status {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-processing {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        
        .status-shipped {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-canceled {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .status-refunded {
            background-color: #fef9c3;
            color: #854d0e;
        }
        
        /* Seções do e-mail */
        .section {
            margin-bottom: 30px;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 20px;
        }
        
        .section h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #1f2937;
        }
        
        /* Detalhes do pedido */
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .order-info-item {
            flex: 1;
            padding: 0 10px;
        }
        
        .order-info-label {
            font-weight: bold;
            font-size: 14px;
            color: #6b7280;
        }
        
        .order-info-value {
            font-size: 15px;
            color: #1f2937;
        }
        
        /* Descrição do status */
        .status-description {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 16px;
            color: #4b5563;
            border-left: 4px solid #6b7280;
        }
        
        /* Botão de ação */
        .action-button {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background-color: #4f46e5;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        /* Rodapé */
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #6b7280;
        }
        
        .social-icons {
            margin: 15px 0;
        }
        
        .social-icon {
            display: inline-block;
            margin: 0 5px;
            width: 32px;
            height: 32px;
        }
        
        /* Responsividade */
        @media screen and (max-width: 600px) {
            .order-info {
                flex-direction: column;
            }
            .order-info-item {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $data['store']['name'] }}">
            <h1>Atualização do seu Pedido</h1>
        </div>
        
        <div class="order-status status-{{ $data['status'] }}">
            {{ $data['statusText'] }}
        </div>
        
        <div class="section">
            <h2>Olá, {{ $data['customer']->name }}!</h2>
            <p>Seu pedido #{{ $data['order']->reference }} teve uma atualização de status.</p>
            
            <div class="status-description">
                {{ $data['description'] }}
            </div>
            
            @if($data['status'] == 'shipped' && $data['order']->tracking_code)
                <p><strong>Código de Rastreamento:</strong> {{ $data['order']->tracking_code }}</p>
                <p><strong>Transportadora:</strong> {{ $data['order']->shipping_company }}</p>
            @endif
        </div>
        
        <div class="section">
            <h2>Informações do Pedido</h2>
            <div class="order-info">
                <div class="order-info-item">
                    <div class="order-info-label">Número do Pedido</div>
                    <div class="order-info-value">#{{ $data['order']->reference }}</div>
                </div>
                
                <div class="order-info-item">
                    <div class="order-info-label">Data</div>
                    <div class="order-info-value">{{ $data['order']->created_at->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="order-info-item">
                    <div class="order-info-label">Total</div>
                    <div class="order-info-value">R$ {{ number_format($data['order']->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <a href="{{ route('site.customer.orders.show', $data['order']->id) }}" class="action-button">
            Ver Detalhes do Pedido
        </a>
        
        <div class="section">
            <h2>Precisa de Ajuda?</h2>
            <p>Se você tiver alguma dúvida sobre seu pedido, entre em contato conosco:</p>
            <p>Email: <a href="mailto:{{ $data['store']['email'] }}" style="color: #4f46e5;">{{ $data['store']['email'] }}</a></p>
            <p>Telefone: {{ $data['store']['phone'] }}</p>
        </div>
        
        <div class="footer">
            <div class="social-icons">
                <a href="#" class="social-icon"><img src="{{ asset('images/facebook-icon.png') }}" alt="Facebook"></a>
                <a href="#" class="social-icon"><img src="{{ asset('images/instagram-icon.png') }}" alt="Instagram"></a>
                <a href="#" class="social-icon"><img src="{{ asset('images/whatsapp-icon.png') }}" alt="WhatsApp"></a>
            </div>
            <p>&copy; {{ date('Y') }} {{ $data['store']['name'] }}. Todos os direitos reservados.</p>
            <p>
                Este e-mail foi enviado para {{ $data['customer']->email }}.<br>
                Por favor não responda a este e-mail, pois ele é enviado automaticamente.
            </p>
        </div>
    </div>
</body>
</html>
