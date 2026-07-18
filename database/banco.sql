-- Cria a base de dados se ela não existir
CREATE DATABASE IF NOT EXISTS sistema_artigos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_artigos;

-- Tabela de Autores
CREATE TABLE IF NOT EXISTS autores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    bio TEXT,
    foto VARCHAR(255),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    descricao TEXT,
    seo_titulo VARCHAR(200),
    seo_descricao TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Subcategorias
CREATE TABLE IF NOT EXISTS subcategorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    descricao TEXT,
    seo_titulo VARCHAR(200),
    seo_descricao TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Tags
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    descricao TEXT,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela principal de Artigos
CREATE TABLE IF NOT EXISTS artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    categoria_id INT NOT NULL,
    subcategoria_id INT,
    autor_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    subtitulo TEXT,
    resumo TEXT,
    conteudo LONGTEXT NOT NULL,
    tempo_leitura INT NOT NULL,
    imagem_destacada VARCHAR(255),
    data_publicacao DATETIME NOT NULL,
    data_ultima_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    seo_titulo VARCHAR(200),
    seo_descricao TEXT,
    seo_keywords TEXT,
    tipo_publicacao VARCHAR(20) DEFAULT 'publico',
    status VARCHAR(20) DEFAULT 'publicado',
    data_agendamento DATETIME NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (subcategoria_id) REFERENCES subcategorias(id),
    FOREIGN KEY (autor_id) REFERENCES autores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento entre Artigos e Tags
CREATE TABLE IF NOT EXISTS artigos_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artigo_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (artigo_id) REFERENCES artigos(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY (artigo_id, tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de visualizações dos artigos
CREATE TABLE IF NOT EXISTS visualizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artigo_id INT NOT NULL,
    ip_usuario VARCHAR(45) NOT NULL,
    data_visualizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artigo_id) REFERENCES artigos(id) ON DELETE CASCADE,
    INDEX (artigo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de imagens da galeria dos artigos
CREATE TABLE IF NOT EXISTS galeria_artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artigo_id INT NOT NULL,
    caminho_imagem VARCHAR(255) NOT NULL,
    legenda TEXT,
    ordem INT DEFAULT 0,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artigo_id) REFERENCES artigos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de dados do FAQ dos artigos
CREATE TABLE IF NOT EXISTS faq_artigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artigo_id INT NOT NULL,
    pergunta TEXT NOT NULL,
    resposta TEXT NOT NULL,
    ordem INT DEFAULT 0,
    FOREIGN KEY (artigo_id) REFERENCES artigos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cria índices para otimizar consultas em milhares de registros
CREATE INDEX idx_artigos_data ON artigos(data_publicacao);
CREATE INDEX idx_artigos_status ON artigos(status);
CREATE INDEX idx_visualizacoes_data ON visualizacoes(data_visualizacao);
