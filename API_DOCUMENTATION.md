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

# Atualizar post (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/posts/{id}

# Deletar post (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/posts/{id}
```

#### Páginas
```bash
# Listar todas as páginas
GET http://localhost:8080/index.php?rest_route=/wp/v2/pages

# Buscar página específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/pages/{id}

# Criar nova página (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/pages

# Atualizar página (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/pages/{id}

# Deletar página (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/pages/{id}
```

#### Usuários
```bash
# Listar usuários (requer autenticação)
GET http://localhost:8080/index.php?rest_route=/wp/v2/users

# Buscar usuário específico
GET http://localhost:8080/index.php?rest_route=/wp/v2/users/{id}

# Criar usuário (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/users

# Atualizar usuário (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/users/{id}

# Deletar usuário (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/users/{id}
```

#### Categorias
```bash
# Listar categorias
GET http://localhost:8080/index.php?rest_route=/wp/v2/categories

# Buscar categoria específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/categories/{id}

# Criar categoria (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/categories

# Atualizar categoria (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/categories/{id}

# Deletar categoria (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/categories/{id}
```

#### Tags
```bash
# Listar tags
GET http://localhost:8080/index.php?rest_route=/wp/v2/tags

# Buscar tag específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/tags/{id}

# Criar tag (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/tags

# Atualizar tag (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/tags/{id}

# Deletar tag (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/tags/{id}
```

#### Mídia
```bash
# Listar mídia
GET http://localhost:8080/index.php?rest_route=/wp/v2/media

# Buscar mídia específica
GET http://localhost:8080/index.php?rest_route=/wp/v2/media/{id}

# Fazer upload de mídia (requer autenticação)
POST http://localhost:8080/index.php?rest_route=/wp/v2/media

# Atualizar mídia (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/media/{id}

# Deletar mídia (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/media/{id}
```

#### Comentários
```bash
# Listar comentários
GET http://localhost:8080/index.php?rest_route=/wp/v2/comments

# Buscar comentário específico
GET http://localhost:8080/index.php?rest_route=/wp/v2/comments/{id}

# Criar comentário
POST http://localhost:8080/index.php?rest_route=/wp/v2/comments

# Atualizar comentário (requer autenticação)
PUT http://localhost:8080/index.php?rest_route=/wp/v2/comments/{id}

# Deletar comentário (requer autenticação)
DELETE http://localhost:8080/index.php?rest_route=/wp/v2/comments/{id}
```

### 2. API do WooCommerce (Requer Autenticação)

#### Produtos (v3)
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

# Buscar produtos por categoria
GET http://localhost:8080/index.php?rest_route=/wc/v3/products?category={category_id}

# Buscar produtos por tag
GET http://localhost:8080/index.php?rest_route=/wc/v3/products?tag={tag_id}

# Buscar produtos por termo
GET http://localhost:8080/index.php?rest_route=/wc/v3/products?search={termo}
```

#### Variações de Produtos
```bash
# Listar variações de um produto
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/{product_id}/variations

# Buscar variação específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/{product_id}/variations/{variation_id}

# Criar variação
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/{product_id}/variations

# Atualizar variação
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/{product_id}/variations/{variation_id}

# Deletar variação
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/{product_id}/variations/{variation_id}
```

#### Categorias de Produtos
```bash
# Listar categorias
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/categories

# Buscar categoria específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/categories/{id}

# Criar categoria
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/categories

# Atualizar categoria
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/categories/{id}

# Deletar categoria
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/categories/{id}
```

#### Tags de Produtos
```bash
# Listar tags
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/tags

# Buscar tag específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/tags/{id}

# Criar tag
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/tags

# Atualizar tag
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/tags/{id}

# Deletar tag
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/tags/{id}
```

#### Atributos de Produtos
```bash
# Listar atributos
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes

# Buscar atributo específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{id}

# Criar atributo
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes

# Atualizar atributo
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{id}

# Deletar atributo
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{id}
```

#### Termos de Atributos
```bash
# Listar termos de um atributo
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{attribute_id}/terms

# Buscar termo específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{attribute_id}/terms/{term_id}

# Criar termo
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{attribute_id}/terms

# Atualizar termo
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{attribute_id}/terms/{term_id}

# Deletar termo
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/attributes/{attribute_id}/terms/{term_id}
```

