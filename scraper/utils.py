# ============================================
# UTILITÁRIOS GERAIS DO PROJETO DYXON
# ============================================
#
# Biblioteca de funções reutilizáveis.
# Este arquivo NÃO coleta notícias.
#
# Todos os coletores utilizarão estas funções.
#
# Nenhuma função é executada automaticamente.
#
# ============================================

import json
import logging
import os
import re

from datetime import datetime
from urllib.parse import urljoin, urlparse

import requests
from requests.exceptions import (
    ConnectionError,
    HTTPError,
    Timeout,
)

from config import (
    DATA_DIR,
    JSON_OUTPUT,
    LOG_FILE,
    LOGS_DIR,
    REQUEST_TIMEOUT,
    USER_AGENT,
)

# ============================================
# DIRETÓRIOS
# ============================================

def create_directories():
    """
    Cria automaticamente os diretórios utilizados
    pelo projeto caso ainda não existam.
    """

    os.makedirs(DATA_DIR, exist_ok=True)
    os.makedirs(LOGS_DIR, exist_ok=True)


# ============================================
# DOWNLOAD DE PÁGINAS
# ============================================

def fetch_page(url):
    """
    Realiza o download de uma página utilizando
    as configurações definidas no config.py.

    Retorna sempre um dicionário padronizado.
    """

    headers = {
        "User-Agent": USER_AGENT
    }

    try:

        response = requests.get(
            url,
            headers=headers,
            timeout=REQUEST_TIMEOUT
        )

        response.raise_for_status()

        return {

            "success": True,
            "status_code": response.status_code,
            "url": response.url,
            "content": response.text,
            "error": None

        }

    except Timeout:

        return {

            "success": False,
            "status_code": None,
            "url": url,
            "content": None,
            "error": "Timeout"

        }

    except ConnectionError:

        return {

            "success": False,
            "status_code": None,
            "url": url,
            "content": None,
            "error": "ConnectionError"

        }

    except HTTPError as erro:

        return {

            "success": False,
            "status_code": getattr(
                erro.response,
                "status_code",
                None
            ),
            "url": url,
            "content": None,
            "error": str(erro)

        }

    except Exception as erro:

        return {

            "success": False,
            "status_code": None,
            "url": url,
            "content": None,
            "error": str(erro)

        }

# ============================================
# JSON
# ============================================

def save_json(data, filepath=JSON_OUTPUT):
    """
    Salva um objeto Python em formato JSON.

    Retorna True em caso de sucesso.
    """

    try:

        create_directories()

        with open(
            filepath,
            "w",
            encoding="utf-8"
        ) as arquivo:

            json.dump(
                data,
                arquivo,
                ensure_ascii=False,
                indent=4
            )

        return True

    except Exception as erro:

        log_message(
            f"Erro ao salvar JSON: {erro}",
            module="utils"
        )

        return False


def load_json(filepath=JSON_OUTPUT):
    """
    Carrega um arquivo JSON.

    Retorna lista vazia caso não exista
    ou ocorra algum erro.
    """

    try:

        if not os.path.exists(filepath):

            return []

        with open(
            filepath,
            "r",
            encoding="utf-8"
        ) as arquivo:

            return json.load(arquivo)

    except Exception as erro:

        log_message(
            f"Erro ao ler JSON: {erro}",
            module="utils"
        )

        return []


def validate_json(data):
    """
    Verifica se o objeto pode ser utilizado
    como estrutura JSON.
    """

    return isinstance(data, (dict, list))


# ============================================
# LOGS
# ============================================

def setup_logging():
    """
    Configura o sistema de logs do projeto.
    """

    create_directories()

    logging.basicConfig(

        level=logging.INFO,

        format="%(asctime)s [%(levelname)s] %(message)s",

        handlers=[

            logging.FileHandler(
                LOG_FILE,
                encoding="utf-8"
            ),

            logging.StreamHandler()

        ]

    )


def log_message(message, module="utils"):
    """
    Registra uma mensagem no arquivo de log.
    """

    logging.info(

        "[%s] %s",

        module,

        message

    )


# ============================================
# DATAS
# ============================================

def parse_date(date_string, formats=None):
    """
    Converte uma string em datetime.

    Retorna None caso não consiga converter.
    """

    if not date_string:

        return None

    if formats is None:

        formats = [

            "%Y-%m-%d",

            "%d/%m/%Y",

            "%Y-%m-%dT%H:%M:%S",

            "%Y-%m-%d %H:%M:%S",

            "%B %d, %Y"

        ]

    for fmt in formats:

        try:

            return datetime.strptime(

                date_string.strip(),

                fmt

            )

        except ValueError:

            continue

    return None


