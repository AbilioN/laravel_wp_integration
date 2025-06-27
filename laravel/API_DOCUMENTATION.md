# API Headless WordPress + WooCommerce

Esta documentação descreve como usar a API headless para comunicação com WordPress e WooCommerce.

## Base URL

```
https://seu-dominio.com/api/v1
```

## Autenticação

### Login
```http
POST /auth/login
```

**Body:**
```json
{
    "email": "usuario@exemplo.com",
    "password": "senha123",
    "remember": true
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "João Silva",
            "email": "usuario@exemplo.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    },
    "message": "Login realizado com sucesso"
}
```

### Registro
```http
POST /auth/register
```

**Body:**
```json
{
    "name": "João Silva",
    "email": "usuario@exemplo.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}
```

### Logout
```http
POST /auth/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

### Refresh Token
```http
POST /auth/refresh
```

**Headers:**
```
Authorization: Bearer {token}
```

## WordPress Content API

### Listar Páginas
```http
GET /wordpress/pages?per_page=10&page=1
```

### Buscar Página por Slug
```http
GET /wordpress/pages/{slug}
```

### Listar Posts
```http
GET /wordpress/posts?per_page=10&category=tecnologia&tag=laravel
```

### Buscar Post por Slug
```http
GET /wordpress/posts/{slug}
```

### Buscar Conteúdo
```http
GET /wordpress/search?q=palavra&type=all&per_page=10
```

### Buscar Menu
```http
GET /wordpress/menu/{location}
```

### Listar Categorias
```http
GET /wordpress/categories?per_page=50
```

### Listar Tags
```http
GET /wordpress/tags?per_page=50
```

## WooCommerce API

### Listar Produtos
```http
GET /woocommerce/products?per_page=12&category=eletronicos&search=iphone&orderby=price&order=asc
```

### Buscar Produto por ID
```http
GET /woocommerce/products/{id}
```

### Buscar Produtos por Categoria
```http
GET /woocommerce/products/category/{category}?per_page=12&orderby=date&order=desc
```

### Buscar Produtos
```http
GET /woocommerce/products/search?q=smartphone&per_page=12&category=eletronicos
```

### Listar Categorias
```http
GET /woocommerce/categories?per_page=50&parent=0
```

### Buscar Categoria por ID
```http
GET /woocommerce/categories/{id}
```

### Carrinho de Compras

#### Ver Carrinho
```http
GET /woocommerce/cart
```

#### Adicionar ao Carrinho
```http
POST /woocommerce/cart/add
```

**Body:**
```json
{
    "product_id": 123,
    "quantity": 2,
    "variation_id": 456,
    "variation": {
        "color": "red",
        "size": "large"
    }
}
```

#### Atualizar Carrinho
```http
PUT /woocommerce/cart/update
```

**Body:**
```json
{
    "item_key": "123_456_abc123",
    "quantity": 3
}
```

#### Remover do Carrinho
```http
DELETE /woocommerce/cart/remove/{item_key}
```

#### Limpar Carrinho
```http
POST /woocommerce/cart/clear
```

### Checkout

#### Processar Checkout
```http
POST /woocommerce/checkout
```

**Body:**
```json
{
    "billing": {
        "first_name": "João",
        "last_name": "Silva",
        "company": "Empresa LTDA",
        "address_1": "Rua das Flores, 123",
        "address_2": "Apto 45",
        "city": "São Paulo",
        "state": "SP",
        "postcode": "01234-567",
        "country": "BR",
        "email": "joao@exemplo.com",
        "phone": "(11) 99999-9999"
    },
    "shipping": {
        "first_name": "João",
        "last_name": "Silva",
        "company": "Empresa LTDA",
        "address_1": "Rua das Flores, 123",
        "address_2": "Apto 45",
        "city": "São Paulo",
        "state": "SP",
        "postcode": "01234-567",
        "country": "BR"
    },
    "payment_method": "bacs",
    "payment_method_title": "Transferência Bancária",
    "customer_note": "Entregar após 18h"
}
```

#### Validar Checkout
```http
GET /woocommerce/checkout/validate
```

### Pedidos (Autenticado)

#### Listar Pedidos do Usuário
```http
GET /woocommerce/orders?per_page=10&status=completed
```

**Headers:**
```
Authorization: Bearer {token}
```

#### Buscar Pedido Específico
```http
GET /woocommerce/orders/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

#### Criar Pedido
```http
POST /woocommerce/orders
```

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
    "billing": {
        "first_name": "João",
        "last_name": "Silva",
        "email": "joao@exemplo.com",
        "address_1": "Rua das Flores, 123",
        "city": "São Paulo",
        "state": "SP",
        "postcode": "01234-567",
        "country": "BR"
    },
    "shipping": {
        "first_name": "João",
        "last_name": "Silva",
        "address_1": "Rua das Flores, 123",
        "city": "São Paulo",
        "state": "SP",
        "postcode": "01234-567",
        "country": "BR"
    },
    "payment_method": "bacs",
    "payment_method_title": "Transferência Bancária",
    "line_items": [
        {
            "product_id": 123,
            "quantity": 2,
            "name": "Produto Exemplo",
            "total": 199.98
        }
    ]
}
```

#### Atualizar Pedido
```http
PUT /woocommerce/orders/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

