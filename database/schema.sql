-- Schema do NewBank
CREATE DATABASE IF NOT EXISTS newbank;
USE newbank;

-- Tabela de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    data_nascimento DATE NOT NULL,
    renda_mensal DECIMAL(10,2) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    endereco TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de empréstimos
CREATE TABLE emprestimos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    valor_solicitado DECIMAL(10,2) NOT NULL,
    taxa_juros DECIMAL(5,2) NOT NULL,
    prazo_meses INT NOT NULL,
    valor_parcela DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'pago') DEFAULT 'pendente',
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_aprovacao TIMESTAMP NULL,
    observacoes TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabela de simulações
CREATE TABLE simulacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    valor DECIMAL(10,2) NOT NULL,
    prazo_meses INT NOT NULL,
    taxa_juros DECIMAL(5,2) NOT NULL,
    valor_parcela DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

-- Inserir contas de teste (senha para todas: 123456)
-- MD5 de "123456" = e10adc3949ba59abbe56e057f20f883e
INSERT INTO clientes (nome, email, cpf, telefone, data_nascimento, renda_mensal, senha, endereco) VALUES
('João Silva', 'joao@teste.com', '123.456.789-00', '(11) 99999-9999', '1990-01-15', 5000.00, 'e10adc3949ba59abbe56e057f20f883e', 'Rua das Flores, 123 - São Paulo/SP'),
('Maria Santos', 'maria@teste.com', '987.654.321-00', '(11) 88888-8888', '1985-05-20', 7500.00, 'e10adc3949ba59abbe56e057f20f883e', 'Av. Paulista, 456 - São Paulo/SP'),
('Pedro Oliveira', 'pedro@teste.com', '111.222.333-44', '(11) 77777-7777', '1992-08-10', 3500.00, 'e10adc3949ba59abbe56e057f20f883e', 'Rua da Consolação, 789 - São Paulo/SP'),
('Ana Costa', 'ana@teste.com', '555.666.777-88', '(11) 66666-6666', '1988-12-03', 8500.00, 'e10adc3949ba59abbe56e057f20f883e', 'Av. Brigadeiro Faria Lima, 321 - São Paulo/SP'),
('Carlos Ferreira', 'carlos@teste.com', '999.888.777-66', '(11) 55555-5555', '1995-03-22', 2800.00, 'e10adc3949ba59abbe56e057f20f883e', 'Rua Augusta, 654 - São Paulo/SP'),
('Fernanda Lima', 'fernanda@teste.com', '444.333.222-11', '(11) 44444-4444', '1987-07-18', 12000.00, 'e10adc3949ba59abbe56e057f20f883e', 'Av. Berrini, 1234 - São Paulo/SP');

-- Inserir alguns empréstimos de exemplo
INSERT INTO emprestimos (cliente_id, valor_solicitado, taxa_juros, prazo_meses, valor_parcela, valor_total, status, observacoes, data_aprovacao) VALUES
(1, 10000.00, 2.15, 12, 917.50, 11010.00, 'aprovado', 'Empréstimo aprovado automaticamente', NOW()),
(2, 25000.00, 1.99, 24, 1247.50, 29940.00, 'aprovado', 'Empréstimo aprovado automaticamente', NOW()),
(3, 5000.00, 2.50, 6, 884.50, 5307.00, 'pendente', 'Aguardando análise manual', NULL),
(4, 50000.00, 1.99, 36, 1831.50, 65934.00, 'aprovado', 'Empréstimo aprovado automaticamente', NOW()),
(5, 15000.00, 2.35, 18, 992.75, 17869.50, 'rejeitado', 'Renda insuficiente para o valor solicitado', NULL);

-- Inserir algumas simulações de exemplo
INSERT INTO simulacoes (cliente_id, valor, prazo_meses, taxa_juros, valor_parcela, valor_total) VALUES
(1, 15000.00, 18, 2.20, 1001.25, 18022.50),
(1, 8000.00, 12, 2.30, 734.00, 8808.00),
(2, 30000.00, 24, 1.99, 1496.25, 35910.00),
(3, 7500.00, 12, 2.35, 698.75, 8385.00),
(4, 40000.00, 48, 1.99, 1332.00, 63936.00),
(6, 20000.00, 24, 2.00, 998.33, 23960.00); 