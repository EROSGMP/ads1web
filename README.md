# NewBank - Plataforma de Empréstimos

Uma plataforma moderna de empréstimos inspirada no Nubank, desenvolvida com PHP, MySQL, HTML, CSS e JavaScript.

## 🚀 Funcionalidades

- **Cadastro de Clientes**: Sistema completo de registro com validações
- **Login Seguro**: Autenticação com senhas criptografadas
- **Simulador de Empréstimos**: Cálculo inteligente de juros baseado em valor e prazo
- **Solicitação de Empréstimos**: Processo 100% digital com análise automática
- **Dashboard Personalizado**: Painel completo para gerenciamento
- **Histórico de Simulações**: Acompanhamento de todas as simulações realizadas
- **Design Responsivo**: Interface adaptável para todos os dispositivos

## 📋 Pré-requisitos

- **MAMP/XAMPP/WAMP** (Apache + MySQL + PHP)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Navegador moderno**

## 🛠️ Instalação

### 1. Configurar o Servidor

1. Instale o MAMP (para macOS/Windows) ou XAMPP
2. Inicie o Apache e MySQL
3. Coloque os arquivos do projeto na pasta `htdocs` do MAMP

### 2. Configurar o Banco de Dados

1. Acesse o phpMyAdmin em `http://localhost/phpmyadmin`
2. Execute o script SQL em `database/schema.sql` para criar as tabelas
3. Verifique se as configurações em `config/database.php` estão corretas

### 3. Configurar URLs

Se necessário, ajuste os caminhos das APIs nos arquivos JavaScript para corresponder à sua configuração local.

## 🎯 Como Usar

### Para Novos Usuários

1. Acesse `http://localhost/trabalho_web_gustavo`
2. Clique em "Criar Conta" no cabeçalho
3. Preencha seus dados pessoais
4. Faça login com seu e-mail e senha

### Simulação de Empréstimo

1. Na página inicial, vá até a seção "Simulador"
2. Insira o valor desejado (R$ 500 - R$ 100.000)
3. Escolha o prazo de pagamento
4. Clique em "Simular" para ver o resultado

### Solicitação de Empréstimo

1. Após fazer uma simulação (logado)
2. Clique em "Solicitar Empréstimo"
3. Sua solicitação será analisada automaticamente
4. Acompanhe o status no dashboard

### Dashboard

Após o login, acesse funcionalidades como:
- Limite disponível baseado na renda
- Histórico de empréstimos
- Simulações salvas
- Atualização de dados pessoais

## 💡 Lógica de Análise de Crédito

O sistema utiliza critérios automáticos para aprovação:

- **Renda mínima**: R$ 1.500
- **Comprometimento máximo**: 30% da renda mensal
- **Valor máximo**: R$ 50.000
- **Taxa de juros**: Varia de 1,99% a 2,5% ao mês baseada no valor e prazo

## 🎨 Design

O design foi inspirado no Nubank com:
- **Cores**: Gradientes em tons de roxo (#8A2BE2, #9400D3)
- **Tipografia**: Segoe UI e fontes modernas
- **Componentes**: Cards, modais e animações suaves
- **Responsividade**: Layout adaptável para mobile e desktop

## 📁 Estrutura do Projeto

```
trabalho_web_gustavo/
├── index.html              # Página principal
├── dashboard.html           # Dashboard do cliente
├── README.md               # Este arquivo
├── assets/
│   ├── css/
│   │   ├── style.css       # Estilos principais
│   │   └── dashboard.css   # Estilos do dashboard
│   └── js/
│       ├── main.js         # JavaScript principal
│       └── dashboard.js    # JavaScript do dashboard
├── api/
│   ├── cadastro.php        # API de cadastro
│   ├── login.php           # API de login
│   ├── solicitar_emprestimo.php
│   ├── salvar_simulacao.php
│   ├── obter_emprestimos.php
│   ├── obter_simulacoes.php
│   └── atualizar_cliente.php
├── config/
│   └── database.php        # Configuração do banco
└── database/
    └── schema.sql          # Schema do banco de dados
```

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Validação de CPF no frontend e backend
- Proteção contra SQL Injection com prepared statements
- Validação de dados em todas as APIs
- Headers CORS configurados

## 📱 Responsividade

A plataforma é totalmente responsiva com:
- Breakpoints para tablet (768px) e mobile (480px)
- Menu adaptável
- Cards e grids flexíveis
- Navegação touch-friendly

## 🚀 Próximas Melhorias

- [ ] Sistema de notificações
- [ ] Integração com APIs de consulta de CPF
- [ ] Dashboard administrativo
- [ ] Relatórios e gráficos
- [ ] Sistema de pagamentos
- [ ] App mobile nativo

## 👨‍💻 Desenvolvedor

Projeto desenvolvido como trabalho acadêmico, demonstrando conhecimentos em:
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Backend**: PHP 7+, MySQL
- **Design**: UI/UX moderno e responsivo
- **Segurança**: Boas práticas de desenvolvimento web

---

**NewBank** - Empréstimos inteligentes para pessoas inteligentes! 🏦✨ 