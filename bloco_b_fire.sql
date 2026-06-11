CREATE DATABASE IF NOT EXISTS bloco_b_fire
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bloco_b_fire;

-- Usuários (admin e visitante)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','usuario') DEFAULT 'usuario',
    codigo_2fa VARCHAR(6) DEFAULT NULL,
    expiracao_2fa DATETIME DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Extintores
CREATE TABLE extintores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    tipo VARCHAR(50) NOT NULL,
    capacidade VARCHAR(20) NOT NULL,
    fabricante VARCHAR(100) DEFAULT NULL,
    numero_serie VARCHAR(100) DEFAULT NULL,
    data_instalacao DATE DEFAULT NULL,
    localizacao VARCHAR(200) NOT NULL,
    pavimento VARCHAR(50) DEFAULT 'Térreo',
    proxima_inspecao DATE NOT NULL,
    status ENUM('normal','a_vencer','vencido') DEFAULT 'normal',
    ativo TINYINT(1) DEFAULT 1,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin padrão (senha: Admin@123)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES (
    'Administrador',
    'admin@blocob.edu.br',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXDmABplC',
    'admin'
);

-- Usuário padrão para testes (senha: Teste@123)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES (
    'Visitante',
    'visitante@blocob.edu.br',
    '$2y$10$TKh8H1.PfunrGVALCEZIkOGMkh.zRi5XwVqXIwDHgTxIYHsBwHs.q',
    'usuario'
);