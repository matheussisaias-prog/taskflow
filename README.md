# Terra System CRM

Sistema CRM para gestão de leads, propostas, ordens de serviço e financeiro.

## Requisitos

- PHP 8.1+
- MySQL / MariaDB 10.4+
- Servidor web com suporte a `.htaccess` (Apache/XAMPP)

## Instalação

1. Clone o repositório na pasta do seu servidor web (ex: `htdocs/terra_system`)

```bash
git clone https://github.com/seu-usuario/terra-system.git
```

2. Copie o arquivo de configuração e preencha com seus dados:

```bash
cp config.example.php config.php
```

3. Edite o `config.php` com as credenciais do seu banco de dados.

4. Importe o banco de dados:

```bash
mysql -u root -p terra_system < system.sql
```

Ou pelo phpMyAdmin: crie um banco chamado `terra_system` e importe o arquivo `system.sql`.

5. Acesse o sistema pelo navegador e faça login com:

- **E-mail:** `admin@terrasystem.com.br`
- **Senha:** `password`

> ⚠️ Altere a senha do administrador imediatamente após o primeiro login.

## Estrutura

```
terra_system/
├── api/               # Endpoints JSON (AJAX)
├── assets/
│   ├── css/           # Estilos
│   └── js/            # Scripts
├── includes/          # Helpers: db.php, auth, nav
├── config.example.php # Modelo de configuração
├── system.sql         # Estrutura do banco
├── index.php          # Login
├── dashboard.php
├── comercial.php      # CRM / leads
├── propostas.php
├── ordens_servico.php
├── financeiro.php
├── empresas.php
└── configuracoes.php
```

## Segurança

- Nunca suba o `config.php` real para o repositório (já está no `.gitignore`)
- Troque a senha padrão após a instalação
- Em produção, configure o `.htaccess` para restringir acesso à pasta `includes/`
