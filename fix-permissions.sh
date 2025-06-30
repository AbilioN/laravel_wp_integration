#!/bin/bash

echo "🔧 Corrigindo permissões e configurações..."

# Parar containers se estiverem rodando
echo "🛑 Parando containers..."
docker-compose down

# Corrigir permissões dos diretórios locais
echo "📁 Corrigindo permissões dos diretórios..."
sudo chown -R $USER:$USER laravel/
sudo chown -R $USER:$USER wp-content/
sudo chown -R $USER:$USER wordpress-uploads/
sudo chown -R $USER:$USER laravel-storage/

chmod -R 755 laravel/
chmod -R 755 wp-content/
chmod -R 755 wordpress-uploads/
chmod -R 755 laravel-storage/

# Criar diretórios necessários se não existirem
echo "📂 Criando diretórios necessários..."
mkdir -p laravel/storage/framework/cache
mkdir -p laravel/storage/framework/sessions
mkdir -p laravel/storage/framework/views
mkdir -p laravel/bootstrap/cache
mkdir -p laravel-storage/framework/cache
mkdir -p laravel-storage/framework/sessions
mkdir -p laravel-storage/framework/views

# Corrigir permissões específicas do Laravel
echo "⚙️ Configurando permissões do Laravel..."
chmod -R 775 laravel/storage/
chmod -R 775 laravel/bootstrap/cache/
chmod -R 775 laravel-storage/

# Iniciar containers
echo "🐳 Iniciando containers..."
docker-compose up -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 15

# Executar comandos de configuração do Laravel
echo "🎯 Configurando Laravel..."
docker-compose exec -T laravel composer install --no-interaction
docker-compose exec -T laravel php artisan config:clear
docker-compose exec -T laravel php artisan cache:clear
docker-compose exec -T laravel php artisan view:clear
docker-compose exec -T laravel php artisan route:clear
docker-compose exec -T laravel php artisan config:cache
docker-compose exec -T laravel php artisan route:cache
docker-compose exec -T laravel php artisan view:cache

# Verificar se as migrations foram executadas
echo "🗄️ Verificando banco de dados..."
docker-compose exec -T laravel php artisan migrate --force

# Corrigir permissões dentro dos containers
echo "🔐 Corrigindo permissões dentro dos containers..."
docker-compose exec -T laravel chown -R www-data:www-data /var/www/html
docker-compose exec -T laravel chmod -R 755 /var/www/html/storage
docker-compose exec -T laravel chmod -R 755 /var/www/html/bootstrap/cache

docker-compose exec -T wordpress chown -R www-data:www-data /var/www/html/wp-content
docker-compose exec -T wordpress chmod -R 755 /var/www/html/wp-content

echo "✅ Permissões e configurações corrigidas!"
echo ""
echo "🌐 Acessos:"
echo "   WordPress: http://localhost:8080"
echo "   Laravel: http://localhost:8005"
echo "   Laravel via Nginx: http://laravel.local"
echo "   WordPress via Nginx: http://wordpress.local"
echo ""
echo "🔧 Para verificar se tudo está funcionando:"
echo "   docker-compose logs laravel"
echo "   docker-compose logs wordpress" 