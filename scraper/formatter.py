# ============================================
# FORMATADOR DO PROJETO DYXON
# ============================================
#
# Responsável por padronizar todas as notícias
# antes da geração do noticias.json.
#
# Este módulo não realiza scraping.
# Este módulo não realiza filtros.
# Este módulo não classifica categorias.
#
# ============================================

import random

from config import (
    MIN_VIEWS,
    MAX_VIEWS,
)

from utils import (
    normalize_text,
)

# ============================================
# NOTÍCIA PADRÃO
# ============================================

def create_news_template():
    """
    Retorna a estrutura padrão utilizada
    pelo projeto DYXON.
    """

    return {

        "titulo": "",

        "descricao": "",

        "imagem": "",

        "categoria": "Outros",

        "visualizacoes": 0,

        "data": "",

        "fonte": "",

        "link": ""

    }


# ============================================
# VISUALIZAÇÕES
# ============================================

def generate_views():
    """
    Gera uma quantidade aleatória
    de visualizações.
    """

    return random.randint(

        MIN_VIEWS,

        MAX_VIEWS

    )

# ============================================
# FORMATAÇÃO DOS CAMPOS
# ============================================

def format_title(title):
    """
    Formata o título da notícia.
    """

    return normalize_text(title)


def format_description(description):
    """
    Formata a descrição da notícia.
    """

    return normalize_text(description)


def format_source(source):
    """
    Formata o nome da fonte.
    """

    return normalize_text(source)


def format_image(image):
    """
    Formata a URL da imagem.
    """

    return normalize_text(image)


def format_link(link):
    """
    Formata o link da notícia.
    """

    return normalize_text(link)


def format_date(date):
    """
    Formata a data.

    Caso não exista, retorna string vazia.
    """

    if not date:

        return ""

    return str(date)

# ============================================
# FORMATAÇÃO DA NOTÍCIA
# ============================================

def format_news(news):
    """
    Padroniza uma única notícia para o formato
    utilizado pelo projeto DYXON.
    """

    if not isinstance(news, dict):

        return create_news_template()

    formatted = create_news_template()

    formatted["titulo"] = format_title(

        news.get("titulo", "")

    )

    formatted["descricao"] = format_description(

        news.get("descricao", "")

    )

    formatted["imagem"] = format_image(

        news.get("imagem", "")

    )

    formatted["categoria"] = normalize_text(

        news.get("categoria", "Outros")

    )

    formatted["visualizacoes"] = generate_views()

    formatted["data"] = format_date(

        news.get("data", "")

    )

    formatted["fonte"] = format_source(

        news.get("fonte", "")

    )

    formatted["link"] = format_link(

        news.get("link", "")

    )

    return formatted


# ============================================
# FORMATAÇÃO DE LISTAS
# ============================================

def format_news_list(news_list):
    """
    Formata uma lista completa de notícias.
    """

    if not isinstance(news_list, list):

        return []

    formatted_news = []

    for news in news_list:

        formatted_news.append(

            format_news(news)

        )

    return formatted_news


# ============================================
# ORDENAÇÃO
# ============================================

def sort_formatted_news(news_list):
    """
    Organiza as notícias por data
    em ordem decrescente.
    """

    try:

        return sorted(

            news_list,

            key=lambda news: news.get(

                "data",

                ""

            ),

            reverse=True

        )

    except Exception:

        return news_list

# ============================================
# ESTATÍSTICAS
# ============================================

def formatter_statistics(news_list):
    """
    Gera estatísticas simples da lista
    formatada.
    """

    total = len(news_list)

    return {

        "total_news": total,

        "formatted_news": total

    }


# ============================================
# ORGANIZAÇÃO
# ============================================

def prepare_formatted_news(news_list):
    """
    Executa toda a preparação final
    das notícias formatadas.
    """

    news_list = format_news_list(

        news_list

    )

    news_list = sort_formatted_news(

        news_list

    )

    return news_list


# ============================================
# VALIDAÇÃO
# ============================================

def validate_formatted_news(news_list):
    """
    Remove notícias cujo título esteja vazio
    após a formatação.
    """

    validated = []

    for news in news_list:

        if news.get("titulo", "").strip():

            validated.append(news)

    return validated

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def apply_formatter(news_list):
    """
    Executa todo o processo de formatação
    das notícias.

    Fluxo:

        1. Formata todas as notícias.
        2. Ordena a lista.
        3. Remove registros inválidos.
        4. Gera estatísticas.

    Retorna um dicionário contendo a lista
    formatada e as estatísticas.
    """

    if not isinstance(news_list, list):

        return {

            "news": [],

            "statistics": {

                "total_news": 0,

                "formatted_news": 0

            }

        }

    formatted_news = prepare_formatted_news(

        news_list

    )

    formatted_news = validate_formatted_news(

        formatted_news

    )

    statistics = formatter_statistics(

        formatted_news

    )

    return {

        "news": formatted_news,

        "statistics": statistics

    }


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "apply_formatter",

    "create_news_template",

    "format_news",

    "format_news_list",

    "prepare_formatted_news",

    "validate_formatted_news",

    "formatter_statistics",

    "generate_views",

    "format_title",

    "format_description",

    "format_source",

    "format_image",

    "format_link",

    "format_date"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável por padronizar
# todas as notícias antes da geração do
# arquivo noticias.json.
#
# Não realiza scraping.
# Não realiza categorização.
# Não realiza filtragem.
#
# Sua única responsabilidade é garantir
# que todas as notícias possuam exatamente
# a mesma estrutura de dados.
#
# ============================================