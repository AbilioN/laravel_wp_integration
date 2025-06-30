# Laravel + WordPress Integration Setup

Este projeto configura um ambiente Docker completo para integração entre Laravel e WordPress, permitindo que ambos compartilhem o mesmo banco de dados MySQL.

## 🏗️ Arquitetura

- **MySQL 5.7**: Banco de dados compartilhado (porta 3309)
- **WordPress**: CMS para gerenciamento de conteúdo (porta 8080)
- **Laravel**: Framework PHP para aplicações customizadas (porta 8005)
- **Nginx**: Servidor web reverso (portas 80/443)

## 📋 Pré-requisitos

- Docker
- Docker Compose
- Git

## 🚀 Instalação Rápida

1. **Clone o repositório:**
   ```bash
   git clone <seu-repositorio>
   cd wp
   ```

2. **Execute o script de setup:**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

## 🔧 Configuração Manual

### 1. Variáveis de Ambiente

Copie o arquivo de exemplo e configure as variáveis:

```bash
cp env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
# MySQL Database Configuration
MYSQL_ROOT_PASSWORD=sua_senha_root
MYSQL_DATABASE=wordpress_db
MYSQL_USER=wordpress_user
MYSQL_PASSWORD=wordpress_password

# WordPress Database Configuration
WORDPRESS_DB_NAME=wordpress_db
WORDPRESS_DB_USER=wordpress_user
WORDPRESS_DB_PASSWORD=wordpress_password

# Laravel Database Configuration
LARAVEL_DB_NAME=laravel_db
LARAVEL_DB_USER=laravel_user
LARAVEL_DB_PASSWORD=laravel_password

# WordPress URL Configuration
WORDPRESS_URL=http://localhost:8080
```

### 2. Criar Projeto Laravel

```bash
docker run --rm -v $(pwd)/laravel:/app composer create-project laravel/laravel .
```

### 3. Configurar Laravel

Edite `laravel/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

# WordPress URL Configuration
WORDPRESS_URL=http://localhost:8080
```

### 4. Iniciar Containers

```bash
docker-compose up -d --build
```

### 5. Configurar Laravel

```bash
docker-compose exec laravel composer install
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan migrate
```

## 🌐 Acessos

- **WordPress**: http://localhost:8080
- **Laravel**: http://localhost:8005
- **Laravel via Nginx**: http://laravel.local
- **Wordpress via Nginx**: http://wordpress.local
- **MySQL**: localhost:3309

## 🔗 Configuração de Domínios Locais

Para usar os domínios `.local`, adicione ao seu `/etc/hosts`:

```
127.0.0.1 laravel.local
127.0.0.1 wordpress.local
```

## 📊 Estrutura de Diretórios

```
wp/
├── docker-compose.yml
├── setup.sh
├── env.example
├── laravel/                 # Projeto Laravel
├── wp-content/             # Conteúdo WordPress
├── wordpress-uploads/      # Uploads WordPress
├── laravel-storage/        # Storage Laravel
└── nginx/
    ├── nginx.conf
    ├── sites-available/
    │   ├── wordpress
    │   └── laravel
    └── sites-enabled/
        ├── wordpress
        └── laravel
```

## 🔧 Comandos Úteis

### Docker Compose
```bash
# Iniciar serviços
docker-compose up -d

# Parar serviços
docker-compose down

# Ver logs
docker-compose logs -f [service]

# Reconstruir containers
docker-compose up -d --build
```

### Laravel
```bash
# Executar comandos Laravel
docker-compose exec laravel php artisan [command]

# Instalar dependências
docker-compose exec laravel composer install

# Executar migrations
docker-compose exec laravel php artisan migrate

# Criar controller
docker-compose exec laravel php artisan make:controller [Name]Controller
```

### WordPress
```bash
# Acessar container WordPress
docker-compose exec wordpress bash

# Instalar WP-CLI (se necessário)
docker-compose exec wordpress wp [command]
```

## 🔄 Integração Laravel-WordPress

### Compartilhando Banco de Dados

Ambos os sistemas podem acessar o mesmo banco MySQL:

- **WordPress**: Usa tabelas com prefixo `wp_`
- **Laravel**: Usa tabelas próprias do framework

### Configuração da URL do WordPress

O Laravel usa a variável `WORDPRESS_URL` do arquivo `.env` para gerar links corretos para o WordPress. Esta configuração é importante para:

- Links de redirecionamento para posts e páginas
- Integração com a API do WordPress
- Navegação entre os sistemas

Para alterar a URL do WordPress, edite a variável `WORDPRESS_URL` no arquivo `laravel/.env`:

```env
WORDPRESS_URL=http://localhost:8080
```

### Exemplo de Integração

Crie um modelo Laravel para acessar dados do WordPress:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordPressPost extends Model
{
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'post_title',
        'post_content',
        'post_status',
        'post_type'
    ];

    public function scopePublished($query)
    {
        return $query->where('post_status', 'publish')
                    ->where('post_type', 'post');
    }
}
```

### Controller de Exemplo

```php
<?php

namespace App\Http\Controllers;

use App\Models\WordPressPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = WordPressPost::published()
            ->orderBy('post_date', 'desc')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }
}
```

## 🛠️ Desenvolvimento

### Adicionando Plugins WordPress

1. Acesse o admin do WordPress
2. Vá em Plugins > Adicionar Novo
3. Instale e ative os plugins desejados

### Desenvolvendo no Laravel

1. Os arquivos Laravel estão em `./laravel/`
2. As mudanças são refletidas automaticamente
3. Use `php artisan serve` para desenvolvimento local

## 🔒 Segurança

- Altere as senhas padrão no arquivo `.env`
- Configure HTTPS em produção
- Mantenha os containers atualizados
- Use variáveis de ambiente para dados sensíveis

## 🐛 Troubleshooting

### Problemas Comuns

1. **Porta já em uso:**
   ```bash
   # Verificar portas em uso
   netstat -tulpn | grep :8080
   # Alterar porta no docker-compose.yml
   ```

2. **Permissões de arquivo:**
   ```bash
   chmod -R 755 laravel
   chmod -R 755 wp-content
   ```

3. **Container não inicia:**
   ```bash
   docker-compose logs [service]
   docker-compose down
   docker-compose up -d --build
   ```

## 📝 Licença

Este projeto está sob a licença MIT. 