## Perfil do Usuário (Autenticado)

### Ver Perfil
```http
GET /user/profile
```

**Headers:**
```
Authorization: Bearer {token}
```

### Atualizar Perfil
```http
PUT /user/profile
```

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "first_name": "João",
    "last_name": "Silva",
    "bio": "Desenvolvedor web",
    "website": "https://joao.com"
}
```

### Alterar Senha
```http
POST /user/change-password
```

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
    "current_password": "senha123",
    "new_password": "nova_senha123",
    "new_password_confirmation": "nova_senha123"
}
```

### Pedidos do Usuário
```http
GET /user/orders?per_page=10
```

**Headers:**
```
Authorization: Bearer {token}
```

### Endereços do Usuário

#### Listar Endereços
```http
GET /user/addresses
```

**Headers:**
```
Authorization: Bearer {token}
```

#### Criar Endereço
```http
POST /user/addresses
```

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
    "type": "billing",
    "first_name": "João",
    "last_name": "Silva",
    "company": "Empresa LTDA",
    "address_1": "Rua das Flores, 123",
    "address_2": "Apto 45",
    "city": "São Paulo",
    "state": "SP",
    "postcode": "01234-567",
    "country": "BR",
    "email": "joao@exemplo.com",
    "phone": "(11) 99999-9999"
}
```

#### Atualizar Endereço
```http
PUT /user/addresses/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

#### Deletar Endereço
```http
DELETE /user/addresses/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

## Webhooks

### Atualização de Post WordPress
```http
POST /webhooks/wordpress/post-updated
```

### Criação de Pedido WooCommerce
```http
POST /webhooks/woocommerce/order-created
```

### Atualização de Pedido WooCommerce
```http
POST /webhooks/woocommerce/order-updated
```

### Atualização de Produto WooCommerce
```http
POST /webhooks/woocommerce/product-updated
```

## Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Dados inválidos
- `401` - Não autenticado
- `403` - Não autorizado
- `404` - Não encontrado
- `422` - Erro de validação
- `500` - Erro interno do servidor

## Exemplos de Uso

### Frontend JavaScript (React/Vue/Angular)

```javascript
// Configuração base
const API_BASE = 'https://seu-dominio.com/api/v1';

// Função para fazer requisições
async function apiRequest(endpoint, options = {}) {
    const token = localStorage.getItem('auth_token');
    
    const config = {
        headers: {
            'Content-Type': 'application/json',
            ...(token && { 'Authorization': `Bearer ${token}` })
        },
        ...options
    };

    const response = await fetch(`${API_BASE}${endpoint}`, config);
    return response.json();
}

// Login
async function login(email, password) {
    const response = await apiRequest('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password })
    });
    
    if (response.success) {
        localStorage.setItem('auth_token', response.data.token);
    }
    
    return response;
}

// Buscar produtos
async function getProducts(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return apiRequest(`/woocommerce/products?${queryString}`);
}

// Adicionar ao carrinho
async function addToCart(productId, quantity) {
    return apiRequest('/woocommerce/cart/add', {
        method: 'POST',
        body: JSON.stringify({ product_id: productId, quantity })
    });
}

// Buscar posts do WordPress
async function getPosts(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return apiRequest(`/wordpress/posts?${queryString}`);
}
```

### Mobile (React Native)

```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE = 'https://seu-dominio.com/api/v1';

class ApiService {
    static async request(endpoint, options = {}) {
        const token = await AsyncStorage.getItem('auth_token');
        
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...(token && { 'Authorization': `Bearer ${token}` })
            },
            ...options
        };

        const response = await fetch(`${API_BASE}${endpoint}`, config);
        return response.json();
    }

    static async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        
        if (response.success) {
            await AsyncStorage.setItem('auth_token', response.data.token);
        }
        
        return response;
    }

    static async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/woocommerce/products?${queryString}`);
    }
}
```

## Cache e Performance

A API implementa cache automático para melhorar a performance:

- **Páginas e Posts**: Cache de 5 minutos
- **Categorias e Tags**: Cache de 10 minutos
- **Produtos**: Cache de 5 minutos
- **Menus**: Cache de 5 minutos

O cache é automaticamente limpo quando há atualizações via webhooks.

## Rate Limiting

A API implementa rate limiting para proteger contra abuso:

- **Público**: 60 requisições por minuto
- **Autenticado**: 120 requisições por minuto

## Segurança

- Todas as requisições autenticadas usam tokens Bearer
- Senhas são hasheadas usando bcrypt
- Validação de entrada em todos os endpoints
- Sanitização de dados
- Proteção contra CSRF
- Rate limiting para prevenir abuso

## Suporte

Para suporte técnico ou dúvidas sobre a API, entre em contato através do email: suporte@exemplo.com 