# ============================================
# CONFIGURAÇÕES GERAIS DO PROJETO DYXON
# ============================================
# Este arquivo centraliza TODAS as configurações do scraper.
# Nenhum outro arquivo deve ter valores fixos.
# Tudo é lido daqui para facilitar manutenção e expansão.

# ============================================
# DIRETÓRIOS DO PROJETO
# ============================================
import os

SCRAPER_DIR = os.path.dirname(os.path.abspath(__file__))
PROJECT_DIR = os.path.dirname(SCRAPER_DIR)
ASSETS_DIR = os.path.join(PROJECT_DIR, "assets")
DATA_DIR = os.path.join(ASSETS_DIR, "data")
JSON_OUTPUT = os.path.join(DATA_DIR, "noticias.json")
NEWS_FILE = "noticias.json"
LOGS_DIR = os.path.join(PROJECT_DIR, "logs")

# ============================================
# CONFIGURAÇÃO DO JSON
# ============================================
MAX_NEWS = 50  # Quantidade máxima de notícias no arquivo final

# ============================================
# TIMEOUT E REQUISIÇÕES
# ============================================
REQUEST_TIMEOUT = 10  # Segundos

# ============================================
# USER AGENT PROFISSIONAL
# ============================================
USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 DYXON-Scraper/1.0"

# ============================================
# IDIOMA E TRADUÇÃO
# ============================================
LANGUAGE = "pt-BR"
ENABLE_TRANSLATION = False

# ============================================
# FONTES DE NOTÍCIAS
# ============================================
SOURCES = {
    "bodybuilding": {
        "name": "Bodybuilding.com",
        "url": "https://www.bodybuilding.com",
        "rss": "https://www.bodybuilding.com/rss",
        "enabled": True
    },
    "muscle_strength": {
        "name": "Muscle & Strength",
        "url": "https://www.muscleandstrength.com",
        "rss": "https://www.muscleandstrength.com/rss",
        "enabled": True
    },
    "barbend": {
        "name": "BarBend",
        "url": "https://barbend.com",
        "rss": "https://barbend.com/feed",
        "enabled": True
    },
    "generation_iron": {
        "name": "Generation Iron",
        "url": "https://generationiron.com",
        "rss": None,
        "enabled": True
    }
}

# ============================================
# CATEGORIAS E PALAVRAS-CHAVE
# ============================================
CATEGORIES = ["Treino", "Nutrição", "Saúde", "Campeonatos", "Atletas"]

KEYWORDS = {

    "Treino":[
        "treino",
        "hipertrofia",
        "academia",
        "agachamento",
        "supino",
        "terra",
        "deadlift",
        "bench",
        "leg press",
        "treino de força",
        "musculação",
        "bodybuilding"
    ],

    "Nutrição":[
        "creatina",
        "whey",
        "proteína",
        "suplemento",
        "caseína",
        "bcaa",
        "glutamina",
        "dieta",
        "macros"
    ],

    "Saúde":[
        "recuperação",
        "sono",
        "lesão",
        "saúde"
    ],

    "Atletas":[
        "cbum",
        "arnold",
        "ronnie",
        "atleta"
    ],

    "Campeonatos":[
        "olympia",
        "arnold classic",
        "ifbb",
        "campeonato"
    ]

}

# ============================================
# VISUALIZAÇÕES ALEATÓRIAS
# ============================================
MIN_VIEWS = 500
MAX_VIEWS = 15000

# ============================================
# FORMATO DE DATA (BRASILEIRO)
# ============================================
DATE_FORMAT="%d/%m/%Y"

# ============================================
# LOGS
# ============================================
LOG_FILE = os.path.join(LOGS_DIR, "scraper.log")