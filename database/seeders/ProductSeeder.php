<?php

namespace Database\Seeders;

use App\Models\{Product, Category, Tag, StockMovement};
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty() || $tags->isEmpty()) {
            $this->command->error('Execute CategorySeeder e TagSeeder antes!');
            return;
        }

        // Eletrônicos
        $smartphones = $categories->where('slug', 'smartphones')->first();
        $notebooks = $categories->where('slug', 'notebooks')->first();
        $tablets = $categories->where('slug', 'tablets')->first();
        $fones = $categories->where('slug', 'fones-de-ouvido')->first();

        Product::create(['name' => 'iPhone 15 Pro Max 256GB', 'slug' => 'iphone-15-pro-max-256gb', 'description' => 'Smartphone Apple com tela de 6.7", câmera 48MP e chip A17 Pro', 'price' => 8999.00, 'cost_price' => 7000.00, 'quantity' => 15, 'category_id' => $smartphones?->id, 'active' => true]);
        Product::create(['name' => 'Samsung Galaxy S24 Ultra', 'slug' => 'samsung-galaxy-s24-ultra', 'description' => 'Tela Dynamic AMOLED 6.8", 200MP, S Pen integrada', 'price' => 7499.00, 'cost_price' => 6000.00, 'quantity' => 20, 'category_id' => $smartphones?->id, 'active' => true]);
        Product::create(['name' => 'Xiaomi Redmi Note 13 Pro', 'slug' => 'xiaomi-redmi-note-13-pro', 'description' => 'Smartphone com câmera 200MP e carregamento rápido 67W', 'price' => 1899.00, 'cost_price' => 1400.00, 'quantity' => 35, 'category_id' => $smartphones?->id, 'active' => true]);

        Product::create(['name' => 'MacBook Pro 14" M3', 'slug' => 'macbook-pro-14-m3', 'description' => 'Notebook Apple com chip M3, 16GB RAM, SSD 512GB', 'price' => 15999.00, 'cost_price' => 13000.00, 'quantity' => 8, 'category_id' => $notebooks?->id, 'active' => true]);
        Product::create(['name' => 'Dell Inspiron 15 i5', 'slug' => 'dell-inspiron-15-i5', 'description' => 'Intel Core i5, 8GB RAM, SSD 256GB, tela Full HD', 'price' => 3299.00, 'cost_price' => 2500.00, 'quantity' => 25, 'category_id' => $notebooks?->id, 'active' => true]);
        Product::create(['name' => 'Lenovo IdeaPad Gaming', 'slug' => 'lenovo-ideapad-gaming', 'description' => 'Ryzen 5, GTX 1650, 16GB RAM, SSD 512GB', 'price' => 4599.00, 'cost_price' => 3500.00, 'quantity' => 12, 'category_id' => $notebooks?->id, 'active' => true]);

        Product::create(['name' => 'iPad Air 11" 128GB', 'slug' => 'ipad-air-11-128gb', 'description' => 'Tablet Apple com chip M2 e tela Liquid Retina', 'price' => 5499.00, 'cost_price' => 4500.00, 'quantity' => 18, 'category_id' => $tablets?->id, 'active' => true]);
        Product::create(['name' => 'Samsung Galaxy Tab S9', 'slug' => 'samsung-galaxy-tab-s9', 'description' => 'Tablet Android com S Pen e tela de 11"', 'price' => 3799.00, 'cost_price' => 3000.00, 'quantity' => 22, 'category_id' => $tablets?->id, 'active' => true]);

        Product::create(['name' => 'AirPods Pro 2ª Geração', 'slug' => 'airpods-pro-2-geracao', 'description' => 'Fones Apple com cancelamento de ruído ativo', 'price' => 2199.00, 'cost_price' => 1800.00, 'quantity' => 40, 'category_id' => $fones?->id, 'active' => true]);
        Product::create(['name' => 'Sony WH-1000XM5', 'slug' => 'sony-wh-1000xm5', 'description' => 'Headphone over-ear com melhor cancelamento de ruído', 'price' => 2499.00, 'cost_price' => 2000.00, 'quantity' => 15, 'category_id' => $fones?->id, 'active' => true]);
        Product::create(['name' => 'JBL Tune 510BT', 'slug' => 'jbl-tune-510bt', 'description' => 'Fone Bluetooth com 40h de bateria', 'price' => 199.00, 'cost_price' => 120.00, 'quantity' => 60, 'category_id' => $fones?->id, 'active' => true]);

        // Moda
        $camisetas = $categories->where('slug', 'camisetas')->first();
        $calcas = $categories->where('slug', 'calcas')->first();
        $tenis = $categories->where('slug', 'tenis')->first();
        $bolsas = $categories->where('slug', 'bolsas')->first();

        Product::create(['name' => 'Camiseta Básica Preta', 'slug' => 'camiseta-basica-preta', 'description' => 'Camiseta 100% algodão, modelagem regular', 'price' => 49.90, 'cost_price' => 25.00, 'quantity' => 100, 'category_id' => $camisetas?->id, 'active' => true]);
        Product::create(['name' => 'Camiseta Polo Lacoste', 'slug' => 'camiseta-polo-lacoste', 'description' => 'Polo clássica com logo bordado', 'price' => 399.00, 'cost_price' => 250.00, 'quantity' => 45, 'category_id' => $camisetas?->id, 'active' => true]);
        Product::create(['name' => 'Camiseta Oversized Branca', 'slug' => 'camiseta-oversized-branca', 'description' => 'Modelagem ampla, tecido premium', 'price' => 89.90, 'cost_price' => 45.00, 'quantity' => 80, 'category_id' => $camisetas?->id, 'active' => true]);

        Product::create(['name' => 'Calça Jeans Skinny', 'slug' => 'calca-jeans-skinny', 'description' => 'Jeans elástico com modelagem ajustada', 'price' => 179.90, 'cost_price' => 100.00, 'quantity' => 55, 'category_id' => $calcas?->id, 'active' => true]);
        Product::create(['name' => 'Calça Social Masculina', 'slug' => 'calca-social-masculina', 'description' => 'Tecido alfaiataria, corte reto', 'price' => 249.00, 'cost_price' => 150.00, 'quantity' => 30, 'category_id' => $calcas?->id, 'active' => true]);
        Product::create(['name' => 'Calça Cargo Bege', 'slug' => 'calca-cargo-bege', 'description' => 'Estilo urbano com bolsos laterais', 'price' => 199.90, 'cost_price' => 120.00, 'quantity' => 40, 'category_id' => $calcas?->id, 'active' => true]);

        Product::create(['name' => 'Nike Air Max 90', 'slug' => 'nike-air-max-90', 'description' => 'Tênis icônico com amortecimento Air', 'price' => 899.90, 'cost_price' => 600.00, 'quantity' => 35, 'category_id' => $tenis?->id, 'active' => true]);
        Product::create(['name' => 'Adidas Ultraboost 22', 'slug' => 'adidas-ultraboost-22', 'description' => 'Tênis de corrida com tecnologia Boost', 'price' => 1099.00, 'cost_price' => 750.00, 'quantity' => 28, 'category_id' => $tenis?->id, 'active' => true]);
        Product::create(['name' => 'Vans Old Skool', 'slug' => 'vans-old-skool', 'description' => 'Tênis skate clássico preto e branco', 'price' => 399.90, 'cost_price' => 250.00, 'quantity' => 50, 'category_id' => $tenis?->id, 'active' => true]);

        Product::create(['name' => 'Mochila Executiva Preta', 'slug' => 'mochila-executiva-preta', 'description' => 'Compartimento para notebook até 15.6"', 'price' => 189.90, 'cost_price' => 100.00, 'quantity' => 45, 'category_id' => $bolsas?->id, 'active' => true]);
        Product::create(['name' => 'Bolsa Feminina Couro', 'slug' => 'bolsa-feminina-couro', 'description' => 'Bolsa tiracolo em couro legítimo', 'price' => 349.00, 'cost_price' => 200.00, 'quantity' => 25, 'category_id' => $bolsas?->id, 'active' => true]);

        // Casa e Decoração
        $moveis = $categories->where('slug', 'moveis')->first();
        $decoracao = $categories->where('slug', 'decoracao')->first();
        $cozinha = $categories->where('slug', 'cozinha')->first();

        Product::create(['name' => 'Sofá 3 Lugares Cinza', 'slug' => 'sofa-3-lugares-cinza', 'description' => 'Sofá retrátil e reclinável com tecido suede', 'price' => 1899.00, 'cost_price' => 1200.00, 'quantity' => 8, 'category_id' => $moveis?->id, 'active' => true]);
        Product::create(['name' => 'Mesa de Jantar 6 Lugares', 'slug' => 'mesa-jantar-6-lugares', 'description' => 'Mesa em MDF com acabamento amadeirado', 'price' => 899.00, 'cost_price' => 500.00, 'quantity' => 12, 'category_id' => $moveis?->id, 'active' => true]);
        Product::create(['name' => 'Cadeira Office Ergonômica', 'slug' => 'cadeira-office-ergonomica', 'description' => 'Cadeira presidente com apoio lombar', 'price' => 649.00, 'cost_price' => 400.00, 'quantity' => 20, 'category_id' => $moveis?->id, 'active' => true]);

        Product::create(['name' => 'Quadro Decorativo Abstrato', 'slug' => 'quadro-decorativo-abstrato', 'description' => 'Quadro canvas 60x80cm com moldura', 'price' => 149.90, 'cost_price' => 80.00, 'quantity' => 35, 'category_id' => $decoracao?->id, 'active' => true]);
        Product::create(['name' => 'Vaso Decorativo Cerâmica', 'slug' => 'vaso-decorativo-ceramica', 'description' => 'Vaso artesanal 40cm de altura', 'price' => 89.90, 'cost_price' => 45.00, 'quantity' => 50, 'category_id' => $decoracao?->id, 'active' => true]);

        Product::create(['name' => 'Jogo de Panelas Antiaderente', 'slug' => 'jogo-panelas-antiaderente', 'description' => 'Kit com 5 peças e revestimento cerâmico', 'price' => 299.00, 'cost_price' => 180.00, 'quantity' => 30, 'category_id' => $cozinha?->id, 'active' => true]);
        Product::create(['name' => 'Liquidificador Philips Walita', 'slug' => 'liquidificador-philips-walita', 'description' => 'Potência 1000W, copo 2L', 'price' => 249.00, 'cost_price' => 150.00, 'quantity' => 25, 'category_id' => $cozinha?->id, 'active' => true]);
        Product::create(['name' => 'Air Fryer Mondial 4L', 'slug' => 'air-fryer-mondial-4l', 'description' => 'Fritadeira elétrica sem óleo', 'price' => 399.00, 'cost_price' => 250.00, 'quantity' => 40, 'category_id' => $cozinha?->id, 'active' => true]);

        // Esportes
        $academia = $categories->where('slug', 'academia')->first();
        $ciclismo = $categories->where('slug', 'ciclismo')->first();

        Product::create(['name' => 'Kit Halteres 2kg a 10kg', 'slug' => 'kit-halteres-2kg-10kg', 'description' => 'Conjunto com 5 pares de halteres emborrachados', 'price' => 599.00, 'cost_price' => 400.00, 'quantity' => 15, 'category_id' => $academia?->id, 'active' => true]);
        Product::create(['name' => 'Colchonete Yoga Premium', 'slug' => 'colchonete-yoga-premium', 'description' => 'Tapete EVA 10mm com bolsa', 'price' => 89.90, 'cost_price' => 45.00, 'quantity' => 60, 'category_id' => $academia?->id, 'active' => true]);
        Product::create(['name' => 'Bicicleta Ergométrica', 'slug' => 'bicicleta-ergometrica', 'description' => 'Bike residencial com monitor LCD', 'price' => 899.00, 'cost_price' => 600.00, 'quantity' => 10, 'category_id' => $academia?->id, 'active' => true]);

        Product::create(['name' => 'Bicicleta Mountain Bike Aro 29', 'slug' => 'bicicleta-mtb-aro-29', 'description' => 'Bike 21 marchas com suspensão dianteira', 'price' => 1499.00, 'cost_price' => 1000.00, 'quantity' => 12, 'category_id' => $ciclismo?->id, 'active' => true]);
        Product::create(['name' => 'Capacete Ciclismo com Viseira', 'slug' => 'capacete-ciclismo-viseira', 'description' => 'Capacete aerodinâmico com LED traseiro', 'price' => 149.90, 'cost_price' => 80.00, 'quantity' => 35, 'category_id' => $ciclismo?->id, 'active' => true]);

        // Livros
        $livros = $categories->where('slug', 'livros')->first();

        Product::create(['name' => 'Atomic Habits - James Clear', 'slug' => 'atomic-habits-james-clear', 'description' => 'Livro sobre formação de hábitos', 'price' => 39.90, 'cost_price' => 20.00, 'quantity' => 80, 'category_id' => $livros?->id, 'active' => true]);
        Product::create(['name' => 'O Poder do Hábito', 'slug' => 'poder-habito', 'description' => 'Best-seller de Charles Duhigg', 'price' => 34.90, 'cost_price' => 18.00, 'quantity' => 65, 'category_id' => $livros?->id, 'active' => true]);
        Product::create(['name' => 'Clean Code - Robert Martin', 'slug' => 'clean-code-robert-martin', 'description' => 'Guia essencial para desenvolvedores', 'price' => 89.90, 'cost_price' => 50.00, 'quantity' => 45, 'category_id' => $livros?->id, 'active' => true]);

        // Beleza
        $beleza = $categories->where('slug', 'beleza-cuidados')->first();

        Product::create(['name' => 'Perfume Malbec Boticario', 'slug' => 'perfume-malbec-boticario', 'description' => 'Eau de Toilette 100ml masculino', 'price' => 179.90, 'cost_price' => 100.00, 'quantity' => 50, 'category_id' => $beleza?->id, 'active' => true]);
        Product::create(['name' => 'Kit Skincare Facial', 'slug' => 'kit-skincare-facial', 'description' => 'Limpeza, tônico e hidratante', 'price' => 149.00, 'cost_price' => 80.00, 'quantity' => 40, 'category_id' => $beleza?->id, 'active' => true]);
        Product::create(['name' => 'Secador de Cabelo Profissional', 'slug' => 'secador-cabelo-profissional', 'description' => 'Secador 2000W com difusor', 'price' => 199.00, 'cost_price' => 120.00, 'quantity' => 30, 'category_id' => $beleza?->id, 'active' => true]);

        // Associar tags aleatórias aos produtos
        Product::all()->each(function ($product) use ($tags) {
            $product->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
            
            // Criar movimentações de estoque
            StockMovement::factory(rand(1, 3))->create(['product_id' => $product->id]);
        });
    }
}