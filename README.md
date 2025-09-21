# IT Inventory

Sistema de inventário de TI desenvolvido em Laravel para gerenciamento de equipamentos e recursos tecnológicos.

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- PHP >= 8.2
- Composer
- Node.js >= 22.x
- NPM
- MySQL
- Git

## 🚀 Instalação

Siga os passos abaixo para configurar o projeto em seu ambiente local:

### 1. Clone o repositório

```bash
git clone https://github.com/Rafael-de-Sa/it-inventory.git
cd it-inventory
```

### 2. Instale as dependências do PHP

```bash
composer install
```

### 3. Instale as dependências do Node.js

```bash
npm install
```

### 4. Configure o arquivo de ambiente

Copie o arquivo de exemplo e configure as variáveis de ambiente:

```bash
cp .env.example .env
```

Edite o arquivo `.env` e configure a conexão com o banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=it_inventory
DB_USERNAME=user_it_inventory
DB_PASSWORD=e1UlwEjHys
```

> **Nota:** Ajuste a porta do banco de dados conforme sua configuração local, se necessário.

### 5. Prepare o banco de dados

Certifique-se de que o banco de dados MySQL esteja rodando e crie o banco `it_inventory` (caso ainda não exista).

### 6. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 7. Execute as migrações

```bash
php artisan migrate
```

### 8. Inicie o servidor de desenvolvimento

```bash
composer run dev
```

### 9. Acesse a aplicação

Abra seu navegador e acesse:

```
http://127.0.0.1:8000/
```

## 🛠️ Comandos Úteis

### Desenvolvimento

```bash
# Iniciar servidor de desenvolvimento
php artisan serve

# Compilar assets para desenvolvimento
npm run dev

# Executar migrações
php artisan migrate

# Reverter migrações
php artisan migrate:rollback

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 📞 Suporte

Se você encontrar algum problema ou tiver dúvidas, por favor abra uma [issue](https://github.com/Rafael-de-Sa/it-inventory/issues) no GitHub.

---

Desenvolvido com ❤️ por [Rafael de Sá](https://github.com/Rafael-de-Sa)
