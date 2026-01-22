# 📦 Sistema de Almoxarifado - Prefeitura de Maranguape

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Sistema completo de gerenciamento de almoxarifado com controle de estoque, solicitações e relatórios**

[Funcionalidades](#-funcionalidades) • [Tecnologias](#-tecnologias) • [Demonstração](#-tecnologias-e-habilidades-demonstradas)

</div>

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Uso do Sistema](#-uso-do-sistema)
- [Screenshots](#-screenshots)
- [Tecnologias e Habilidades](#-tecnologias-e-habilidades-demonstradas)
- [Desenvolvedores](#-desenvolvedores)
- [Contato](#-contato)

---

## 🎯 Sobre o Projeto

O **Sistema de Almoxarifado** é uma aplicação web desenvolvida para a **Prefeitura de Maranguape** com o objetivo de modernizar e otimizar o controle de estoque e gerenciamento de materiais. O sistema oferece uma interface intuitiva e responsiva, permitindo o controle completo de entradas, saídas, solicitações e relatórios de movimentação.

### ✨ Principais Benefícios

- ✅ **Controle Total de Estoque**: Acompanhamento em tempo real de todos os itens
- ✅ **Gestão de Solicitações**: Sistema completo de aprovação e recusa de pedidos
- ✅ **Dashboard Inteligente**: Visão geral com estatísticas e alertas
- ✅ **Interface Responsiva**: Funciona perfeitamente em desktop, tablet e mobile
- ✅ **Sistema de Notificações**: Alertas para estoque crítico e solicitações pendentes
- ✅ **Relatórios em PDF**: Geração de relatórios por período

---

## 🚀 Funcionalidades

### 👤 Para Usuários

- **Criação de Solicitações**: Solicitar retirada de itens do estoque
- **Acompanhamento**: Visualizar status das solicitações em tempo real
- **Histórico**: Consultar histórico completo de solicitações
- **Dashboard Personalizado**: Visualizar estatísticas pessoais

### 🧑‍💼 Para Administradores

- **Cadastro de Itens**: Cadastrar novos produtos com informações completas
- **Gestão de Estoque**: Adicionar quantidades aos itens existentes
- **Aprovação/Recusa**: Gerenciar solicitações com observações obrigatórias
- **Dashboard Administrativo**: 
  - Visão geral de todo o sistema
  - Alertas de estoque crítico
  - Estatísticas de solicitações
  - Itens em falta
- **Relatórios**: Gerar relatórios em PDF por período
- **Gestão de Usuários**: Visualizar todos os usuários do sistema

### 📊 Dashboard

O dashboard oferece uma visão completa do sistema:

- **Cards de Estatísticas**:
  - Total de itens em estoque
  - Solicitações do dia
  - Itens críticos (estoque ≤ 10)
  - Itens em falta (estoque = 0)

- **Alertas Inteligentes**:
  - Notificação de itens críticos
  - Alertas de itens em falta
  - Contador de solicitações pendentes

- **Tabelas Informativas**:
  - Lista de itens com estoque crítico
  - Últimas solicitações realizadas
  - Resumo do dia

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 8.0+**: Linguagem de programação server-side
- **MySQL/MariaDB**: Banco de dados relacional
- **PDO**: Interface de acesso a dados
- **FPDF**: Geração de relatórios em PDF

### Frontend
- **HTML5**: Estrutura semântica
- **Tailwind CSS**: Framework CSS utility-first
- **JavaScript (Vanilla)**: Interatividade e validações
- **SVG Icons**: Ícones vetoriais personalizados

### Arquitetura
- **MVC (Model-View-Controller)**: Padrão de arquitetura
- **Sessões PHP**: Sistema de autenticação
- **Prepared Statements**: Segurança contra SQL Injection

---

## 📦 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- [XAMPP](https://www.apachefriends.org/) (ou similar) com:
  - PHP 8.0 ou superior
  - MySQL/MariaDB 10.4 ou superior
  - Apache Server
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Editor de código (opcional, para desenvolvimento)

---

## 💻 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/Almoxarifado.git
cd Almoxarifado
```

### 2. Configure o ambiente

1. Copie o projeto para a pasta `htdocs` do XAMPP:
   ```
   C:\xampp\htdocs\Almoxarifado
   ```

2. Inicie os serviços do XAMPP:
   - Apache
   - MySQL

### 3. Configure o banco de dados

1. Acesse o phpMyAdmin: `http://localhost/phpmyadmin`

2. Crie um novo banco de dados:
   ```sql
   CREATE DATABASE almoxarifado CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

3. Importe o arquivo SQL:
   - Vá em "Importar" no phpMyAdmin
   - Selecione o arquivo: `app/main/config/almoxarifado.sql`
   - Clique em "Executar"

### 4. Configure a conexão

Edite o arquivo `app/main/config/db.php`:

```php
$host = 'localhost';
$usuario = 'root';
$senha = ''; // Sua senha do MySQL (se houver)
$nome_banco = 'almoxarifado';
```

### 5. Execute o script de atualização (se necessário)

Acesse no navegador:
```
http://localhost/Almoxarifado/app/main/config/adicionar_observacao.php
```

Este script verifica e adiciona campos faltantes automaticamente.

---

## ⚙️ Configuração

### Criar primeiro usuário administrador

1. Acesse a página de cadastro: `http://localhost/Almoxarifado/app/main/view/cadastro.php`

2. Crie uma conta com tipo "Administrador"

3. Faça login: `http://localhost/Almoxarifado/app/main/view/index.php`

### Configurações adicionais

- **Timezone**: Configurado para `America/Sao_Paulo` em `db.php`
- **Sessões**: Gerenciadas automaticamente pelo sistema de autenticação
- **Upload de arquivos**: Não necessário (sistema não utiliza uploads)

---

## 📁 Estrutura do Projeto

```
Almoxarifado/
│
├── app/
│   └── main/
│       ├── assets/              # Recursos estáticos
│       │   ├── css/            # Estilos CSS
│       │   ├── images/         # Imagens do sistema
│       │   ├── js/             # Scripts JavaScript
│       │   └── vendor/         # Bibliotecas externas (FPDF)
│       │
│       ├── config/             # Configurações
│       │   ├── auth.php        # Sistema de autenticação
│       │   ├── db.php          # Conexão com banco de dados
│       │   └── *.sql           # Scripts SQL
│       │
│       ├── control/            # Controladores (Controllers)
│       │   ├── itensController.php
│       │   ├── solicitacoesController.php
│       │   └── ...
│       │
│       ├── model/              # Modelos (Models)
│       │   ├── ItensModel.php
│       │   ├── SolicitacoesModel.php
│       │   └── ...
│       │
│       └── view/               # Visualizações (Views)
│           ├── index.php       # Página de login
│           ├── cadastro.php    # Página de cadastro
│           └── painel/         # Painéis do sistema
│               ├── Admin/      # Área administrativa
│               └── Usuario/    # Área do usuário
│
└── README.md                   # Este arquivo
```

### Padrão MVC

O projeto segue o padrão **MVC (Model-View-Controller)**:

- **Model**: Lógica de negócio e acesso a dados (`model/`)
- **View**: Interface do usuário (`view/`)
- **Controller**: Intermediação entre Model e View (`control/`)

---

## 📖 Uso do Sistema

### Login

1. Acesse: `http://localhost/Almoxarifado/app/main/view/index.php`
2. Informe email e senha
3. O sistema redireciona automaticamente conforme o tipo de usuário

### Como Usuário

1. **Criar Solicitação**:
   - Acesse "Solicitações"
   - Selecione o item desejado
   - Informe a quantidade
   - Clique em "Enviar Solicitação"

2. **Acompanhar Solicitações**:
   - Visualize o status na página de solicitações
   - Clique no ícone de olho para ver observações

### Como Administrador

1. **Cadastrar Item**:
   - Acesse "Cadastrar Itens"
   - Preencha todos os campos
   - Clique em "Cadastrar"

2. **Gerenciar Estoque**:
   - Acesse "Estoque"
   - Clique em "Adicionar" no item desejado
   - Informe a quantidade a adicionar

3. **Aprovar/Recusar Solicitação**:
   - Acesse "Solicitações"
   - Clique no ícone de check (aprovar) ou X (recusar)
   - **Obrigatório**: Adicione uma observação
   - Confirme a ação

4. **Gerar Relatório**:
   - Acesse o Dashboard
   - Clique em "Exportar Relatório"
   - Selecione o período
   - Gere o PDF

---

## 📸 Screenshots

### Dashboard Administrador
O dashboard oferece uma visão completa com estatísticas, alertas e tabelas informativas.

<img width="1906" height="918" alt="image" src="https://github.com/user-attachments/assets/c98871f0-7a72-4a2c-bda9-4b3e4b9487fa" />


### Página de Solicitações
Interface intuitiva para criação e gerenciamento de solicitações.

<img width="1904" height="919" alt="image" src="https://github.com/user-attachments/assets/9b3da44b-c8a0-4881-a162-d08943fca2f1" />


### Gestão de Estoque
Controle completo de entrada e saída de materiais.

<img width="1918" height="919" alt="image" src="https://github.com/user-attachments/assets/a8b5d44b-b274-4fcf-b075-c1a145bd42cd" />


---

## 👥 Desenvolvedores

Este projeto foi desenvolvido por:

- **Roger Cavalcante** - [@rogercavalcantetz](https://instagram.com/rogercavalcantetz)

---

## 🔒 Segurança

O sistema implementa várias medidas de segurança:

- ✅ **Prepared Statements**: Proteção contra SQL Injection
- ✅ **Sistema de Autenticação**: Controle de acesso por tipo de usuário
- ✅ **Sanitização de Dados**: `htmlspecialchars()` em todas as saídas
- ✅ **Validação de Sessões**: Verificação de login em todas as páginas protegidas
- ✅ **Hash de Senhas**: Uso de `password_hash()` para armazenamento seguro

---

## 📝 Licença

Este projeto está sob a licença **MIT**. Veja o arquivo `LICENSE` para mais detalhes.

---

## 📞 Contato

Para mais informações sobre este projeto:

- 📱 Instagram: [@rogercavalcantetz](https://instagram.com/rogercavalcantetz)
- 💼 LinkedIn: [Roger Cavalcante](https://www.linkedin.com/in/roger-cavalcante-2a4704355/)

---

## 🎯 Tecnologias e Habilidades Demonstradas

Este projeto demonstra conhecimento e experiência em:

- **Backend**: PHP, MySQL, PDO, Arquitetura MVC
- **Frontend**: HTML5, Tailwind CSS, JavaScript (Vanilla)
- **Segurança**: Prepared Statements, Autenticação, Sanitização de dados
- **UX/UI**: Design responsivo, Interface intuitiva, Experiência do usuário
- **Documentação**: Código limpo, Comentários, Estrutura organizada

---

<div align="center">

**Desenvolvido com ❤️ para a Prefeitura de Maranguape**

*Sistema em produção - Projeto real desenvolvido para gestão pública*

</div>
