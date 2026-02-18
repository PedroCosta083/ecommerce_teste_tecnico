<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .order-details { background: white; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .item { padding: 10px 0; border-bottom: 1px solid #eee; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Thank you for your order! Your order #{{ $order->id }} has been received and is being processed.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                
                <h4>Items:</h4>
                @foreach($items as $item)
                <div class="item">
                    <strong>{{ $item->product->name }}</strong><br>
                    Quantity: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                    = ${{ number_format($item->total_price, 2) }}
                </div>
                @endforeach
                
                <div style="margin-top: 20px;">
                    <p>Subtotal: ${{ number_format($order->subtotal, 2) }}</p>
                    <p>Tax: ${{ number_format($order->tax, 2) }}</p>
                    <p>Shipping: ${{ number_format($order->shipping_cost, 2) }}</p>
                    <p class="total">Total: ${{ number_format($order->total, 2) }}</p>
                </div>
                
                <h4>Shipping Address:</h4>
                <p>{{ $order->shipping_address }}</p>
            </div>
            
            <p>We'll send you another email when your order ships.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} E-commerce. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
