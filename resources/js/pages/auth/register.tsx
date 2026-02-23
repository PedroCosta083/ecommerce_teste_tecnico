import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head, Link } from '@inertiajs/react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Store } from 'lucide-react';

export default function Register() {
    return (
        <>
            <Head title="Criar Conta" />
            
            <div className="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 flex flex-col">
                {/* Header */}
                <header className="bg-white/80 backdrop-blur-md shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <Link href="/" className="flex items-center gap-2 w-fit">
                            <Store className="h-8 w-8 text-primary" />
                            <span className="text-2xl font-bold bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                                Loja
                            </span>
                        </Link>
                    </div>
                </header>

                {/* Main Content */}
                <div className="flex-1 flex items-center justify-center p-4">
                    <div className="w-full max-w-md">
                        <div className="bg-white rounded-2xl shadow-xl p-8 border">
                            <div className="text-center mb-8">
                                <h1 className="text-3xl font-bold text-gray-900 mb-2">Criar sua conta</h1>
                                <p className="text-gray-600">Preencha os dados abaixo para começar</p>
                            </div>

                            <Form
                                {...store.form()}
                                resetOnSuccess={['password', 'password_confirmation']}
                                disableWhileProcessing
                                className="space-y-5"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="space-y-2">
                                            <Label htmlFor="name">Nome completo</Label>
                                            <Input
                                                id="name"
                                                type="text"
                                                required
                                                autoFocus
                                                tabIndex={1}
                                                autoComplete="name"
                                                name="name"
                                                placeholder="Seu nome completo"
                                                className="h-11"
                                            />
                                            <InputError message={errors.name} />
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="email">E-mail</Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                required
                                                tabIndex={2}
                                                autoComplete="email"
                                                name="email"
                                                placeholder="seu@email.com"
                                                className="h-11"
                                            />
                                            <InputError message={errors.email} />
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="password">Senha</Label>
                                            <Input
                                                id="password"
                                                type="password"
                                                required
                                                tabIndex={3}
                                                autoComplete="new-password"
                                                name="password"
                                                placeholder="••••••••"
                                                className="h-11"
                                            />
                                            <InputError message={errors.password} />
                                        </div>

                                        <div className="space-y-2">
                                            <Label htmlFor="password_confirmation">
                                                Confirmar senha
                                            </Label>
                                            <Input
                                                id="password_confirmation"
                                                type="password"
                                                required
                                                tabIndex={4}
                                                autoComplete="new-password"
                                                name="password_confirmation"
                                                placeholder="••••••••"
                                                className="h-11"
                                            />
                                            <InputError message={errors.password_confirmation} />
                                        </div>

                                        <Button
                                            type="submit"
                                            className="w-full h-11 text-base"
                                            tabIndex={5}
                                        >
                                            {processing && <Spinner />}
                                            Criar conta
                                        </Button>

                                        <div className="text-center text-sm text-gray-600 pt-4 border-t">
                                            Já tem uma conta?{' '}
                                            <TextLink href={login()} tabIndex={6} className="text-primary font-medium hover:underline">
                                                Entrar
                                            </TextLink>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </div>

                        <div className="text-center mt-6 text-sm text-gray-500">
                            <Link href="/" className="hover:text-primary transition-colors">
                                ← Voltar para a loja
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