def format_date_for_display(date_obj):
    """
    Formata uma data para exibição.

    Hoje

    Ontem

    dd/mm/aaaa
    """

    if date_obj is None:

        return ""

    hoje = datetime.now().date()

    data = date_obj.date()

    if data == hoje:

        return "Hoje"

    if (hoje - data).days == 1:

        return "Ontem"

    return date_obj.strftime("%d/%m/%Y")

# ============================================
# TEXTO
# ============================================

def remove_html(text):
    """
    Remove todas as tags HTML.
    """

    if not text:

        return ""

    return re.sub(r"<.*?>", "", text)


def remove_line_breaks(text):
    """
    Remove quebras de linha.
    """

    if not text:

        return ""

    return text.replace("\r", " ").replace("\n", " ")


def remove_extra_spaces(text):
    """
    Remove espaços duplicados.
    """

    if not text:

        return ""

    return re.sub(r"\s+", " ", text).strip()


def normalize_text(text):
    """
    Padroniza completamente um texto.

    Remove:

    • HTML

    • Quebras de linha

    • Espaços extras
    """

    if not text:

        return ""

    text = remove_html(text)

    text = remove_line_breaks(text)

    text = remove_extra_spaces(text)

    return text


def truncate_summary(text, max_chars=200):
    """
    Limita um resumo sem cortar palavras.
    """

    text = normalize_text(text)

    if len(text) <= max_chars:

        return text

    text = text[:max_chars]

    return text.rsplit(" ", 1)[0] + "..."


def slugify(text):
    """
    Converte um texto em slug.

    Exemplo:

    Arnold Classic 2026

    →

    arnold-classic-2026
    """

    text = normalize_text(text)

    text = text.lower()

    text = re.sub(r"[^\w\s-]", "", text)

    text = re.sub(r"[-\s]+", "-", text)

    return text.strip("-")


def safe_filename(text):
    """
    Converte um texto em nome de arquivo seguro.
    """

    text = normalize_text(text)

    text = re.sub(r"[<>:\"/\\\\|?*]", "", text)

    text = text.replace(" ", "_")

    return text[:120]


# ============================================
# IDENTIFICAÇÃO
# ============================================

def get_timestamp():
    """
    Retorna timestamp para logs e arquivos.
    """

    return datetime.now().strftime("%Y%m%d_%H%M%S")


def get_current_datetime():
    """
    Retorna data e hora atual.
    """

    return datetime.now()


def get_current_date():
    """
    Retorna apenas a data atual.
    """

    return datetime.now().date()

# ============================================
# URLS
# ============================================

def is_valid_url(url):
    """
    Verifica se uma URL é válida.
    """

    if not url:

        return False

    try:

        parsed = urlparse(url)

        return all([parsed.scheme, parsed.netloc])

    except Exception:

        return False


def make_absolute_url(base_url, link):
    """
    Converte links relativos em absolutos.
    """

    if not link:

        return ""

    return urljoin(base_url, link)


def remove_url_parameters(url):
    """
    Remove parâmetros da URL.

    Exemplo:

    site.com/post?utm=123

    →

    site.com/post
    """

    if not url:

        return ""

    parsed = urlparse(url)

    return f"{parsed.scheme}://{parsed.netloc}{parsed.path}"


# ============================================
# IMAGENS
# ============================================

def is_valid_image(url):
    """
    Verifica se a URL parece ser uma imagem.
    """

    if not is_valid_url(url):

        return False

    extensoes = (

        ".jpg",
        ".jpeg",
        ".png",
        ".webp",
        ".gif",
        ".bmp",
        ".avif"

    )

    return url.lower().endswith(extensoes)


def image_exists(url):
    """
    Verifica se uma imagem realmente existe.
    """

    if not is_valid_url(url):

        return False

    try:

        resposta = requests.head(

            url,

            headers={

                "User-Agent": USER_AGENT

            },

            timeout=REQUEST_TIMEOUT,

            allow_redirects=True

        )

        return resposta.status_code == 200

    except Exception:

        return False


def get_image_or_default(url, default=""):
    """
    Retorna a imagem original caso exista.

    Caso contrário retorna a imagem padrão.
    """

    if image_exists(url):

        return url

    return default


