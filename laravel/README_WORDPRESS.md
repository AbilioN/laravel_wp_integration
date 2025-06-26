# Integração Laravel + WordPress

Este sistema permite ler e exibir páginas do WordPress através do Laravel, com uma navbar dinâmica que se atualiza automaticamente e uma página inicial que reflete a configuração do WordPress.

## Funcionalidades

### ✅ Página Inicial Dinâmica
- **Rota `/`** - Mostra a mesma página inicial que o WordPress
- **Configuração automática** - Detecta se WordPress mostra posts ou página específica
- **Título e descrição** do site vindos do WordPress
- **Posts recentes** na sidebar ou como conteúdo principal

### ✅ Páginas do WordPress
- **Listar todas as páginas:** `/wordpress/pages`
- **Visualizar página específica:** `/wordpress/pages/{slug}`
- **Buscar páginas:** `/wordpress/pages/search?q=termo`
- **Visualizar por ID:** `/wordpress/pages/id/{id}`

### ✅ Posts do WordPress
- **Listar posts:** `/wordpress/posts`
- **Configurar limite:** `/wordpress/posts?limit=20`

### ✅ Navbar Dinâmica
- **Atualização automática** quando páginas são criadas/excluídas no WordPress
- **Cache de 5 minutos** para melhor performance
- **Posts recentes** em dropdown
- **Busca integrada** na navbar
- **Indicador de contagem** de páginas e posts

### ✅ APIs REST
- **Página inicial:** `/wordpress/api/home`
- **Páginas:** `/wordpress/api/pages`
- **Página específica:** `/wordpress/api/pages/{slug}`
- **Dados da navbar:** `/wordpress/api/navbar`
- **Limpar cache:** `POST /wordpress/api/navbar/clear-cache`

## Como Funciona

### 1. Página Inicial Inteligente
- **Detecta automaticamente** se o WordPress está configurado para mostrar posts ou página específica
- **Respeita as configurações** do WordPress (Settings > Reading)
- **Mostra posts recentes** se configurado para posts
- **Mostra página específica** se configurado para página estática

### 2. Conexão com WordPress
- O Laravel conecta diretamente ao banco do WordPress
- Usa as tabelas `wp_posts` e `wp_options` do WordPress
- Respeita o status `publish` das páginas e posts

### 3. Navbar Dinâmica
- Busca automaticamente todas as páginas publicadas
- Ordena por `menu_order` e `post_title`
- Cache de 5 minutos para performance
- Se atualiza quando você cria/exclui páginas no WordPress

### 4. Cache Inteligente
- Cache automático da navbar
- Cache das consultas ao banco
- Limpeza automática do cache

## URLs Disponíveis

### Interface Web
```
http://localhost:8005/                    # Página inicial (mesma do WordPress)
http://localhost:8005/wordpress/pages     # Lista de páginas
http://localhost:8005/wordpress/pages/{slug} # Página específica
http://localhost:8005/wordpress/pages/search # Busca
http://localhost:8005/wordpress/posts     # Lista de posts
```

### APIs
```
http://localhost:8005/wordpress/api/home  # JSON com dados da página inicial
http://localhost:8005/wordpress/api/pages # JSON com todas as páginas
http://localhost:8005/wordpress/api/pages/{slug} # JSON com página específica
http://localhost:8005/wordpress/api/navbar # JSON com dados da navbar
```

## Estrutura dos Arquivos

```
laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── WordPressController.php    # Controller principal
│   │   └── HomeController.php         # Controller da página inicial
│   └── Models/
│       ├── WordPressPost.php         # Modelo para páginas/posts
│       ├── WordPressMenu.php         # Modelo para navbar
│       └── WordPressSettings.php     # Modelo para configurações
├── resources/views/
│   ├── components/
│   │   └── wordpress-navbar.blade.php # Componente da navbar
│   ├── home.blade.php                # Página inicial
│   └── wordpress/
│       ├── pages/
│       │   ├── index.blade.php       # Lista de páginas
│       │   ├── show.blade.php        # Página específica
│       │   └── search.blade.php      # Resultados de busca
│       └── posts/
│           └── index.blade.php       # Lista de posts
└── routes/
    └── web.php                       # Rotas do WordPress
```

## Configuração

### 1. Banco de Dados
O sistema usa duas conexões:
- **Laravel:** Para dados do Laravel
- **WordPress:** Para dados do WordPress

### 2. Variáveis de Ambiente
```env
# WordPress Database
WORDPRESS_DB_HOST=db
WORDPRESS_DB_PORT=3306
WORDPRESS_DB_NAME=wordpress_db
WORDPRESS_DB_USER=wordpress_user
WORDPRESS_DB_PASSWORD=wordpress_password
```

## Como Usar

### 1. Configurar Página Inicial no WordPress
- Acesse: `http://wordpress.local/wp-admin`
- Vá em **Settings > Reading**
- Escolha:
  - **"Your latest posts"** - Para mostrar posts na inicial
  - **"A static page"** - Para mostrar uma página específica

### 2. Criar/Editar Páginas
- Crie ou edite páginas normalmente no WordPress
- A navbar do Laravel se atualizará automaticamente
- A página inicial refletirá as mudanças

### 3. Navegar pelo Laravel
- Acesse: `http://localhost:8005/` (página inicial)
- Use a navbar dinâmica para navegar
- Busque páginas usando a barra de pesquisa

### 4. Integrar com Outros Sistemas
- Use as APIs REST para consumir os dados
- Exemplo: `curl http://localhost:8005/wordpress/api/home`

## Tipos de Página Inicial

### 1. Posts na Página Inicial
- Mostra uma seção hero com título do site
- Lista posts recentes em cards
- Sidebar com navegação

### 2. Página Estática na Inicial
- Mostra o conteúdo da página específica
- Sidebar com posts recentes
- Layout otimizado para conteúdo

## Vantagens

✅ **Página inicial sincronizada** - Mesma configuração do WordPress
✅ **Detecção automática** - Posts ou página estática
✅ **Sincronização automática** - Mudanças no WordPress refletem no Laravel
✅ **Performance otimizada** - Cache inteligente
✅ **Interface responsiva** - Bootstrap + Font Awesome
✅ **APIs REST** - Fácil integração
✅ **Busca avançada** - Busca em título e conteúdo
✅ **Navbar dinâmica** - Sempre atualizada

## Tecnologias Utilizadas

- **Laravel 10** - Framework PHP
- **Bootstrap 5** - Interface responsiva
- **Font Awesome** - Ícones
- **MySQL** - Banco de dados
- **Docker** - Containerização

## Suporte

Para dúvidas ou problemas:
1. Verifique se os containers estão rodando: `docker-compose ps`
2. Teste a página inicial: `curl http://localhost:8005/`
3. Teste a API: `curl http://localhost:8005/wordpress/api/home`
4. Limpe o cache se necessário: `curl -X POST http://localhost:8005/wordpress/api/navbar/clear-cache` 