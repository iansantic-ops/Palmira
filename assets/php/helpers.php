<?php
// Helpers de seguridad y salida
if (!function_exists('safe_out')) {
    /**
     * Escapa texto para salida en HTML manteniendo UTF-8 y evitando XSS
     */
    function safe_out($s) {
        return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sanitize_iframe_or_url')) {
    /**
     * A partir de un input que puede ser un iframe o una URL, devuelve
     * un iframe seguro o una cadena vacía si no es válido.
     * - Si recibe un <iframe ... src="..."> intenta extraer el src.
     * - Si recibe una URL válida (http/https) la usa como src.
     * - Siempre valida que el src sea una URL HTTP/HTTPS.
     */
    function sanitize_iframe_or_url($input) {
        $input = trim((string)$input);
        if ($input === '') return '';

        // Buscar src dentro de un iframe si lo hay
        if (stripos($input, '<iframe') !== false) {
            // simple regex para extraer src
            if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $input, $m)) {
                $src = $m[1];
            } else {
                return '';
            }
        } else {
            $src = $input;
        }

        // Normalizar y validar URL
        $src = trim($src);
        // Si es relativa, rechazar (solo permitir http/https)
        if (!preg_match('#^https?://#i', $src)) return '';
        if (!filter_var($src, FILTER_VALIDATE_URL)) return '';

        // Construir un iframe seguro con atributos restringidos
        $safeSrc = htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<iframe src="' . $safeSrc . '" loading="lazy" style="border:0; width:100%; height:250px;" allowfullscreen></iframe>';
    }
}

?>
