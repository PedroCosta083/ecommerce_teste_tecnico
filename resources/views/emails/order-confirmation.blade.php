<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #46685b 0%, #5a8270 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; color: #46685b; margin-bottom: 15px; }
        .order-details { background: #f9fafb; padding: 20px; margin: 25px 0; border-radius: 8px; border-left: 4px solid #46685b; }
        .order-details h3 { color: #46685b; margin-top: 0; font-size: 20px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { font-weight: 600; color: #6b7280; }
        .info-value { color: #111827; }
        .items-section { margin: 20px 0; }
        .items-section h4 { color: #46685b; margin-bottom: 15px; font-size: 16px; }
        .item { padding: 15px; background: white; margin-bottom: 10px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .item-name { font-weight: 600; color: #111827; font-size: 15px; margin-bottom: 5px; }
        .item-details { color: #6b7280; font-size: 14px; }
        .totals { margin-top: 25px; padding-top: 15px; border-top: 2px solid #e5e7eb; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 15px; }
        .total-final { font-size: 20px; font-weight: bold; color: #46685b; padding-top: 10px; border-top: 2px solid #46685b; margin-top: 10px; }
        .address-section { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin-top: 15px; }
        .address-section h4 { color: #46685b; margin-top: 0; margin-bottom: 10px; font-size: 16px; }
        .address-text { color: #374151; line-height: 1.8; }
        .footer-note { background: #fef3c7; padding: 15px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #f59e0b; }
        .footer { text-align: center; padding: 20px; background: #f9fafb; color: #6b7280; font-size: 13px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; }
        .status-processing { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Confirmação de Pedido</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Olá, {{ $order->user->name }}!</p>
            <p>Obrigado por seu pedido! Seu pedido <strong>#{{ $order->id }}</strong> foi recebido e está sendo processado.</p>
            
            <div class="order-details">
                <h3>Detalhes do Pedido</h3>
                
                <div class="info-row">
                    <span class="info-label">Número do Pedido:</span>
                    <span class="info-value">#{{ $order->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-processing">
                            @if($order->status === 'pending') Pendente
                            @elseif($order->status === 'processing') Processando
                            @elseif($order->status === 'shipped') Enviado
                            @elseif($order->status === 'delivered') Entregue
                            @else {{ ucfirst($order->status) }}
                            @endif
                        </span>
                    </span>
                </div>
                <div class="info-row" style="border-bottom: none;">
                    <span class="info-label">Data:</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="items-section">
                    <h4>Itens do Pedido:</h4>
                    @foreach($items as $item)
                    <div class="item">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">
                            Quantidade: {{ $item->quantity }} × R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                            = <strong>R$ {{ number_format($item->total_price, 2, ',', '.') }}</strong>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Taxa:</span>
                        <span>R$ {{ number_format($order->tax, 2, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Frete:</span>
                        <span>R$ {{ number_format($order->shipping_cost, 2, ',', '.') }}</span>
                    </div>
                    <div class="total-row total-final">
                        <span>Total:</span>
                        <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="address-section">
                    <h4>📍 Endereço de Entrega:</h4>
                    <div class="address-text">
                        @php
                            $address = is_string($order->shipping_address) 
                                ? json_decode($order->shipping_address, true) 
                                : $order->shipping_address;
                        @endphp
                        @if(is_array($address))
                            {{ $address['street'] ?? '' }}{{ isset($address['number']) ? ', ' . $address['number'] : '' }}<br>
                            @if(!empty($address['complement']))
                                {{ $address['complement'] }}<br>
                            @endif
                            {{ $address['neighborhood'] ?? '' }}<br>
                            {{ $address['city'] ?? '' }} - {{ $address['state'] ?? '' }}<br>
                            CEP: {{ $address['zip_code'] ?? '' }}
                        @else
                            {{ $address }}
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="footer-note">
                <strong>📦 Próximos passos:</strong><br>
                Enviaremos outro e-mail quando seu pedido for enviado com o código de rastreamento.
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} E-commerce Store. Todos os direitos reservados.</p>
            <p>Este é um e-mail automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>
