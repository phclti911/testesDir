-- Script de criação das tabelas para o sistema de recrutamento (ajustado para UUID e score)
CREATE TABLE IF NOT EXISTS vagas (
    id CHAR(36) PRIMARY KEY, -- UUID
    empresa VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    localizacao VARCHAR(255) NOT NULL,
    nivel INT NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pessoa (
    id CHAR(36) PRIMARY KEY, -- UUID
    nome VARCHAR(255) NOT NULL,
    profissao VARCHAR(255) NOT NULL,
    localizacao VARCHAR(255) NOT NULL,
    nivel INT NOT NULL
);

CREATE TABLE IF NOT EXISTS candidaturas (
    id CHAR(36) PRIMARY KEY, -- UUID
    id_vaga CHAR(36) NOT NULL,
    id_pessoa CHAR(36) NOT NULL,
    score INT NOT NULL,
    data_candidatura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vaga) REFERENCES vagas(id),
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id)
);
