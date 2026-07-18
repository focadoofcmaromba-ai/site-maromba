# ============================================
# DYXON SCRAPER
# ============================================
#
# Arquivo principal do projeto.
#
# Responsável por iniciar toda
# a coleta de notícias.
#
# ============================================

from datetime import datetime
import sys
import traceback

from collect_news import run

from utils import (
    print_separator,
    print_title,
)


# ============================================
# INFORMAÇÕES
# ============================================

APP_NAME = "DYXON Scraper"

VERSION = "1.0.0"


# ============================================
# INICIALIZAÇÃO
# ============================================

def start():

    print_title(APP_NAME)

    print(

        f"Iniciado em: {datetime.now()}"

    )

    print_separator()

# ============================================
# EXECUÇÃO
# ============================================

def execute():
    """
    Executa o pipeline principal
    do scraper.
    """

    try:

        result = run()

        news = result.get(

            "news",

            []

        )

        statistics = result.get(

            "statistics",

            {}

        )

        print(

            f"Notícias processadas: {len(news)}"

        )

        print(

            "Estatísticas:"

        )

        print(

            statistics

        )

        print_separator()

        print(

            "Processo concluído com sucesso."

        )

        return 0

    except Exception:

        print_separator()

        print(

            "Erro durante a execução do scraper."

        )

        print_separator()

        traceback.print_exc()

        return 1

# ============================================
# ENCERRAMENTO
# ============================================

def finish(exit_code):
    """
    Finaliza a execução do scraper.
    """

    print_separator()

    print(

        f"Finalizado em: {datetime.now()}"

    )

    print_separator()

    sys.exit(exit_code)


# ============================================
# PONTO DE ENTRADA
# ============================================

if __name__ == "__main__":

    start()

    exit_code = execute()

    finish(exit_code)