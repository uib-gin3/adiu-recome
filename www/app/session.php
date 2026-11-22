<?php
// Session with the interface language and the flash message shared by all pages

session_start();

// The language in the URL wins, then the session one, then Catalan
function currentLanguage()
{
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['ca', 'es', 'en'], true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ca';
}

// One translated text by key
function t($key)
{
    static $texts = null;
    if ($texts === null) {
        $texts = require __DIR__ . '/../lang/' . currentLanguage() . '.php';
    }
    return isset($texts[$key]) ? $texts[$key] : $key;
}

// Escape any dynamic value before printing it
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Quantities without trailing zeros
function formatQuantity($value)
{
    $rounded = round((float)$value, 2);
    return floor($rounded) === $rounded ? (string)(int)$rounded : (string)$rounded;
}

// Keep one short message for the next page load
function flash($message)
{
    $_SESSION['flash'] = $message;
}

// Read and clear the message
function takeFlash()
{
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return '';
}

// The current address with the language changed, so edits and filters survive
function languageUrl($lang)
{
    $params = $_GET;
    $params['lang'] = $lang;
    return basename($_SERVER['SCRIPT_NAME']) . '?' . http_build_query($params);
}
