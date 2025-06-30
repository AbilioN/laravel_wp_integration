#!/bin/bash

echo "🚀 Iniciando ambiente Laravel + WordPress..."

# Verificar se os containers estão rodando
if docker-compose ps | grep -q "Up"; then
    echo "📦 Containers já estão rodando!"
    echo ""
    echo "🌐 Acessos:"
    echo "   WordPress: http://localhost:8080"
    echo "   Laravel: http://localhost:8005"
    echo "   Laravel via Nginx: http://laravel.local"
    echo "   WordPress via Nginx: http://wordpress.local"
    exit 0
fi

# Iniciar containers
echo "🐳 Iniciando containers..."
docker-compose up -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Verificar se Laravel precisa de configuração
echo "🔍 Verificando configuração do Laravel..."
if ! docker-compose exec -T laravel php artisan --version > /dev/null 2>&1; then
    echo "⚙️ Configurando Laravel..."
    docker-compose exec -T laravel composer install --no-interaction
    docker-compose exec -T laravel php artisan key:generate --force
    docker-compose exec -T laravel php artisan migrate --force
fi

# Corrigir permissões rapidamente
echo "🔐 Corrigindo permissões..."
docker-compose exec -T laravel chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
docker-compose exec -T wordpress chown -R www-data:www-data /var/www/html/wp-content 2>/dev/null || true

echo "✅ Ambiente iniciado com sucesso!"
echo ""
echo "🌐 Acessos:"
echo "   WordPress: http://localhost:8080"
echo "   Laravel: http://localhost:8005"
echo "   Laravel via Nginx: http://laravel.local"
echo "   WordPress via Nginx: http://wordpress.local"
echo ""
echo "🔧 Comandos úteis:"
echo "   ./fix-permissions.sh  # Corrigir permissões completas"
echo "   docker-compose logs -f [service]  # Ver logs"
echo "   docker-compose down  # Parar containers" 