<?php
// includes/helpers.php - Helper functions for security, JSON, and templating

function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function renderHuaLogoSvg($variant = 'positive') {
    // Renders the official HUA logo using authentic branding from HUA_huisstijlmanual digitaal_2.pdf
    // Pantone 5255 purple: #010044, White: #ffffff
    $isWhite = ($variant === 'white' || $variant === 'diapositief');
    $fill = $isWhite ? '#ffffff' : '#010044';
    $slashFill = $isWhite ? '#ffffff' : '#010044';

    return <<<SVG
<svg class="hua-brand-logo hua-logo-{$variant}" viewBox="0 0 240 100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Logo Het Utrechts Archief">
    <g fill="{$fill}" font-family="Arial, 'Helvetica Neue', Helvetica, sans-serif" font-weight="900" font-style="italic">
        <text x="0" y="24" font-size="24" letter-spacing="2">HET</text>
        <text x="65" y="24" font-size="22" font-weight="400" fill="{$slashFill}" letter-spacing="1">//////////</text>
        
        <text x="0" y="50" font-size="24" letter-spacing="2">UTRECHTS</text>
        
        <text x="0" y="74" font-size="24" letter-spacing="2">ARCHIEF</text>
        <text x="135" y="74" font-size="22" font-weight="400" fill="{$slashFill}" letter-spacing="1">//</text>

        <text x="0" y="93" font-size="18" font-weight="400" fill="{$slashFill}" letter-spacing="2">////////////////</text>
    </g>
</svg>
SVG;
}

function renderDiagonalDivider() {
    return '<div class="hua-diagonal-strip" aria-hidden="true">/ / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / / /</div>';
}
