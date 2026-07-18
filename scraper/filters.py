# ============================================
# FILTROS DO PROJETO DYXON
# ============================================
#
# Este módulo é responsável por validar,
# limpar e filtrar as notícias coletadas.
#
# Nenhum scraping é realizado aqui.
#
# Entrada:
#     Lista de notícias
#
# Saída:
#     Lista limpa e padronizada
#
# ============================================

from config import MAX_NEWS

from utils import (
    is_valid_url,
    normalize_text,
    remove_duplicates,
    truncate_summary,
    validate_news,
)

# ============================================
# NORMALIZAÇÃO
# ============================================

def normalize_news(news):
    """
    Normaliza todos os campos textuais
    de uma notícia.
    """

    if not isinstance(news, dict):

        return {}

    normalized = news.copy()

    text_fields = [

        "titulo",
        "descricao",
        "categoria",
        "fonte",
        "link",
        "imagem"

    ]

    for field in text_fields:

        if field in normalized:

            normalized[field] = normalize_text(

                str(normalized[field])

            )

    return normalized


# ============================================
# CAMPOS OBRIGATÓRIOS
# ============================================

def validate_required_fields(news):
    """
    Verifica se a notícia possui
    todos os campos obrigatórios.
    """

    if not validate_news(news):

        return False

    required = [

        "titulo",
        "descricao",
        "imagem",
        "fonte",
        "link"

    ]

    for field in required:

        if field not in news:

            return False

        if not news[field]:

            return False

    return True

# ============================================
# FILTROS INDIVIDUAIS
# ============================================

def filter_invalid_news(news_list):
    """
    Remove notícias com estrutura inválida.
    """

    if not isinstance(news_list, list):

        return []

    valid_news = []

    for news in news_list:

        if validate_required_fields(news):

            valid_news.append(news)

    return valid_news


def filter_invalid_links(news_list):
    """
    Remove notícias com links inválidos.
    """

    filtered = []

    for news in news_list:

        if is_valid_url(news["link"]):

            filtered.append(news)

    return filtered


def filter_invalid_images(news_list):
    """
    Remove notícias sem imagem válida.
    """

    filtered = []

    for news in news_list:

        image = news.get("imagem", "").strip()

        if image and is_valid_url(image):

            filtered.append(news)

    return filtered


def filter_small_descriptions(news_list, minimum_length=50):
    """
    Remove notícias cuja descrição seja
    muito pequena.
    """

    filtered = []

    for news in news_list:

        description = normalize_text(

            news.get("descricao", "")

        )

        if len(description) >= minimum_length:

            filtered.append(news)

    return filtered


def filter_empty_titles(news_list):
    """
    Remove notícias sem título.
    """

    filtered = []

    for news in news_list:

        title = normalize_text(

            news.get("titulo", "")

        )

        if title:

            filtered.append(news)

    return filtered

# ============================================
# LIMPEZA E PADRONIZAÇÃO
# ============================================

def clean_news(news_list):
    """
    Normaliza completamente todas as notícias.
    """

    cleaned = []

    for news in news_list:

        normalized = normalize_news(news)

        normalized["titulo"] = normalize_text(
            normalized.get("titulo", "")
        )

        normalized["descricao"] = truncate_summary(
            normalized.get("descricao", "")
        )

        normalized["categoria"] = normalize_text(
            normalized.get("categoria", "")
        )

        normalized["fonte"] = normalize_text(
            normalized.get("fonte", "")
        )

        cleaned.append(normalized)

    return cleaned


# ============================================
# DUPLICADAS
# ============================================

def remove_duplicate_news(news_list):
    """
    Remove notícias duplicadas utilizando
    o título como chave principal.
    """

    return remove_duplicates(

        news_list,

        key="titulo"

    )


# ============================================
# ORDENAÇÃO
# ============================================

def sort_news(news_list):
    """
    Ordena as notícias da mais recente
    para a mais antiga.

    Caso a data não exista,
    mantém a ordem original.
    """

    try:

        return sorted(

            news_list,

            key=lambda item: item.get("data", ""),

            reverse=True

        )

    except Exception:

        return news_list


# ============================================
# RESUMOS
# ============================================

def normalize_summaries(news_list):
    """
    Garante que todos os resumos
    respeitem o tamanho máximo.
    """

    for news in news_list:

        news["descricao"] = truncate_summary(

            news.get("descricao", "")

        )

    return news_list

# ============================================
# LIMITES
# ============================================

def limit_news(news_list):
    """
    Limita a quantidade máxima de notícias
    utilizando o valor definido em config.py.
    """

    return news_list[:MAX_NEWS]


# ============================================
# ESTATÍSTICAS
# ============================================

def filter_statistics(original_list, filtered_list):
    """
    Retorna estatísticas simples do processo
    de filtragem.
    """

    original = len(original_list)

    final = len(filtered_list)

    removed = original - final

    percentage = 0

    if original > 0:

        percentage = round((removed / original) * 100, 2)

    return {

        "original": original,

        "final": final,

        "removed": removed,

        "percentage_removed": percentage

    }


# ============================================
# ORGANIZAÇÃO
# ============================================

def prepare_news(news_list):
    """
    Executa toda a preparação das notícias
    antes da etapa final de aplicação
    dos filtros.
    """

    news_list = clean_news(news_list)

    news_list = remove_duplicate_news(news_list)

    news_list = normalize_summaries(news_list)

    news_list = sort_news(news_list)

    news_list = limit_news(news_list)

    return news_list

# ============================================
# FILTRO PRINCIPAL
# ============================================

def apply_filters(news_list):
    """
    Executa toda a sequência de filtros do projeto.

    Fluxo:

        1. Validação da estrutura
        2. Remoção de títulos vazios
        3. Remoção de descrições pequenas
        4. Remoção de links inválidos
        5. Remoção de imagens inválidas
        6. Limpeza dos textos
        7. Remoção de duplicadas
        8. Padronização dos resumos
        9. Ordenação
       10. Limitação da quantidade
    """

    if not isinstance(news_list, list):

        return []

    original = news_list.copy()

    news_list = filter_invalid_news(news_list)

    news_list = filter_empty_titles(news_list)

    news_list = filter_small_descriptions(news_list)

    news_list = filter_invalid_links(news_list)

    news_list = filter_invalid_images(news_list)

    news_list = prepare_news(news_list)

    stats = filter_statistics(

        original,

        news_list

    )

    return {

        "news": news_list,

        "statistics": stats

    }


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "apply_filters",

    "prepare_news",

    "clean_news",

    "remove_duplicate_news",

    "filter_invalid_news",

    "filter_invalid_links",

    "filter_invalid_images",

    "filter_small_descriptions",

    "filter_empty_titles",

    "filter_statistics",

    "normalize_news",

    "validate_required_fields"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável exclusivamente
# pela limpeza e validação das notícias.
#
# Nenhum scraping é realizado aqui.
#
# O resultado deste módulo será utilizado
# pelo formatter.py.
#
# ============================================