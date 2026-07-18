# ============================================
# TRADUTOR DO PROJETO DYXON
# ============================================
#
# Responsável por traduzir textos
# para português.
#
# Este módulo não realiza:
#
# • scraping
# • filtros
# • categorização
# • formatter
# • geração de JSON
#
# ============================================

from deep_translator import GoogleTranslator

from config import (
    LANGUAGE,
    ENABLE_TRANSLATION,
)

from utils import (
    normalize_text,
)

# ============================================
# CONFIGURAÇÃO
# ============================================

SOURCE_LANGUAGE = "auto"

TARGET_LANGUAGE = LANGUAGE.split("-")[0].lower()

TRANSLATOR = GoogleTranslator(

    source=SOURCE_LANGUAGE,

    target=TARGET_LANGUAGE

)


# ============================================
# TRADUTOR
# ============================================

def create_translator():
    """
    Cria a instância do tradutor.
    """

    return GoogleTranslator(

        source=SOURCE_LANGUAGE,

        target=TARGET_LANGUAGE

    )

# ============================================
# TRADUÇÃO DE TEXTO
# ============================================

def translate_text(text):
    """
    Traduz um texto para português.

    Caso a tradução esteja desativada
    ou ocorra algum erro, retorna
    o texto original.
    """

    text = normalize_text(text)

    if not text:

        return ""

    if not ENABLE_TRANSLATION:

        return text

    try:

       translated = TRANSLATOR.translate(

           text

       )

       return normalize_text(

           translated

       )

    except Exception:

        return text


# ============================================
# TRADUÇÃO DE CAMPOS
# ============================================

def translate_title(title):
    """
    Traduz o título da notícia.
    """

    return translate_text(

        title

    )


def translate_description(description):
    """
    Traduz a descrição da notícia.
    """

    return translate_text(

        description

    )

# ============================================
# TRADUÇÃO DE NOTÍCIAS
# ============================================

def translate_news(news):
    """
    Traduz os campos textuais
    de uma única notícia.
    """

    if not isinstance(news, dict):

        return {}

    translated = news.copy()

    translated["titulo"] = translate_title(

        translated.get(

            "titulo",

            ""

        )

    )

    translated["descricao"] = translate_description(

        translated.get(

            "descricao",

            ""

        )

    )

    return translated


def translate_news_list(news_list):
    """
    Traduz todas as notícias
    de uma lista.
    """

    if not isinstance(news_list, list):

        return []

    translated_news = []

    for news in news_list:

        translated_news.append(

            translate_news(news)

        )

    return translated_news


# ============================================
# VALIDAÇÃO
# ============================================

def validate_translation(news):
    """
    Garante que os campos obrigatórios
    permaneçam válidos após a tradução.
    """

    if not isinstance(news, dict):

        return False

    if not news.get("titulo"):

        return False

    if not news.get("descricao"):

        return False

    return True

# ============================================
# ESTATÍSTICAS
# ============================================

def translation_statistics(original_news, translated_news):
    """
    Gera estatísticas do processo
    de tradução.
    """

    return {

        "original_news": len(original_news),

        "translated_news": len(translated_news),

        "language": LANGUAGE,

        "translation_enabled": ENABLE_TRANSLATION

    }


# ============================================
# PREPARAÇÃO
# ============================================

def prepare_translation(news_list):
    """
    Traduz todas as notícias e remove
    registros inválidos.
    """

    translated_news = []

    for news in translate_news_list(news_list):

        if validate_translation(news):

            translated_news.append(news)

    return translated_news

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def apply_translation(news_list):
    """
    Executa todo o processo de tradução
    das notícias.

    Fluxo:

        1. Traduz todas as notícias.
        2. Remove registros inválidos.
        3. Gera estatísticas.

    Retorna um dicionário contendo a lista
    traduzida e as estatísticas.
    """

    if not isinstance(news_list, list):

        return {

            "news": [],

            "statistics": {

                "original_news": 0,

                "translated_news": 0,

                "language": LANGUAGE,

                "translation_enabled": ENABLE_TRANSLATION

            }

        }

    translated_news = prepare_translation(

        news_list

    )

    statistics = translation_statistics(

        news_list,

        translated_news

    )

    return {

        "news": translated_news,

        "statistics": statistics

    }


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "apply_translation",

    "prepare_translation",

    "translate_news",

    "translate_news_list",

    "translate_text",

    "translate_title",

    "translate_description",

    "validate_translation",

    "translation_statistics",

    "create_translator"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável exclusivamente
# pela tradução dos textos das notícias.
#
# Responsabilidades:
#
# • traduzir títulos;
# • traduzir descrições;
# • manter os demais campos intactos;
# • gerar estatísticas da tradução.
#
# Não realiza:
#
# • scraping;
# • filtros;
# • categorização;
# • formatação;
# • geração de JSON.
#
# O resultado será utilizado pelo
# collect_news.py.
#
# ============================================