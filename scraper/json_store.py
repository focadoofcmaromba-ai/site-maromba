# ============================================
# ARMAZENAMENTO JSON
# ============================================
#
# Responsável por salvar e carregar
# o noticias.json.
#
# Não realiza:
#
# • scraping
# • tradução
# • filtros
# • categorias
# • formatter
#
# ============================================

import json

from pathlib import Path

from config import (
    DATA_DIR,
    NEWS_FILE,
)

# ============================================
# CAMINHOS
# ============================================

DATA_PATH = Path(DATA_DIR)

JSON_PATH = DATA_PATH / NEWS_FILE


# ============================================
# PREPARAÇÃO
# ============================================

def ensure_directory():
    """
    Garante que o diretório exista.
    """

    DATA_PATH.mkdir(

        parents=True,

        exist_ok=True

    )


def ensure_file():
    """
    Garante que o arquivo JSON exista.
    """

    ensure_directory()

    if JSON_PATH.exists():

        return

    JSON_PATH.write_text(

        "[]",

        encoding="utf-8"

    )

# ============================================
# LEITURA
# ============================================

def load_json():
    """
    Carrega o conteúdo do noticias.json.
    """

    ensure_file()

    try:

        with JSON_PATH.open(

            "r",

            encoding="utf-8"

        ) as file:

            return json.load(file)

    except Exception:

        return []


# ============================================
# VALIDAÇÃO
# ============================================

def validate_news(news):
    """
    Verifica se uma notícia possui
    a estrutura esperada.
    """

    if not isinstance(news, dict):

        return False

    required_fields = [

        "titulo",

        "descricao",

        "imagem",

        "categoria",

        "visualizacoes",

        "data",

        "fonte",

        "link"

    ]

    for field in required_fields:

        if field not in news:

            return False

    return True


def validate_news_list(news_list):
    """
    Remove registros inválidos.
    """

    if not isinstance(news_list, list):

        return []

    valid_news = []

    for news in news_list:

        if validate_news(news):

            valid_news.append(news)

    return valid_news

# ============================================
# ESCRITA
# ============================================

def save_json(news_list):
    """
    Salva a lista de notícias
    no noticias.json.
    """

    ensure_file()

    news_list = validate_news_list(

        news_list

    )

    with JSON_PATH.open(

        "w",

        encoding="utf-8"

    ) as file:

        json.dump(

            news_list,

            file,

            ensure_ascii=False,

            indent=4

        )

    return True


# ============================================
# LIMPEZA
# ============================================

def clear_json():
    """
    Remove todas as notícias
    do arquivo.
    """

    ensure_file()

    return save_json([])


# ============================================
# QUANTIDADE
# ============================================

def news_count():
    """
    Retorna a quantidade
    de notícias armazenadas.
    """

    return len(

        load_json()

    )

# ============================================
# ESTATÍSTICAS
# ============================================

def json_statistics():
    """
    Retorna estatísticas do
    noticias.json.
    """

    news = load_json()

    return {

        "total_news": len(news),

        "file_exists": JSON_PATH.exists(),

        "directory_exists": DATA_PATH.exists(),

        "path": str(JSON_PATH)

    }


# ============================================
# ATUALIZAÇÃO
# ============================================

def update_json(news_list):
    """
    Atualiza o arquivo noticias.json
    substituindo completamente o conteúdo.
    """

    return save_json(

        news_list

    )


def append_news(news):
    """
    Adiciona uma notícia ao JSON.
    """

    if not validate_news(news):

        return False

    news_list = load_json()

    news_list.append(

        news

    )

    return save_json(

        news_list

    )

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def initialize_store():
    """
    Inicializa a estrutura de armazenamento.

    Garante que o diretório e o arquivo
    noticias.json existam.
    """

    ensure_file()

    return JSON_PATH.exists()


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "initialize_store",

    "ensure_directory",

    "ensure_file",

    "load_json",

    "save_json",

    "update_json",

    "append_news",

    "clear_json",

    "news_count",

    "json_statistics",

    "validate_news",

    "validate_news_list"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável exclusivamente
# pelo armazenamento do noticias.json.
#
# Responsabilidades:
#
# • criar diretório;
# • criar arquivo;
# • carregar notícias;
# • salvar notícias;
# • atualizar notícias;
# • validar estrutura;
# • gerar estatísticas.
#
# Não realiza:
#
# • scraping;
# • tradução;
# • filtros;
# • categorização;
# • formatação.
#
# O resultado será utilizado pelo
# collect_news.py.
#
# ============================================