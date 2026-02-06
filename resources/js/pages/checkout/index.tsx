import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, CreditCard, MapPin, Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useState } from 'react';

interface Product {
  id: number;
  name: string;
  price: string;
}

interface CheckoutItem {
  product: Product;
  quantity: number;
  subtotal: string;
}

interface Props {
  items: CheckoutItem[];
  subtotal: string;
  tax: string;
  shipping: string;
  total: string;
  directPurchase: boolean;
}

export default function CheckoutIndex({ items, subtotal, tax, shipping, total, directPurchase }: Props) {
  const [sameAddress, setSameAddress] = useState(true);
  
  const { data, setData, post, processing } = useForm({
    items: items.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity,
    })),
    shipping_address: {
      street: '',
      number: '',
      complement: '',
      neighborhood: '',
      city: '',
      state: '',
      zip_code: '',
    },
    billing_address: {
      street: '',
      number: '',
      complement: '',
      neighborhood: '',
      city: '',
      state: '',
      zip_code: '',
    },
    notes: '',
    direct_purchase: directPurchase,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    const submitData = {
      ...data,
      billing_address: sameAddress ? data.shipping_address : data.billing_address,
    };

    post('/checkout', {
      onSuccess: () => {
        // Redireciona para página de sucesso
      },
    });
  };

  return (
    <>
      <Head title="Finalizar Compra" />

      <div className="min-h-screen bg-gray-50">

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Button
          variant="ghost"
          size="sm"
          className="mb-6 cursor-pointer"
          onClick={() => router.visit('/')}
        >
          <ArrowLeft className="h-4 w-4 mr-2" />
          Voltar
        </Button>

        <h1 className="text-3xl font-bold mb-8">Finalizar Compra</h1>

        <form onSubmit={handleSubmit}>
          <div className="grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <MapPin className="h-5 w-5" />
                    Endereço de Entrega
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-4 gap-4">
                    <div className="col-span-3">
                      <Label>Rua</Label>
                      <Input
                        value={data.shipping_address.street}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, street: e.target.value })}
                        required
                      />
                    </div>
                    <div>
                      <Label>Número</Label>
                      <Input
                        value={data.shipping_address.number}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, number: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <div>
                    <Label>Complemento</Label>
                    <Input
                      value={data.shipping_address.complement}
                      onChange={(e) => setData('shipping_address', { ...data.shipping_address, complement: e.target.value })}
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Bairro</Label>
                      <Input
                        value={data.shipping_address.neighborhood}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, neighborhood: e.target.value })}
                        required
                      />
                    </div>
                    <div>
                      <Label>CEP</Label>
                      <Input
                        value={data.shipping_address.zip_code}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, zip_code: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Cidade</Label>
                      <Input
                        value={data.shipping_address.city}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, city: e.target.value })}
                        required
                      />
                    </div>
                    <div>
                      <Label>Estado</Label>
                      <Input
                        value={data.shipping_address.state}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, state: e.target.value })}
                        maxLength={2}
                        required
                      />
                    </div>
                  </div>

                  <div className="flex items-center gap-2">
                    <input
                      type="checkbox"
                      id="sameAddress"
                      checked={sameAddress}
                      onChange={(e) => setSameAddress(e.target.checked)}
                      className="cursor-pointer"
                    />
                    <Label htmlFor="sameAddress" className="cursor-pointer">
                      Endereço de cobrança é o mesmo
                    </Label>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Observações</CardTitle>
                </CardHeader>
                <CardContent>
                  <Textarea
                    placeholder="Alguma observação sobre o pedido?"
                    value={data.notes}
                    onChange={(e) => setData('notes', e.target.value)}
                    rows={3}
                  />
                </CardContent>
              </Card>
            </div>

            <div className="space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Package className="h-5 w-5" />
                    Resumo do Pedido
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {items.map((item, index) => (
                    <div key={index} className="flex justify-between text-sm">
                      <div>
                        <p className="font-medium">{item.product.name}</p>
                        <p className="text-gray-500">Qtd: {item.quantity}</p>
                      </div>
                      <p className="font-semibold">R$ {parseFloat(item.subtotal).toFixed(2)}</p>
                    </div>
                  ))}

                  <div className="border-t pt-4 space-y-2">
                    <div className="flex justify-between text-sm">
                      <span>Subtotal</span>
                      <span>R$ {parseFloat(subtotal).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span>Impostos</span>
                      <span>R$ {parseFloat(tax).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span>Frete</span>
                      <span>R$ {parseFloat(shipping).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-lg font-bold border-t pt-2">
                      <span>Total</span>
                      <span className="text-green-600">R$ {parseFloat(total).toFixed(2)}</span>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <CreditCard className="h-5 w-5" />
                    Pagamento
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-sm text-gray-600 mb-4">
                    Pagamento será processado na entrega
                  </p>
                  <Button
                    type="submit"
                    className="w-full cursor-pointer"
                    size="lg"
                    disabled={processing}
                  >
                    {processing ? 'Processando...' : 'Finalizar Pedido'}
                  </Button>
                </CardContent>
              </Card>
            </div>
          </div>
        </form>
      </div>
      </div>
    </>
  );
}
