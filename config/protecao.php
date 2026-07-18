<?php
/**
 * Arquivo: config/protecao.php
 * Objetivo: Enviar cabeçalhos de segurança para todas as páginas do sistema.
 */

if (!headers_sent()) {

    // Impede que o site seja carregado dentro de iframes de outros domínios
    header('X-Frame-Options: SAMEORIGIN');

    // Impede o navegador de interpretar tipos de arquivos incorretamente
    header('X-Content-Type-Options: nosniff');

    // Política de referência
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Desativa recursos do navegador que o site não utiliza
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), accelerometer=(), gyroscope=()');

    // Força HTTPS (somente quando a conexão realmente for HTTPS)
    if (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    ) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // Remove identificação da tecnologia utilizada
    header_remove('X-Powered-By');

    // Política de Segurança de Conteúdo (CSP)
    header(
        "Content-Security-Policy: "
        . "default-src 'self'; "
        . "script-src 'self' 'unsafe-inline'; "
        . "style-src 'self' 'unsafe-inline'; "
        . "img-src 'self' data: https:; "
        . "font-src 'self' data:; "
        . "connect-src 'self'; "
        . "object-src 'none'; "
        . "base-uri 'self'; "
        . "frame-ancestors 'self'; "
        . "form-action 'self';"
    );
}
?>