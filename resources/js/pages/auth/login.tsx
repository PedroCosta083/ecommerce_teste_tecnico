import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head, Link } from '@inertiajs/react';
import { Store, ShoppingCart } from 'lucide-react';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}

export default function Login({
    status,
    canResetPassword,
    canRegister,
}: LoginProps) {
    return (
        <>
            <Head title="Entrar" />
            
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
                                <h1 className="text-3xl font-bold text-gray-900 mb-2">Bem-vindo de volta</h1>
                                <p className="text-gray-600">Entre com sua conta para continuar</p>
                            </div>

                            {status && (
                                <div className="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg text-center text-sm font-medium text-green-700">
                                    {status}
                                </div>
                            )}

                            <Form
                                {...store.form()}
                                resetOnSuccess={['password']}
                                className="space-y-5"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <div className="space-y-2">
                                            <Label htmlFor="email">E-mail</Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                name="email"
                                                required
                                                autoFocus
                                                tabIndex={1}
                                                autoComplete="email"
                                                placeholder="seu@email.com"
                                                className="h-11"
                                            />
                                            <InputError message={errors.email} />
                                        </div>

                                        <div className="space-y-2">
                                            <div className="flex items-center justify-between">
                                                <Label htmlFor="password">Senha</Label>
                                                {canResetPassword && (
                                                    <TextLink
                                                        href={request()}
                                                        className="text-sm text-primary hover:underline"
                                                        tabIndex={5}
                                                    >
                                                        Esqueceu a senha?
                                                    </TextLink>
                                                )}
                                            </div>
                                            <Input
                                                id="password"
                                                type="password"
                                                name="password"
                                                required
                                                tabIndex={2}
                                                autoComplete="current-password"
                                                placeholder="••••••••"
                                                className="h-11"
                                            />
                                            <InputError message={errors.password} />
                                        </div>

                                        <div className="flex items-center space-x-2">
                                            <Checkbox
                                                id="remember"
                                                name="remember"
                                                tabIndex={3}
                                            />
                                            <Label htmlFor="remember" className="text-sm font-normal cursor-pointer">
                                                Lembrar de mim
                                            </Label>
                                        </div>

                                        <Button
                                            type="submit"
                                            className="w-full h-11 text-base"
                                            tabIndex={4}
                                            disabled={processing}
                                        >
                                            {processing && <Spinner />}
                                            Entrar
                                        </Button>

                                        {canRegister && (
                                            <div className="text-center text-sm text-gray-600 pt-4 border-t">
                                                Não tem uma conta?{' '}
                                                <TextLink href={register()} tabIndex={5} className="text-primary font-medium hover:underline">
                                                    Criar conta
                                                </TextLink>
                                            </div>
                                        )}
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
