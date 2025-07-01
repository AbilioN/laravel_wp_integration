# Exemplos de Uso da API WordPress

## 1. Testando a API (Sem Autenticação)

### Listar Posts
```bash
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts"
```

### Listar Categorias
```bash
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/categories"
```

### Buscar Post Específico
```bash
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts/1"
```

## 2. Autenticação JWT

### Fazer Login
```bash
curl -X POST "http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "password"
  }'
```

**Resposta esperada:**
```json
{
  "success": true,
  "statusCode": 200,
  "code": "jwt_auth_valid_credential",
  "message": "Credential is valid",
  "data": {
    "id": 1,
    "user_login": "admin",
    "user_nicename": "admin",
    "user_email": "admin@example.com",
    "user_registered": "2025-06-20 17:17:47",
    "user_display_name": "admin",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user_id": 1
  }
}
```

### Validar Token
```bash
curl -X POST "http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token/validate" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## 3. API do WooCommerce (Com Autenticação)

### Listar Produtos
```bash
curl -X GET "http://localhost:8080/index.php?rest_route=/wc/v3/products" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Criar Produto
```bash
curl -X POST "http://localhost:8080/index.php?rest_route=/wc/v3/products" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Produto Teste",
    "type": "simple",
    "regular_price": "29.99",
    "description": "Descrição do produto teste",
    "short_description": "Descrição curta",
    "categories": [
      {
        "id": 1
      }
    ],
    "images": [
      {
        "src": "http://localhost:8080/wp-content/uploads/2025/06/produto.jpg",
        "position": 0
      }
    ]
  }'
```

### Atualizar Produto
```bash
curl -X PUT "http://localhost:8080/index.php?rest_route=/wc/v3/products/1" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "regular_price": "39.99"
  }'
```

### Deletar Produto
```bash
curl -X DELETE "http://localhost:8080/index.php?rest_route=/wc/v3/products/1" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## 4. Parâmetros de Query

### Paginação
```bash
# Primeira página, 5 itens por página
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts?page=1&per_page=5"
```

### Busca
```bash
# Buscar posts com "wordpress" no título ou conteúdo
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts?search=wordpress"
```

### Ordenação
```bash
# Ordenar por data (mais recente primeiro)
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts?orderby=date&order=desc"
```

### Filtros
```bash
# Apenas posts publicados
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts?status=publish"

# Posts de uma categoria específica
curl -X GET "http://localhost:8080/index.php?rest_route=/wp/v2/posts?categories=1"
```

## 5. Exemplos em JavaScript

### Usando Fetch API
```javascript
// Login
async function login(username, password) {
  const response = await fetch('http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ username, password })
  });
  
  const data = await response.json();
  return data.data.token;
}

// Buscar posts
async function getPosts(token = null) {
  const headers = {};
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const response = await fetch('http://localhost:8080/index.php?rest_route=/wp/v2/posts', {
    headers
  });
  
  return await response.json();
}

// Buscar produtos (requer autenticação)
async function getProducts(token) {
  const response = await fetch('http://localhost:8080/index.php?rest_route=/wc/v3/products', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  return await response.json();
}

// Exemplo de uso
async function exemplo() {
  try {
    // Fazer login
    const token = await login('admin', 'password');
    console.log('Token:', token);
    
    // Buscar posts (público)
    const posts = await getPosts();
    console.log('Posts:', posts);
    
    // Buscar produtos (autenticado)
    const products = await getProducts(token);
    console.log('Produtos:', products);
    
  } catch (error) {
    console.error('Erro:', error);
  }
}
```

### Usando Axios
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8080',
  timeout: 10000,
});

// Interceptor para adicionar token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Funções da API
export const wordpressApi = {
  // Login
  async login(username, password) {
    const response = await api.post('/index.php?rest_route=/jwt-auth/v1/token', {
      username,
      password
    });
    return response.data.data.token;
  },
  
  // Posts
  async getPosts(params = {}) {
    const response = await api.get('/index.php?rest_route=/wp/v2/posts', { params });
    return response.data;
  },
  
  // Produtos
  async getProducts(params = {}) {
    const response = await api.get('/index.php?rest_route=/wc/v3/products', { params });
    return response.data;
  },
  
  // Criar produto
  async createProduct(productData) {
    const response = await api.post('/index.php?rest_route=/wc/v3/products', productData);
    return response.data;
  }
};
```

## 6. Exemplos em Python

```python
import requests
import json

class WordPressAPI:
    def __init__(self, base_url="http://localhost:8080"):
        self.base_url = base_url
        self.token = None
    
    def login(self, username, password):
        url = f"{self.base_url}/index.php?rest_route=/jwt-auth/v1/token"
        data = {"username": username, "password": password}
        
        response = requests.post(url, json=data)
        if response.status_code == 200:
            self.token = response.json()["data"]["token"]
            return self.token
        else:
            raise Exception("Login failed")
    
    def get_posts(self, params=None):
        url = f"{self.base_url}/index.php?rest_route=/wp/v2/posts"
        headers = {}
        if self.token:
            headers["Authorization"] = f"Bearer {self.token}"
        
        response = requests.get(url, headers=headers, params=params)
        return response.json()
    
    def get_products(self, params=None):
        if not self.token:
            raise Exception("Authentication required")
        
        url = f"{self.base_url}/index.php?rest_route=/wc/v3/products"
        headers = {"Authorization": f"Bearer {self.token}"}
        
        response = requests.get(url, headers=headers, params=params)
        return response.json()

# Exemplo de uso
api = WordPressAPI()
api.login("admin", "password")
posts = api.get_posts()
products = api.get_products()
```

## 7. Testando com Postman

### Collection para Postman
```json
{
  "info": {
    "name": "WordPress API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Login",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"username\": \"admin\",\n  \"password\": \"password\"\n}"
        },
        "url": {
          "raw": "http://localhost:8080/index.php?rest_route=/jwt-auth/v1/token",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8080",
          "path": ["index.php"],
          "query": [
            {
              "key": "rest_route",
              "value": "/jwt-auth/v1/token"
            }
          ]
        }
      }
    },
    {
      "name": "Get Posts",
      "request": {
        "method": "GET",
        "url": {
          "raw": "http://localhost:8080/index.php?rest_route=/wp/v2/posts",
          "protocol": "http",
          "host": ["localhost"],
          "port": "8080",
          "path": ["index.php"],
          "query": [
            {
              "key": "rest_route",
              "value": "/wp/v2/posts"
            }
          ]
        }
      }
    }
  ]
}
```

## 8. Troubleshooting

### Erro 401 (Não Autorizado)
- Verifique se o token JWT é válido
- Confirme se o usuário tem permissões adequadas
- Teste o endpoint de validação do token

### Erro 404 (Não Encontrado)
- Verifique se a URL está correta
- Confirme se o endpoint existe
- Teste com `index.php?rest_route=` em vez de `/wp-json/`

### Erro CORS
- O WordPress já está configurado para aceitar requisições de qualquer origem
- Se persistir, verifique se o plugin JWT está ativo

### Token Expirado
- Use o endpoint de refresh para renovar o token
- Ou faça login novamente 