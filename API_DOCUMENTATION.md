# API WordPress Documentation

## Base URL
```
http://localhost:8080
```

## Como acessar a API
Como o WordPress está usando a imagem oficial (sem .htaccess personalizado), acesse a API usando:
```
http://localhost:8080/index.php?rest_route=/endpoint
```

## Endpoints Disponíveis

### 1. API REST do WordPress (Pública)

#### Posts
```bash
# Listar todos os posts
GET http://localhost:8080/index.php?rest_route=/wp/v2/posts

# Buscar post específico
GET http://localhost:8080/index.php?rest_route=/wp/v2/posts/{id}

# Criar novo post (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/posts
```

#### Páginas
```bash
# Listar todas as páginas
GET http://localhost:8080/index.php?rest_route=/wp/v2/pages

# Buscar página específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/pages/{id}
```

#### Usuários
```bash
# Listar usuários (requer autenticação)
GET http://localhost:8080/index.php?rest_route=/wp/v2/users

# Buscar usuário específico
GET http://localhost:8080/index.php?rest_route=/wp/v2/users/{id}
```

#### Categorias
```bash
# Listar categorias
GET http://localhost:8080/index.php?rest_route=/wp/v2/categories

# Buscar categoria específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/categories/{id}
```

#### Tags
```bash
# Listar tags
GET http://localhost:8080/index.php?rest_route=/wp/v2/tags

# Buscar tag específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/tags/{id}
```

#### Mídia
```bash
# Listar mídia
GET http://localhost:8080/index.php?rest_route=/wp/v2/media

# Buscar mídia específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/media/{id}
```

### 2. API do WooCommerce (Requer Autenticação)

#### Produtos
```bash
# Listar produtos
GET http://localhost:8080/index.php?rest_route=/wc/v3/products

# Buscar produto específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/{id}

# Criar produto
POST http://localhost:8080/index.php?rest_route=/wc/v3/products

# Atualizar produto
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/{id}

# Deletar produto
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/{id}
```

#### Categorias de Produtos
```bash
# Listar categorias
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/categories

# Buscar categoria específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/categories/{id}
```

#### Pedidos
```bash
# Listar pedidos
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders

# Buscar pedido específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{id}

# Criar pedido
POST http://localhost:8080/index.php?rest_route=/wc/v3/orders
```

#### Clientes
```bash
# Listar clientes
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers

# Buscar cliente específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers/{id}

# Criar cliente
POST http://localhost:8080/index.php?rest_route=/wc/v3/customers
```

### 3. JWT Authentication

#### Login
```bash
# Fazer login e obter token
POST http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token
Content-Type: application/json

{
  "username": "admin",
  "password": "password"
}
```

#### Validar Token
```bash
# Validar token
POST http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token/validate
Authorization: Bearer {token}
```

#### Renovar Token
```bash
# Renovar token
POST http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token/refresh
Authorization: Bearer {token}
```

## Autenticação

### JWT Token
Para endpoints que requerem autenticação, inclua o header:
```
Authorization: Bearer {seu_token_jwt}
```

### Exemplo de uso com curl
```bash
# Login
curl -X POST http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "password"}'

# Usar token para acessar produtos
curl -X GET http://localhost:8080/index.php?rest_route=/wc/v3/products \
  -H "Authorization: Bearer {token_aqui}"
```

## Parâmetros de Query Comuns

### Paginação
```bash
?page=1&per_page=10
```

### Ordenação
```bash
?orderby=date&order=desc
```

### Filtros
```bash
?status=publish
?categories=1,2,3
?search=termo_busca
```

## Exemplos de Resposta

### Posts
```json
[
  {
    "id": 1,
    "date": "2025-06-20T17:17:47",
    "title": {
      "rendered": "Hello world!"
    },
    "content": {
      "rendered": "<p>Welcome to WordPress...</p>"
    },
    "excerpt": {
      "rendered": "<p>Welcome to WordPress...</p>"
    },
    "author": 1,
    "featured_media": 0,
    "categories": [1],
    "tags": [],
    "link": "http://localhost:8080/?p=1"
  }
]
```

### Produtos WooCommerce
```json
[
  {
    "id": 1,
    "name": "Produto Exemplo",
    "slug": "produto-exemplo",
    "permalink": "http://localhost:8080/product/produto-exemplo/",
    "date_created": "2025-06-20T17:17:47",
    "type": "simple",
    "status": "publish",
    "featured": false,
    "catalog_visibility": "visible",
    "description": "Descrição do produto",
    "short_description": "Descrição curta",
    "sku": "SKU123",
    "price": "29.99",
    "regular_price": "29.99",
    "sale_price": "",
    "on_sale": false,
    "purchasable": true,
    "total_sales": 0,
    "virtual": false,
    "downloadable": false,
    "categories": [
      {
        "id": 1,
        "name": "Categoria",
        "slug": "categoria"
      }
    ],
    "images": [],
    "attributes": [],
    "variations": [],
    "menu_order": 0,
    "meta_data": []
  }
]
```

## Status Codes

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Requisição inválida
- `401` - Não autorizado
- `403` - Proibido
- `404` - Não encontrado
- `500` - Erro interno do servidor

## Configuração CORS

O WordPress está configurado para aceitar requisições de qualquer origem:
- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type, Authorization`

## Notas Importantes

1. **URL da API**: Sempre use `index.php?rest_route=` em vez de `/wp-json/`
2. **Autenticação**: WooCommerce requer JWT token para a maioria dos endpoints
3. **CORS**: Configurado para aceitar requisições de qualquer origem
4. **Permalinks**: Não é necessário configurar permalinks personalizados
5. **Plugins**: JWT Authentication for WP REST API está instalado e configurado 