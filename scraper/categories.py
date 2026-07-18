# ============================================
# CATEGORIAS DO PROJETO DYXON
# ============================================
#
# Responsável por identificar automaticamente
# a categoria de cada notícia.
#
# Nenhum scraping é realizado aqui.
#
# ============================================

from config import (
    CATEGORIES,
    KEYWORDS,
)

from utils import (
    normalize_text,
)

# ============================================
# PALAVRAS-CHAVE
# ============================================

def get_keywords(category):
    """
    Retorna as palavras-chave de uma categoria.
    """

    return KEYWORDS.get(category, [])


def keyword_exists(text, keyword):
    """
    Verifica se uma palavra-chave existe no texto.
    """

    text = normalize_text(text).lower()

    keyword = normalize_text(keyword).lower()

    return keyword in text


def count_keyword_matches(text, keywords):
    """
    Conta quantas palavras-chave foram encontradas.
    """

    matches = 0

    for keyword in keywords:

        if keyword_exists(text, keyword):

            matches += 1

    return matches

# ============================================
# IDENTIFICAÇÃO DE CATEGORIAS
# ============================================

def detect_category(text):
    """
    Identifica a categoria mais adequada
    para um determinado texto.
    """

    if not text:

        return "Outros"

    text = normalize_text(text)

    best_category = "Outros"

    best_score = 0

    for category in CATEGORIES:

        keywords = get_keywords(category)

        score = count_keyword_matches(

            text,

            keywords

        )

        if score > best_score:

            best_score = score

            best_category = category

    return best_category


def detect_category_from_news(news):
    """
    Identifica a categoria utilizando
    título e descrição da notícia.
    """

    if not isinstance(news, dict):

        return "Outros"

    title = news.get(

        "titulo",

        ""

    )

    description = news.get(

        "descricao",

        ""

    )

    full_text = f"{title} {description}"

    return detect_category(full_text)


# ============================================
# PRIORIDADE
# ============================================

def category_priority(category):
    """
    Retorna a prioridade de cada categoria.
    """

    priorities = {

        "Treino": 1,

        "Nutrição": 2,

        "Saúde": 3,

        "Atletas": 4,

        "Campeonatos": 5,

        "Outros": 99

    }

    return priorities.get(

        category,

        99

    )

# ============================================
# CLASSIFICAÇÃO
# ============================================

def classify_news(news):
    """
    Classifica uma única notícia.

    Adiciona ou atualiza o campo
    'categoria'.
    """

    if not isinstance(news, dict):

        return {}

    classified = news.copy()

    classified["categoria"] = detect_category_from_news(

        classified

    )

    return classified


def classify_news_list(news_list):
    """
    Classifica todas as notícias
    da lista.
    """

    if not isinstance(news_list, list):

        return []

    classified = []

    for news in news_list:

        classified.append(

            classify_news(news)

        )

    return classified


# ============================================
# ORDENAÇÃO POR CATEGORIA
# ============================================

def sort_by_category(news_list):
    """
    Organiza a lista pela prioridade
    das categorias.
    """

    try:

        return sorted(

            news_list,

            key=lambda news: category_priority(

                news.get(

                    "categoria",

                    "Outros"

                )

            )

        )

    except Exception:

        return news_list


# ============================================
# VALIDAÇÃO
# ============================================

def validate_category(category):
    """
    Garante que a categoria exista.

    Caso contrário retorna 'Outros'.
    """

    if category in CATEGORIES:

        return category

    return "Outros"

# ============================================
# ESTATÍSTICAS
# ============================================

def count_categories(news_list):
    """
    Conta quantas notícias existem
    em cada categoria.
    """

    statistics = {

        category: 0

        for category in CATEGORIES

    }

    statistics["Outros"] = 0

    for news in news_list:

        category = validate_category(

            news.get(

                "categoria",

                "Outros"

            )

        )

        statistics[category] += 1

    return statistics


def get_categories_statistics(news_list):
    """
    Retorna informações estatísticas
    das categorias.
    """

    statistics = count_categories(news_list)

    total = len(news_list)

    return {

        "total_news": total,

        "categories": statistics

    }


# ============================================
# ORGANIZAÇÃO
# ============================================

def prepare_categories(news_list):
    """
    Classifica e organiza todas
    as notícias.
    """

    news_list = classify_news_list(

        news_list

    )

    news_list = sort_by_category(

        news_list

    )

    return news_list

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def apply_categories(news_list):
    """
    Executa todo o processo de categorização.

    Fluxo:

        1. Classifica todas as notícias.
        2. Organiza por prioridade.
        3. Gera estatísticas.

    Retorna um dicionário contendo a lista
    classificada e as estatísticas.
    """

    if not isinstance(news_list, list):

        return {

            "news": [],

            "statistics": {

                "total_news": 0,

                "categories": {}

            }

        }

    classified_news = prepare_categories(

        news_list

    )

    statistics = get_categories_statistics(

        classified_news

    )

    return {

        "news": classified_news,

        "statistics": statistics

    }


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "apply_categories",

    "prepare_categories",

    "classify_news",

    "classify_news_list",

    "detect_category",

    "detect_category_from_news",

    "validate_category",

    "count_categories",

    "get_categories_statistics",

    "category_priority"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável exclusivamente
# pela categorização automática das notícias.
#
# Nenhum scraping é realizado aqui.
#
# O resultado deste módulo será utilizado
# pelo formatter.py.
#
# ============================================