#### Avaliações de Produtos
```bash
# Listar avaliações de um produto (MÉTODO CORRETO)
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews?product={product_id}

# Listar todas as avaliações
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews

# Buscar avaliação específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews/{review_id}

# Criar avaliação
POST http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews

# Exemplo de criação de review:
curl -X POST "http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 16,
    "reviewer": "João Silva",
    "reviewer_email": "joao@email.com",
    "review": "Excelente produto! Casa muito bem localizada e com ótima qualidade.",
    "rating": 5,
    "status": "approved"
  }'

# Parâmetros obrigatórios:
# - product_id: ID do produto
# - reviewer: Nome do revisor
# - reviewer_email: Email do revisor
# - review: Texto da avaliação
# - rating: Nota (1-5)

# Parâmetros opcionais:
# - status: "approved", "pending", "spam" (padrão: "pending")
# - verified: true/false (se é compra verificada)

# Exemplos de filtros:
# Reviews de produto específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews?product=16

# Reviews aprovadas
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews?status=approved

# Reviews com paginação
GET http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews?page=1&per_page=10

# Atualizar avaliação
PUT http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews/{review_id}

# Deletar avaliação
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/products/reviews/{review_id}
```

#### Pedidos
```bash
# Listar pedidos
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders

# Buscar pedido específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{id}

# Criar pedido
POST http://localhost:8080/index.php?rest_route=/wc/v3/orders

# Atualizar pedido
PUT http://localhost:8080/index.php?rest_route=/wc/v3/orders/{id}

# Deletar pedido
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/orders/{id}

# Buscar pedidos por status
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders?status=pending

# Buscar pedidos por cliente
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders?customer={customer_id}
```

#### Notas de Pedidos
```bash
# Listar notas de um pedido
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/notes

# Buscar nota específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/notes/{note_id}

# Criar nota
POST http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/notes

# Deletar nota
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/notes/{note_id}
```

#### Reembolsos
```bash
# Listar reembolsos de um pedido
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/refunds

# Buscar reembolso específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/refunds/{refund_id}

# Criar reembolso
POST http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/refunds

# Deletar reembolso
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/orders/{order_id}/refunds/{refund_id}
```

#### Clientes
```bash
# Listar clientes
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers

# Buscar cliente específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers/{id}

# Criar cliente
POST http://localhost:8080/index.php?rest_route=/wc/v3/customers

# Atualizar cliente
PUT http://localhost:8080/index.php?rest_route=/wc/v3/customers/{id}

# Deletar cliente
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/customers/{id}

# Buscar cliente por email
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers?email={email}
```

#### Downloads de Clientes
```bash
# Listar downloads de um cliente
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers/{customer_id}/downloads

# Buscar download específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/customers/{customer_id}/downloads/{download_id}
```

#### Cupons
```bash
# Listar cupons
GET http://localhost:8080/index.php?rest_route=/wc/v3/coupons

# Buscar cupom específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/coupons/{id}

# Criar cupom
POST http://localhost:8080/index.php?rest_route=/wc/v3/coupons

# Atualizar cupom
PUT http://localhost:8080/index.php?rest_route=/wc/v3/coupons/{id}

# Deletar cupom
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/coupons/{id}

# Buscar cupom por código
GET http://localhost:8080/index.php?rest_route=/wc/v3/coupons?code={codigo}
```

#### Relatórios
```bash
# Listar relatórios disponíveis
GET http://localhost:8080/index.php?rest_route=/wc/v3/reports

# Relatório de vendas
GET http://localhost:8080/index.php?rest_route=/wc/v3/reports/sales

# Relatório de produtos mais vendidos
GET http://localhost:8080/index.php?rest_route=/wc/v3/reports/products/totals

# Relatório de clientes
GET http://localhost:8080/index.php?rest_route=/wc/v3/reports/customers/totals

# Relatório de pedidos
GET http://localhost:8080/index.php?rest_route=/wc/v3/reports/orders/totals
```

#### Taxas
```bash
# Listar taxas
GET http://localhost:8080/index.php?rest_route=/wc/v3/taxes

# Buscar taxa específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/taxes/{id}

# Criar taxa
POST http://localhost:8080/index.php?rest_route=/wc/v3/taxes

# Atualizar taxa
PUT http://localhost:8080/index.php?rest_route=/wc/v3/taxes/{id}

# Deletar taxa
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/taxes/{id}
```

#### Classes de Taxa
```bash
# Listar classes de taxa
GET http://localhost:8080/index.php?rest_route=/wc/v3/taxes/classes

# Criar classe de taxa
POST http://localhost:8080/index.php?rest_route=/wc/v3/taxes/classes

# Atualizar classe de taxa
PUT http://localhost:8080/index.php?rest_route=/wc/v3/taxes/classes/{slug}

# Deletar classe de taxa
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/taxes/classes/{slug}
```

#### Métodos de Envio
```bash
# Listar métodos de envio
GET http://localhost:8080/index.php?rest_route=/wc/v3/shipping/zones

# Buscar zona específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/shipping/zones/{id}

# Criar zona
POST http://localhost:8080/index.php?rest_route=/wc/v3/shipping/zones

# Atualizar zona
PUT http://localhost:8080/index.php?rest_route=/wc/v3/shipping/zones/{id}

# Deletar zona
DELETE http://localhost:8080/index.php?rest_route=/wc/v3/shipping/zones/{id}
```

