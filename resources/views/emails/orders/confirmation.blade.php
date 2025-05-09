<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pedido #{{ $data['order']->reference }}</title>
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
            background-color: #dcfce7;
            border-radius: 6px;
            color: #166534;
            font-weight: bold;
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
        
        /* Informações do pedido */
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
        
        /* Tabela de produtos */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .products-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 14px;
            color: #4b5563;
        }
        
        .products-table td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        /* Resumo de valores */
        .summary {
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        
        .summary-label {
            font-size: 14px;
            color: #6b7280;
        }
        
        .summary-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: bold;
        }
        
        .total-row {
            padding: 10px 0;
            border-top: 2px solid #f3f4f6;
            margin-top: 10px;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .total-value {
            font-size: 16px;
            font-weight: bold;
            color: #166534;
        }
        
        /* Botão de rastreamento */
        .tracking-button {
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
            <h1>Obrigado pela sua compra!</h1>
        </div>
        
        <div class="order-status">
            Pagamento Confirmado
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
                    <div class="order-info-label">Status</div>
                    <div class="order-info-value">Pago</div>
                </div>
            </div>
            
            <div class="order-info">
                <div class="order-info-item">
                    <div class="order-info-label">Nome</div>
                    <div class="order-info-value">{{ $data['customer']->name }}</div>
                </div>
                
                <div class="order-info-item">
                    <div class="order-info-label">Email</div>
                    <div class="order-info-value">{{ $data['customer']->email }}</div>
                </div>
                
                <div class="order-info-item">
                    <div class="order-info-label">Forma de Pagamento</div>
                    <div class="order-info-value">{{ $data['payment']['method'] }}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>Produtos Adquiridos</h2>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['items'] as $item)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                @if($item->product && $item->product->images->count() > 0)
                                <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" class="product-image" alt="{{ $item->product_name }}">
                                @endif
                                <div style="margin-left: 10px;">
                                    <div style="font-weight: bold;">{{ $item->product_name }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">
                                        @if($item->product && $item->product->artist)
                                        {{ $item->product->artist }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="summary">
                <div class="summary-row">
                    <div class="summary-label">Subtotal</div>
                    <div class="summary-value">R$ {{ number_format($data['order']->subtotal, 2, ',', '.') }}</div>
                </div>
                
                <div class="summary-row">
                    <div class="summary-label">Frete: {{ $data['shipping']['method'] }}</div>
                    <div class="summary-value">R$ {{ number_format($data['shipping']['price'], 2, ',', '.') }}</div>
                </div>
                
                @if($data['order']->discount > 0)
                <div class="summary-row">
                    <div class="summary-label">Desconto</div>
                    <div class="summary-value">- R$ {{ number_format($data['order']->discount, 2, ',', '.') }}</div>
                </div>
                @endif
                
                <div class="summary-row total-row">
                    <div class="total-label">Total</div>
                    <div class="total-value">R$ {{ number_format($data['order']->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>Endereço de Entrega</h2>
            <div style="font-size: 14px; line-height: 1.6;">
                <p>
                    <strong>{{ $data['shipping']['address']->type }}</strong><br>
                    {{ $data['shipping']['address']->street }}, {{ $data['shipping']['address']->number }}
                    @if($data['shipping']['address']->complement)
                    - {{ $data['shipping']['address']->complement }}
                    @endif
                    <br>
                    {{ $data['shipping']['address']->neighborhood }}, {{ $data['shipping']['address']->city }}/{{ $data['shipping']['address']->state }}<br>
                    CEP: {{ substr_replace($data['shipping']['address']->zip_code, '-', 5, 0) }}
                </p>
            </div>
        </div>
        
        <a href="{{ route('site.customer.orders.show', $data['order']->id) }}" class="tracking-button">
            Acompanhar Pedido
        </a>
        
        <div class="section">
            <h2>Precisa de Ajuda?</h2>
            <p style="font-size: 14px; line-height: 1.6;">
                Se você tiver alguma dúvida sobre seu pedido, entre em contato conosco:<br>
                Email: <a href="mailto:{{ $data['store']['email'] }}" style="color: #4f46e5;">{{ $data['store']['email'] }}</a><br>
                Telefone: {{ $data['store']['phone'] }}
            </p>
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
