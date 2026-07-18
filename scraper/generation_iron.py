# ============================================
# COLETOR GENERATION IRON
# ============================================
#
# Responsável por coletar notícias do
# Generation Iron.
#
# Este módulo apenas coleta os dados.
#
# Não realiza:
#
# • filtros
# • categorização
# • tradução
# • geração de JSON
#
# ============================================

from bs4 import BeautifulSoup

from config import (
    SOURCES,
    MAX_NEWS,
)

from utils import (
    fetch_page,
    make_absolute_url,
    normalize_text,
)

# ============================================
# CONFIGURAÇÃO DA FONTE
# ============================================

SOURCE = SOURCES["generation_iron"]

SOURCE_NAME = SOURCE["name"]

BASE_URL = SOURCE["url"]

NEWS_URL = "https://generationiron.com"

# ============================================
# DOWNLOAD DA PÁGINA
# ============================================

def download_news_page():
    """
    Realiza o download da página principal
    do Generation Iron.
    """

    return fetch_page(

        NEWS_URL

    )


# ============================================
# HTML
# ============================================

def create_soup(html):
    """
    Cria um objeto BeautifulSoup.
    """

    return BeautifulSoup(

        html,

        "html.parser"

    )

# ============================================
# ARTIGOS
# ============================================

def find_articles(soup):
    """
    Localiza apenas os links dos artigos.
    """

    if soup is None:

        return []

    articles = []

    for link in soup.find_all("a", href=True):

        href = link["href"]

        if "/20" in href:

            articles.append(link)

    return articles


# ============================================
# EXTRAÇÃO DOS LINKS
# ============================================

def extract_article_links(soup):
    """
    Extrai os links dos artigos encontrados
    na página principal.
    """

    links = []

    for article in find_articles(soup):

        href = article.get("href")

        if not href:

            continue

        href = make_absolute_url(

            BASE_URL,

            href

        )

        href = normalize_text(href)

        if "/20" not in href:

            continue

        if href not in links:

            links.append(href)

        if len(links) >= MAX_NEWS:

            break

    return links


# ============================================
# DOWNLOAD DOS ARTIGOS
# ============================================

def download_article(url):
    """
    Realiza o download de um artigo.
    """

    return fetch_page(

        url

    )


def create_article_soup(response):
    """
    Cria um BeautifulSoup para o artigo.
    """

    if not response["success"]:

        return None

    return BeautifulSoup(

        response["content"],

        "html.parser"

    )

# ============================================
# EXTRAÇÃO DO TÍTULO
# ============================================

def extract_title(soup):
    """
    Extrai o título do artigo.
    """

    if soup is None:

        return ""

    title = soup.find("h1")

    if title is None:

        return ""

    return normalize_text(

        title.get_text()

    )


# ============================================
# EXTRAÇÃO DA IMAGEM
# ============================================

def extract_image(soup):
    """
    Extrai a imagem principal do artigo.
    """

    if soup is None:

        return ""

    meta = soup.find(

        "meta",

        property="og:image"

    )

    if meta and meta.get("content"):

        return normalize_text(

            meta["content"]

        )

    image = soup.find("img")

    if image and image.get("src"):

        return make_absolute_url(

            BASE_URL,

            image["src"]

        )

    return ""


# ============================================
# EXTRAÇÃO DA DATA
# ============================================

def extract_date(soup):
    """
    Extrai a data de publicação.
    """

    if soup is None:

        return ""

    meta = soup.find(

        "meta",

        property="article:published_time"

    )

    if meta and meta.get("content"):

        return normalize_text(

            meta["content"]

        )

    time_tag = soup.find("time")

    if time_tag:

        if time_tag.get("datetime"):

            return normalize_text(

                time_tag["datetime"]

            )

        return normalize_text(

            time_tag.get_text()

        )

    return ""


# ============================================
# EXTRAÇÃO DA FONTE
# ============================================

def extract_source():
    """
    Retorna o nome da fonte.
    """

    return SOURCE_NAME


# ============================================
# EXTRAÇÃO DO LINK
# ============================================

def extract_link(url):
    """
    Retorna o link do artigo.
    """

    return normalize_text(url)

# ============================================
# EXTRAÇÃO DA DESCRIÇÃO
# ============================================

def extract_description(soup):
    """
    Extrai a descrição do artigo.
    """

    if soup is None:

        return ""

    meta = soup.find(

        "meta",

        attrs={"name": "description"}

    )

    if meta and meta.get("content"):

        return normalize_text(

            meta["content"]

        )

    article = soup.find("article")

    if article:

        paragraphs = article.find_all("p")

    else:

        paragraphs = soup.find_all("p")

    description = []

    for paragraph in paragraphs:

        text = normalize_text(

            paragraph.get_text()

        )

        if len(text) < 40:

            continue

        description.append(text)

        if len(description) >= 3:

            break

    return normalize_text(

        " ".join(description)

    )


# ============================================
# MONTAGEM DA NOTÍCIA
# ============================================

def build_news(article_url):
    """
    Monta um dicionário contendo
    todas as informações da notícia.
    """

    response = download_article(

        article_url

    )

    if not response["success"]:

        return None

    soup = create_article_soup(

        response

    )

    news = {

        "titulo": extract_title(soup),

        "descricao": extract_description(soup),

        "imagem": extract_image(soup),

        "categoria": "",

        "visualizacoes": 0,

        "data": extract_date(soup),

        "fonte": extract_source(),

        "link": extract_link(article_url)

    }

    return news

# ============================================
# COLETA DAS NOTÍCIAS
# ============================================

def collect_news():
    """
    Coleta todas as notícias disponíveis
    na página principal do Generation Iron.
    """

    response = download_news_page()

    if not response["success"]:

        return []

    soup = create_soup(

        response["content"]

    )

    article_links = extract_article_links(

        soup

    )

    news_list = []

    for article_url in article_links:

        try:

            news = build_news(

                article_url

            )

            if news is None:

                continue

            if not news["titulo"]:

                continue

            news_list.append(

                news

            )

        except Exception:

            continue

    return news_list


# ============================================
# INFORMAÇÕES
# ============================================

def collector_info():
    """
    Retorna informações do coletor.
    """

    return {

        "source": SOURCE_NAME,

        "url": NEWS_URL,

        "enabled": SOURCE["enabled"]

    }


# ============================================
# TESTE
# ============================================

def test_connection():
    """
    Verifica se a página principal
    pode ser acessada.
    """

    response = download_news_page()

    return response["success"]

# ============================================
# PROCESSAMENTO PRINCIPAL
# ============================================

def run():
    """
    Executa o coletor do Generation Iron.

    Retorna uma lista contendo todas
    as notícias encontradas.
    """

    return collect_news()


# ============================================
# EXPORTAÇÃO
# ============================================

__all__ = [

    "run",

    "collect_news",

    "collector_info",

    "test_connection",

    "download_news_page",

    "download_article",

    "extract_article_links",

    "extract_title",

    "extract_description",

    "extract_image",

    "extract_date",

    "extract_link",

    "extract_source",

    "build_news"

]


# ============================================
# FIM DO ARQUIVO
# ============================================
#
# Este módulo é responsável exclusivamente
# pela coleta de notícias do Generation Iron.
#
# Responsabilidades:
#
# • baixar a página principal;
# • localizar os artigos;
# • acessar cada artigo;
# • extrair:
#     - título
#     - descrição
#     - imagem
#     - data
#     - link
#     - fonte
#
# Não realiza:
#
# • filtros
# • categorização
# • tradução
# • geração de JSON
#
# O resultado será utilizado pelo
# collect_news.py.
#
# ============================================