#### Métodos de Pagamento
```bash
# Listar métodos de pagamento
GET http://localhost:8080/index.php?rest_route=/wc/v3/payment_gateways

# Buscar método específico
GET http://localhost:8080/index.php?rest_route=/wc/v3/payment_gateways/{id}

# Atualizar método
PUT http://localhost:8080/index.php?rest_route=/wc/v3/payment_gateways/{id}
```

#### Configurações
```bash
# Listar configurações
GET http://localhost:8080/index.php?rest_route=/wc/v3/settings

# Buscar configuração específica
GET http://localhost:8080/index.php?rest_route=/wc/v3/settings/{group_id}

# Atualizar configuração
PUT http://localhost:8080/index.php?rest_route=/wc/v3/settings/{group_id}
```

#### Sistema
```bash
# Informações do sistema
GET http://localhost:8080/index.php?rest_route=/wc/v3/system_status

# Ferramentas do sistema
GET http://localhost:8080/index.php?rest_route=/wc/v3/system_status/tools

# Executar ferramenta
POST http://localhost:8080/index.php?rest_route=/wc/v3/system_status/tools/{id}
```

### 3. API Store (Frontend - Pública)

#### Carrinho
```bash
# Obter carrinho atual
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/cart

# Adicionar item ao carrinho
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/add-item

# Remover item do carrinho
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/remove-item

# Atualizar item do carrinho
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/update-item

# Aplicar cupom
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/apply-coupon

# Remover cupom
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/remove-coupon

# Calcular totais
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/update-customer

# Selecionar método de envio
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/cart/select-shipping-rate
```

#### Checkout
```bash
# Obter dados do checkout
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/checkout

# Processar checkout
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/checkout

# Atualizar dados do checkout
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/checkout
```

#### Produtos (Store)
```bash
# Listar produtos (público)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/products

# Buscar produto específico (público)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/products/{id}

# Listar categorias (público)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/products/categories

# Listar tags (público)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/products/tags

# Listar atributos (público)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/products/attributes
```

#### Pedidos (Store)
```bash
# Buscar pedido específico (público - com chave)
GET http://localhost:8080/index.php?rest_route=/wc/store/v1/order/{id}

# Processar pagamento de pedido
POST http://localhost:8080/index.php?rest_route=/wc/store/v1/order/{id}
```

### 4. JWT Authentication

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
?featured=true
?on_sale=true
?min_price=10&max_price=100
```

### Incluir/Excluir
```bash
?include=1,2,3
?exclude=4,5,6
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

### Carrinho (Store API)
```json
{
  "items": [
    {
      "key": "abc123",
      "id": 1,
      "quantity": 2,
      "name": "Produto Exemplo",
      "summary": "Produto Exemplo",
      "short_description": "Descrição curta",
      "description": "Descrição completa",
      "sku": "SKU123",
      "low_stock_remaining": null,
      "backorders_allowed": false,
      "show_backorder_badge": false,
      "sold_individually": false,
      "permalink": "http://localhost:8080/product/produto-exemplo/",
      "images": [],
      "variation": [],
      "item_data": [],
      "prices": {
        "price": "2999",
        "regular_price": "2999",
        "sale_price": "2999",
        "currency_code": "BRL",
        "currency_symbol": "R$",
        "currency_minor_unit": 2,
        "currency_decimal_separator": ",",
        "currency_thousand_separator": ".",
        "currency_prefix": "R$",
        "currency_suffix": "",
        "raw_prices": {
          "precision": 2,
          "price": "29.99",
          "regular_price": "29.99",
          "sale_price": "29.99"
        }
      },
      "totals": {
        "line_subtotal": "5998",
        "line_subtotal_tax": "0",
        "line_total": "5998",
        "line_total_tax": "0"
      }
    }
  ],
  "items_count": 2,
  "needs_payment": true,
  "needs_shipping": true,
  "has_calculated_shipping": false,
  "totals": {
    "total_items": "5998",
    "total_items_tax": "0",
    "total_fees": "0",
    "total_fees_tax": "0",
    "total_discount": "0",
    "total_discount_tax": "0",
    "total_shipping": "0",
    "total_shipping_tax": "0",
    "total_price": "5998",
    "total_tax": "0",
    "tax_lines": [],
    "currency_code": "BRL",
    "currency_symbol": "R$",
    "currency_minor_unit": 2,
    "currency_decimal_separator": ",",
    "currency_thousand_separator": ".",
    "currency_prefix": "R$",
    "currency_suffix": ""
  }
}
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
6. **Versões**: WooCommerce v3 é a versão mais recente e recomendada
7. **Store API**: Para frontend, use a Store API que é pública
8. **Rate Limiting**: Não há limite de requisições configurado 