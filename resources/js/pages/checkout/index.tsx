import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, CreditCard, MapPin, Package } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
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
  const [loadingCep, setLoadingCep] = useState(false);
  
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
    payment_method: 'credit_card',
    card_number: '',
    card_name: '',
    card_expiry: '',
    card_cvv: '',
    notes: '',
    direct_purchase: directPurchase,
  });

  const fetchAddressByCep = async (cep: string) => {
    const cleanCep = cep.replace(/\D/g, '');
    
    if (cleanCep.length !== 8) return;
    
    setLoadingCep(true);
    try {
      const response = await fetch(`https://viacep.com.br/ws/${cleanCep}/json/`);
      const addressData = await response.json();
      
      if (!addressData.erro) {
        setData('shipping_address', {
          ...data.shipping_address,
          zip_code: cep,
          street: addressData.logradouro || '',
          neighborhood: addressData.bairro || '',
          city: addressData.localidade || '',
          state: addressData.uf || '',
        });
      }
    } catch (error) {
      console.error('Erro ao buscar CEP:', error);
    } finally {
      setLoadingCep(false);
    }
  };

  const handleCepChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newCep = e.target.value;
    setData('shipping_address', { ...data.shipping_address, zip_code: newCep });
    
    if (newCep.replace(/\D/g, '').length === 8) {
      fetchAddressByCep(newCep);
    }
  };

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

      <div className="min-h-screen bg-background">

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

        <h1 className="text-3xl font-bold mb-8 text-foreground">Finalizar Compra</h1>

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
                      <Label>CEP</Label>
                      <MaskedInput
                        mask="99999-999"
                        value={data.shipping_address.zip_code}
                        onChange={handleCepChange}
                        placeholder="00000-000"
                        disabled={loadingCep}
                        required
                      />
                      {loadingCep && <p className="text-xs text-muted-foreground mt-1">Buscando endereço...</p>}
                    </div>
                    <div>
                      <Label>Bairro</Label>
                      <Input
                        value={data.shipping_address.neighborhood}
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, neighborhood: e.target.value })}
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
                        onChange={(e) => setData('shipping_address', { ...data.shipping_address, state: e.target.value.toUpperCase() })}
                        maxLength={2}
                        placeholder="SP"
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

              {!sameAddress && (
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <MapPin className="h-5 w-5" />
                      Endereço de Cobrança
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <div className="grid grid-cols-4 gap-4">
                      <div className="col-span-3">
                        <Label>Rua</Label>
                        <Input
                          value={data.billing_address.street}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, street: e.target.value })}
                          required
                        />
                      </div>
                      <div>
                        <Label>Número</Label>
                        <Input
                          value={data.billing_address.number}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, number: e.target.value })}
                          required
                        />
                      </div>
                    </div>

                    <div>
                      <Label>Complemento</Label>
                      <Input
                        value={data.billing_address.complement}
                        onChange={(e) => setData('billing_address', { ...data.billing_address, complement: e.target.value })}
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label>CEP</Label>
                        <MaskedInput
                          mask="99999-999"
                          value={data.billing_address.zip_code}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, zip_code: e.target.value })}
                          placeholder="00000-000"
                          required
                        />
                      </div>
                      <div>
                        <Label>Bairro</Label>
                        <Input
                          value={data.billing_address.neighborhood}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, neighborhood: e.target.value })}
                          required
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label>Cidade</Label>
                        <Input
                          value={data.billing_address.city}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, city: e.target.value })}
                          required
                        />
                      </div>
                      <div>
                        <Label>Estado</Label>
                        <Input
                          value={data.billing_address.state}
                          onChange={(e) => setData('billing_address', { ...data.billing_address, state: e.target.value.toUpperCase() })}
                          maxLength={2}
                          placeholder="SP"
                          required
                        />
                      </div>
                    </div>
                  </CardContent>
                </Card>
              )}

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

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <CreditCard className="h-5 w-5" />
                    Pagamento (Simulação)
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label>Método de Pagamento</Label>
                    <select
                      className="w-full border border-input bg-background rounded-md p-2 text-foreground"
                      value={data.payment_method}
                      onChange={(e) => setData('payment_method', e.target.value)}
                    >
                      <option value="credit_card">Cartão de Crédito</option>
                      <option value="debit_card">Cartão de Débito</option>
                      <option value="pix">PIX</option>
                      <option value="boleto">Boleto</option>
                    </select>
                  </div>

                  {(data.payment_method === 'credit_card' || data.payment_method === 'debit_card') && (
                    <>
                      <div>
                        <Label>Número do Cartão</Label>
                        <MaskedInput
                          mask="9999 9999 9999 9999"
                          value={data.card_number}
                          onChange={(e) => setData('card_number', e.target.value)}
                          placeholder="0000 0000 0000 0000"
                          required
                        />
                      </div>
                      <div>
                        <Label>Nome no Cartão</Label>
                        <Input
                          value={data.card_name}
                          onChange={(e) => setData('card_name', e.target.value.toUpperCase())}
                          placeholder="NOME COMPLETO"
                          required
                        />
                      </div>
                      <div className="grid grid-cols-2 gap-4">
                        <div>
                          <Label>Validade</Label>
                          <MaskedInput
                            mask="99/99"
                            value={data.card_expiry}
                            onChange={(e) => setData('card_expiry', e.target.value)}
                            placeholder="MM/AA"
                            required
                          />
                        </div>
                        <div>
                          <Label>CVV</Label>
                          <MaskedInput
                            mask="999"
                            value={data.card_cvv}
                            onChange={(e) => setData('card_cvv', e.target.value)}
                            placeholder="000"
                            required
                          />
                        </div>
                      </div>
                    </>
                  )}

                  {data.payment_method === 'pix' && (
                    <div className="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-lg p-4">
                      <p className="text-sm text-blue-800 dark:text-blue-300">
                        Após finalizar, você receberá um código PIX para pagamento.
                      </p>
                    </div>
                  )}

                  {data.payment_method === 'boleto' && (
                    <div className="bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-900 rounded-lg p-4">
                      <p className="text-sm text-yellow-800 dark:text-yellow-300">
                        O boleto será gerado após finalizar o pedido.
                      </p>
                    </div>
                  )}

                  <div className="bg-muted border rounded-lg p-3">
                    <p className="text-xs text-muted-foreground">
                      🔒 Esta é uma simulação de pagamento. Nenhum valor real será cobrado.
                    </p>
                  </div>
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
                        <p className="font-medium text-foreground">{item.product.name}</p>
                        <p className="text-muted-foreground">Qtd: {item.quantity}</p>
                      </div>
                      <p className="font-semibold text-foreground">R$ {parseFloat(item.subtotal).toFixed(2)}</p>
                    </div>
                  ))}

                  <div className="border-t pt-4 space-y-2">
                    <div className="flex justify-between text-sm text-foreground">
                      <span>Subtotal</span>
                      <span>R$ {parseFloat(subtotal).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-sm text-foreground">
                      <span>Impostos</span>
                      <span>R$ {parseFloat(tax).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-sm text-foreground">
                      <span>Frete</span>
                      <span>R$ {parseFloat(shipping).toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-lg font-bold border-t pt-2">
                      <span className="text-foreground">Total</span>
                      <span className="text-green-600 dark:text-green-400">R$ {parseFloat(total).toFixed(2)}</span>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <CreditCard className="h-5 w-5" />
                    Finalizar Compra
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <Button
                    type="submit"
                    className="w-full cursor-pointer"
                    size="lg"
                    disabled={processing}
                  >
                    {processing ? 'Processando Pagamento...' : 'Confirmar Pagamento'}
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
