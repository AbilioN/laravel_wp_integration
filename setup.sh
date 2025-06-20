#!/bin/bash

echo "🚀 Configurando ambiente Laravel + WordPress..."

# Criar diretórios necessários
echo "📁 Criando diretórios..."
mkdir -p laravel
mkdir -p wp-content
mkdir -p wordpress-uploads
mkdir -p laravel-storage
mkdir -p nginx/sites-available
mkdir -p nginx/sites-enabled

# Copiar arquivo de ambiente
if [ ! -f .env ]; then
    echo "📄 Criando arquivo .env..."
    cp env.example .env
    echo "✅ Arquivo .env criado. Edite as variáveis conforme necessário."
fi

# Criar Laravel project se não existir
if [ ! -f laravel/composer.json ]; then
    echo "🎯 Criando projeto Laravel..."
    docker run --rm -v $(pwd)/laravel:/app composer create-project laravel/laravel .
    echo "✅ Projeto Laravel criado."
fi

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 laravel
chmod -R 755 wp-content
chmod -R 755 wordpress-uploads
chmod -R 755 laravel-storage

# Criar links simbólicos do nginx
echo "🔗 Configurando Nginx..."
ln -sf ../sites-available/wordpress nginx/sites-enabled/wordpress
ln -sf ../sites-available/laravel nginx/sites-enabled/laravel

# Construir e iniciar containers
echo "🐳 Construindo e iniciando containers..."
docker-compose up -d --build

echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Configurar Laravel
echo "⚙️ Configurando Laravel..."
docker-compose exec laravel composer install
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan migrate

echo "✅ Setup concluído!"
echo ""
echo "🌐 Acessos:"
echo "   WordPress: http://localhost:8080"
echo "   Laravel: http://localhost:8005"
echo "   Laravel via Nginx: http://laravel.local"
echo "   WordPress via Nginx: http://wordpress.local"
echo "   MySQL: localhost:3309"
echo ""
echo "📝 Para acessar via Nginx, adicione ao seu /etc/hosts:"
echo "   127.0.0.1 laravel.local"
echo "   127.0.0.1 wordpress.local"
echo ""
echo "🔧 Comandos úteis:"
echo "   docker-compose logs -f [service]  # Ver logs"
echo "   docker-compose exec laravel php artisan [command]  # Comandos Laravel"
echo "   docker-compose exec wordpress wp [command]  # Comandos WordPress" 