# ============================================
# ARQUIVOS
# ============================================

def file_exists(filepath):
    """
    Verifica se um arquivo existe.
    """

    return os.path.isfile(filepath)


def get_file_size(filepath):
    """
    Retorna o tamanho do arquivo em bytes.
    """

    try:

        return os.path.getsize(filepath)

    except Exception:

        return 0


def delete_file(filepath):
    """
    Remove um arquivo se existir.
    """

    try:

        if os.path.isfile(filepath):

            os.remove(filepath)

            return True

    except Exception:

        pass

    return False

# ============================================
# VALIDAÇÕES
# ============================================

def is_empty(value):
    """
    Verifica se um valor está vazio.
    """

    if value is None:
        return True

    if isinstance(value, str):
        return value.strip() == ""

    if isinstance(value, (list, dict, tuple, set)):
        return len(value) == 0

    return False


def remove_duplicates(items, key=None):
    """
    Remove itens duplicados preservando a ordem.

    Se key for informado, ele será utilizado
    como chave de comparação.
    """

    resultado = []
    vistos = set()

    for item in items:

        valor = item

        if key and isinstance(item, dict):

            valor = item.get(key)

        if valor in vistos:
            continue

        vistos.add(valor)
        resultado.append(item)

    return resultado


def safe_int(value, default=0):
    """
    Converte para inteiro com segurança.
    """

    try:

        return int(value)

    except Exception:

        return default


def safe_float(value, default=0.0):
    """
    Converte para float com segurança.
    """

    try:

        return float(value)

    except Exception:

        return default


def limit_list(items, maximum):
    """
    Limita a quantidade de itens de uma lista.
    """

    if not isinstance(items, list):

        return []

    return items[:maximum]


# ============================================
# DICIONÁRIOS
# ============================================

def merge_dicts(dict1, dict2):
    """
    Une dois dicionários.

    dict2 sobrescreve dict1.
    """

    resultado = dict1.copy()

    resultado.update(dict2)

    return resultado


def remove_none_values(data):
    """
    Remove chaves cujo valor seja None.
    """

    if not isinstance(data, dict):

        return data

    return {

        chave: valor

        for chave, valor in data.items()

        if valor is not None

    }


# ============================================
# ESTATÍSTICAS
# ============================================

def calculate_percentage(part, total):
    """
    Calcula porcentagem.
    """

    if total == 0:

        return 0

    return (part / total) * 100


# ============================================
# DEBUG
# ============================================

def print_separator():
    """
    Exibe um separador visual no terminal.
    """

    print("=" * 70)


def print_title(title):
    """
    Exibe um título padronizado.
    """

    print_separator()

    print(title)

    print_separator()

# ============================================
# REQUISIÇÕES HTTP
# ============================================

def get_status_code(url):
    """
    Retorna apenas o código HTTP de uma URL.
    """

    if not is_valid_url(url):

        return None

    try:

        resposta = requests.head(

            url,

            headers={

                "User-Agent": USER_AGENT

            },

            timeout=REQUEST_TIMEOUT,

            allow_redirects=True

        )

        return resposta.status_code

    except Exception:

        return None


# ============================================
# VALIDAÇÃO DE NOTÍCIAS
# ============================================

def validate_news(news):
    """
    Valida se uma notícia possui os campos mínimos
    necessários para entrar no noticias.json.
    """

    if not isinstance(news, dict):

        return False

    required = [

        "titulo",
        "descricao",
        "imagem",
        "fonte",
        "link"

    ]

    for campo in required:

        if campo not in news:

            return False

        if is_empty(news[campo]):

            return False

    return True


def validate_news_list(news_list):
    """
    Remove notícias inválidas de uma lista.
    """

    if not isinstance(news_list, list):

        return []

    return [

        noticia

        for noticia in news_list

        if validate_news(noticia)

    ]


# ============================================
# INFORMAÇÕES
# ============================================

def project_info():
    """
    Retorna informações básicas do módulo.
    """

    return {

        "project": "DYXON Scraper",

        "module": "utils.py",

        "version": "1.0.0",

        "status": "stable"

    }


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo contém apenas funções auxiliares
# reutilizadas pelos demais módulos do scraper.
#
# Nenhuma função é executada automaticamente.
#
# Os próximos arquivos utilizarão estas funções
# para evitar duplicação de código.
#
# ============================================