# ============================================
# COLETOR PRINCIPAL DO PROJETO DYXON
# ============================================
#
# Responsável por integrar todos
# os módulos do sistema.
#
# Fluxo:
#
# Coletores
# ↓
# Tradução
# ↓
# Filtros
# ↓
# Categorias
# ↓
# Formatter
# ↓
# JSON
#
# ============================================

from bodybuilding import run as bodybuilding

from muscle_strength import run as muscle_strength

from barbend import run as barbend

from generation_iron import run as generation_iron

from translator import apply_translation

from filters import apply_filters

from categories import apply_categories

from formatter import format_news_list

from json_store import (
    initialize_store,
    save_json,
)

# ============================================
# INICIALIZAÇÃO
# ============================================

def initialize():

    initialize_store()

    return True

# ============================================
# EXECUÇÃO DOS COLETORES
# ============================================

def collect_bodybuilding():
    """
    Executa o coletor Bodybuilding.
    """

    return bodybuilding()


def collect_muscle_strength():
    """
    Executa o coletor Muscle & Strength.
    """

    return muscle_strength()


def collect_barbend():
    """
    Executa o coletor BarBend.
    """

    return barbend()


def collect_generation_iron():
    """
    Executa o coletor Generation Iron.
    """

    return generation_iron()


# ============================================
# COLETA COMPLETA
# ============================================

def collect_all_news():
    """
    Executa todos os coletores.

    Caso algum coletor apresente erro,
    os demais continuam sendo executados.
    """

    news = []

    collectors = [

        collect_bodybuilding,

        collect_muscle_strength,

        collect_barbend,

        collect_generation_iron,

    ]

    for collector in collectors:

        try:

            result = collector()

            if isinstance(result, list):

                news.extend(result)

        except Exception:

            continue

    return news

# ============================================
# REMOÇÃO DE DUPLICADAS
# ============================================

def remove_duplicates(news_list):
    """
    Remove notícias duplicadas
    utilizando o link como chave.
    """

    unique_news = []

    visited_links = set()

    for news in news_list:

        link = news.get(

            "link",

            ""

        ).strip().lower()

        if not link:

            continue

        if link in visited_links:

            continue

        visited_links.add(

            link

        )

        unique_news.append(

            news

        )

    return unique_news


# ============================================
# PREPARAÇÃO
# ============================================

def prepare_news():
    """
    Executa todos os coletores
    e remove notícias duplicadas.
    """

    news = collect_all_news()

    news = remove_duplicates(

        news

    )

    return news

# ============================================
# TRADUÇÃO
# ============================================

def translate_news(news_list):
    """
    Traduz todas as notícias
    para português.
    """

    result = apply_translation(

        news_list

    )

    return result["news"]


# ============================================
# PREPARAÇÃO DA TRADUÇÃO
# ============================================

def prepare_translation():
    """
    Executa a coleta e a tradução
    das notícias.
    """

    news = prepare_news()

    news = translate_news(

        news

    )

    return news

# ============================================
# FILTROS E CATEGORIAS
# ============================================

def filter_news(news_list):
    """
    Aplica os filtros configurados
    às notícias.
    """

    result = apply_filters(

        news_list

    )

    return result["news"]


def categorize_news(news_list):
    """
    Categoriza todas as notícias.
    """

    result = apply_categories(

        news_list

    )

    return result["news"]


# ============================================
# PREPARAÇÃO DOS DADOS
# ============================================

def prepare_processed_news():
    """
    Executa todas as etapas
    até a categorização.
    """

    news = prepare_translation()

    news = filter_news(

        news

    )

    news = categorize_news(

        news

    )

    return news

# ============================================
# FORMATAÇÃO
# ============================================

def format_news(news_list):
    """
    Formata todas as notícias
    antes da gravação no JSON.
    """

    return format_news_list(

        news_list

    )


# ============================================
# ESTATÍSTICAS
# ============================================

def execution_statistics(news_list):
    """
    Gera estatísticas da execução.
    """

    categories = {}

    for news in news_list:

        category = news.get(

            "categoria",

            "Sem categoria"

        )

        categories[category] = categories.get(

            category,

            0

        ) + 1

    return {

        "total_news": len(news_list),

        "categories": categories

    }


# ============================================
# PREPARAÇÃO FINAL
# ============================================

def prepare_final_news():
    """
    Executa todas as etapas
    até a formatação final.
    """

    news = prepare_processed_news()

    news = format_news(

        news

    )

    return news

# ============================================
# SALVAMENTO
# ============================================

def save_news(news_list):
    """
    Salva as notícias no arquivo JSON.
    """

    save_json(

        news_list

    )

    return news_list


# ============================================
# EXECUÇÃO COMPLETA
# ============================================

def execute_pipeline():
    """
    Executa todo o pipeline
    do projeto.

    Fluxo:

    1. Coleta
    2. Remove duplicadas
    3. Traduz
    4. Filtra
    5. Categoriza
    6. Formata
    7. Salva
    """

    news = prepare_final_news()

    save_news(

        news

    )

    statistics = execution_statistics(

        news

    )

    return {

        "news": news,

        "statistics": statistics

    }

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def run():
    """
    Executa todo o processo
    de coleta do projeto.

    Retorna um dicionário contendo:

    • notícias
    • estatísticas
    """

    initialize()

    return execute_pipeline()


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "run",

    "initialize",

    "collect_bodybuilding",

    "collect_muscle_strength",

    "collect_barbend",

    "collect_generation_iron",

    "collect_all_news",

    "remove_duplicates",

    "prepare_news",

    "translate_news",

    "prepare_translation",

    "filter_news",

    "categorize_news",

    "prepare_processed_news",

    "format_news",

    "prepare_final_news",

    "save_news",

    "execution_statistics",

    "execute_pipeline"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo integra todo o pipeline
# do projeto DYXON.
#
# Fluxo completo:
#
# • executa todos os coletores;
# • remove notícias duplicadas;
# • traduz para português;
# • aplica filtros;
# • categoriza;
# • formata;
# • salva no noticias.json;
# • retorna estatísticas.
#
# Este é o ponto central do sistema e
# será utilizado pelo agendador para
# atualizar automaticamente o
# noticias.json.
#
# ============================================